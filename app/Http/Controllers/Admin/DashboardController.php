<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Services\SalesReportService;
use Illuminate\Contracts\View\View;

class DashboardController
{
    public function index(SalesReportService $reportService): View
    {
        return view('admin.dashboard', [
            'report' => $reportService->getLastWeekReport()
        ]);
    }
}
