# Membership & Loyalty System - Complete Technical Specification

## 🎯 Core Concept & Design Goals

### Customer Experience
- **Identification**: Phone number only - zero friction
- **No friction**: No card swipes, no logins at POS
- **Automatic**: Points earned on every sale instantly
- **Choice**: Points can be redeemed as discount, free item, or cashback
- **Reference**: Think Starbucks-level loyalty, but simpler

### System Design Goals (LOCKED)
1. ✅ Customer identified **only by phone number**
2. ✅ Zero friction at POS terminal
3. ✅ Fully rule-driven (no hardcoding)
4. ✅ Fully auditable (ledger-based)
5. ✅ Multi-warehouse support
6. ✅ Real-time point calculations
7. ✅ Flexible redemption options

---

## 📊 Database Schema

### 1. **loyalty_programs** Table
Stores loyalty program configurations (one per restaurant/warehouse)

```sql
CREATE TABLE loyalty_programs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    warehouse_id BIGINT NOT NULL,
    name VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT 1,
    
    -- Points Earning Rules
    earning_type ENUM('per_transaction', 'per_amount') DEFAULT 'per_amount',
    earning_rate DECIMAL(8,2),  -- e.g., 1 point per $1 (1.00)
    min_transaction_amount DECIMAL(10,2),
    
    -- Redemption Rules
    redemption_type ENUM('discount', 'free_item', 'cashback') DEFAULT 'discount',
    points_per_unit DECIMAL(8,2),  -- e.g., 100 points = $1 discount
    
    -- Rules Configuration
    earning_rules JSON,  -- { "exclude_items": [], "categories": [...] }
    redemption_rules JSON,  -- { "min_points": 100, "max_per_transaction": 500 }
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    created_by BIGINT,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);
```

### 2. **loyalty_customers** Table
Customer loyalty profile linked by phone number

```sql
CREATE TABLE loyalty_customers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    phone VARCHAR(20) UNIQUE NOT NULL,
    user_id BIGINT,  -- Optional: link to registered User
    name VARCHAR(255),
    email VARCHAR(255),
    
    -- Points Balance
    total_points DECIMAL(10,2) DEFAULT 0,
    lifetime_points DECIMAL(10,2) DEFAULT 0,
    redeemed_points DECIMAL(10,2) DEFAULT 0,
    
    -- Status & Preferences
    status ENUM('active', 'blocked', 'suspended') DEFAULT 'active',
    joined_at TIMESTAMP,
    last_purchase_at TIMESTAMP,
    
    -- Preferences
    opt_in_sms BOOLEAN DEFAULT 1,
    opt_in_email BOOLEAN DEFAULT 1,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_phone (phone)
);
```

### 3. **loyalty_transactions** Table
Every point earning/redemption transaction (audit trail)

```sql
CREATE TABLE loyalty_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    loyalty_customer_id BIGINT NOT NULL,
    warehouse_id BIGINT NOT NULL,
    
    -- Transaction Details
    transaction_type ENUM('earn', 'redeem', 'adjust', 'expire') DEFAULT 'earn',
    points_amount DECIMAL(10,2) NOT NULL,
    points_balance_before DECIMAL(10,2),
    points_balance_after DECIMAL(10,2),
    
    -- Source of Transaction
    source_type ENUM('sale', 'manual_adjust', 'refund', 'expiry') DEFAULT 'sale',
    source_id BIGINT,  -- sale_id, expense_id, etc.
    
    -- Redemption Details (if applicable)
    redemption_method ENUM('discount', 'free_item', 'cashback'),
    redemption_value DECIMAL(10,2),
    
    -- Metadata
    description TEXT,
    notes TEXT,
    created_by BIGINT,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (loyalty_customer_id) REFERENCES loyalty_customers(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES admins(id),
    INDEX idx_customer (loyalty_customer_id),
    INDEX idx_warehouse (warehouse_id),
    INDEX idx_source (source_type, source_id)
);
```

### 4. **loyalty_rules** Table
Flexible rule engine for different earning/redemption scenarios

