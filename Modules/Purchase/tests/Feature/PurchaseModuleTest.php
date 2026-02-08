<?php

namespace Modules\Purchase\tests\Feature;

use App\Models\Admin;
use App\Models\Ledger;
use App\Models\Stock;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Accounts\app\Models\Account;
use Modules\Ingredient\app\Models\Category;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\UnitType;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Purchase\app\Services\PurchaseService;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;
use Modules\Supplier\app\Services\SupplierService;
use Tests\TestCase;

class PurchaseModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected PurchaseService $purchaseService;
    protected SupplierService $supplierService;
    protected Admin $admin;
    protected Supplier $supplier;
    protected Ingredient $ingredient;
    protected Account $cashAccount;
    protected Account $bankAccount;
    protected UnitType $unit;
    protected float $initialStock = 100;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchaseService = app(PurchaseService::class);
        $this->supplierService = app(SupplierService::class);

        // Login as admin
        $this->admin = Admin::first();
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found in database');
        }
        auth('admin')->login($this->admin);
        Auth::shouldUse('admin');

        // Seed setting cache for invoice generation
        Cache::put('setting', (object) [
            'invoice_prefix' => 'TEST-',
            'invoice_suffix' => '1',
            'app_name' => 'Test App',
        ]);

        // Create or find test unit
        $this->unit = UnitType::firstOrCreate(
            ['name' => 'Piece'],
            ['ShortName' => 'Pc', 'status' => 1, 'operator' => '*', 'operator_value' => 1]
        );

        // Create test category
        $category = Category::firstOrCreate(
            ['name' => 'Test Category'],
            ['status' => 1]
        );

        // Create test ingredient with known stock
        $this->ingredient = Ingredient::create([
            'name' => 'Test Ingredient ' . uniqid(),
            'category_id' => $category->id,
            'unit_id' => $this->unit->id,
            'purchase_unit_id' => $this->unit->id,
            'purchase_price' => 100,
            'cost' => 100,
            'stock' => $this->initialStock,
            'stock_status' => 'in_stock',
            'sku' => 'TEST-' . uniqid(),
            'status' => 1,
        ]);

        // Create test supplier
        $this->supplier = Supplier::create([
            'name' => 'Test Supplier ' . uniqid(),
            'company' => 'Test Company',
            'phone' => '01700000000',
            'email' => 'test@supplier.com',
            'status' => 1,
        ]);

        // Create cash account
        $this->cashAccount = Account::firstOrCreate(
            ['account_type' => 'cash'],
            ['bank_account_name' => 'Cash Register']
        );

        // Create bank account
        $this->bankAccount = Account::firstOrCreate(
            ['account_type' => 'bank'],
            ['bank_account_name' => 'Test Bank Account']
        );
    }

    // ==========================================
    // Helper: Build a purchase request
    // ==========================================

    private function makePurchaseRequest(array $overrides = []): Request
    {
        $defaults = [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => null,
            'invoice_number' => 'TEST-INV-' . uniqid(),
            'memo_no' => 'MEMO-' . uniqid(),
            'reference_no' => 'REF-' . uniqid(),
            'purchase_date' => now()->format('d-m-Y'),
            'items' => 1,
            'total_amount' => 1000,
            'due_amount' => 0,
            'payment_type' => ['cash'],
            'paid_amount' => [1000],
            'account_id' => ['cash'],
            'note' => 'Test purchase',
            'ingredient_id' => [$this->ingredient->id],
            'quantity' => [10],
            'unit_price' => [100],
            'total' => [1000],
            'purchase_unit_id' => [$this->unit->id],
        ];

        $request = new Request();
        $request->merge(array_merge($defaults, $overrides));
        return $request;
    }

    private function makeReturnRequest(Purchase $purchase, array $overrides = []): Request
    {
        $defaults = [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => null,
            'purchase_id' => $purchase->id,
            'return_type_id' => null,
            'return_date' => now()->format('d-m-Y'),
            'note' => 'Test return',
            'payment_type' => 'cash',
            'account_id' => null,
            'received_amount' => 300,
            'invoice_amount' => 500,
            'shipping_cost' => 0,
            'invoice_number' => $purchase->invoice_number,
            'ingredient_id' => [$this->ingredient->id],
            'return_quantity' => [5],
            'return_subtotal' => [500],
            'return_unit_id' => [$this->unit->id],
        ];

        $request = new Request();
        $request->merge(array_merge($defaults, $overrides));
        return $request;
    }

    // ==========================================
    // Purchase CRUD & Payments
    // ==========================================

    /** @test */
    public function test_can_create_purchase_with_single_cash_payment()
    {
        $request = $this->makePurchaseRequest([
            'total_amount' => 1000,
            'due_amount' => 0,
            'payment_type' => ['cash'],
            'paid_amount' => [1000],
            'account_id' => ['cash'],
        ]);

        $purchase = $this->purchaseService->store($request);

        // Verify purchase record
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'total_amount' => 1000,
            'paid_amount' => 1000,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);

        // Verify payment created
        $this->assertEquals(1, $purchase->payments()->count());
        $payment = $purchase->payments()->first();
        $this->assertEquals('purchase', $payment->payment_type);
        $this->assertEquals(1000, $payment->amount);
        $this->assertEquals(1, $payment->is_paid);

        // Verify stock increased
        $this->ingredient->refresh();
        $this->assertEquals($this->initialStock + 10, (float) $this->ingredient->getRawOriginal('stock'));

        // Verify supplier total_purchase
        $this->supplier->load('purchases');
        $this->assertEquals(1000, $this->supplier->total_purchase);
    }

    /** @test */
    public function test_can_create_purchase_with_partial_payment_shows_due()
    {
        $request = $this->makePurchaseRequest([
            'total_amount' => 1000,
            'due_amount' => 300,
            'payment_type' => ['cash'],
            'paid_amount' => [700],
            'account_id' => ['cash'],
        ]);

        $purchase = $this->purchaseService->store($request);

        $this->assertEquals('due', $purchase->payment_status);
        $this->assertEquals(700, (float) $purchase->paid_amount);
        $this->assertEquals(300, (float) $purchase->due_amount);

        // Verify supplier due
        $this->supplier->load('purchases', 'payments', 'purchaseReturn');
        $this->assertEquals(300, $this->supplier->total_due);
    }

    /** @test */
    public function test_can_create_purchase_with_multiple_payment_methods()
    {
        $cashBalanceBefore = $this->cashAccount->balance();
        $bankBalanceBefore = $this->bankAccount->balance();

        $request = $this->makePurchaseRequest([
            'total_amount' => 1000,
            'due_amount' => 0,
            'payment_type' => ['cash', 'bank'],
            'paid_amount' => [600, 400],
            'account_id' => ['cash', $this->bankAccount->id],
        ]);

        $purchase = $this->purchaseService->store($request);

        // Verify 2 payment records created
        $payments = $purchase->payments;
        $this->assertEquals(2, $payments->count());

        // Verify cash payment
        $cashPayment = $payments->where('account_id', $this->cashAccount->id)->first();
        $this->assertNotNull($cashPayment);
        $this->assertEquals(600, (float) $cashPayment->amount);

        // Verify bank payment
        $bankPayment = $payments->where('account_id', $this->bankAccount->id)->first();
        $this->assertNotNull($bankPayment);
        $this->assertEquals(400, (float) $bankPayment->amount);

        // Verify both accounts debited
        $this->cashAccount->refresh();
        $this->bankAccount->refresh();
        $cashBalanceAfter = $this->cashAccount->balance();
        $bankBalanceAfter = $this->bankAccount->balance();

        $this->assertEqualsWithDelta($cashBalanceBefore - 600, $cashBalanceAfter, 0.01);
        $this->assertEqualsWithDelta($bankBalanceBefore - 400, $bankBalanceAfter, 0.01);
    }

    /** @test */
    public function test_update_purchase_preserves_due_pay_records()
    {
        // 1. Create purchase with due
        $request = $this->makePurchaseRequest([
            'total_amount' => 1000,
            'due_amount' => 400,
            'payment_type' => ['cash'],
            'paid_amount' => [600],
            'account_id' => ['cash'],
        ]);
        $purchase = $this->purchaseService->store($request);

        // 2. Simulate a due payment (create a due_pay SupplierPayment)
        SupplierPayment::create([
            'payment_type' => 'due_pay',
            'purchase_id' => $purchase->id,
            'supplier_id' => $this->supplier->id,
            'account_id' => $this->cashAccount->id,
            'is_paid' => 1,
            'amount' => 200,
            'payment_date' => now()->format('Y-m-d'),
            'account_type' => 'Cash',
            'created_by' => $this->admin->id,
        ]);

        $duePayCountBefore = SupplierPayment::where('purchase_id', $purchase->id)
            ->where('payment_type', 'due_pay')
            ->count();
        $this->assertEquals(1, $duePayCountBefore);

        // 3. Update the purchase
        $updateRequest = $this->makePurchaseRequest([
            'invoice_number' => $purchase->invoice_number,
            'total_amount' => 1000,
            'due_amount' => 400,
            'payment_type' => ['cash'],
            'paid_amount' => [600],
            'account_id' => ['cash'],
        ]);
        $this->purchaseService->update($updateRequest, $purchase->id);

        // 4. Verify due_pay record is preserved
        $duePayCountAfter = SupplierPayment::where('purchase_id', $purchase->id)
            ->where('payment_type', 'due_pay')
            ->count();
        $this->assertEquals(1, $duePayCountAfter, 'due_pay record should be preserved after purchase update');
    }

    /** @test */
    public function test_update_purchase_payment_status_correct()
    {
        // Create purchase with due
        $request = $this->makePurchaseRequest([
            'total_amount' => 1000,
            'due_amount' => 200,
            'payment_type' => ['cash'],
            'paid_amount' => [800],
            'account_id' => ['cash'],
        ]);
        $purchase = $this->purchaseService->store($request);
        $this->assertEquals('due', $purchase->payment_status);

        // Update to fully paid
        $updateRequest = $this->makePurchaseRequest([
            'invoice_number' => $purchase->invoice_number,
            'total_amount' => 1000,
            'due_amount' => 0,
            'payment_type' => ['cash'],
            'paid_amount' => [1000],
            'account_id' => ['cash'],
        ]);
        $updated = $this->purchaseService->update($updateRequest, $purchase->id);

        // This was the critical bug: payment_status was comparing array vs number
        $this->assertEquals('paid', $updated->payment_status, 'payment_status should be "paid" when fully paid');
    }

    /** @test */
    public function test_delete_purchase_cleans_up_everything()
    {
        $request = $this->makePurchaseRequest();
        $purchase = $this->purchaseService->store($request);
        $invoiceNumber = $purchase->invoice_number;
        $purchaseId = $purchase->id;

        // Verify data exists before delete
        $this->assertTrue(PurchaseDetails::where('purchase_id', $purchaseId)->exists());
        $this->assertTrue(SupplierPayment::where('purchase_id', $purchaseId)->exists());
        $this->assertTrue(Stock::where('purchase_id', $purchaseId)->exists());
        $this->assertTrue(Ledger::where('invoice_no', $invoiceNumber)->exists());

        // Delete
        $this->purchaseService->destroy($purchaseId);

        // Verify everything cleaned up
        $this->assertNull(Purchase::find($purchaseId));
        $this->assertFalse(PurchaseDetails::where('purchase_id', $purchaseId)->exists());
        $this->assertFalse(SupplierPayment::where('purchase_id', $purchaseId)->exists());
        $this->assertFalse(Stock::where('purchase_id', $purchaseId)->exists());
        $this->assertFalse(Ledger::where('invoice_no', $invoiceNumber)->exists(), 'Ledger entries should be cleaned up (whereIn fix)');

        // Verify stock restored
        $this->ingredient->refresh();
        $this->assertEqualsWithDelta($this->initialStock, (float) $this->ingredient->getRawOriginal('stock'), 0.5);
    }

    // ==========================================
    // Ledger & Cashflow
    // ==========================================

    /** @test */
    public function test_purchase_creates_correct_ledger_entry()
    {
        $request = $this->makePurchaseRequest([
            'total_amount' => 1000,
            'due_amount' => 200,
            'payment_type' => ['cash'],
            'paid_amount' => [800],
            'account_id' => ['cash'],
        ]);

        $purchase = $this->purchaseService->store($request);

        $ledger = Ledger::where('invoice_no', $purchase->invoice_number)
            ->where('invoice_type', 'purchase')
            ->first();

        $this->assertNotNull($ledger, 'Ledger entry should exist');
        $this->assertEquals($this->supplier->id, $ledger->supplier_id);
        $this->assertEquals(800, (float) $ledger->amount);
        $this->assertEquals(200, (float) $ledger->due_amount);
        $this->assertEquals(1000, (float) $ledger->total_amount);
        $this->assertEquals(1, $ledger->is_paid);
        $this->assertEquals('purchase', $ledger->invoice_type);
    }

    /** @test */
    public function test_account_balance_decreases_on_purchase()
    {
        $balanceBefore = $this->cashAccount->balance();

        $request = $this->makePurchaseRequest([
            'total_amount' => 500,
            'due_amount' => 0,
            'payment_type' => ['cash'],
            'paid_amount' => [500],
            'account_id' => ['cash'],
            'quantity' => [5],
            'unit_price' => [100],
            'total' => [500],
        ]);
        $this->purchaseService->store($request);

        $this->cashAccount->refresh();
        $balanceAfter = $this->cashAccount->balance();

        $this->assertEqualsWithDelta($balanceBefore - 500, $balanceAfter, 0.01,
            'Cash account balance should decrease by paid amount');
    }

    /** @test */
    public function test_account_balance_increases_on_return()
    {
        // Create purchase first
        $request = $this->makePurchaseRequest();
        $purchase = $this->purchaseService->store($request);

        $balanceBefore = $this->cashAccount->balance();

        // Create return with received_amount
        $returnReq = $this->makeReturnRequest($purchase, [
            'received_amount' => 300,
        ]);
        $this->purchaseService->storeReturn($returnReq, $purchase->id);

        $this->cashAccount->refresh();
        $balanceAfter = $this->cashAccount->balance();

        $this->assertEqualsWithDelta($balanceBefore + 300, $balanceAfter, 0.01,
            'Cash account balance should increase by received return amount');
    }

    // ==========================================
    // Purchase Returns
    // ==========================================

    /** @test */
    public function test_can_create_purchase_return_with_payment()
    {
        // Create purchase
        $request = $this->makePurchaseRequest();
        $purchase = $this->purchaseService->store($request);

        $this->ingredient->refresh();
        $stockAfterPurchase = (float) $this->ingredient->getRawOriginal('stock');

        // Create return
        $returnReq = $this->makeReturnRequest($purchase, [
            'received_amount' => 300,
            'invoice_amount' => 500,
            'return_quantity' => [5],
            'return_subtotal' => [500],
        ]);
        $return = $this->purchaseService->storeReturn($returnReq, $purchase->id);

        // Verify return record
        $this->assertNotNull($return);
        $this->assertEquals(500, (float) $return->return_amount);
        $this->assertEquals(300, (float) $return->received_amount);

        // Verify payment created with correct type
        $payment = $return->payment;
        $this->assertNotNull($payment, 'Return should have a SupplierPayment');
        $this->assertEquals('purchase_receive', $payment->payment_type);
        $this->assertEquals(1, $payment->is_received);
        $this->assertEquals(300, (float) $payment->amount);

        // Verify ledger created
        $ledger = Ledger::where('invoice_type', 'purchase return')
            ->where('supplier_id', $this->supplier->id)
            ->latest()
            ->first();
        $this->assertNotNull($ledger, 'Return ledger entry should exist');
        $this->assertEquals(1, $ledger->is_received);

        // Verify stock decreased
        $this->ingredient->refresh();
        $stockAfterReturn = (float) $this->ingredient->getRawOriginal('stock');
        $this->assertLessThan($stockAfterPurchase, $stockAfterReturn);
        $this->assertEqualsWithDelta($stockAfterPurchase - 5, $stockAfterReturn, 0.5);
    }

    /** @test */
    public function test_update_return_recreates_payment_and_ledger()
    {
        // Create purchase + return
        $request = $this->makePurchaseRequest();
        $purchase = $this->purchaseService->store($request);

        $returnReq = $this->makeReturnRequest($purchase, ['received_amount' => 300]);
        $return = $this->purchaseService->storeReturn($returnReq, $purchase->id);

        // Verify initial payment
        $return->refresh();
        $this->assertNotNull($return->payment);
        $this->assertEquals(300, (float) $return->payment->amount);

        // Update return with different amount
        $updateReq = $this->makeReturnRequest($purchase, ['received_amount' => 450]);
        $updatedReturn = $this->purchaseService->updateReturn($updateReq, $return->id);

        // THE CRITICAL BUG FIX: Payment and ledger must be recreated
        $updatedReturn->refresh();
        $newPayment = $updatedReturn->payment;
        $this->assertNotNull($newPayment, 'Payment must be recreated after updateReturn');
        $this->assertEquals(450, (float) $newPayment->amount, 'Payment amount should match updated received_amount');
        $this->assertEquals('purchase_receive', $newPayment->payment_type);

        // Verify ledger recreated
        $returnLedger = Ledger::where('invoice_type', 'purchase return')
            ->where('supplier_id', $this->supplier->id)
            ->latest()
            ->first();
        $this->assertNotNull($returnLedger, 'Ledger must be recreated after updateReturn');
    }

    /** @test */
    public function test_delete_return_cleans_up_ledger()
    {
        // Create purchase + return
        $request = $this->makePurchaseRequest();
        $purchase = $this->purchaseService->store($request);

        $returnReq = $this->makeReturnRequest($purchase, ['received_amount' => 300]);
        $return = $this->purchaseService->storeReturn($returnReq, $purchase->id);
        $returnId = $return->id;

        // Verify data exists
        $this->assertTrue(SupplierPayment::where('purchase_return_id', $returnId)->exists());

        $this->ingredient->refresh();
        $stockBeforeDelete = (float) $this->ingredient->getRawOriginal('stock');

        // Delete return
        $this->purchaseService->deleteReturn($returnId);

        // Verify everything cleaned
        $this->assertNull(PurchaseReturn::find($returnId));
        $this->assertFalse(SupplierPayment::where('purchase_return_id', $returnId)->exists(), 'Return payments should be deleted');
        $this->assertFalse(
            Ledger::where('invoice_type', 'purchase return')
                ->where('invoice_no', $purchase->invoice_number)
                ->exists(),
            'Return ledger entries should be cleaned up'
        );

        // Verify stock restored (returned items added back)
        $this->ingredient->refresh();
        $stockAfterDelete = (float) $this->ingredient->getRawOriginal('stock');
        $this->assertGreaterThan($stockBeforeDelete, $stockAfterDelete, 'Stock should be restored after deleting return');
    }

    // ==========================================
    // Supplier Due Handling
    // ==========================================

    /** @test */
    public function test_supplier_total_due_calculation()
    {
        // Purchase 1: 1000 total, 600 paid, 400 due
        $req1 = $this->makePurchaseRequest([
            'invoice_number' => 'DUE-TEST-1-' . uniqid(),
            'total_amount' => 1000,
            'due_amount' => 400,
            'payment_type' => ['cash'],
            'paid_amount' => [600],
            'account_id' => ['cash'],
        ]);
        $this->purchaseService->store($req1);

        // Purchase 2: 500 total, 500 paid, 0 due
        $req2 = $this->makePurchaseRequest([
            'invoice_number' => 'DUE-TEST-2-' . uniqid(),
            'total_amount' => 500,
            'due_amount' => 0,
            'payment_type' => ['cash'],
            'paid_amount' => [500],
            'account_id' => ['cash'],
            'quantity' => [5],
            'unit_price' => [100],
            'total' => [500],
        ]);
        $purchase2 = $this->purchaseService->store($req2);

        // Create a return against purchase 2
        $returnReq = $this->makeReturnRequest($purchase2, [
            'received_amount' => 100,
            'invoice_amount' => 200,
            'return_quantity' => [2],
            'return_subtotal' => [200],
        ]);
        $this->purchaseService->storeReturn($returnReq, $purchase2->id);

        // Verify: total_due = total_purchase(1500) - total_paid(1100) - total_return(200) = 200
        $this->supplier->load('purchases', 'payments', 'purchaseReturn');
        $this->assertEquals(1500, $this->supplier->total_purchase);
        $this->assertEquals(1100, $this->supplier->total_paid);
        $this->assertEquals(200, $this->supplier->total_return);
        $this->assertEqualsWithDelta(200, $this->supplier->total_due, 0.01,
            'total_due = total_purchase - total_paid - total_return');
    }

    /** @test */
    public function test_supplier_due_decreases_after_due_payment()
    {
        // Create purchase with due
        $invoice = 'DUE-PAY-TEST-' . uniqid();
        $request = $this->makePurchaseRequest([
            'invoice_number' => $invoice,
            'total_amount' => 1000,
            'due_amount' => 400,
            'payment_type' => ['cash'],
            'paid_amount' => [600],
            'account_id' => ['cash'],
        ]);
        $purchase = $this->purchaseService->store($request);

        // Verify initial due
        $this->supplier->load('purchases', 'payments', 'purchaseReturn');
        $this->assertEquals(400, $this->supplier->total_due);

        // Make a due payment using SupplierService
        $duePayRequest = new Request();
        $duePayRequest->merge([
            'paying_amount' => 200,
            'account_id' => 'cash',
            'note' => 'Due payment test',
            'date' => now()->format('d-m-Y'),
            'invoice_no' => [$invoice],
            'amount' => [200],
        ]);

        $this->supplierService->duePay($duePayRequest, $this->supplier->id);

        // Verify purchase paid_amount updated
        $purchase->refresh();
        $this->assertEquals(800, (float) $purchase->paid_amount, 'Purchase paid_amount should increase');
        $this->assertEquals(200, (float) $purchase->due_amount, 'Purchase due_amount should decrease');

        // Verify supplier total_due decreased
        $this->supplier->load('purchases', 'payments', 'purchaseReturn');
        $this->assertEqualsWithDelta(200, $this->supplier->total_due, 0.01,
            'Supplier total_due should decrease after due payment');

        // Verify due_pay SupplierPayment created
        $duePay = SupplierPayment::where('purchase_id', $purchase->id)
            ->where('payment_type', 'due_pay')
            ->first();
        $this->assertNotNull($duePay, 'due_pay SupplierPayment should exist');
        $this->assertEquals(200, (float) $duePay->amount);
    }

    // ==========================================
    // Supplier Advance
    // ==========================================

    /** @test */
    public function test_supplier_advance_pay_and_refund()
    {
        $cashBalanceBefore = $this->cashAccount->balance();

        // 1. Pay advance to supplier
        $advancePayReq = new Request();
        $advancePayReq->merge([
            'paying_amount' => 500,
            'refund_amount' => null,
            'account_id' => 'cash',
            'note' => 'Advance payment test',
            'date' => now()->format('d-m-Y'),
            'memo' => 'ADV-MEMO',
        ]);
        $this->supplierService->advancePay($advancePayReq, $this->supplier->id);

        // Verify supplier advance = 500
        $this->supplier->load('payments');
        $this->assertEquals(500, $this->supplier->advance, 'Supplier advance should be 500 after advance_pay');

        // Verify cash account decreased by 500
        $this->cashAccount->refresh();
        $cashAfterAdvance = $this->cashAccount->balance();
        $this->assertEqualsWithDelta($cashBalanceBefore - 500, $cashAfterAdvance, 0.01,
            'Cash balance should decrease by advance_pay amount');

        // Verify advance_pay SupplierPayment
        $advancePayment = SupplierPayment::where('supplier_id', $this->supplier->id)
            ->where('payment_type', 'advance_pay')
            ->first();
        $this->assertNotNull($advancePayment);
        $this->assertEquals(1, $advancePayment->is_paid);
        $this->assertEquals(0, $advancePayment->is_received);

        // 2. Refund part of advance
        $refundReq = new Request();
        $refundReq->merge([
            'paying_amount' => null,
            'refund_amount' => 200,
            'account_id' => 'cash',
            'note' => 'Advance refund test',
            'date' => now()->format('d-m-Y'),
            'memo' => 'REF-MEMO',
        ]);
        $this->supplierService->advancePay($refundReq, $this->supplier->id);

        // Verify supplier advance = 500 - 200 = 300
        $this->supplier->load('payments');
        $this->assertEquals(300, $this->supplier->advance, 'Supplier advance should be 300 after refund');

        // Verify cash account increased by 200 (refund)
        $this->cashAccount->refresh();
        $cashAfterRefund = $this->cashAccount->balance();
        $this->assertEqualsWithDelta($cashAfterAdvance + 200, $cashAfterRefund, 0.01,
            'Cash balance should increase by advance_refund amount');

        // Verify advance_refund SupplierPayment
        $refundPayment = SupplierPayment::where('supplier_id', $this->supplier->id)
            ->where('payment_type', 'advance_refund')
            ->first();
        $this->assertNotNull($refundPayment);
        $this->assertEquals(0, $refundPayment->is_paid);
        $this->assertEquals(1, $refundPayment->is_received);

        // Verify ledger entries
        $advanceLedger = Ledger::where('supplier_id', $this->supplier->id)
            ->where('invoice_type', 'Advance Payment')
            ->first();
        $this->assertNotNull($advanceLedger, 'Advance payment ledger should exist');

        $refundLedger = Ledger::where('supplier_id', $this->supplier->id)
            ->where('invoice_type', 'Payment Return')
            ->first();
        $this->assertNotNull($refundLedger, 'Advance refund ledger should exist');
    }
}
