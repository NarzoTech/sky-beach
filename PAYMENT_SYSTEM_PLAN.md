# Comprehensive Payment System Plan

## Current System Analysis

### What Already Exists

#### 1. Waiter Panel (Modules/POS - Waiter Dashboard)
- **Routes**: `/admin/waiter/*`
- **Capabilities**:
  - Select tables and create dine-in orders
  - Add items to existing orders
  - Transfer tables
  - Cancel orders
  - View order details and payment status
- **Limitations**:
  - Cannot accept payments (by design)
  - Orders created with `payment_status=0` (unpaid)

#### 2. POS Module (Admin)
- **Routes**: `/admin/pos/*`
- **Capabilities**:
  - Create orders directly (dine-in, take-away, delivery)
  - View "Running Orders" (status=0)
  - Complete orders with payment (`completeRunningOrder`)
  - Multiple payment methods (cash, card, bank)
  - Partial payments (creates CustomerDue)
- **Limitations**:
  - Completing an order marks it as `status=1` (completed) - no more items can be added
  - Payment is tied to order completion

#### 3. Customer Due System (Modules/Customer)
- **Routes**: `/admin/customer/due-receive/*`
- **Capabilities**:
  - Receive dues against invoices
  - Track payment history
- **Limitations**:
  - Designed for traditional customer dues, not restaurant workflow
  - Requires selecting customer and invoice manually

---

## Identified Gaps

| Requirement | Current Status | Gap |
|-------------|---------------|-----|
| Waiter creates orders | ✅ Working | None |
| Admin takes payment for waiter orders | ⚠️ Partial | Payment = Order completion, no separate payment flow |
| Admin can place orders | ✅ Working (POS) | None |
| Payment at order creation | ✅ Working (POS) | None |
| Payment after order creation | ⚠️ Partial | Limited to "complete order" flow |
| Partial payment support | ✅ Working | Creates CustomerDue record |
| Multiple payments on same order | ❌ Missing | Only one payment event per order |

---

## Proposed Solution: Dedicated Cashier/Checkout Module

### Overview

Create a dedicated **Cashier Panel** that separates:
1. **Order Management** (waiter/admin creates and manages orders)
2. **Payment Collection** (cashier/admin collects payments)

### Key Principles

1. **Order Status** and **Payment Status** are independent
2. An order can receive multiple partial payments
3. Payment can be taken at any time (during order, after completion, etc.)
4. Waiters create orders → Admin/Cashier handles payments

---

## Implementation Plan

### Phase 1: Database Modifications

#### 1.1 New Migration: Order Payment Transactions Table
```php
// database/migrations/xxxx_create_order_payment_transactions_table.php

Schema::create('order_payment_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
    $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('account_id')->constrained('accounts');
    $table->foreignId('processed_by')->constrained('admins');
    $table->string('payment_method'); // cash, card, bank, mobile
    $table->decimal('amount', 15, 2);
    $table->decimal('received_amount', 15, 2)->nullable(); // cash received
    $table->decimal('change_amount', 15, 2)->default(0); // change given
    $table->string('reference_number')->nullable(); // card/cheque number
    $table->text('note')->nullable();
    $table->enum('status', ['completed', 'voided', 'refunded'])->default('completed');
    $table->timestamp('voided_at')->nullable();
    $table->foreignId('voided_by')->nullable()->constrained('admins');
    $table->string('void_reason')->nullable();
    $table->timestamps();

    $table->index(['sale_id', 'status']);
    $table->index('processed_by');
});
```

#### 1.2 Modify Sales Table (if needed)
```php
// Add fields if not present
$table->boolean('is_checkout_ready')->default(false); // waiter marks order ready for payment
$table->timestamp('checkout_ready_at')->nullable();
```

### Phase 2: Cashier Module

#### 2.1 Create Cashier Controller
**Location**: `Modules/POS/app/Http/Controllers/CashierController.php`

