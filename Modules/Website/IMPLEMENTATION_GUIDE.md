# Website Module - Dynamic Implementation Guide

## Quick Reference

### Project Structure
```
Modules/Website/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── BlogController.php         ✅ Complete
│   │   │   ├── BookingController.php      ✅ Complete
│   │   │   ├── ChefController.php         ✅ Complete
│   │   │   ├── CmsPageController.php      ✅ Complete
│   │   │   ├── FaqController.php          ✅ Complete
│   │   │   ├── RestaurantMenuItemController.php ✅ Complete
│   │   │   └── WebsiteServiceController.php     ✅ Complete
│   │   ├── WebsiteController.php          ✅ Complete
│   │   └── MenuActionController.php       ⚠️ Partial (needs update)
│   └── Models/
│       ├── Blog.php                       ✅ Complete
│       ├── Booking.php                    ✅ Complete
│       ├── Chef.php                       ✅ Complete
│       ├── CmsPage.php                    ✅ Complete
│       ├── Faq.php                        ✅ Complete
│       ├── RestaurantMenuItem.php         ✅ Complete
│       └── WebsiteService.php             ✅ Complete
├── resources/views/
│   ├── admin/                             ✅ Complete
│   ├── layouts/master.blade.php           ✅ Complete
│   ├── partials/                          ✅ Complete
│   ├── index.blade.php                    ✅ Complete
│   ├── menu.blade.php                     ✅ Complete
│   ├── blogs.blade.php                    ✅ Complete
│   ├── chefs.blade.php                    ✅ Complete
│   ├── cart_view.blade.php                ❌ Static HTML
│   ├── checkout.blade.php                 ❌ Static HTML
│   ├── reservation.blade.php              ❌ Static HTML
│   └── contact.blade.php                  ❌ Static HTML
└── routes/web.php                         ⚠️ Needs expansion
```

### Related Modules
- **Menu Module:** `Modules/Menu/app/Models/MenuItem.php` - Main menu items
- **Sales Module:** `Modules/Sales/app/Models/Sale.php` - Orders (use for website orders)
- **Sales Module:** `Modules/Sales/app/Models/ProductSale.php` - Order line items
- **TableManagement:** `Modules/TableManagement/app/Models/RestaurantTable.php`

### Existing Cart Model (app/Models/Cart.php)
Current Cart model is for Ingredients, not MenuItem. We'll create WebsiteCart for menu items.

### Database Tables Reference
```
menu_items          - Menu items with variants, addons
menu_categories     - Menu categories
menu_variants       - Item size variants
menu_addons         - Item addons
sales               - Orders (reuse with order_type='website')
product_sales       - Order line items
bookings            - Reservations (exists, needs frontend)
```

---

## PHASE 1: Cart & Checkout System

### Status: ✅ COMPLETED (January 17, 2026)

### 1.1 Create WebsiteCart Model

**File:** `Modules/Website/app/Models/WebsiteCart.php`

**Migration:** `Modules/Website/database/migrations/xxxx_create_website_carts_table.php`

**Schema:**
```php
Schema::create('website_carts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('session_id')->nullable()->index();
    $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
    $table->foreignId('variant_id')->nullable()->constrained('menu_variants')->onDelete('set null');
    $table->integer('quantity')->default(1);
    $table->decimal('unit_price', 10, 2);
    $table->json('addons')->nullable(); // Array of addon IDs with prices
    $table->text('special_instructions')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'session_id']);
});
```

### 1.2 Create CartController

**File:** `Modules/Website/app/Http/Controllers/CartController.php`

**Methods:**
| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /cart | Display cart page |
| `getCart()` | GET /cart/items | AJAX - Get cart items JSON |
| `addItem()` | POST /cart/add | AJAX - Add item to cart |
| `updateQuantity()` | PUT /cart/update/{id} | AJAX - Update quantity |
| `removeItem()` | DELETE /cart/remove/{id} | AJAX - Remove item |
| `clearCart()` | DELETE /cart/clear | Clear entire cart |
| `applyCoupon()` | POST /cart/coupon | Apply discount coupon |
| `getCartCount()` | GET /cart/count | AJAX - Get cart count for header |

### 1.3 Create CheckoutController

**File:** `Modules/Website/app/Http/Controllers/CheckoutController.php`

**Methods:**
| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /checkout | Display checkout page |
| `processOrder()` | POST /checkout/process | Create order from cart |
| `orderSuccess($id)` | GET /checkout/success/{id} | Order confirmation page |

### 1.4 Routes to Add

**File:** `Modules/Website/routes/web.php`

