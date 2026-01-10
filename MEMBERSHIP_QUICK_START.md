# Membership & Loyalty System - Quick Start Guide

## 🚀 5-Minute Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Create Loyalty Program (Via Admin Panel)
1. Go to Dashboard → Membership
2. Click "Manage Programs"
3. Create New Program:
   - **Name**: Default Loyalty Program
   - **Warehouse**: Select your warehouse
   - **Earning Type**: per_amount
   - **Earning Rate**: 1.00 (1 point per $1)
   - **Redemption Type**: discount
   - **Points Per Unit**: 100 (100 points = $1 discount)
4. Save

### 3. Create Basic Rule (Via Admin Panel)
1. Go to Dashboard → Membership → Manage Rules
2. Create New Rule:
   - **Name**: Earn 1 point per $1 spent
   - **Program**: Select your program
   - **Action Type**: earn_points
   - **Action Value**: 1
   - **Applies To**: all
   - **Priority**: 1
3. Save

---

## 💻 Using the API

### Get API Token
```bash
# For testing, get a Sanctum token from your admin user
php artisan tinker
> $admin = App\Models\Admin::first();
> $token = $admin->createToken('test-pos')->plainTextToken;
# Copy the token
```

### Identify Customer (First Time)
```bash
curl -X POST http://localhost:8000/api/v1/membership/identify \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890"
  }'
```

**Response:**
```json
{
  "success": true,
  "customer": {
    "id": 1,
    "phone": "+1234567890",
    "total_points": 0,
    "status": "active"
  }
}
```

### Earn Points on Sale
```bash
curl -X POST http://localhost:8000/api/v1/membership/earn-points \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "amount": 100,
    "sale_id": 1
  }'
```

**Response:**
```json
{
  "success": true,
  "points_earned": 100,
  "total_points": 100,
  "breakdown": [
    {
      "rule_name": "Earn 1 point per $1 spent",
      "points_generated": 100
    }
  ]
}
```

### Check Balance
```bash
curl -X GET http://localhost:8000/api/v1/membership/balance/+1234567890 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**
```json
{
  "success": true,
  "balance": {
    "phone": "+1234567890",
    "total_points": 100,
    "lifetime_earned": 100,
    "redeemed": 0
  }
}
```

### Redeem Points
```bash
curl -X POST http://localhost:8000/api/v1/membership/redeem-points \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "points_to_redeem": 100,
    "redemption_type": "discount",
    "sale_id": 2
  }'
```

**Response:**
```json
{
  "success": true,
  "redemption_id": 1,
  "calculation": {
    "points_redeemed": 100,
    "redemption_value": 1.00,
    "redemption_type": "discount"
  }
}
```

---

## 📱 Complete POS Checkout Flow

```
1. Customer arrives at POS
   ↓
2. Cashier enters phone number: +1234567890
   ↓
3. API: POST /identify → Get customer profile
   ↓
4. Display: "Welcome John! You have 50 points"
   ↓
5. Customer selects items, total: $50
   ↓
6. (Optional) Customer wants to redeem 100 points ($1 off)
   → API: POST /redeem-points → Apply $1 discount
   → Total becomes: $49
   ↓
7. Sale completed for $49
   ↓
