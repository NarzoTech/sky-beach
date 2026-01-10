# Membership & Loyalty System - API Reference

## 🔐 Authentication

All API endpoints require Bearer token authentication using Laravel Sanctum.

### Get Token
```bash
# Get token for your admin user
php artisan tinker
> $admin = App\Models\Admin::first();
> $token = $admin->createToken('pos-terminal')->plainTextToken;
```

### Using Token
```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     -H "Content-Type: application/json" \
     https://yourapp.com/api/v1/membership/identify
```

---

## 📡 Endpoints

### 1. Identify Customer
**Endpoint**: `POST /api/v1/membership/identify`

Identify a customer by phone number. Creates new customer if doesn't exist.

**Request**:
```json
{
  "phone": "+1234567890"
}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Customer identified",
  "customer": {
    "id": 1,
    "phone": "+1234567890",
    "name": "John Doe",
    "total_points": 250,
    "status": "active",
    "joined_at": "2026-01-10T10:30:00Z"
  }
}
```

**Error Response (400/500)**:
```json
{
  "success": false,
  "error": "Failed to identify customer"
}
```

**cURL Example**:
```bash
curl -X POST http://localhost:8000/api/v1/membership/identify \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"phone": "+1234567890"}'
```

---

### 2. Earn Points on Sale
**Endpoint**: `POST /api/v1/membership/earn-points`

Calculate and award points for a completed sale.

**Request**:
```json
{
  "phone": "+1234567890",
  "warehouse_id": 1,
  "amount": 100.50,
  "sale_id": 123,
  "description": "Sale from POS terminal"
}
```

**Parameters**:
- `phone` (required): Customer phone number
- `warehouse_id` (required): Warehouse ID
- `amount` (required): Sale amount in decimal
- `sale_id` (optional): Sale record ID for tracking
- `description` (optional): Transaction description

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Points earned successfully",
  "points_earned": 100,
  "total_points": 350,
  "customer": {
    "phone": "+1234567890",
    "name": "John Doe",
    "total_points": 350,
    "lifetime_points": 350
  },
  "breakdown": [
    {
      "rule_id": 1,
      "rule_name": "Earn 1 point per $1 spent",
      "action_type": "earn_points",
      "action_value": "1.00",
      "points_generated": 100
    },
    {
      "rule_id": 2,
      "rule_name": "Weekend bonus (2x)",
      "action_type": "multiply_points",
      "action_value": "2.00",
      "points_generated": 0
    }
  ],
  "transaction_id": 456
}
```

**Error Response (400)**:
```json
{
  "success": false,
  "error": "No active loyalty program for this warehouse"
}
```

**cURL Example**:
```bash
curl -X POST http://localhost:8000/api/v1/membership/earn-points \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "amount": 100.50,
    "sale_id": 123
  }'
```

---

### 3. Check Redemption Eligibility
**Endpoint**: `POST /api/v1/membership/check-redemption`

Check if customer can redeem points and get eligibility details.

**Request**:
```json
{
  "phone": "+1234567890",
  "warehouse_id": 1,
  "points": 100
}
```

**Parameters**:
- `phone` (required): Customer phone number
- `warehouse_id` (required): Warehouse ID
- `points` (optional): Points to check value for

**Success Response (200)**:
```json
{
  "success": true,
  "customer": {
    "id": 1,
    "phone": "+1234567890",
    "name": "John Doe",
    "total_points": 350,
    "status": "active"
  },
  "eligibility": {
    "is_eligible": true,
    "current_balance": 350,
    "min_required": 0,
    "max_allowed": 350,
    "redemption_type": "discount",
    "rate": "100.00",
    "status": "active"
  },
  "redemption_value": 1.00
}
```

**cURL Example**:
```bash
curl -X POST http://localhost:8000/api/v1/membership/check-redemption \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "points": 100
  }'
