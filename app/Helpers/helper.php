<?php

use App\Exceptions\AccessPermissionDeniedException;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Modules\Accounts\app\Models\Account;
use Modules\Currency\app\Models\MultiCurrency;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\Language\app\Models\Language;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

function file_upload(UploadedFile $file, string $path = 'uploads/custom-images/', string | null $oldFile = '', bool $optimize = false)
{
    $extention = $file->getClientOriginalExtension();
    $file_name = 'img' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $extention;
    $file_name = $path . $file_name;
    $file->move(public_path($path), $file_name);

    try {
        if ($oldFile && ! str($oldFile)->contains('uploads/website-images') && File::exists(public_path($oldFile))) {
            unlink(public_path($oldFile));
        }

        if ($optimize) {
            ImageOptimizer::optimize(public_path($file_name));
        }
    } catch (Exception $e) {
        Log::info($e->getMessage());
    }

    return $file_name;
}

if (! function_exists('delete_file')) {
    function delete_file($path)
    {
        if (File::exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
if (! function_exists('remove_comma')) {
    // remove , from number
    function remove_comma($number)
    {
        return str_replace(',', '', $number);
    }
}

// file upload method
if (! function_exists('allLanguages')) {
    function allLanguages()
    {
        $allLanguages = Cache::rememberForever('allLanguages', function () {
            return Language::select('code', 'name', 'direction', 'status')->get();
        });

        if (! $allLanguages) {
            $allLanguages = Language::select('code', 'name', 'direction', 'status')->get();
        }

        return $allLanguages;
    }
}

if (! function_exists('accountList')) {
    function accountList()
    {
        $list = [
            'cash'           => 'Cash',
            'bank'           => 'Bank',
            'mobile_banking' => 'Mobile Banking',
            'card'           => 'Card',
        ];

        return $list;
    }
}

if (! function_exists('mobileBankList')) {
    function mobileBankList()
    {
        $list = [
            'bkash'    => 'bKash',
            'rocket'   => 'Rocket',
            'nagad'    => 'Nagad',
            'surecash' => 'SureCash',
            'ucash'    => 'UCash',
            'mCash'    => 'mCash',
            'tap'      => 'Tap',
        ];

        return $list;
    }
}

if (! function_exists('selectedAccount')) {
    function selectedAccount($inputValue, $selectedId = null)
    {
        $accounts = Cache::rememberForever('accounts', function () {
            return Account::all();
        });

        $html = '';

        if (! empty($accounts)) {
            foreach ($accounts as $account) {
                // Only process accounts that match the input type
                if ($account->account_type !== $inputValue) {
                    continue;
                }

                $isSelected = $account->id == $selectedId ? 'selected' : '';

                switch ($inputValue) {
                    case 'bank':
                        $html .= generateOption(
                            $account->id,
                            "{$account->bank_account_number} ({$account->bank?->name})",
                            $isSelected
                        );
                        break;

                    case 'mobile_banking':
                        $html .= generateOption(
                            $account->id,
                            "{$account->mobile_number} ({$account->mobile_bank_name})",
                            $isSelected
                        );
                        break;

                    case 'card':
                        $html .= generateOption(
                            $account->id,
                            "{$account->card_number} ({$account->bank?->name})",
                            $isSelected
                        );
                        break;

                    default:
                        break;
                }
            }
        }

        // Add 'cash' option if input value is 'cash'
        if ($inputValue === 'cash') {
            $isSelected = $selectedId === 'cash' ? 'selected' : '';
            $html .= generateOption('cash', __('Cash'), $isSelected);
        }

        return $html;
    }

    /**
     * Generate an option HTML tag with optional selected attribute.
     *
     * @param string $value
     * @param string $label
     * @param string $selected
     * @return string
     */
    function generateOption(string $value, string $label, string $selected = ''): string
    {
        return sprintf(
            '<option value="%s" %s>%s</option>',
            htmlspecialchars($value, ENT_QUOTES, 'UTF-8'),
            $selected,
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }
}

// card type
if (! function_exists('cardTypeList')) {
    function cardTypeList()
    {
        $list = [
            'mastercard' => 'MasterCard',
            'visa'       => 'Visa',
            'amex'       => 'American Express',
            'nexus'      => 'Nexus',
            'credit'     => 'Credit Card',
            'debit'      => 'Debit Card',
            'prepaid'    => 'Prepaid Card',

        ];

        return $list;
    }
}

if (! function_exists('getSessionLanguage')) {
    function getSessionLanguage(): string
    {
        if (! session()->has('lang')) {
            session()->put('lang', config('app.locale'));
            session()->forget('text_direction');
            session()->put('text_direction', 'ltr');
        }

        $lang = Session::get('lang');

        return $lang;
    }
}

// all payment methods
if (! function_exists('allPaymentMethods')) {
    function allPaymentMethods($key = null)
    {
        $methods = [
            'bkash'         => 'bKash',
            'rocket'        => 'Rocket',
            'nagad'         => 'Nagad',
            'bank_transfer' => 'Bank Transfer',
            'hand_cash'     => 'Hand Cash',
            'cod'           => 'Cash On Delivery',
            'check'         => 'Bank Check',
        ];

        if ($key) {
            return $methods[$key];
        }

        return $methods;
    }
}
function admin_lang()
{
    return Session::get('admin_lang');
}

// calculate currency
function currency($price = '')
{
    // currency information will be loaded by Session value

    $currencySetting = Cache::rememberForever('currency', function () {
        $siteCurrencyId = Session::get('site_currency');

        $currency = MultiCurrency::when($siteCurrencyId, function ($query) use ($siteCurrencyId) {
            return $query->where('id', $siteCurrencyId);
        })->when(! $siteCurrencyId, function ($query) {
            return $query->where('is_default', 'yes');
        })->first();

        return $currency;
    });

    $currency_icon     = $currencySetting->currency_icon;
    $currency_code     = $currencySetting->currency_code;
    $currency_rate     = $currencySetting->currency_rate ? $currencySetting->currency_rate : 1;
    $currency_position = $currencySetting->currency_position;
    if ($price) {
        $price = floatval(str_replace(',', '', $price));
        $price = $price * $currency_rate;
        $price = number_format($price, 2, '.', ',');

        if ($currency_position == 'before_price') {
            $price = $currency_icon . $price;
        } elseif ($currency_position == 'before_price_with_space') {
            $price = $currency_icon . ' ' . $price;
        } elseif ($currency_position == 'after_price') {
            $price = $price . $currency_icon;
        } elseif ($currency_position == 'after_price_with_space') {
            $price = $price . ' ' . $currency_icon;
        } else {
            $price = $currency_icon . $price;
        }

        return $price;
    } else {
        return $currency_icon . '0';
    }
}

// get currency icon
function currency_icon()
{
    $currencySetting = Cache::rememberForever('currency', function () {
        $siteCurrencyId = Session::get('site_currency');

        $currency = MultiCurrency::when($siteCurrencyId, function ($query) use ($siteCurrencyId) {
            return $query->where('id', $siteCurrencyId);
        })->when(! $siteCurrencyId, function ($query) {
            return $query->where('is_default', 'yes');
        })->first();

        return $currency;
    });

    return $currencySetting->currency_icon;
}

/**
 * Suggest quick payment amounts based on total
 *
 * @param float $total The order total
 * @return array Suggested quick amounts
 */
if (!function_exists('suggestQuickAmounts')) {
    function suggestQuickAmounts($total)
    {
        $total = ceil($total);
        $suggestions = [];

        // Round up to nearest 100
        $nearest100 = ceil($total / 100) * 100;
        if ($nearest100 > $total) {
            $suggestions[] = $nearest100;
        }

        // Round up to nearest 500
        $nearest500 = ceil($total / 500) * 500;
        if ($nearest500 > $total && !in_array($nearest500, $suggestions)) {
            $suggestions[] = $nearest500;
        }

        // Round up to nearest 1000
        $nearest1000 = ceil($total / 1000) * 1000;
        if ($nearest1000 > $total && !in_array($nearest1000, $suggestions)) {
            $suggestions[] = $nearest1000;
        }

        // Add +500 and +1000 options
        $plus500 = $nearest100 + 500;
        $plus1000 = $nearest100 + 1000;

        if (!in_array($plus500, $suggestions)) {
            $suggestions[] = $plus500;
        }
        if (!in_array($plus1000, $suggestions)) {
            $suggestions[] = $plus1000;
        }

        // Sort and take top 5
        sort($suggestions);
        return array_slice($suggestions, 0, 5);
    }
}

// remove currency icon using regular expression
function remove_icon($price)
{
    $price = preg_replace('/[^0-9,.]/', '', $price);

    return $price;
}

// custom decode and encode input value
function html_decode($text)
{
    $after_decode = htmlspecialchars_decode($text, ENT_QUOTES);

    return $after_decode;
}

if (! function_exists('checkAdminHasPermission')) {
    function checkAdminHasPermission($permission): bool
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return false;
        }

        return $admin->can($permission);
    }
}

if (! function_exists('checkAdminHasPermissionAndThrowException')) {
    function checkAdminHasPermissionAndThrowException($permission)
    {
        if (! checkAdminHasPermission($permission)) {
            throw new AccessPermissionDeniedException();
        }
    }
}

if (! function_exists('getSettingStatus')) {
    function getSettingStatus($key)
    {
        if (Cache::has('setting')) {
            $setting = Cache::get('setting');
            if (! is_null($key)) {
                return $setting->$key == 'active' ? true : false;
            }
        } else {
            try {
                return Setting::where('key', $key)->first()?->value == 'active' ? true : false;
            } catch (Exception $e) {
                Log::info($e->getMessage());
                return false;
            }
        }

        return false;
    }
}

if (! function_exists('saveLog')) {
    function saveLog($message, $level = 'info')
    {
        Log::log($level, $message);
    }
}
if (! function_exists('isRoute')) {
    function isRoute(string | array $route, string $returnValue = null)
    {
        if (is_array($route)) {
            foreach ($route as $value) {
                if (Route::is($value)) {
                    return is_null($returnValue) ? true : $returnValue;
                }
            }
            return false;
        }

        if (Route::is($route)) {
            return is_null($returnValue) ? true : $returnValue;
        }

        return false;
    }
}

if (! function_exists('numberToWord')) {

    function numberToWord($num)
    {
        $num = (string) ((int) $num);

        if ((int) ($num) && ctype_digit($num)) {

            $words = [];

            $num = str_replace([',', ' '], '', trim($num));

            $list1 = [
                '',
                'one',
                'two',
                'three',
                'four',
                'five',
                'six',
                'seven',

                'eight',
                'nine',
                'ten',
                'eleven',
                'twelve',
                'thirteen',
                'fourteen',

                'fifteen',
                'sixteen',
                'seventeen',
                'eighteen',
                'nineteen',
            ];

            $list2 = [
                '',
                'ten',
                'twenty',
                'thirty',
                'forty',
                'fifty',
                'sixty',

                'seventy',
                'eighty',
                'ninety',
                'hundred',
            ];

            $list3 = [
                '',
                'thousand',
                'million',
                'billion',
                'trillion',

                'quadrillion',
                'quintillion',
                'sextillion',
                'septillion',

                'octillion',
                'nonillion',
                'decillion',
                'undecillion',

                'duodecillion',
                'tredecillion',
                'quattuordecillion',

                'quindecillion',
                'sexdecillion',
                'septendecillion',

                'octodecillion',
                'novemdecillion',
                'vigintillion',
            ];

            $num_length = strlen($num);

            $levels = (int) (($num_length + 2) / 3);

            $max_length = $levels * 3;

            $num = substr('00' . $num, -$max_length);

            $num_levels = str_split($num, 3);

            foreach ($num_levels as $num_part) {

                $levels--;

                $hundreds = (int) ($num_part / 100);

                $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : 's') . ' ' : '');

                $tens = (int) ($num_part % 100);

                $singles = '';

                if ($tens < 20) {
                    $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens    = (int) ($tens / 10);
                    $tens    = ' ' . $list2[$tens] . ' ';
                    $singles = (int) ($num_part % 10);
                    $singles = ' ' . $list1[$singles] . ' ';
                }
                $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_part)) ? ' ' . $list3[$levels] . ' ' : '');
            }
            $commas = count($words);
            if ($commas > 1) {

                $commas = $commas - 1;
            }

            $words = implode(', ', $words);

            $words = trim(str_replace(' ,', ',', ucwords($words)), ', ');

            if ($commas) {

                $words = str_replace(',', ' and', $words);
            }
        } else if (! ((int) $num)) {

            $words = 'Zero';
        } else {

            $words = '';
        }

        return $words;
    }
}