```php
class CashierController extends Controller
{
    // List all orders pending payment
    public function index();

    // Show payment interface for specific order
    public function showOrder($id);

    // Process payment for an order
    public function processPayment(Request $request, $id);

    // Add partial payment
    public function addPartialPayment(Request $request, $id);

    // View payment history for order
    public function paymentHistory($id);

    // Void a payment transaction
    public function voidPayment($transactionId);

    // Mark order as ready for checkout (from waiter side)
    public function markCheckoutReady($id);

    // Complete order and close table
    public function completeAndClose($id);

    // Print receipt
    public function printReceipt($id);

    // Split bill functionality
    public function splitBill(Request $request, $id);
}
```

#### 2.2 Cashier Routes
```php
// Modules/POS/routes/web.php

Route::prefix('admin/cashier')->name('admin.cashier.')->middleware(['auth:admin'])->group(function () {
    // Dashboard - all orders pending payment
    Route::get('/', [CashierController::class, 'index'])->name('index');

    // Orders ready for checkout
    Route::get('/checkout-queue', [CashierController::class, 'checkoutQueue'])->name('checkout-queue');

    // Order payment interface
    Route::get('/order/{id}', [CashierController::class, 'showOrder'])->name('order');

    // Payment actions
    Route::post('/order/{id}/payment', [CashierController::class, 'processPayment'])->name('payment');
    Route::post('/order/{id}/partial-payment', [CashierController::class, 'addPartialPayment'])->name('partial-payment');
    Route::post('/order/{id}/complete', [CashierController::class, 'completeAndClose'])->name('complete');

    // Payment management
    Route::get('/order/{id}/payments', [CashierController::class, 'paymentHistory'])->name('payments');
    Route::post('/payment/{id}/void', [CashierController::class, 'voidPayment'])->name('void-payment');

    // Split bill
    Route::post('/order/{id}/split-bill', [CashierController::class, 'splitBill'])->name('split-bill');

    // Receipt
    Route::get('/order/{id}/receipt', [CashierController::class, 'printReceipt'])->name('receipt');

    // Quick actions
    Route::post('/order/{id}/mark-ready', [CashierController::class, 'markCheckoutReady'])->name('mark-ready');
});
```

#### 2.3 Cashier Views

```
Modules/POS/resources/views/cashier/
├── index.blade.php           # Main dashboard - all pending orders
├── checkout-queue.blade.php  # Orders marked ready for payment
├── order.blade.php           # Payment interface for single order
├── partials/
│   ├── order-summary.blade.php
│   ├── payment-form.blade.php
│   ├── payment-history.blade.php
│   └── split-bill-modal.blade.php
└── receipt.blade.php         # Print receipt
```

### Phase 3: Waiter Panel Enhancements

#### 3.1 Add "Request Checkout" Button
- Waiter can mark order as "Ready for Checkout"
- Sends notification to cashier panel
- Table status changes to "Checkout Pending"

#### 3.2 Modified Waiter Order Details
```blade
{{-- Add to order-details.blade.php --}}
@if($order->status == 0 && $order->payment_status == 0)
    <button class="btn btn-success w-100" onclick="requestCheckout()">
        <i class="bx bx-check me-1"></i>{{ __('Request Checkout') }}
    </button>
@endif
```