```

---

### 4. Redeem Points
**Endpoint**: `POST /api/v1/membership/redeem-points`

Redeem points for discount, free item, or cashback.

**Request**:
```json
{
  "phone": "+1234567890",
  "warehouse_id": 1,
  "points_to_redeem": 100,
  "redemption_type": "discount",
  "sale_id": 124
}
```

**Parameters**:
- `phone` (required): Customer phone number
- `warehouse_id` (required): Warehouse ID
- `points_to_redeem` (required): Number of points to redeem
- `redemption_type` (required): Type - `discount`, `free_item`, or `cashback`
- `menu_item_id` (optional): For free_item type
- `ingredient_id` (optional): For free_item type
- `quantity` (optional): For free_item type
- `sale_id` (optional): Sale record ID

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Redemption created successfully",
  "redemption_id": 1,
  "redemption": {
    "id": 1,
    "loyalty_customer_id": 1,
    "points_used": 100,
    "redemption_type": "discount",
    "amount_value": "1.00",
    "status": "applied"
  },
  "calculation": {
    "valid": true,
    "points_redeemed": 100,
    "redemption_value": "1.00",
    "redemption_type": "discount",
    "rate": "100.00"
  },
  "customer": {
    "phone": "+1234567890",
    "name": "John Doe",
    "total_points": 250
  }
}
```

**Error Response (400)**:
```json
{
  "success": false,
  "error": "Insufficient points for redemption",
  "available_points": 50,
  "requested_points": 100
}
```

**cURL Example**:
```bash
curl -X POST http://localhost:8000/api/v1/membership/redeem-points \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "points_to_redeem": 100,
    "redemption_type": "discount",
    "sale_id": 124
  }'
```

---

### 5. Get Customer Balance
**Endpoint**: `GET /api/v1/membership/balance/{phone}`

Get current points balance for a customer.

**Parameters**:
- `phone` (URL parameter): Customer phone number

**Success Response (200)**:
```json
{
  "success": true,
  "balance": {
    "phone": "+1234567890",
    "total_points": 250,
    "available_points": 250,
    "lifetime_earned": 350,
    "redeemed": 100
  }
}
```

**Error Response (404)**:
```json
{
  "success": false,
  "error": "Customer not found"
}
```

**cURL Example**:
```bash
curl -X GET http://localhost:8000/api/v1/membership/balance/+1234567890 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 6. Get Customer Profile
**Endpoint**: `GET /api/v1/membership/customer/{phone}`

Get complete customer profile with all statistics.

**Parameters**:
- `phone` (URL parameter): Customer phone number

**Success Response (200)**:
```json
{
  "success": true,
  "customer": {
    "id": 1,
    "phone": "+1234567890",
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active",
    "joined_at": "2026-01-05T10:30:00Z",
    "current_balance": 250,
    "lifetime_earned": 350,
    "total_redeemed": 100,
    "available": 250,
    "transaction_count": 5,
    "earnings_count": 3,
    "redemptions_count": 2,
    "member_since": "2026-01-05T10:30:00Z",
    "last_purchase": "2026-01-10T14:20:00Z",
    "last_redemption": "2026-01-09T16:45:00Z",
    "last_transaction": {
      "type": "earn",
      "amount": "50.00",
      "date": "2026-01-10T14:20:00Z"
    }
  }
}
```

**cURL Example**:
```bash
curl -X GET http://localhost:8000/api/v1/membership/customer/+1234567890 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 7. Get Transaction History
**Endpoint**: `GET /api/v1/membership/transactions/{phone}`

Get transaction history for a customer.