```sql
CREATE TABLE loyalty_rules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    loyalty_program_id BIGINT NOT NULL,
    
    -- Rule Details
    name VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT 1,
    
    -- Condition Type
    condition_type ENUM('category', 'item', 'amount', 'time_period', 'customer_group') DEFAULT 'amount',
    condition_value JSON,  -- { "category_ids": [1,2,3] } or { "min": 100, "max": 500 }
    
    -- Action Type
    action_type ENUM('earn_points', 'bonus_points', 'multiply_points', 'redeem_discount') DEFAULT 'earn_points',
    action_value DECIMAL(8,2),  -- e.g., 2 (multiply points by 2)
    
    -- Timing
    start_date DATE,
    end_date DATE,
    day_of_week JSON,  -- ["MON", "TUE", ...] or null for all days
    start_time TIME,
    end_time TIME,
    
    -- Applicability
    applies_to ENUM('all', 'specific_items', 'specific_categories', 'specific_customers') DEFAULT 'all',
    applicable_items JSON,
    applicable_categories JSON,
    
    priority INT DEFAULT 0,  -- Higher priority rules apply first
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    created_by BIGINT,
    
    FOREIGN KEY (loyalty_program_id) REFERENCES loyalty_programs(id),
    FOREIGN KEY (created_by) REFERENCES admins(id),
    INDEX idx_program (loyalty_program_id),
    INDEX idx_active (is_active)
);
```

### 5. **loyalty_redemptions** Table
Track redemption usage for reconciliation

```sql
CREATE TABLE loyalty_redemptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    loyalty_customer_id BIGINT NOT NULL,
    sale_id BIGINT,  -- Link to sale where points were redeemed
    
    -- Redemption Details
    points_used DECIMAL(10,2),
    redemption_type ENUM('discount', 'free_item', 'cashback'),
    amount_value DECIMAL(10,2),  -- Discount amount or cashback value
    
    -- Free Item (if applicable)
    menu_item_id BIGINT,
    ingredient_id BIGINT,
    quantity INT,
    
    status ENUM('pending', 'applied', 'cancelled') DEFAULT 'applied',
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    created_by BIGINT,
    
    FOREIGN KEY (loyalty_customer_id) REFERENCES loyalty_customers(id),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);
```

### 6. **loyalty_customer_segments** Table
For targeted rules/bonuses

```sql
CREATE TABLE loyalty_customer_segments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    loyalty_program_id BIGINT NOT NULL,
    
    name VARCHAR(255),
    description TEXT,
    
    -- Segment Criteria
    min_lifetime_points DECIMAL(10,2) DEFAULT 0,
    max_lifetime_points DECIMAL(10,2),
    min_transactions INT DEFAULT 0,
    min_spent DECIMAL(10,2) DEFAULT 0,
    
    is_active BOOLEAN DEFAULT 1,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (loyalty_program_id) REFERENCES loyalty_programs(id)
);
```

---

## 🏗️ Models Architecture

### Model Hierarchy

```
LoyaltyProgram
├── LoyaltyCustomer (many)
├── LoyaltyRule (many)
├── LoyaltyCustomerSegment (many)
└── LoyaltyTransaction (many)

LoyaltyCustomer
├── User (optional, one)
├── LoyaltyTransaction (many)
├── LoyaltyRedemption (many)
└── LoyaltyProgram (one)

LoyaltyTransaction
├── LoyaltyCustomer (one)
├── Warehouse (one)
├── Sale (optional, one)
└── Admin (created_by)

LoyaltyRule
├── LoyaltyProgram (one)
└── Admin (created_by)

LoyaltyRedemption
├── LoyaltyCustomer (one)
├── Sale (one)
└── Admin (created_by)
```

### Key Models to Create

1. **LoyaltyProgram** - Core program config
2. **LoyaltyCustomer** - Customer profile
3. **LoyaltyTransaction** - Audit trail
4. **LoyaltyRule** - Rule engine
5. **LoyaltyRedemption** - Redemption tracking
6. **LoyaltyCustomerSegment** - Segmentation

---

## 🔄 Process Flows

### 1. **Customer Identification at POS**
```
POS Terminal Input: Phone Number
    ↓
Query: loyalty_customers WHERE phone = ?
    ↓
Found? 
  ├─ YES → Load customer profile (phone, points balance)
  └─ NO → Create new loyalty_customer record
    ↓
Return customer object with current points balance
```

### 2. **Point Earning on Sale**
```
Sale Completed → Calculate Points
    ↓
Query: LoyaltyProgram WHERE warehouse_id = ? AND is_active = 1
    ↓
Execute LoyaltyRules (in priority order)
    ├─ Check conditions (time, category, amount, etc.)
    ├─ Apply earning rules
    └─ Accumulate total points
    ↓
Create LoyaltyTransaction (earn)
    ├─ loyalty_customer_id
    ├─ points_amount (earned)
    ├─ source_id = sale_id
    └─ source_type = 'sale'
    ↓
Update LoyaltyCustomer
    ├─ total_points += earned_points
    ├─ lifetime_points += earned_points
    └─ last_purchase_at = NOW()
    ↓
Return: {customer_points, points_earned, messages}
```

