<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function create()
    {
        checkAdminHasPermissionAndThrowException('setting.view');
        return view('admin.pages.notice.create');
    }

    public function store(Request $request) {}
}