**Parameters**:
- `phone` (URL parameter): Customer phone number
- `limit` (query param, optional): Number of transactions to return (default: 20, max: 100)

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "customer": {
      "phone": "+1234567890",
      "name": "John Doe"
    },
    "transactions": [
      {
        "id": 5,
        "type": "earn",
        "amount": "50.00",
        "balance_before": "200.00",
        "balance_after": "250.00",
        "description": "Points earned from sale",
        "created_at": "2026-01-10T14:20:00Z"
      },
      {
        "id": 4,
        "type": "redeem",
        "amount": "-100.00",
        "balance_before": "300.00",
        "balance_after": "200.00",
        "description": "Points redeemed",
        "created_at": "2026-01-09T16:45:00Z"
      },
      {
        "id": 3,
        "type": "earn",
        "amount": "75.00",
        "balance_before": "225.00",
        "balance_after": "300.00",
        "description": "Points earned from sale",
        "created_at": "2026-01-08T10:15:00Z"
      }
    ]
  }
}
```

**Query Parameters**:
```bash
# Get last 50 transactions
?limit=50

# Get last 10 transactions
?limit=10
```

**cURL Example**:
```bash
curl -X GET "http://localhost:8000/api/v1/membership/transactions/+1234567890?limit=50" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🔄 Complete Transaction Flow

### Example: Customer's First Purchase

```
Step 1: Customer arrives at POS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Cashier enters phone: +1234567890
  
  Request:
  POST /api/v1/membership/identify
  { "phone": "+1234567890" }
  
  Response:
  {
    "success": true,
    "customer": {
      "id": 1,
      "phone": "+1234567890",
      "total_points": 0,
      "status": "active"
    }
  }
  
  POS Display: "New customer - Welcome!"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Step 2: Customer selects items
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Order Total: $50.00
  Available Points: 0
  
  POS Display: "Total: $50.00 | Points: 0"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Step 3: Check if customer wants to redeem
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Request:
  POST /api/v1/membership/check-redemption
  {
    "phone": "+1234567890",
    "warehouse_id": 1,
    "points": 50
  }
  
  Response:
  {
    "eligibility": {
      "is_eligible": false,
      "current_balance": 0,
      "min_required": 0
    }
  }
  
  POS Display: "Not enough points to redeem"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Step 4: Sale completed
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Request:
  POST /api/v1/membership/earn-points
  {
    "phone": "+1234567890",
    "warehouse_id": 1,
    "amount": 50.00,
    "sale_id": 123
  }
  
  Response:
  {
    "success": true,
    "points_earned": 50,
    "total_points": 50,
    "breakdown": [
      {
        "rule_name": "Earn 1 point per $1 spent",
        "points_generated": 50
      }
    ]
  }
  
  POS Display: "Thank you! You earned 50 points!"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Step 5: Receipt printed
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  ─────────────────────────────────
  Thank you for your purchase!
  
  Total: $50.00
  Points Earned: 50
  Your Balance: 50 points
  
  Next time you can redeem 50 points
  for $0.50 discount!
  ─────────────────────────────────
```

---

## 📊 Response Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 400 | Bad Request | Invalid input or business logic error |
| 401 | Unauthorized | Missing or invalid authentication token |
| 404 | Not Found | Resource not found |
| 500 | Server Error | Internal server error |

---

## ⚠️ Error Handling

### Common Errors and Solutions

**Error**: `"Program not found"`
- **Cause**: No active loyalty program for the warehouse
- **Solution**: Create a program in admin panel for this warehouse

**Error**: `"Insufficient points for redemption"`
- **Cause**: Customer doesn't have enough points
- **Solution**: Show customer available balance, allow them to enter less points

**Error**: `"Customer not found"`
- **Cause**: Phone number doesn't match any customer (shouldn't happen with identify first)
- **Solution**: Call identify endpoint first to create/get customer

**Error**: `"No points earned"`
- **Cause**: Sale amount was below minimum transaction amount
- **Solution**: Inform customer that sale didn't qualify for points

---

## 🔄 Retry Strategy

For production systems, implement retry logic:

