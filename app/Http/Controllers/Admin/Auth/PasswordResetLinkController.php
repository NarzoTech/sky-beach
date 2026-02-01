<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\MailSenderService;
use App\Traits\GetGlobalInformationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    use GetGlobalInformationTrait;

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function custom_forget_password(Request $request)
    {

        $setting = Cache::get('setting');

        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => __('Email is required'),
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin) {
            $admin->forget_password_token = Str::random(100);
            $admin->save();

            (new MailSenderService)->sendUserForgetPasswordFromTrait($admin, 'admin.auth');

            $notification = __('A password reset link has been send to your mail');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->back()->with($notification);
        } else {
            $notification = __('Email does not exist');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }
    }

    /**
     * Get security questions for an email (AJAX)
     */
    public function get_security_questions(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => __('Email does not exist')
            ], 404);
        }

        if (!$admin->security_question_1 || !$admin->security_question_2) {
            return response()->json([
                'success' => false,
                'message' => __('Security questions not set up')
            ]);
        }

        return response()->json([
            'success' => true,
            'questions' => [
                'question_1' => $admin->security_question_1,
                'question_2' => $admin->security_question_2,
            ]
        ]);
    }

    /**
     * Verify security questions and show reset password form
     */
    public function verify_security_questions(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'security_answer_1' => ['required', 'string'],
            'security_answer_2' => ['required', 'string'],
        ], [
            'email.required' => __('Email is required'),
            'security_answer_1.required' => __('Answer 1 is required'),
            'security_answer_2.required' => __('Answer 2 is required'),
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            $notification = __('Email does not exist');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        if (!$admin->security_question_1 || !$admin->security_question_2) {
            $notification = __('Security questions not set up for this account');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        // Verify answers (case-insensitive)
        $answer1Match = strtolower(trim($request->security_answer_1)) === strtolower(trim($admin->security_answer_1));
        $answer2Match = strtolower(trim($request->security_answer_2)) === strtolower(trim($admin->security_answer_2));

        if (!$answer1Match || !$answer2Match) {
            $notification = __('Security answers do not match');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        // Generate a token and redirect to reset password page
        $token = Str::random(100);
        $admin->forget_password_token = $token;
        $admin->save();

        return redirect()->route('admin.password.reset', ['token' => $token])
            ->with(['messege' => __('Security questions verified. Please set your new password.'), 'alert-type' => 'success']);
    }
}
