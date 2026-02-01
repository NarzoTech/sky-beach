# Project Memory - SkyBeach Restaurant Management System

## Date Formatting Convention

### Helper Function

Always use the `formatDate()` helper function from `app/Helpers/helper.php` for all date formatting in blade files.

```php
function formatDate($date, $format = 'd-m-Y')
```

### Standard Format

**Default format: `d-m-Y`** (e.g., `12-12-2025`)

Use the default format for all display dates by calling `formatDate()` without a second parameter.

### Special Cases (use explicit format only when needed)

| Use Case                | Format        | Example                 |
| ----------------------- | ------------- | ----------------------- |
| Date with time          | `d-m-Y h:i A` | `12-12-2025 10:30 AM`   |
| Database/HTML inputs    | `Y-m-d`       | `2025-12-12`            |
| Month picker            | `m/Y`         | `12/2025`               |
| Dashboard month display | `F Y`         | `December 2025`         |
| Dashboard year display  | `Y`           | `2025`                  |
| Dashboard weekday       | `l, d M Y`    | `Thursday, 12 Dec 2025` |

### Usage Examples

```blade
{{-- Standard date display (PREFERRED) --}}
{{ formatDate($sale->order_date) }}
{{ formatDate($payment->payment_date) }}
{{ formatDate($expense->date) }}

{{-- Current date --}}
{{ formatDate(now()) }}

{{-- Date with time (when time is important) --}}
{{ formatDate($sale->created_at, 'd-m-Y h:i A') }}

{{-- Database/form inputs --}}
value="{{ formatDate(now(), 'Y-m-d') }}"
```

### DO NOT Use

- `$date->format()` directly in blade files
- `date()` PHP function in blade files
- `Carbon::now()->format()` directly
- `now()->parse($date)->format()`
- Different display formats like `d M, Y`, `d - M - Y`, `d F, Y` (use default `d-m-Y`)

### Exceptions

- Vendor files (don't modify)
- JavaScript Date objects (use Y-m-d for compatibility)
- Settings page examples (showing format options)
- Time-only display: `h:i A`

---

## Account Validation

### Cash Account

- Only ONE cash account is allowed per system
- Validation is in `Modules\Accounts\app\Http\Requests\AccountRequest.php`
- Uses `withValidator()` method to check for existing cash accounts

---

## Code Style Notes

### Blade Files

- Use helper functions for formatting (formatDate, currency, etc.)
- Keep consistent formatting across all modules
- Use `{{ __('text') }}` for translatable strings

### Request Validation

- Always return an array from `rules()` method
- Use `withValidator()` for complex validation logic
