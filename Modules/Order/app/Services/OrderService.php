<?php

namespace Modules\Order\app\Services;

use App\Models\Payment;
use App\Models\User;

use App\Traits\MailSenderTrait;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\GlobalSetting\app\Models\EmailTemplate;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderDetails;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\Variant;

class OrderService
{
    use MailSenderTrait;
    protected Order $order;
    protected OrderDetails $orderDetails;
    public function __construct(Order $order, OrderDetails $orderDetails)
    {
        $this->order = $order;
        $this->orderDetails = $orderDetails;
    }
    public function getOrders()
    {
        return $this->order->with('user');
    }

    public function getOrder($id): ?Order
    {
        return $this->order->where('order_id', $id)->first();
    }
    public function storeOrder(Request $request, $user, $cart)
    {
        $order = new Order();
        $order->order_id = substr(rand(0, time()), 0, 10);
        $order->user_id = $user != null ?  $user->id : null;
        $order->walk_in_customer = $user != null ?  0 : 1;
        $order->tax = $request->order_tax;
        $order->discount = $request->discount_amount;
        $order->total_amount = $request->total_amount;
        $order->currency_rate = cache()->get('currency')->currency_rate;
        $order->currency_name = cache()->get('currency')->currency_name;
        $order->currency_icon = cache()->get('currency')->currency_icon;
        $order->order_amount = $request->sub_total;
        $order->receive_amount = $request->receive_amount;
        $order->return_amount = $request->return_amount;
        $order->paid_amount = array_sum($request->paying_amount);
        $order->due_amount = $request->total_amount - array_sum($request->paying_amount);
        $order->due_date = $request->due_date;
        $order->created_by = auth('admin')->user()->id;

        $order->save();

        // if ($user != null) {
        //     $this->sendOrderSuccessMail($user, $order,);
        // }
        foreach ($cart as $item) {
            $variant = isset($item['variant']) ?  Variant::where('sku', $item['sku'])->first() : null;
            $orderDetails = new OrderDetails();
            $orderDetails->order_id = $order->id;
            $orderDetails->product_id = $item['id'];
            $orderDetails->product_name = $item['name'];
            $orderDetails->product_sku = $item['sku'];
            $orderDetails->variant_id = $variant != null ? $variant->id : null;
            $orderDetails->price = $item['price'];
            $orderDetails->quantity = $item['qty'];
            $orderDetails->total = $item['sub_total'];
            $orderDetails->attributes = $variant != null ? $item['variant']['attribute'] : null;
            $orderDetails->status = 1;
            $orderDetails->save();


            // update stock
            $product = Ingredient::where('id', $item['id'])->first();
            if ($product != null) {
                $product->stock = $product->stock - $item['qty'];
                $product->save();
            }
        }


        // update payments

        foreach ($request->payment_type as $key => $item) {

            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            Payment::create([
                'payment_type' => 'sale',
                'sale_id' => $order->id,
                'customer_id' => $request->order_customer_id,
                'account_id' => $account->id,
                'amount' => $request->paying_amount[$key],
                'payment_date' => now(),
                'created_by' => auth()->user()->id,
            ]);
        }

        return $order;
    }

    public function orderStatus(Request $request, Order $order)
    {

        $order->delivery_status = $request->status;

        $order->payment_status = $request->payment;

        if ($request->status == 5) {
            $order->order_status = 'success';

            if ($order->payment_status == 'pending') {
                $order->payment_status = 'success';
            }
        }
        if ($request->status == 6) {
            $order->order_status = 'cancelled';
            $order->payment_status = 'rejected';
            $order->delivery_cancel_note = $request->cancel_note;
        }
        $order->save();
    }

    public function destroy(Order $order)
    {

        $orderProducts = $order->orderDetails;
        foreach ($orderProducts as $orderProduct) {
            $orderProduct->delete();
        }
        $order->delete();
    }

    public function sendOrderSuccessMail($user, $order)
    {
        $template = EmailTemplate::where('name', 'Order Successfully')->first();
        $payment_status = $order->payment_status == 'success' ? 'Paid' : 'Unpaid';
        $subject = $template->subject;
        $message = $template->message;
        $message = str_replace('{{user_name}}', $user->name, $message);
        $message = str_replace('{{total_amount}}', currency($order->total_amount), $message);
        $message = str_replace('{{payment_method}}', $order->payment_method, $message);
        $message = str_replace('{{payment_status}}', $payment_status, $message);
        $message = str_replace('{{order_status}}', 'Pending', $message);
        $message = str_replace('{{order_date}}', $order->created_at->format('d F, Y'), $message);

        $this->sendOrderSuccessMailFromTrait($subject, $message, $user);
    }


    public function getUserOrders($user)
    {
        return $this->order->where('user_id', $user->id)->with('orderDetails')->orderBy('id', 'desc')->get();
    }
}