### Phase 4: Order Status Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        ORDER LIFECYCLE                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  [Waiter/Admin Creates Order]                                   │
│            │                                                    │
│            ▼                                                    │
│  ┌─────────────────┐                                           │
│  │ status = 0      │ (Processing/Active)                       │
│  │ payment = 0     │ (Unpaid)                                  │
│  │ table = occupied│                                           │
│  └────────┬────────┘                                           │
│           │                                                     │
│           ├──────────── [Add Items] ◄─────────────┐            │
│           │                                        │            │
│           ▼                                        │            │
│  ┌─────────────────┐                              │            │
│  │ Waiter clicks   │──────────────────────────────┘            │
│  │ "Add Items"     │ (Can keep adding while unpaid)            │
│  └────────┬────────┘                                           │
│           │                                                     │
│           │ [Request Checkout]                                  │
│           ▼                                                     │
│  ┌─────────────────┐                                           │
│  │ checkout_ready  │ (Visible in Cashier Queue)                │
│  │ = true          │                                           │
│  └────────┬────────┘                                           │
│           │                                                     │
│           │ [Cashier Processes Payment]                         │
│           ▼                                                     │
│  ┌─────────────────┐                                           │
│  │ OPTION A:       │ Partial Payment                           │
│  │ payment = 0     │ (Still unpaid/partial)                    │
│  │ paid_amount > 0 │                                           │
│  │ status = 0      │ (Can still add items if needed)           │
│  └────────┬────────┘                                           │
│           │                                                     │
│           │ [Full Payment Received]                             │
│           ▼                                                     │
│  ┌─────────────────┐                                           │
│  │ OPTION B:       │ Fully Paid                                │
│  │ payment = 1     │ (Paid)                                    │
│  │ status = 0 or 1 │ (Still active OR completed)               │
│  └────────┬────────┘                                           │
│           │                                                     │
│           │ [Complete & Close Order]                            │
│           ▼                                                     │
│  ┌─────────────────┐                                           │
│  │ status = 1      │ (Completed)                               │
│  │ payment = 1     │ (Paid)                                    │
│  │ table = released│                                           │
│  └─────────────────┘                                           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Phase 5: Payment Method Configuration

#### 5.1 Payment Methods Available
- Cash (default)
- Card (Credit/Debit)
- Bank Transfer
- Mobile Payment (bKash, Nagad, etc.)
- Split Payment (multiple methods)
- Customer Account (for registered customers with balance)

#### 5.2 Account Integration
Link to existing Accounts module for:
- Cash register tracking
- Bank account tracking
- Daily reconciliation

### Phase 6: Cashier Dashboard Features