if (! function_exists('checkPaginate')) {
    function checkPaginate($list)
    {
        return $list instanceof \Illuminate\Pagination\LengthAwarePaginator;
    }
}

if (! function_exists('formatDate')) {
    function formatDate($date, $format = 'd-m-Y')
    {
        if (empty($date)) {
            return null;
        }

        return Carbon::parse($date)->format($format);
    }
}

/**
 * Upload image to storage/app/public directory
 *
 * @param \Illuminate\Http\UploadedFile $file The uploaded file
 * @param string $folder The folder name inside storage/app/public
 * @param string|null $oldImage The old image path to delete (optional)
 * @return string The stored file path
 */
if (!function_exists('upload_image')) {
    function upload_image($file, string $folder, ?string $oldImage = null): string
    {
        // Delete old image if exists
        if ($oldImage) {
            delete_image($oldImage);
        }

        // Store new image
        return $file->store($folder, 'public');
    }
}

/**
 * Get the proper URL for an image path
 * Handles various storage patterns consistently:
 * - uploads/... → public directory
 * - website/... → public directory (static assets)
 * - assets/... → public directory
 * - storage/... → already prefixed
 * - http... → external URL
 * - everything else → storage directory
 *
 * @param string|null $path The image path from database
 * @param string|null $default The default image path if $path is empty
 * @return string|null
 */