```javascript
// Example: JavaScript with exponential backoff
async function callLoyaltyAPI(endpoint, data, retries = 3) {
  for (let attempt = 0; attempt < retries; attempt++) {
    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });
      
      if (response.ok) {
        return await response.json();
      }
      
      if (response.status >= 500 && attempt < retries - 1) {
        // Retry on server errors
        await delay(Math.pow(2, attempt) * 1000);
        continue;
      }
      
      throw new Error(`HTTP ${response.status}`);
    } catch (error) {
      if (attempt === retries - 1) throw error;
      await delay(Math.pow(2, attempt) * 1000);
    }
  }
}
```

---

## 📱 Integration Examples

### Example 1: React/JavaScript
```javascript
const identifyCustomer = async (phone) => {
  const response = await fetch('/api/v1/membership/identify', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ phone })
  });
  
  return await response.json();
};

const earnPoints = async (phone, warehouseId, amount) => {
  const response = await fetch('/api/v1/membership/earn-points', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ phone, warehouse_id: warehouseId, amount })
  });
  
  return await response.json();
};
```

### Example 2: Python
```python
import requests

def identify_customer(phone, token):
    headers = {
        'Authorization': f'Bearer {token}',
        'Content-Type': 'application/json'
    }
    
    response = requests.post(
        'http://localhost:8000/api/v1/membership/identify',
        headers=headers,
        json={'phone': phone}
    )
    
    return response.json()

def earn_points(phone, warehouse_id, amount, token):
    headers = {
        'Authorization': f'Bearer {token}',
        'Content-Type': 'application/json'
    }
    
    response = requests.post(
        'http://localhost:8000/api/v1/membership/earn-points',
        headers=headers,
        json={
            'phone': phone,
            'warehouse_id': warehouse_id,
            'amount': amount
        }
    )
    
    return response.json()
```

---

## 🧪 Testing with cURL

### Test Suite
```bash
#!/bin/bash

TOKEN="your_token_here"
BASE_URL="http://localhost:8000/api/v1/membership"

# Test 1: Identify
echo "Test 1: Identify Customer"
curl -X POST $BASE_URL/identify \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"phone": "+1234567890"}'
echo "\n"

# Test 2: Get Balance
echo "Test 2: Get Balance"
curl -X GET $BASE_URL/balance/+1234567890 \
  -H "Authorization: Bearer $TOKEN"
echo "\n"

# Test 3: Earn Points
echo "Test 3: Earn Points"
curl -X POST $BASE_URL/earn-points \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "amount": 100
  }'
echo "\n"

# Test 4: Check Redemption
echo "Test 4: Check Redemption"
curl -X POST $BASE_URL/check-redemption \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "points": 100
  }'
echo "\n"

# Test 5: Redeem Points
echo "Test 5: Redeem Points"
curl -X POST $BASE_URL/redeem-points \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1234567890",
    "warehouse_id": 1,
    "points_to_redeem": 100,
    "redemption_type": "discount"
  }'
echo "\n"

# Test 6: Get Profile
echo "Test 6: Get Profile"
curl -X GET $BASE_URL/customer/+1234567890 \
  -H "Authorization: Bearer $TOKEN"
echo "\n"

# Test 7: Get History
echo "Test 7: Get Transaction History"
curl -X GET "$BASE_URL/transactions/+1234567890?limit=10" \
  -H "Authorization: Bearer $TOKEN"
echo "\n"
```

---

## 📝 Rate Limiting

Recommendations for production:
- Identify: 1000 req/min per token
- Earn Points: 500 req/min per token
- Redeem: 200 req/min per token
- Balance: 5000 req/min per token

Implement using Laravel middleware or external service.

---

## 🔒 Security Best Practices

1. **Never expose tokens**: Keep API tokens secret
2. **Use HTTPS**: Always use HTTPS in production
3. **Validate input**: All endpoints validate input server-side
4. **Rate limit**: Implement rate limiting
5. **Log access**: Log all API access for security audits
6. **Rotate tokens**: Regularly rotate authentication tokens

---

**API Reference Complete!**

For more information, see:
- MEMBERSHIP_IMPLEMENTATION_COMPLETE.md
- MEMBERSHIP_QUICK_START.md
