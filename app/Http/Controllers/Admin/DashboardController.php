<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

class DashboardController
{
    public function index()
    {
        return view('admin.dashboard');
    }
}