```php
// Cart Routes
Route::prefix('cart')->name('website.cart.')->group(function() {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/items', [CartController::class, 'getCart'])->name('items');
    Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
    Route::post('/add', [CartController::class, 'addItem'])->name('add');
    Route::put('/update/{id}', [CartController::class, 'updateQuantity'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'removeItem'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clearCart'])->name('clear');
    Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon');
});

// Checkout Routes
Route::prefix('checkout')->name('website.checkout.')->group(function() {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'processOrder'])->name('process');
    Route::get('/success/{id}', [CheckoutController::class, 'orderSuccess'])->name('success');
});
```

### 1.5 Views to Update

| View | Action |
|------|--------|
| `cart_view.blade.php` | Make dynamic with real cart data |
| `checkout.blade.php` | Add form functionality, order type selection |
| `partials/header.blade.php` | Add cart count badge |
| Create `checkout_success.blade.php` | Order confirmation page |

### 1.6 JavaScript Functions Needed

```javascript
// Cart operations
function addToCart(menuItemId, variantId, quantity, addons, specialInstructions)
function updateCartQuantity(cartItemId, quantity)
function removeFromCart(cartItemId)
function clearCart()
function applyCoupon(code)
function updateCartBadge()
```

---

## PHASE 2: Order Management System

### Status: ✅ COMPLETED (January 20, 2026)

### 2.1 Use Existing Sale Model

**File:** `Modules/Sales/app/Models/Sale.php`

Add new order_type: `'website'` to existing types.

**Existing Order Types:**
- `dine_in`
- `take_away`
- `delivery`
- **NEW:** `website`

### 2.2 Create OrderController (Frontend)

**File:** `Modules/Website/app/Http/Controllers/OrderController.php`

**Methods:**
| Method | Route | Auth | Description |
|--------|-------|------|-------------|
| `myOrders()` | GET /my-orders | Yes | List user's orders |
| `orderDetails($id)` | GET /order/{id} | Yes | Order details |
| `trackOrder($id)` | GET /order/{id}/track | Yes | Real-time tracking |
| `cancelOrder($id)` | POST /order/{id}/cancel | Yes | Request cancellation |
| `reorder($id)` | POST /order/{id}/reorder | Yes | Add items to cart |

### 2.3 Create Admin WebsiteOrderController

**File:** `Modules/Website/app/Http/Controllers/Admin/WebsiteOrderController.php`

**Methods:**
| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET admin/website-orders | List all website orders |
| `show($id)` | GET admin/website-orders/{id} | Order details |
| `updateStatus()` | PUT admin/website-orders/{id}/status | Change status |
| `printOrder()` | GET admin/website-orders/{id}/print | Print order |

### 2.4 Order Status Flow

```
pending → confirmed → preparing → ready → out_for_delivery → delivered
                                      ↓
                               (for pickup) → completed
        ↓ (any stage)
     cancelled
```

### 2.5 Routes to Add

```php
// Frontend Order Routes (Auth Required)
Route::middleware('auth')->group(function() {
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('website.orders.index');
    Route::get('/order/{id}', [OrderController::class, 'orderDetails'])->name('website.orders.show');
    Route::get('/order/{id}/track', [OrderController::class, 'trackOrder'])->name('website.orders.track');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('website.orders.cancel');
    Route::post('/order/{id}/reorder', [OrderController::class, 'reorder'])->name('website.orders.reorder');
});

// Admin Routes
Route::prefix('admin/website-orders')->name('admin.website-orders.')->group(function() {
    Route::get('/', [WebsiteOrderController::class, 'index'])->name('index');
    Route::get('/{id}', [WebsiteOrderController::class, 'show'])->name('show');
    Route::put('/{id}/status', [WebsiteOrderController::class, 'updateStatus'])->name('status');
});
```

### 2.6 Views to Create

| View | Description |
|------|-------------|
| `orders/index.blade.php` | My orders list with status badges |
| `orders/details.blade.php` | Order details with timeline |
| `orders/track.blade.php` | Live order tracking |
| `admin/website-orders/index.blade.php` | Admin order list |
| `admin/website-orders/show.blade.php` | Admin order details |

---

## PHASE 3: Reservation System

### Status: ✅ COMPLETED (January 20, 2026)

### 3.1 Update Booking Model

**File:** `Modules/Website/app/Models/Booking.php`

**Add fields:**
```php
$fillable = [
    // ... existing
    'user_id',           // Link to registered user
    'confirmation_code', // Unique confirmation code
    'reminder_sent',     // Boolean for email reminder
];
```

### 3.2 Create TimeSlot Model (Optional)

**File:** `Modules/Website/app/Models/TimeSlot.php`

