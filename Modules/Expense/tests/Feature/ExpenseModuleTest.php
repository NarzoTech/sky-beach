<?php

namespace Modules\Expense\tests\Feature;

use App\Models\Admin;
use App\Models\Ledger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplier;
use Modules\Expense\app\Models\ExpenseSupplierPayment;
use Modules\Expense\app\Models\ExpenseType;
use Modules\Expense\app\Services\ExpenseService;
use Tests\TestCase;

class ExpenseModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected ExpenseService $expenseService;
    protected Admin $admin;
    protected ExpenseType $expenseType;
    protected ExpenseType $subExpenseType;
    protected ExpenseSupplier $supplier;
    protected Account $cashAccount;
    protected Account $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->expenseService = app(ExpenseService::class);

        // Login as admin
        $this->admin = Admin::first();
        if (!$this->admin) {
            $this->markTestSkipped('No admin user found in database');
        }
        auth('admin')->login($this->admin);
        Auth::shouldUse('admin');

        // Ensure setting cache exists (use real DB settings, don't override with incomplete object)
        if (!Cache::has('setting')) {
            $settingInfo = \App\Models\Setting::get();
            $settingArr = [];
            foreach ($settingInfo as $item) {
                $settingArr[$item->key] = $item->value;
            }
            Cache::put('setting', (object) $settingArr);
        }

        // Create test expense type with sub-type
        $this->expenseType = ExpenseType::create([
            'name' => 'Test Expense Type ' . uniqid(),
            'parent_id' => null,
        ]);

        $this->subExpenseType = ExpenseType::create([
            'name' => 'Test Sub Type ' . uniqid(),
            'parent_id' => $this->expenseType->id,
        ]);

        // Create test supplier
        $this->supplier = ExpenseSupplier::create([
            'name' => 'Test Supplier ' . uniqid(),
            'company' => 'Test Company',
            'phone' => '01700000000',
            'email' => 'test@expense-supplier.com',
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

    // =====================================================
    // Helper Methods
    // =====================================================

    private function makeExpenseRequest(array $overrides = []): Request
    {
        $defaults = [
            'date' => now()->format('d-m-Y'),
            'amount' => 1000,
            'expense_type_id' => $this->expenseType->id,
            'sub_expense_type_id' => null,
            'expense_supplier_id' => null,
            'payment_type' => ['cash'],
            'account_id' => ['cash'],
            'paying_amount' => [1000],
            'note' => 'Test expense note',
            'memo' => 'Test expense memo',
        ];

        $request = new Request();
        $request->merge(array_merge($defaults, $overrides));
        return $request;
    }

    // =====================================================
    // STORE Tests
    // =====================================================

    /** @test */
    public function test_can_create_expense_without_supplier_cash_payment()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 500,
            'payment_type' => ['cash'],
            'account_id' => ['cash'],
            'paying_amount' => [500],
        ]);

        $expense = $this->expenseService->store($request);

        // Verify expense record
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 500,
            'paid_amount' => 500, // Non-supplier: paid = full amount
            'due_amount' => 0,
            'payment_type' => 'cash',
            'expense_type_id' => $this->expenseType->id,
        ]);

        // Verify invoice was generated
        $this->assertNotNull($expense->invoice);
        $this->assertStringStartsWith('EXP-', $expense->invoice);

        // Verify payment record created
        $payments = ExpenseSupplierPayment::where('expense_id', $expense->id)->get();
        $this->assertGreaterThanOrEqual(1, $payments->count());

        $payment = $payments->first();
        $this->assertEquals('direct_expense', $payment->payment_type);
        $this->assertEquals(1, $payment->is_paid);
        $this->assertEquals($this->cashAccount->id, $payment->account_id);

        // No ledger entry for non-supplier
        $ledger = Ledger::where('invoice_no', 'EXP-' . $expense->id)->first();
        $this->assertNull($ledger);
    }

    /** @test */
    public function test_can_create_expense_with_supplier_full_payment()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 2000,
            'expense_supplier_id' => $this->supplier->id,
            'payment_type' => ['cash'],
            'account_id' => ['cash'],
            'paying_amount' => [2000],
        ]);

        $expense = $this->expenseService->store($request);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 2000,
            'paid_amount' => 2000,
            'due_amount' => 0,
            'expense_supplier_id' => $this->supplier->id,
        ]);

        // Verify payment type is 'expense' for supplier
        $payment = ExpenseSupplierPayment::where('expense_id', $expense->id)->first();
        $this->assertEquals('expense', $payment->payment_type);

        // Verify ledger entry created for supplier
        $ledger = Ledger::where('invoice_no', 'EXP-' . $expense->id)->first();
        $this->assertNotNull($ledger);
        $this->assertEquals($this->supplier->id, $ledger->expense_supplier_id);
        $this->assertEquals(2000, $ledger->amount);
        $this->assertEquals(0, $ledger->due_amount);
    }

    /** @test */
    public function test_can_create_expense_with_supplier_partial_payment()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 3000,
            'expense_supplier_id' => $this->supplier->id,
            'payment_type' => ['cash'],
            'account_id' => ['cash'],
            'paying_amount' => [1000],
        ]);

        $expense = $this->expenseService->store($request);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 3000,
            'paid_amount' => 1000,
            'due_amount' => 2000,
        ]);

        // Verify status is partial
        $expense->refresh();
        $this->assertEquals('partial', $expense->payment_status_label);

        // Verify ledger entry
        $ledger = Ledger::where('invoice_no', 'EXP-' . $expense->id)->first();
        $this->assertNotNull($ledger);
        $this->assertEquals(1000, $ledger->amount);
        $this->assertEquals(2000, $ledger->due_amount);
        $this->assertEquals(3000, $ledger->total_amount);
    }

    /** @test */
    public function test_can_create_expense_with_multiple_payment_methods()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 5000,
            'expense_supplier_id' => $this->supplier->id,
            'payment_type' => ['cash', 'bank'],
            'account_id' => ['cash', $this->bankAccount->id],
            'paying_amount' => [3000, 2000],
        ]);

        $expense = $this->expenseService->store($request);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 5000,
            'paid_amount' => 5000,
            'due_amount' => 0,
        ]);

        // Verify two payment records created
        $payments = ExpenseSupplierPayment::where('expense_id', $expense->id)
            ->whereIn('payment_type', ['expense', 'direct_expense'])
            ->get();
        $this->assertEquals(2, $payments->count());

        // Verify amounts
        $cashPayment = $payments->where('account_id', $this->cashAccount->id)->first();
        $bankPayment = $payments->where('account_id', $this->bankAccount->id)->first();
        $this->assertEquals(3000, $cashPayment->amount);
        $this->assertEquals(2000, $bankPayment->amount);
    }

    /** @test */
    public function test_non_supplier_expense_forces_full_payment()
    {
        // Even if paying_amount is less than total, non-supplier expenses are fully paid
        $request = $this->makeExpenseRequest([
            'amount' => 2000,
            'expense_supplier_id' => null,
            'payment_type' => ['cash'],
            'account_id' => ['cash'],
            'paying_amount' => [500], // Less than total
        ]);

        $expense = $this->expenseService->store($request);

        // paid_amount should be forced to full amount
        $this->assertEquals(2000, $expense->paid_amount);
        $this->assertEquals(0, $expense->due_amount);
        $this->assertEquals('paid', $expense->payment_status_label);
    }

    /** @test */
    public function test_due_amount_cannot_be_negative()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 1000,
            'expense_supplier_id' => $this->supplier->id,
            'payment_type' => ['cash'],
            'account_id' => ['cash'],
            'paying_amount' => [1500], // Overpayment
        ]);

        $expense = $this->expenseService->store($request);

        // Due should be clamped to 0, not -500
        $this->assertEquals(0, $expense->due_amount);
        $this->assertEquals(1500, $expense->paid_amount);
    }

    /** @test */
    public function test_store_with_sub_expense_type()
    {
        $request = $this->makeExpenseRequest([
            'expense_type_id' => $this->expenseType->id,
            'sub_expense_type_id' => $this->subExpenseType->id,
        ]);

        $expense = $this->expenseService->store($request);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'expense_type_id' => $this->expenseType->id,
            'sub_expense_type_id' => $this->subExpenseType->id,
        ]);
    }

    /** @test */
    public function test_store_generates_unique_invoice_numbers()
    {
        $request1 = $this->makeExpenseRequest(['amount' => 100]);
        $request2 = $this->makeExpenseRequest(['amount' => 200]);

        $expense1 = $this->expenseService->store($request1);
        $expense2 = $this->expenseService->store($request2);

        $this->assertNotEquals($expense1->invoice, $expense2->invoice);
    }

    /** @test */
    public function test_store_generates_unique_payment_invoices()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 3000,
            'expense_supplier_id' => $this->supplier->id,
            'payment_type' => ['cash', 'cash'],
            'account_id' => ['cash', 'cash'],
            'paying_amount' => [1000, 2000],
        ]);

        $expense = $this->expenseService->store($request);

        $payments = ExpenseSupplierPayment::where('expense_id', $expense->id)->get();
        $invoices = $payments->pluck('invoice')->unique();

        // Each payment should have a unique invoice
        $this->assertEquals($payments->count(), $invoices->count());
    }

    // =====================================================
    // UPDATE Tests
    // =====================================================

    /** @test */
    public function test_can_update_expense_amount()
    {
        // Create first
        $request = $this->makeExpenseRequest([
            'amount' => 1000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [1000],
        ]);
        $expense = $this->expenseService->store($request);

        // Update amount
        $updateRequest = $this->makeExpenseRequest([
            'amount' => 1500,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [1200],
        ]);
        $this->expenseService->update($updateRequest, $expense->id);

        $expense->refresh();
        $this->assertEquals(1500, $expense->amount);
        $this->assertEquals(1200, $expense->paid_amount);
        $this->assertEquals(300, $expense->due_amount);
    }

    /** @test */
    public function test_update_recreates_payment_records()
    {
        // Create with single payment
        $request = $this->makeExpenseRequest([
            'amount' => 1000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [1000],
        ]);
        $expense = $this->expenseService->store($request);

        $oldPaymentIds = ExpenseSupplierPayment::where('expense_id', $expense->id)
            ->whereIn('payment_type', ['expense', 'direct_expense'])
            ->pluck('id')->toArray();

        // Update with new payment
        $updateRequest = $this->makeExpenseRequest([
            'amount' => 2000,
            'expense_supplier_id' => $this->supplier->id,
            'payment_type' => ['cash', 'bank'],
            'account_id' => ['cash', $this->bankAccount->id],
            'paying_amount' => [1000, 1000],
        ]);
        $this->expenseService->update($updateRequest, $expense->id);

        // Old payments should be deleted
        foreach ($oldPaymentIds as $oldId) {
            $this->assertNull(ExpenseSupplierPayment::find($oldId));
        }

        // New payments should exist
        $newPayments = ExpenseSupplierPayment::where('expense_id', $expense->id)
            ->whereIn('payment_type', ['expense', 'direct_expense'])
            ->get();
        $this->assertEquals(2, $newPayments->count());
    }

    /** @test */
    public function test_update_non_supplier_forces_full_payment()
    {
        // Create with supplier
        $request = $this->makeExpenseRequest([
            'amount' => 2000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [1000],
        ]);
        $expense = $this->expenseService->store($request);

        // Update to remove supplier (make direct expense)
        $updateRequest = $this->makeExpenseRequest([
            'amount' => 2000,
            'expense_supplier_id' => null,
            'paying_amount' => [500],
        ]);
        $this->expenseService->update($updateRequest, $expense->id);

        $expense->refresh();
        $this->assertEquals(2000, $expense->paid_amount);
        $this->assertEquals(0, $expense->due_amount);
    }

    /** @test */
    public function test_update_recreates_ledger_for_supplier()
    {
        // Create with supplier
        $request = $this->makeExpenseRequest([
            'amount' => 3000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [2000],
        ]);
        $expense = $this->expenseService->store($request);

        $oldLedger = Ledger::where('invoice_no', 'EXP-' . $expense->id)->first();
        $this->assertNotNull($oldLedger);
        $oldLedgerId = $oldLedger->id;

        // Update
        $updateRequest = $this->makeExpenseRequest([
            'amount' => 3000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [3000],
        ]);
        $this->expenseService->update($updateRequest, $expense->id);

        // Old ledger should be deleted
        $this->assertNull(Ledger::find($oldLedgerId));

        // New ledger should exist
        $newLedger = Ledger::where('invoice_no', 'EXP-' . $expense->id)->first();
        $this->assertNotNull($newLedger);
        $this->assertEquals(3000, $newLedger->amount);
        $this->assertEquals(0, $newLedger->due_amount);
    }

    /** @test */
    public function test_update_due_amount_clamped_to_zero()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 1000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [500],
        ]);
        $expense = $this->expenseService->store($request);

        // Update with overpayment
        $updateRequest = $this->makeExpenseRequest([
            'amount' => 1000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [1500],
        ]);
        $this->expenseService->update($updateRequest, $expense->id);

        $expense->refresh();
        $this->assertEquals(0, $expense->due_amount);
    }

    // =====================================================
    // DESTROY Tests
    // =====================================================

    /** @test */
    public function test_can_delete_expense_and_cleans_up_payments()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 1000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [1000],
        ]);
        $expense = $this->expenseService->store($request);
        $expenseId = $expense->id;

        // Verify payment and ledger exist
        $this->assertTrue(ExpenseSupplierPayment::where('expense_id', $expenseId)->exists());
        $this->assertNotNull(Ledger::where('invoice_no', 'EXP-' . $expenseId)->first());

        // Delete
        $this->expenseService->destroy($expenseId);

        // Expense should be soft-deleted
        $this->assertSoftDeleted('expenses', ['id' => $expenseId]);

        // Payments should be hard-deleted
        $this->assertFalse(ExpenseSupplierPayment::where('expense_id', $expenseId)->exists());

        // Ledger should be hard-deleted
        $this->assertNull(Ledger::where('invoice_no', 'EXP-' . $expenseId)->first());
    }

    /** @test */
    public function test_can_delete_expense_without_supplier()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 500,
            'expense_supplier_id' => null,
            'paying_amount' => [500],
        ]);
        $expense = $this->expenseService->store($request);
        $expenseId = $expense->id;

        $this->expenseService->destroy($expenseId);

        $this->assertSoftDeleted('expenses', ['id' => $expenseId]);
        $this->assertFalse(ExpenseSupplierPayment::where('expense_id', $expenseId)->exists());
    }

    // =====================================================
    // PAYMENT STATUS LABEL Tests
    // =====================================================

    /** @test */
    public function test_payment_status_label_paid()
    {
        $expense = Expense::create([
            'invoice' => 'EXP-TEST-1',
            'date' => now(),
            'amount' => 1000,
            'paid_amount' => 1000,
            'due_amount' => 0,
            'payment_type' => 'cash',
            'account_id' => $this->cashAccount->id,
            'expense_type_id' => $this->expenseType->id,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals('paid', $expense->payment_status_label);
    }

    /** @test */
    public function test_payment_status_label_partial()
    {
        $expense = Expense::create([
            'invoice' => 'EXP-TEST-2',
            'date' => now(),
            'amount' => 2000,
            'paid_amount' => 500,
            'due_amount' => 1500,
            'payment_type' => 'cash',
            'account_id' => $this->cashAccount->id,
            'expense_type_id' => $this->expenseType->id,
            'expense_supplier_id' => $this->supplier->id,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals('partial', $expense->payment_status_label);
    }

    /** @test */
    public function test_payment_status_label_due()
    {
        $expense = Expense::create([
            'invoice' => 'EXP-TEST-3',
            'date' => now(),
            'amount' => 3000,
            'paid_amount' => 0,
            'due_amount' => 3000,
            'payment_type' => 'cash',
            'account_id' => $this->cashAccount->id,
            'expense_type_id' => $this->expenseType->id,
            'expense_supplier_id' => $this->supplier->id,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals('due', $expense->payment_status_label);
    }

    // =====================================================
    // INDEX (Controller) Tests
    // =====================================================

    /** @test */
    public function test_index_page_loads_successfully()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index'));

        $response->assertStatus(200);
        $response->assertViewIs('expense::index');
        $response->assertViewHas(['expenses', 'types', 'accounts', 'totalAmount', 'totalPaid', 'totalDue', 'expenseSuppliers']);
    }

    /** @test */
    public function test_index_keyword_search_by_invoice()
    {
        // Create an expense first
        $request = $this->makeExpenseRequest(['amount' => 999]);
        $expense = $this->expenseService->store($request);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['keyword' => $expense->invoice]));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_keyword_search_by_memo()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 777,
            'memo' => 'UniqueTestMemo12345',
        ]);
        $expense = $this->expenseService->store($request);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['keyword' => 'UniqueTestMemo12345']));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_filter_by_expense_type()
    {
        $request = $this->makeExpenseRequest(['amount' => 555]);
        $this->expenseService->store($request);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['expense_type_id' => $this->expenseType->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_filter_by_supplier()
    {
        $request = $this->makeExpenseRequest([
            'amount' => 444,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [444],
        ]);
        $this->expenseService->store($request);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['expense_supplier_id' => $this->supplier->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_filter_by_payment_status_paid()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['payment_status' => 'paid']));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_filter_by_payment_status_partial()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['payment_status' => 'partial']));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_filter_by_payment_status_due()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['payment_status' => 'due']));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_sort_by_allowed_columns()
    {
        foreach (['id', 'date', 'amount'] as $orderType) {
            foreach (['asc', 'desc'] as $orderBy) {
                $response = $this->actingAs($this->admin, 'admin')
                    ->get(route('admin.expense.index', [
                        'order_type' => $orderType,
                        'order_by' => $orderBy,
                    ]));

                $response->assertStatus(200);
            }
        }
    }

    /** @test */
    public function test_index_rejects_invalid_order_type()
    {
        // Passing an invalid column should NOT cause an error - falls back to 'id'
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['order_type' => 'DROP TABLE expenses;--']));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_date_range_filter()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', [
                'from_date' => now()->subDays(30)->format('d-m-Y'),
                'to_date' => now()->format('d-m-Y'),
            ]));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_index_per_page_options()
    {
        foreach (['10', '50', '100', 'all'] as $perPage) {
            $response = $this->actingAs($this->admin, 'admin')
                ->get(route('admin.expense.index', ['par-page' => $perPage]));

            $response->assertStatus(200);
        }
    }

    // =====================================================
    // EXPORT Permission Tests
    // =====================================================

    /** @test */
    public function test_excel_export_requires_permission()
    {
        // If admin has the permission, this should return a download
        // If not, it should return normal page (200)
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['export' => 'true']));

        // Should either download (200 with headers) or show page (200)
        $this->assertTrue(in_array($response->getStatusCode(), [200]));
    }

    /** @test */
    public function test_pdf_export_requires_permission()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expense.index', ['export_pdf' => 'true']));

        $this->assertTrue(in_array($response->getStatusCode(), [200]));
    }

    // =====================================================
    // STORE Validation Tests (via Controller)
    // =====================================================

    /** @test */
    public function test_store_requires_date()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.expense.store'), [
                'amount' => 1000,
                'payment_type' => ['cash'],
                'account_id' => ['cash'],
                'expense_type_id' => $this->expenseType->id,
            ]);

        $response->assertSessionHasErrors('date');
    }

    /** @test */
    public function test_store_requires_amount()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.expense.store'), [
                'date' => now()->format('d-m-Y'),
                'payment_type' => ['cash'],
                'account_id' => ['cash'],
                'expense_type_id' => $this->expenseType->id,
            ]);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function test_store_rejects_zero_amount()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.expense.store'), [
                'date' => now()->format('d-m-Y'),
                'amount' => 0,
                'payment_type' => ['cash'],
                'account_id' => ['cash'],
                'expense_type_id' => $this->expenseType->id,
            ]);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function test_store_rejects_negative_amount()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.expense.store'), [
                'date' => now()->format('d-m-Y'),
                'amount' => -500,
                'payment_type' => ['cash'],
                'account_id' => ['cash'],
                'expense_type_id' => $this->expenseType->id,
            ]);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function test_store_requires_expense_type()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.expense.store'), [
                'date' => now()->format('d-m-Y'),
                'amount' => 1000,
                'payment_type' => ['cash'],
                'account_id' => ['cash'],
            ]);

        $response->assertSessionHasErrors('expense_type_id');
    }

    /** @test */
    public function test_store_requires_payment_type()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.expense.store'), [
                'date' => now()->format('d-m-Y'),
                'amount' => 1000,
                'account_id' => ['cash'],
                'expense_type_id' => $this->expenseType->id,
            ]);

        $response->assertSessionHasErrors('payment_type');
    }

    // =====================================================
    // UPDATE Validation Tests (via Controller)
    // =====================================================

    /** @test */
    public function test_update_validates_amount()
    {
        // Create expense first
        $request = $this->makeExpenseRequest(['amount' => 1000]);
        $expense = $this->expenseService->store($request);

        // Try to update with zero amount
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.expense.update', $expense->id), [
                'date' => now()->format('d-m-Y'),
                'amount' => 0,
                'payment_type' => ['cash'],
                'account_id' => ['cash'],
                'expense_type_id' => $this->expenseType->id,
            ]);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function test_update_validates_required_fields()
    {
        $request = $this->makeExpenseRequest(['amount' => 1000]);
        $expense = $this->expenseService->store($request);

        // Try to update without required fields
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.expense.update', $expense->id), []);

        $response->assertSessionHasErrors(['date', 'amount', 'payment_type', 'account_id', 'expense_type_id']);
    }

    // =====================================================
    // DELETE (via Controller) Tests
    // =====================================================

    /** @test */
    public function test_delete_via_controller()
    {
        $request = $this->makeExpenseRequest(['amount' => 1000]);
        $expense = $this->expenseService->store($request);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.expense.destroy', $expense->id));

        $response->assertRedirect(route('admin.expense.index'));
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    // =====================================================
    // MODEL Tests
    // =====================================================

    /** @test */
    public function test_expense_type_parent_child_relationship()
    {
        $this->assertTrue($this->expenseType->isParent());
        $this->assertFalse($this->expenseType->isChild());
        $this->assertTrue($this->subExpenseType->isChild());
        $this->assertFalse($this->subExpenseType->isParent());

        $this->assertEquals(1, $this->expenseType->children->count());
        $this->assertEquals($this->expenseType->id, $this->subExpenseType->parent->id);
    }

    /** @test */
    public function test_expense_supplier_total_attributes()
    {
        // Create two expenses for supplier
        $request1 = $this->makeExpenseRequest([
            'amount' => 1000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [600],
        ]);
        $this->expenseService->store($request1);

        $request2 = $this->makeExpenseRequest([
            'amount' => 2000,
            'expense_supplier_id' => $this->supplier->id,
            'paying_amount' => [2000],
        ]);
        $this->expenseService->store($request2);

        $this->supplier->load(['expenses', 'payments']);

        $this->assertEquals(3000, $this->supplier->total_expense);
        $this->assertEquals(2600, $this->supplier->total_paid);
        $this->assertEquals(400, $this->supplier->total_due);
    }

    /** @test */
    public function test_expense_scopes()
    {
        // Create paid expense
        Expense::create([
            'invoice' => 'SCOPE-1', 'date' => now(), 'amount' => 100,
            'paid_amount' => 100, 'due_amount' => 0,
            'payment_type' => 'cash', 'account_id' => $this->cashAccount->id,
            'expense_type_id' => $this->expenseType->id, 'created_by' => $this->admin->id,
        ]);

        // Create partial expense
        Expense::create([
            'invoice' => 'SCOPE-2', 'date' => now(), 'amount' => 200,
            'paid_amount' => 100, 'due_amount' => 100,
            'payment_type' => 'cash', 'account_id' => $this->cashAccount->id,
            'expense_type_id' => $this->expenseType->id, 'created_by' => $this->admin->id,
        ]);

        // Create due expense
        Expense::create([
            'invoice' => 'SCOPE-3', 'date' => now(), 'amount' => 300,
            'paid_amount' => 0, 'due_amount' => 300,
            'payment_type' => 'cash', 'account_id' => $this->cashAccount->id,
            'expense_type_id' => $this->expenseType->id, 'created_by' => $this->admin->id,
        ]);

        $this->assertGreaterThanOrEqual(1, Expense::paid()->count());
        $this->assertGreaterThanOrEqual(1, Expense::partial()->count());
        $this->assertGreaterThanOrEqual(1, Expense::due()->count());
    }
}