### 3. **Point Redemption at POS**
```
Customer chooses to redeem X points
    ↓
Query: LoyaltyProgram redemption_rules
    ├─ Check min_points requirement
    ├─ Check max_per_transaction limit
    └─ Validate redemption type allowed
    ↓
Calculate redemption value
    ├─ Type: discount → amount_value = X / points_per_unit
    ├─ Type: free_item → validate item exists & quantity
    └─ Type: cashback → amount_value = X / points_per_unit
    ↓
Create LoyaltyRedemption record
    ├─ loyalty_customer_id
    ├─ points_used = X
    ├─ redemption_type
    ├─ amount_value or menu_item_id
    └─ status = 'pending'
    ↓
Apply to Sale
    ├─ If discount: sale.order_discount += amount_value
    ├─ If free_item: add to sale.details
    └─ If cashback: track for later payout
    ↓
Create LoyaltyTransaction (redeem)
    ├─ transaction_type = 'redeem'
    ├─ points_amount = -X
    └─ source_id = sale_id
    ↓
Update LoyaltyCustomer
    ├─ total_points -= X
    ├─ redeemed_points += X
    └─ last_redemption_at = NOW()
    ↓
Return: {remaining_points, redemption_details}
```

### 4. **Manual Point Adjustment (Admin)**
```
Admin adjusts points in dashboard
    ↓
Validate: adjustment_reason, adjustment_amount, customer_id
    ↓
Create LoyaltyTransaction (adjust)
    ├─ transaction_type = 'adjust'
    ├─ points_amount (positive or negative)
    ├─ description = reason
    └─ created_by = admin_id
    ↓
Update LoyaltyCustomer.total_points
    ↓
Log audit trail with reason
```

### 5. **Rule Evaluation Engine**
```
Rules apply in order: priority DESC
    ↓
For each active rule:
  ├─ Check time conditions (date, time, day_of_week)
  ├─ Check applicability (items, categories, customers)
  ├─ If conditions match:
  │   ├─ Calculate base points (earning_rate * amount)
  │   ├─ Apply rule action
  │   │   ├─ earn_points: add flat amount
  │   │   ├─ bonus_points: add bonus (e.g., 2x earning)
  │   │   └─ multiply_points: multiply (e.g., 3x multiplier)
  │   └─ Add to total_earned_points
  └─ Continue to next rule
    ↓
Return: total_points_earned, breakdown
```

---

## 🗂️ Module Structure

```
Modules/Membership/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── MembershipController.php (Admin dashboard)
│   │   │   ├── LoyaltyProgramController.php (Program CRUD)
│   │   │   ├── LoyaltyRuleController.php (Rules CRUD)
│   │   │   ├── LoyaltyCustomerController.php (Customer management)
│   │   │   ├── LoyaltyTransactionController.php (Audit logs)
│   │   │   └── POSController.php (POS API endpoints)
│   │   ├── Requests/
│   │   │   ├── StoreLoyaltyProgramRequest.php
│   │   │   ├── UpdateLoyaltyProgramRequest.php
│   │   │   ├── StoreLoyaltyRuleRequest.php
│   │   │   ├── IdentifyCustomerRequest.php
│   │   │   └── RedeemPointsRequest.php
│   │   └── Resources/
│   │       ├── LoyaltyCustomerResource.php
│   │       ├── LoyaltyTransactionResource.php
│   │       └── LoyaltyProgramResource.php
│   ├── Models/
│   │   ├── LoyaltyProgram.php
│   │   ├── LoyaltyCustomer.php
│   │   ├── LoyaltyTransaction.php
│   │   ├── LoyaltyRule.php
│   │   ├── LoyaltyRedemption.php
│   │   └── LoyaltyCustomerSegment.php
│   ├── Services/
│   │   ├── LoyaltyService.php (Main orchestrator)
│   │   ├── PointCalculationService.php (Point math)
│   │   ├── RuleEngineService.php (Rule evaluation)
│   │   ├── RedemptionService.php (Redemption logic)
│   │   └── CustomerIdentificationService.php (Phone lookup)
│   ├── Events/
│   │   ├── PointsEarned.php
│   │   ├── PointsRedeemed.php
│   │   └── CustomerJoined.php
│   ├── Listeners/
│   │   ├── SendPointsEarnedNotification.php
│   │   └── SendPointsRedeemedNotification.php
│   ├── Traits/
│   │   └── HasLoyaltyPoints.php (To add to User model)
│   └── Providers/
│       ├── MembershipServiceProvider.php
│       └── RouteServiceProvider.php
├── database/
│   └── migrations/
│       ├── create_loyalty_programs_table.php
│       ├── create_loyalty_customers_table.php
│       ├── create_loyalty_transactions_table.php
│       ├── create_loyalty_rules_table.php
│       ├── create_loyalty_redemptions_table.php
│       └── create_loyalty_customer_segments_table.php
├── resources/
│   ├── views/
│   │   ├── dashboard.blade.php (Admin overview)
│   │   ├── programs/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── rules/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── edit.blade.php
│   │   ├── customers/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   └── transactions/
│   │       └── index.blade.php
│   └── assets/
│       └── js/
│           └── loyalty-admin.js
├── routes/
│   ├── web.php (Admin routes)
│   └── api.php (POS API routes)
└── tests/
    ├── Feature/
    │   ├── LoyaltyProgramTest.php
    │   ├── PointEarningTest.php
    │   ├── PointRedemptionTest.php
    │   └── RuleEngineTest.php
    └── Unit/
        ├── LoyaltyServiceTest.php
        └── RuleEngineServiceTest.php
```

