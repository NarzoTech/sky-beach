<?php

namespace App\Exceptions;

use Exception;

class DemoModeEnabledException extends Exception
{
    public function render($request): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'alert-type' => 'error',
                'messege' => __('In Demo Mode You Can Not Perform This Action'),
            ], 403);
        }

        return redirect()->back()->with([
            'alert-type' => 'error',
            'messege' => __('In Demo Mode You Can Not Perform This Action'),
        ]);
    }
}
