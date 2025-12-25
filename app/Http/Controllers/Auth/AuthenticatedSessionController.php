<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CustomRecaptcha;
use Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $setting = Cache::get('setting');

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => $setting->recaptcha_status == 'active' ? ['required', new CustomRecaptcha()] : '',
        ];

        $customMessages = [
            'email.required' => __('Email is required'),
            'password.required' => __('Password is required'),
            'g-recaptcha-response.required' => __('Please complete the recaptcha to submit the form'),
        ];
        $this->validate($request, $rules, $customMessages);

        $credential = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->status == UserStatus::ACTIVE->value) {
                if ($user->is_banned == UserStatus::UNBANNED->value) {
                    if ($user->email_verified_at == null) {
                        $notification = __('Please verify your email');
                        $notification = ['messege' => $notification, 'alert-type' => 'error'];

                        return redirect()->back()->with($notification);
                    }

                    if (Hash::check($request->password, $user->password)) {
                        if (Auth::guard('web')->attempt($credential, $request->remember)) {

                            $notification = __('Login Successfully');
                            $notification = ['messege' => $notification, 'alert-type' => 'success'];

                            return redirect()->intended(route('dashboard'))->with($notification);
                        }
                    } else {
                        $notification = __('Invalid Password');
                        $notification = ['messege' => $notification, 'alert-type' => 'error'];

                        return redirect()->back()->with($notification);
                    }
                } else {
                    $notification = __('Inactive account');
                    $notification = ['messege' => $notification, 'alert-type' => 'error'];

                    return redirect()->back()->with($notification);
                }
            } else {
                $notification = __('Inactive account');
                $notification = ['messege' => $notification, 'alert-type' => 'error'];

                return redirect()->back()->with($notification);
            }
        } else {
            $notification = __('Invalid Email');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $notification = __('Logout Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('login')->with($notification);
    }
}
