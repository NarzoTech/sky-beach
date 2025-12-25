<?php

namespace Modules\Currency\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Currency\app\Models\MultiCurrency;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('currency.view');
        $currencies = MultiCurrency::get();

        return view('currency::index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('currency.create');

        return view('currency::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('currency.store');
        $rules = [
            'currency_name' => 'required|unique:multi_currencies',
            'country_code' => 'required|unique:multi_currencies',
            'currency_code' => 'required|unique:multi_currencies',
            'currency_icon' => 'required|unique:multi_currencies',
            'currency_rate' => 'required|numeric',
        ];
        $customMessages = [
            'currency_name.required' => __('Currency name is required'),
            'currency_name.unique' => __('Currency name already exist'),
            'country_code.required' => __('Country code is required'),
            'country_code.unique' => __('Country code already exist'),
            'currency_code.required' => __('Currency code is required'),
            'currency_code.unique' => __('Currency code already exist'),
            'currency_icon.required' => __('Currency icon is required'),
            'currency_icon.unique' => __('Currency icon already exist'),
            'currency_rate.required' => __('Currency rate is required'),
            'currency_rate.numeric' => __('Currency rate must be number'),
        ];

        $request->validate($rules, $customMessages);

        $currency = new MultiCurrency();

        if ($request->is_default == 'yes') {
            MultiCurrency::where(['is_default' => 'yes'])->update(['is_default' => 'no']);
        }

        $currency->currency_name = $request->currency_name;
        $currency->country_code = $request->country_code;
        $currency->currency_code = $request->currency_code;
        $currency->currency_icon = $request->currency_icon;
        $currency->currency_rate = $request->currency_rate;
        $currency->is_default = $request->is_default;
        $currency->currency_position = $request->currency_position;
        $currency->status = $request->status;
        $currency->save();

        $notification = __('Created Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('currency.edit');
        $currency = MultiCurrency::findOrFail($id);

        return view('currency::edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('currency.update');
        $rules = [
            'currency_name' => 'required|unique:multi_currencies,currency_name,'.$id,
            'country_code' => 'required|unique:multi_currencies,country_code,'.$id,
            'currency_code' => 'required|unique:multi_currencies,currency_code,'.$id,
            'currency_icon' => 'required|unique:multi_currencies,currency_icon,'.$id,
            'currency_rate' => 'required|numeric',
        ];
        $customMessages = [
            'currency_name.required' => __('Currency name is required'),
            'currency_name.unique' => __('Currency name already exist'),
            'country_code.required' => __('Country code is required'),
            'country_code.unique' => __('Country code already exist'),
            'currency_code.required' => __('Currency code is required'),
            'currency_code.unique' => __('Currency code already exist'),
            'currency_icon.required' => __('Currency icon is required'),
            'currency_icon.unique' => __('Currency icon already exist'),
            'currency_rate.required' => __('Currency rate is required'),
            'currency_rate.numeric' => __('Currency rate must be number'),
        ];

        $request->validate($rules, $customMessages);

        $currency = MultiCurrency::findOrFail($id);

        if ($request->is_default == 'yes') {
            MultiCurrency::where(['is_default' => 'yes'])->update(['is_default' => 'no']);
        }

        if ($currency->is_default == 'yes' && $request->is_default == 'no') {
            MultiCurrency::where('id', 1)->update(['is_default' => 'yes']);
        }

        $currency->currency_name = $request->currency_name;
        $currency->country_code = $request->country_code;
        $currency->currency_code = $request->currency_code;
        $currency->currency_icon = $request->currency_icon;
        $currency->currency_rate = $request->currency_rate;
        $currency->is_default = $request->is_default;
        $currency->currency_position = $request->currency_position;
        $currency->status = $request->status;
        $currency->save();

        $notification = __('Updated Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('admin.currency.index')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('currency.delete');

        return 'pending, admin can not be able to delete item when currency has assign to payment gateway';
        $currency = MultiCurrency::find($id);
        if ($currency->is_default == 'yes') {
            MultiCurrency::where('id', 1)->update(['is_default' => 'yes']);
        }
        $currency->delete();

        $notification = __('Delete Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('admin.currency.index')->with($notification);

        $is_flutterwave = Flutterwave::where('currency_id', $id)->first();
        $is_instamojo = InstamojoPayment::where('currency_id', $id)->first();
        $is_paypal = PaypalPayment::where('currency_id', $id)->first();
        $is_paystack = PaystackAndMollie::where('paystack_currency_id', $id)->first();
        $is_mollie = PaystackAndMollie::where('mollie_currency_id', $id)->first();
        $is_razorpay = RazorpayPayment::where('currency_id', $id)->first();
        $is_sslcommerz = SslcommerzPayment::where('currency_id', $id)->first();
        $is_stripe = StripePayment::where('currency_id', $id)->first();

        if ($is_flutterwave || $is_instamojo || $is_paypal || $is_paystack || $is_mollie || $is_razorpay || $is_sslcommerz || $is_stripe) {
            $notification = __('You can not delete this currency. Because there are one or more payment method has been created in this currency.');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->route('admin.currency.index')->with($notification);
        } else {
            $currency->delete();
            $notification = __('Delete Successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->route('admin.currency.index')->with($notification);
        }
    }
}