8. API: POST /earn-points with amount=$49
   → Earns 49 new points
   → Total: 50 + 49 - 100 = -1 (no, it's after earning)
   → Actually: Previous 50 - 100 (redeemed) + 49 (earned) = -1 + 49...
   ↓
   Wait, let me recalculate:
   - Before: 50 points
   - After redemption: 50 - 100 = -50? No, only 50 available
   - So can't redeem 100, max is 50 points ($0.50 discount)
   ↓
9. Sale completed for $49.50 (with $0.50 discount)
   ↓
10. Customer earns 49.50 points
    Final balance: (50 - 50 redeemed) + 49.50 earned = 49.50 points
```

---

## 🎯 Common Scenarios

### Scenario 1: New Customer First Purchase
```
Phone: +1111111111
Sale Amount: $50

Step 1: Identify
→ New customer created with 0 points

Step 2: Earn Points
→ Earns 50 points
→ Total: 50 points

Step 3: Points Available
→ Customer can now redeem up to 50 points on next purchase
```

### Scenario 2: Existing Customer with Discount
```
Phone: +2222222222
Current Points: 200
Sale Amount: $100

Step 1: Check Balance
→ Shows 200 points available
→ 200 points = $2.00 discount possible

Step 2: Redeem 100 Points
→ Deducts 100 points
→ Applies $1.00 discount
→ Sale: $100 → $99.00

Step 3: Earn Points
→ Earns 99 points on $99 sale
→ Final Balance: (200 - 100) + 99 = 199 points
```

### Scenario 3: Special Promotion (2x Points on Weekend)
```
Saturday Sale
Amount: $50
Customer: John

Step 1: Evaluate Rules
→ Day is Saturday ✓
→ Apply 2x multiplier
→ Base points: 50
→ With multiplier: 50 × 2 = 100 points

Step 2: Earn Points
→ Earns 100 points (instead of 50)
```

---

## 🛠️ Admin Dashboard Features

### Dashboard View
- View programs and customer count
- See recent transactions
- View top customers by points
- Quick access to all sections

### Manage Programs
- Create/Edit/Delete loyalty programs
- Configure earning and redemption rules
- Set minimum transaction amounts
- Manage multiple warehouses

### Manage Rules
- Create complex earning rules
- Set priorities (higher = more important)
- Time-based rules (date, time, day of week)
- Category/Item specific bonuses
- Drag-and-drop priority ordering

### Customer Management
- Search customers by phone/name/email
- View customer details and history
- Manually adjust points (admin only)
- Block/suspend/resume customers
- Export customer list

### Transaction Logs
- View all transactions (earn/redeem/adjust/expire)
- Filter by type, customer, date range
- Export audit log as CSV
- View transaction details

### Statistics
- Total points earned/redeemed
- Active customer count
- Breakdown by transaction type
- Date range filtering

---

## 🔑 Key Concepts

### Phone Normalization
- All phone numbers are normalized
- Format: +1234567890 (international format)
- Duplicates handled: same phone = same customer

### Points Balance
- **total_points**: Current available balance
- **lifetime_points**: All earned (ever)
- **redeemed_points**: All redeemed (ever)

### Transaction Types
- **earn**: Customer earned points
- **redeem**: Customer redeemed points
- **adjust**: Admin manually adjusted
- **expire**: Points expired (if applicable)

### Rule Priorities
- Higher priority rules evaluated first
- Multiple applicable rules combine
- Multipliers stack
- Bonuses add together

---

## ✅ Verification Checklist

After setup, verify:

- [ ] Migrations ran successfully
- [ ] Program created and active
- [ ] Rule created for basic earning
- [ ] Can identify customer via API
- [ ] Points earned on sale
- [ ] Points deducted on redemption
- [ ] Admin dashboard loads
- [ ] Customer list shows data
- [ ] Transaction log shows activity
- [ ] Export functions work

---

## 🆘 Troubleshooting

### "Program not found" Error
**Solution**: Create an active program for the warehouse

### "Insufficient points" Error
**Solution**: Customer doesn't have enough points for redemption

### "Customer not found" (first time)
**Solution**: This is normal - customer is auto-created on first request

### No points earned
**Solution**: 
- Check program is active
- Check rule exists and is active
- Check rule conditions match
- Check warehouse ID is correct

### API returns 401 Unauthorized
**Solution**: 
- Verify Sanctum token is correct
- Verify token includes Authorization header
- Check admin user exists

---

## 📞 Support Resources

1. **Implementation Plan**: `MEMBERSHIP_IMPLEMENTATION_PLAN.md`
2. **Complete Docs**: `MEMBERSHIP_IMPLEMENTATION_COMPLETE.md`
3. **Models**: `Modules/Membership/app/Models/`
4. **Services**: `Modules/Membership/app/Services/`
5. **Controllers**: `Modules/Membership/app/Http/Controllers/`

---

## 🎉 You're Ready!

The system is fully functional. Start using it now:

1. **For POS**: Use the API endpoints
2. **For Admin**: Use the web dashboard
3. **For Reporting**: Use the transaction logs and statistics

Happy loyalty building! 🚀