if (!function_exists('image_url')) {
    function image_url(?string $path, ?string $default = null): ?string
    {
        if (empty($path)) {
            return $default ? asset($default) : null;
        }

        // Paths that are in public directory (no storage prefix needed)
        $publicPrefixes = ['uploads/', 'website/', 'assets/', 'images/', 'storage/', 'http://', 'https://'];

        foreach ($publicPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return asset($path);
            }
        }

        // Everything else is assumed to be in storage
        return asset('storage/' . $path);
    }
}

// Alias for backwards compatibility
if (!function_exists('storage_url')) {
    function storage_url(?string $path, ?string $default = null): ?string
    {
        return image_url($path, $default);
    }
}

/**
 * Delete image from storage/app/public directory
 *
 * @param string|null $path The image path to delete
 * @return bool
 */
if (!function_exists('delete_image')) {
    function delete_image(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Handle paths with 'storage/' prefix (used by some modules)
        $cleanPath = str_replace('storage/', '', $path);

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanPath);
        }

        return false;
    }
}

if (! function_exists('routeList')) {
    function routeList(): object
    {
        $route_list = [
            (object) ['name' => __('Dashboard'), 'route' => 'dashboard', 'permission' => 'dashboard.view'],
            (object) ['name' => 'Supplier List', 'route' => 'suppliers.index', 'permission' => 'suppliers.view'],
            (object) ['name' => 'Supplier Due Paid List', 'route' => 'suppliers.due-pay-history', 'permission' => 'suppliers.due-pay.view'],
            (object) ['name' => 'Supplier Group', 'route' => 'supplierGroup.index', 'permission' => 'supplierGroup.view'],
            (object) ['name' => 'Customers', 'route' => 'customers.index', 'permission' => 'customers.view'],
            (object) ['name' => 'Due Receive List', 'route' => 'customers.due-receive.list', 'permission' => 'customers.due-receive.view'],
            (object) ['name' => 'Customer Group', 'route' => 'customerGroup.index', 'permission' => 'customerGroup.view'],
            (object) ['name' => 'Area List', 'route' => 'area.index', 'permission' => 'area.view'],
            (object) [
                'name'       => 'Ingredient List',
                'route'      => 'ingredient.index',
                'permission' => 'ingredient.view',
                'children'   => ['ingredient.edit', 'ingredient.show'],
            ],
            (object) [
                'name'       => 'Add Ingredient',
                'route'      => 'ingredient.create',
                'permission' => 'ingredient.create',
            ],
            (object) [
                'name'       => 'Unit Type',
                'route'      => 'unit.index',
                'permission' => 'unit.view',
            ],
            (object) [
                'name'       => 'Category',
                'route'      => 'category.index',
                'permission' => 'category.view',
            ],
            (object) [
                'name'       => 'Brand',
                'route'      => 'brand.index',
                'permission' => 'brand.view',
            ],
            (object) [
                'name'       => 'Add Purchase',
                'route'      => 'purchase.create',
                'permission' => 'purchase.create',
            ],
            (object) [
                'name'       => 'Manage Purchase',
                'route'      => 'purchase.index',
                'permission' => 'purchase.view',
            ],
            (object) [
                'name'       => 'Purchases Return List',
                'route'      => 'purchase.return.index',
                'permission' => 'purchase.return.view',
            ],
            (object) [
                'name'       => 'Purchases Return Type',
                'route'      => 'purchase.return.type.list',
                'permission' => 'purchase.return.type.view',
            ],
            (object) [
                'name'       => __('Stock'),
                'route'      => 'stock.index',
                'permission' => 'stock.view',
            ],
            (object) [
                'name'       => __('Service List'),
                'route'      => 'service.index',
                'permission' => 'service.view',
            ],
            (object) [
                'name'       => __('Service Category'),
                'route'      => 'serviceCategory.index',
                'permission' => 'serviceCategory.view',
            ],
            (object) [
                'name'       => __('POS'),
                'route'      => 'pos',
                'permission' => 'pos.view',
            ],
            (object) [
                'name'       => __('Manage Sales'),
                'route'      => 'sales.index',
                'permission' => 'sales.view',
            ],
            (object) [
                'name'       => __('Sales Return List'),
                'route'      => 'sales.return.list',
                'permission' => 'salesReturn.view',
            ],
            (object) [
                'name'       => __('Cash Flow'),
                'route'      => 'cashflow',
                'permission' => 'cashflow.view',
            ],
            (object) [
                'name'       => __('Create Account'),
                'route'      => 'accounts.create',
                'permission' => 'accounts.create',
            ],
            (object) [
                'name'       => __('Account List'),
                'route'      => 'accounts.index',
                'permission' => 'accounts.view',
            ],
            (object) [
                'name'       => __('Balance Transfer'),
                'route'      => 'balance.transfer',
                'permission' => 'balance.transfer',
            ],
            (object) [
                'name'       => __('Deposit') . '/' . __('Withdraw'),
                'route'      => 'opening-balance',
                'permission' => 'opening-balance.manage',
            ],
            (object) [
                'name'       => __('Bank'),
                'route'      => 'bank.index',
                'permission' => 'bank.view',
            ],
            (object) [
                'name'       => __('Add Quotation'),
                'route'      => 'quotation.create',
                'permission' => 'quotation.create',
            ],
            (object) [
                'name'       => __('Quotation Manage'),
                'route'      => 'quotation.index',
                'permission' => 'quotation.manage',
            ],
            (object) [
                'name'  => __('Barcode Wise Product Report'),
                'route' => 'report.barcode-wise-product',
            ],
            (object) [
                'name'  => __('Barcode Wise Sale Report'),
                'route' => 'report.barcode-sale',
            ],
            (object) [
                'name'  => __('Categories Report'),
                'route' => 'report.categories',
            ],
            (object) [
                'name'  => __('Customers Report'),
                'route' => 'report.customers',
            ],
            (object) [
                'name'  => __('Due Report'),
                'route' => 'report.receivable',
            ],
            (object) [
                'name'  => __('Detail Sales Report'),
                'route' => 'report.details-sale',

            ],
            (object) [
                'name'  => __('Due Date Sales Report'),
                'route' => 'report.due-date-sale',
            ],
            (object) [
                'name'  => __('Expense Report'),
                'route' => 'report.expense',
            ],
            (object) [
                'name'  => __('Payment Received Report'),
                'route' => 'report.received-report',
            ],
            (object) [
                'name'  => __('Purchases Report'),
                'route' => 'report.purchase',
            ],
            (object) [
                'name'  => __('Suppliers Report'),
                'route' => 'report.supplier',
            ],
            (object) [
                'name'  => __('Suppliers Payment'),
                'route' => 'report.supplier-payment',
            ],
            (object) [
                'name'  => __('Salary Report'),
                'route' => 'report.salary',
            ],
            (object) [
                'name'  => __('New Expense'),
                'route' => 'expense.create',
                'query' => '?type=new',
            ],
            (object) [
                'name'  => __('Expense List'),
                'route' => 'expense.index',
                'query' => '',
            ],
            (object) [
                'name'  => __('Expense Type'),
                'route' => 'expense.type.index',
                'query' => '',
            ],
            (object) [
                'name'  => __('Asset List'),
                'route' => 'assets.index',
                'query' => '',
            ],
            (object) [
                'name'  => __('Asset Type'),
                'route' => 'asset-category.index',
                'query' => '',
            ],
            (object) [
                'name'  => __('Employee List'),
                'route' => 'employee.index',
            ],
            (object) [
                'name'  => __('Add New Employee'),
                'route' => 'employee.create',

            ],
            (object) [
                'name'  => __('All Paid Salary'),
                'route' => 'salary.list',
            ],
            (object) [
                'name'     => __('Attendance Sheet'),
                'route'    => 'attendance.index',
                'sub_menu' => [],
            ],
            (object) [
                'name'  => __('Holiday Setup'),
                'route' => 'attendance.settings.holidays.index',
            ],
            (object) [
                'name'  => __('Business Settings'),
                'route' => 'settings',
            ],
            (object) [
                'name'  => __('Reset Database'),
                'route' => 'reset.database',
            ],
            (object) [
                'name'  => __('Clear Cache'),
                'route' => 'cache.clear',
            ],
        ];
        usort($route_list, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return (object) $route_list;
    }
}
