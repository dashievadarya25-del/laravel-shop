<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

class DashboardController
{
    public function index()
    {
        return view('admin.dashboard');
    }
}