#### 6.1 Main Dashboard (`/admin/cashier`)
```
┌─────────────────────────────────────────────────────────────────┐
│  CASHIER DASHBOARD                                      [Today] │
├──────────────────────┬──────────────────────┬───────────────────┤
│  Orders to Pay: 12   │  Total Due: TK 45,230│  Collected: 32,100│
├──────────────────────┴──────────────────────┴───────────────────┤
│                                                                 │
│  [Checkout Queue] [All Unpaid Orders] [Recently Paid]          │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ Table 5 - Order #000234         Ready for Checkout      │   │
│  │ 3 items | Guest: 2 | Waiter: Ahmed    TK 2,340         │   │
│  │ [View] [Take Payment]                                   │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ Table 8 - Order #000231         Processing              │   │
│  │ 5 items | Guest: 4 | Waiter: Karim    TK 4,560         │   │
│  │ Partial Paid: TK 2,000 | Due: TK 2,560                 │   │
│  │ [View] [Continue Payment]                               │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### 6.2 Order Payment Interface (`/admin/cashier/order/{id}`)
```
┌─────────────────────────────────────────────────────────────────┐
│  ORDER #000234 - TABLE 5                           [Print Bill] │
├───────────────────────────────┬─────────────────────────────────┤
│  ORDER ITEMS                  │  PAYMENT                        │
│  ─────────────────────────── │  ─────────────────────────────  │
│  Grilled Chicken    x2  800  │  Subtotal:        TK 2,340     │
│  + Extra Sauce      x2  100  │  Discount:        TK    0       │
│  Caesar Salad       x1  450  │  Tax (5%):        TK  117       │
│  Mango Smoothie     x2  500  │  ─────────────────────────────  │
│  Fish & Chips       x1  590  │  TOTAL:           TK 2,457     │
│                              │                                  │
│  ─────────────────────────── │  Previous Payments:             │
│  Special: No onions on salad │  - Cash: TK 1,000 (12:30 PM)   │
│                              │                                  │
│                              │  REMAINING DUE:   TK 1,457     │
│                              │                                  │
│                              │  ┌────────────────────────────┐ │
│                              │  │ Payment Method             │ │
│                              │  │ [Cash] [Card] [Mobile]     │ │
│                              │  │                            │ │
│                              │  │ Amount: [________1,457___] │ │
│                              │  │ Received: [__________2000] │ │
│                              │  │ Change: TK 543             │ │
│                              │  │                            │ │
│                              │  │ [Process Payment]          │ │
│                              │  │ [Split Bill]               │ │
│                              │  └────────────────────────────┘ │
├───────────────────────────────┴─────────────────────────────────┤
│  [Add More Items] [Apply Discount] [Complete & Print Receipt]  │
└─────────────────────────────────────────────────────────────────┘
```

### Phase 7: Integration Points

#### 7.1 Existing System Integration
- **POS Module**: Keep existing POS for order creation with immediate payment
- **Running Orders**: Keep for backward compatibility, enhance with cashier redirect
- **Customer Payments**: Link new transactions to existing CustomerPayment model
- **Accounts**: Use existing Account model for payment destination

#### 7.2 New Features to Integrate
- **Kitchen Display**: Show payment status (optional)
- **Waiter App**: Add checkout request notification
- **Reports**: Add cashier transaction reports

### Phase 8: Permissions

```php
// Add new permissions
'cashier.view' => 'View Cashier Dashboard',
'cashier.process_payment' => 'Process Payments',
'cashier.void_payment' => 'Void Payment Transactions',
'cashier.apply_discount' => 'Apply Order Discounts',
'cashier.split_bill' => 'Split Bills',
'cashier.complete_order' => 'Complete Orders',
'waiter.request_checkout' => 'Request Checkout',
```

---

## Implementation Priority

### Priority 1 (Core - Must Have)
1. [ ] Database migration for order_payment_transactions
2. [ ] CashierController with basic payment processing
3. [ ] Cashier index view (pending orders list)
4. [ ] Order payment view with payment form
5. [ ] Process payment functionality
6. [ ] Link to existing CustomerPayment system

### Priority 2 (Enhanced Experience)
7. [ ] Waiter "Request Checkout" button
8. [ ] Checkout queue for cashier
9. [ ] Partial payment support
10. [ ] Payment history per order
11. [ ] Receipt printing

### Priority 3 (Advanced Features)
12. [ ] Split bill functionality
13. [ ] Void payment transactions
14. [ ] Real-time notifications (websocket)
15. [ ] Cashier shift management
16. [ ] End-of-day reconciliation

---

## Files to Create/Modify

### New Files
```
Modules/POS/
├── app/Http/Controllers/CashierController.php
├── app/Services/CashierService.php
├── resources/views/cashier/
│   ├── index.blade.php
│   ├── order.blade.php
│   ├── checkout-queue.blade.php
│   └── partials/
│       ├── payment-form.blade.php
│       └── order-summary.blade.php
└── routes/web.php (add cashier routes)

database/migrations/
└── xxxx_create_order_payment_transactions_table.php
```

### Files to Modify
```
Modules/POS/resources/views/waiter/
└── order-details.blade.php (add checkout request button)

Modules/POS/app/Http/Controllers/
└── WaiterDashboardController.php (add markCheckoutReady method)

resources/views/admin/layouts/
└── sidebar.blade.php (add Cashier menu item)

config/
└── permission.php (add cashier permissions)
```

---

## Summary

This plan creates a **separation of concerns**:

| Role | Create Order | Add Items | Take Payment | Complete Order |
|------|-------------|-----------|--------------|----------------|
| Waiter | ✅ | ✅ | ❌ | ❌ |
| Cashier | ❌ | ❌ | ✅ | ✅ |
| Admin | ✅ (POS) | ✅ | ✅ | ✅ |

The key benefits:
1. **Waiters focus on service**, not handling money
2. **Cashier has dedicated payment interface**
3. **Multiple partial payments** supported on same order
4. **Order stays active** until explicitly completed
5. **Full audit trail** of all payment transactions
6. **Works alongside existing POS** - no breaking changes

---

## Questions to Clarify Before Implementation

1. Should waiters be able to apply discounts, or only cashier/admin?
2. Do you need shift management for cashiers (clock in/out)?
3. Is real-time notification needed when waiter requests checkout?
4. Should the existing POS "Complete Running Order" flow be kept or replaced?
5. Do you need split bill by items or just by amount?
6. Any specific payment methods beyond cash/card/mobile?