**Migration:**
```php
Schema::create('time_slots', function (Blueprint $table) {
    $table->id();
    $table->tinyInteger('day_of_week'); // 0=Sunday, 6=Saturday
    $table->time('start_time');
    $table->time('end_time');
    $table->integer('max_reservations')->default(10);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 3.3 Create ReservationController

**File:** `Modules/Website/app/Http/Controllers/ReservationController.php`

**Methods:**
| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /reservation | Show reservation form |
| `store()` | POST /reservation | Submit reservation |
| `checkAvailability()` | GET /reservation/check | AJAX availability check |
| `myReservations()` | GET /my-reservations | User's reservations |
| `cancel($id)` | DELETE /reservation/{id} | Cancel reservation |
| `success($code)` | GET /reservation/success/{code} | Confirmation page |

### 3.4 Routes to Add

```php
Route::prefix('reservation')->name('website.reservation.')->group(function() {
    Route::get('/', [ReservationController::class, 'index'])->name('index');
    Route::post('/', [ReservationController::class, 'store'])->name('store');
    Route::get('/check', [ReservationController::class, 'checkAvailability'])->name('check');
    Route::get('/success/{code}', [ReservationController::class, 'success'])->name('success');
});

Route::middleware('auth')->group(function() {
    Route::get('/my-reservations', [ReservationController::class, 'myReservations'])->name('website.reservations.index');
    Route::delete('/reservation/{id}', [ReservationController::class, 'cancel'])->name('website.reservation.cancel');
});
```

### 3.5 Views to Update/Create

| View | Action |
|------|--------|
| `reservation.blade.php` | Make form dynamic with AJAX availability |
| Create `reservation_success.blade.php` | Confirmation page |
| Create `my_reservations.blade.php` | User's reservations list |

---

## PHASE 4: Contact Form

### Status: ⏳ Pending

### 4.1 Create ContactMessage Model

**File:** `Modules/Website/app/Models/ContactMessage.php`

**Migration:**
```php
Schema::create('contact_messages', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->string('phone')->nullable();
    $table->string('subject');
    $table->text('message');
    $table->enum('status', ['new', 'read', 'replied'])->default('new');
    $table->timestamp('replied_at')->nullable();
    $table->foreignId('replied_by')->nullable()->constrained('admins')->onDelete('set null');
    $table->text('reply_message')->nullable();
    $table->timestamps();
});
```

### 4.2 Create ContactController

**File:** `Modules/Website/app/Http/Controllers/ContactController.php`

**Methods:**
| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /contact | Show contact form |
| `store()` | POST /contact | Submit message |
| `success()` | GET /contact/success | Thank you page |

### 4.3 Create Admin ContactController

**File:** `Modules/Website/app/Http/Controllers/Admin/ContactController.php`

**Methods:**
| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET admin/contact-messages | List messages |
| `show($id)` | GET admin/contact-messages/{id} | View message |
| `reply($id)` | POST admin/contact-messages/{id}/reply | Reply to message |
| `destroy($id)` | DELETE admin/contact-messages/{id} | Delete message |

---

## PHASE 5: Catering Services

### Status: ⏳ Pending

### 5.1 Create CateringPackage Model

**File:** `Modules/Website/app/Models/CateringPackage.php`

**Migration:**
```php
Schema::create('catering_packages', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->text('long_description')->nullable();
    $table->integer('min_guests')->default(10);
    $table->integer('max_guests')->default(100);
    $table->decimal('price_per_person', 10, 2);
    $table->json('includes')->nullable(); // List of what's included
    $table->json('menu_items')->nullable(); // Linked menu items
    $table->string('image')->nullable();
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_active')->default(true);
    $table->integer('display_order')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

### 5.2 Create CateringInquiry Model

**File:** `Modules/Website/app/Models/CateringInquiry.php`

**Migration:**
```php
Schema::create('catering_inquiries', function (Blueprint $table) {
    $table->id();
    $table->string('inquiry_number')->unique();
    $table->foreignId('package_id')->nullable()->constrained('catering_packages')->onDelete('set null');
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('name');
    $table->string('email');
    $table->string('phone');
    $table->string('event_type'); // Wedding, Corporate, Birthday, etc.
    $table->date('event_date');
    $table->time('event_time')->nullable();
    $table->integer('guest_count');
    $table->text('venue_address')->nullable();
    $table->text('special_requirements')->nullable();
    $table->enum('status', ['pending', 'contacted', 'quoted', 'confirmed', 'cancelled'])->default('pending');
    $table->decimal('quoted_amount', 10, 2)->nullable();
    $table->text('admin_notes')->nullable();
    $table->timestamps();
});
```

### 5.3 Controllers

**Frontend:** `Modules/Website/app/Http/Controllers/CateringController.php`
- `index()` - List packages
- `show($slug)` - Package details
- `inquiryForm()` - Custom inquiry form
- `submitInquiry()` - Submit inquiry

**Admin:** `Modules/Website/app/Http/Controllers/Admin/CateringController.php`
- CRUD for packages
- Manage inquiries

---

## PHASE 6: User Account Integration

### Status: ⏳ Pending

### 6.1 Create UserAddress Model

**File:** `Modules/Website/app/Models/UserAddress.php`

**Migration:**
```php
Schema::create('user_addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('label')->default('Home'); // Home, Office, Other
    $table->string('address_line_1');
    $table->string('address_line_2')->nullable();
    $table->string('city');
    $table->string('state')->nullable();
    $table->string('postal_code')->nullable();
    $table->string('phone')->nullable();
    $table->text('delivery_instructions')->nullable();
    $table->boolean('is_default')->default(false);
    $table->timestamps();
});
```

### 6.2 Create UserAccountController

**File:** `Modules/Website/app/Http/Controllers/UserAccountController.php`

**Methods:**
| Method | Route | Description |
|--------|-------|-------------|
| `dashboard()` | GET /account | Account overview |
| `profile()` | GET /account/profile | Edit profile |
| `updateProfile()` | PUT /account/profile | Save profile |
| `addresses()` | GET /account/addresses | Manage addresses |
| `storeAddress()` | POST /account/addresses | Add address |
| `updateAddress($id)` | PUT /account/addresses/{id} | Update address |
| `deleteAddress($id)` | DELETE /account/addresses/{id} | Delete address |
| `setDefaultAddress($id)` | POST /account/addresses/{id}/default | Set default |

---

## PHASE 7: Enhancements

### Status: ⏳ Pending

### 7.1 Coupon System

**Model:** `Modules/Website/app/Models/Coupon.php`

**Schema:**
```php
Schema::create('coupons', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->enum('type', ['percentage', 'fixed'])->default('percentage');
    $table->decimal('value', 10, 2);
    $table->decimal('min_order_amount', 10, 2)->default(0);
    $table->decimal('max_discount', 10, 2)->nullable();
    $table->integer('usage_limit')->nullable();
    $table->integer('usage_limit_per_user')->default(1);
    $table->integer('used_count')->default(0);
    $table->date('valid_from')->nullable();
    $table->date('valid_until')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 7.2 Review System

**Model:** `Modules/Website/app/Models/Review.php`

**Schema:**
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
    $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
    $table->tinyInteger('rating'); // 1-5
    $table->text('review_text')->nullable();
    $table->boolean('is_approved')->default(false);
    $table->timestamps();

    $table->unique(['user_id', 'menu_item_id', 'sale_id']);
});
```

---

## Implementation Checklist

### Phase 1: Cart & Checkout ✅ COMPLETED
- [x] Create WebsiteCart model & migration
- [x] Create CartController
- [x] Create CheckoutController
- [x] Update cart_view.blade.php
- [x] Update checkout.blade.php
- [x] Create checkout_success.blade.php
- [x] Add cart routes
- [x] Add cart badge to header
- [ ] Test full cart flow (manual testing required)

### Phase 2: Order Management ✅ COMPLETED
- [x] Add 'website' order type to Sale model
- [x] Create OrderController
- [x] Create Admin WebsiteOrderController
- [x] Create order views (frontend)
- [x] Create order views (admin)
- [x] Add order routes
- [ ] Test order flow (manual testing required)

### Phase 3: Reservation System ✅ COMPLETED
- [x] Update Booking model
- [x] Create ReservationController
- [x] Update reservation.blade.php
- [x] Create confirmation views (reservation_success.blade.php)
- [x] Create my_reservations.blade.php
- [x] Add reservation routes
- [ ] Test reservation flow (manual testing required)

### Phase 4: Contact Form
- [ ] Create ContactMessage model & migration
- [ ] Create ContactController
- [ ] Create Admin ContactController
- [ ] Update contact.blade.php
- [ ] Create admin views
- [ ] Add routes
- [ ] Test contact flow

### Phase 5: Catering Services
- [ ] Create CateringPackage model & migration
- [ ] Create CateringInquiry model & migration
- [ ] Create CateringController
- [ ] Create Admin CateringController
- [ ] Create catering views
- [ ] Add routes
- [ ] Test catering flow

### Phase 6: User Account
- [ ] Create UserAddress model & migration
- [ ] Create UserAccountController
- [ ] Create account views
- [ ] Add routes
- [ ] Test account flow

### Phase 7: Enhancements
- [ ] Create Coupon model & migration
- [ ] Create Review model & migration
- [ ] Integrate coupons with checkout
- [ ] Add review functionality
- [ ] Email notifications

---

## Quick Commands

```bash
# Create migration
php artisan make:migration create_website_carts_table --path=Modules/Website/database/migrations

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear && php artisan config:clear && php artisan view:clear
```

---

## Color Scheme (Admin Panel Standard)

```css
--primary: #696cff;
--success: #71dd37;
--danger: #ff3e1d;
--warning: #ffab00;
--secondary: #8592a3;
--background: #f5f5f9;
```

---

*Last Updated: January 20, 2026 (Phase 3 Completed)*
