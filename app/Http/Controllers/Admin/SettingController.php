<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Balance;
use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Models\BalanceTransfer;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplierPayment;
use Modules\GlobalSetting\app\Enums\AllTimeZoneEnum;
use Modules\GlobalSetting\app\Enums\CountryEnum;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Purchase\app\Models\PurchaseReturnDetails;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Supplier\app\Models\SupplierPayment;

class SettingController extends Controller
{
    use RedirectHelperTrait;
    public function settings()
    {
        checkAdminHasPermissionAndThrowException('setting.view');
        $all_timezones = AllTimeZoneEnum::getAll();
        $allCountries = CountryEnum::getAll();
        return view('admin.settings.settings', compact('all_timezones', 'allCountries'));
    }


    public function printSetting()
    {
        return view('globalsetting::settings.print-settings');
    }

    public function courierSetting()
    {
        return view('globalsetting::settings.courier-settings');
    }

    public function resetDatabase()
    {
        return view('admin.pages.database_clear');
    }

    public function clearDatabase(Request $request)
    {
        checkAdminHasPermissionAndThrowException('database.reset');
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth('admin')->user()->password)) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Password does not match.', 'alert-type' => 'error']);
        }


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        // truncate payment
        CustomerPayment::truncate();
        // truncate due
        CustomerDue::truncate();

        Sale::truncate();
        ProductSale::truncate();
        // truncate sale stock
        Stock::truncate();
        // supplier payment
        SupplierPayment::truncate();
        Asset::truncate();
        Balance::truncate();
        BalanceTransfer::truncate();
        EmployeeSalary::truncate();
        Expense::truncate();
        ExpenseSupplierPayment::truncate();
        Ledger::truncate();
        Payment::truncate();

        // purchase
        Purchase::truncate();
        PurchaseDetails::truncate();
        PurchaseReturn::truncate();
        PurchaseReturnDetails::truncate();
        Artisan::call('db:seed', [
            '--class' => 'RolePermissionSeeder',
            '--force' => true,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure a cash account exists for balance transfers
        if (!Account::where('account_type', 'cash')->exists()) {
            Account::create([
                'account_type' => 'cash',
            ]);
        }

        // cache clear
        Cache::clear();
        return back()->with(['alert-type' => 'success', 'messege' => 'Database cleared successfully.']);
    }
}