---

## 📡 API Endpoints (POS Integration)

### Admin Dashboard APIs

```
GET    /admin/membership/dashboard              # Overview stats
GET    /admin/membership/programs               # List programs
POST   /admin/membership/programs               # Create program
PUT    /admin/membership/programs/:id           # Update program
DELETE /admin/membership/programs/:id           # Delete program

GET    /admin/membership/rules                  # List rules
POST   /admin/membership/rules                  # Create rule
PUT    /admin/membership/rules/:id              # Update rule
DELETE /admin/membership/rules/:id              # Delete rule

GET    /admin/membership/customers              # List customers
GET    /admin/membership/customers/:id          # Get customer details
POST   /admin/membership/customers/:id/adjust   # Adjust points (manual)

GET    /admin/membership/transactions           # Audit log
GET    /admin/membership/transactions/:id       # Transaction details
```

### POS Terminal APIs (High Priority)

```
POST   /api/v1/membership/identify
  Request: { "phone": "+1234567890" }
  Response: { 
    success: true,
    customer: { id, phone, name, total_points, last_purchase_at },
    message: "Customer identified"
  }

POST   /api/v1/membership/earn-points
  Request: { 
    customer_id: 123, 
    sale_id: 456, 
    amount: 500, 
    warehouse_id: 1 
  }
  Response: { 
    success: true,
    points_earned: 50,
    total_points: 250,
    breakdown: [{ rule: "...", points: 25 }, ...]
  }

POST   /api/v1/membership/redeem-points
  Request: { 
    customer_id: 123, 
    points_to_redeem: 100, 
    redemption_type: "discount",
    warehouse_id: 1
  }
  Response: { 
    success: true,
    redemption_value: 10,
    remaining_points: 150,
    redemption_id: 789
  }

GET    /api/v1/membership/customer/:phone
  Response: { customer details with points }

POST   /api/v1/membership/check-redemption
  Request: { customer_id, points, warehouse_id }
  Response: { is_valid, max_available, value }
```

---

## 🔐 Security & Permissions

### Required Permissions (Spatie)
```
membership.view              # View programs and customers
membership.create            # Create programs and rules
membership.edit              # Edit programs and rules
membership.delete            # Delete programs and rules
membership.manage_points     # Manually adjust points
membership.view_transactions # View audit logs
```

### Access Control
- POS API: Authenticated via sanctum/API token
- Admin Dashboard: Via permission system
- Phone lookup: Rate-limited to prevent abuse

---

## 🧪 Test Coverage

### Unit Tests
- PointCalculationService
- RuleEngineService
- CustomerIdentificationService
- RedemptionService

### Feature Tests
- Customer identification by phone
- Point earning on sales
- Point redemption process
- Manual point adjustments
- Rule engine evaluation
- Edge cases (insufficient points, blocked customers, etc.)

### Integration Tests
- Sale completion → Point earning flow
- Redemption → Sale discount/item application
- Multiple rules interaction

---

## 📋 Implementation Checklist

### Phase 1: Foundation (Database & Models)
- [ ] Create all migration files
- [ ] Create Model classes with relationships
- [ ] Add model scopes and accessors
- [ ] Seed sample data

### Phase 2: Core Services
- [ ] LoyaltyService (orchestrator)
- [ ] PointCalculationService
- [ ] RuleEngineService
- [ ] CustomerIdentificationService
- [ ] RedemptionService

### Phase 3: Admin Interface
- [ ] LoyaltyProgramController (CRUD)
- [ ] LoyaltyRuleController (CRUD)
- [ ] Views for program management
- [ ] Views for rule management
- [ ] Dashboard overview

### Phase 4: POS Integration
- [ ] POSController API endpoints
- [ ] Identify customer endpoint
- [ ] Earn points endpoint
- [ ] Redeem points endpoint
- [ ] Point balance check

### Phase 5: Admin Features
- [ ] Customer management UI
- [ ] Transaction/audit log viewer
- [ ] Manual point adjustment
- [ ] Reports & analytics

### Phase 6: Testing & Refinement
- [ ] Unit tests
- [ ] Feature tests
- [ ] Integration tests
- [ ] Performance testing

---

## 🎨 Example Business Rules

### Rule 1: Basic Point Earning
```json
{
  "name": "Earn 1 point per $1 spent",
  "condition_type": "amount",
  "condition_value": { "min": 0 },
  "action_type": "earn_points",
  "action_value": 1,
  "applies_to": "all",
  "priority": 1
}
```

### Rule 2: Weekend Bonus (Double Points)
```json
{
  "name": "Double points on weekends",
  "condition_type": "time_period",
  "condition_value": {},
  "action_type": "multiply_points",
  "action_value": 2,
  "day_of_week": ["SAT", "SUN"],
  "applies_to": "all",
  "priority": 2
}
```

### Rule 3: Category Specific Bonus
```json
{
  "name": "3x points on beverages",
  "condition_type": "category",
  "condition_value": { "category_ids": [5, 6, 7] },
  "action_type": "multiply_points",
  "action_value": 3,
  "applies_to": "specific_categories",
  "applicable_categories": [5, 6, 7],
  "priority": 3
}
```

### Rule 4: Time-Based Promotion
```json
{
  "name": "Happy hour bonus (2-5 PM)",
  "condition_type": "time_period",
  "condition_value": {},
  "action_type": "bonus_points",
  "action_value": 10,
  "start_time": "14:00",
  "end_time": "17:00",
  "applies_to": "all",
  "priority": 4
}
```

---

## 🚀 Integration Points

### With Sales Module
- Hook into sale completion
- Calculate and apply points
- Handle redemptions in checkout

### With POS Module
- API endpoint for customer identification
- Real-time point calculation display
- Redemption options at checkout

### With Customer Module
- Link loyalty profile to customer account
- Phone number as primary key
- SMS/Email notifications

### With User Model
- Add `HasLoyaltyPoints` trait
- Add loyalty methods to User model
- Track membership in user profile

---

## 📊 Data Integrity & Audit

### Immutability
- LoyaltyTransaction records are immutable (no updates after creation)
- Only admins can create adjustment transactions
- Every change is logged with admin_id and timestamp

### Audit Trail
- Every point earn/redeem/adjust creates a transaction
- Complete history available for each customer
- Exportable for compliance

### Reconciliation
- Can verify total_points = lifetime_points - redeemed_points - expired_points
- Monthly reconciliation reports
- Point expiry rules (configurable)

---

## 🔄 Future Enhancements

1. **Tiered Loyalty**: Different earning rates based on customer segment
2. **Referral Bonus**: Points for referring friends
3. **Birthday Bonus**: Special points on birthday
4. **VIP Program**: Premium tiers with benefits
5. **Point Expiry**: Automatic expiration after 12 months
6. **SMS Integration**: Send point balance via SMS
7. **Mobile App**: Customer loyalty app
8. **Gamification**: Achievements and badges
9. **Analytics**: Detailed loyalty analytics dashboard
10. **Export**: Customer data export for CRM

---

## 📝 Key Decisions

1. **Phone as Primary Identifier**: Simplifies POS flow, no login needed
2. **Ledger-Based Audit**: Every transaction is immutable and traceable
3. **Rule Engine**: Flexible JSON-based rules for unlimited scenarios
4. **Per-Warehouse Programs**: Each location can have different rules
5. **Soft Linking to User**: Optional connection to user accounts
6. **Transaction Immutability**: Cannot modify history, only adjust forward
7. **JSON for Complex Rules**: Stores flexible conditions without additional tables

---

## 🎯 Success Metrics

1. **Adoption Rate**: % of transactions using loyalty
2. **Point Redemption Rate**: % of earned points redeemed
3. **Customer Retention**: Repeat customer rate
4. **Average Transaction Value**: With vs without loyalty
5. **Database Performance**: Query times under 100ms at POS
6. **Data Consistency**: Zero discrepancies in point audits

