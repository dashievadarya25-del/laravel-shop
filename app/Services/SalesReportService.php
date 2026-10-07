<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;

class SalesReportService
{
    private const SUCCESSFUL_STATUSES = [
     Order::STATUS_PAID,
     Order::STATUS_SHIPPED,
     Order::STATUS_COMPLETED,
    ];

    /**
     * Формирование финансового отчёта за последние 7 календарных дней с непрерывной сеткой дат
     */
    public function getLastWeekReport(): array
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $baseQuery = Order::query()->whereBetween('created_at', [$startDate, $endDate]);

        $ordersCount = (int) (clone $baseQuery)->count();

        $salesCount = (int) (clone $baseQuery)
            ->whereIn('status', self::SUCCESSFUL_STATUSES)
            ->count();

        $canceledCount = (int) (clone $baseQuery)
            ->where('status', Order::STATUS_CANCELED)
            ->count();

        $revenue = (float) (clone $baseQuery)
            ->whereIn('status', self::SUCCESSFUL_STATUSES)
            ->sum('total');

        $dailySalesGrid = [];
        for ($i = 6; $i >= 0; $i--) {
            $formattedDate = Carbon::now()->subDays($i)->format('d.m');
            $dailySalesGrid[$formattedDate] = [
                'date'       => $formattedDate,
                'salesCount' => 0,
                'revenue'    => 0.00
            ];
        }

        $rawDailyOrders = (clone $baseQuery)
            ->select('status', 'total', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        $groupedByDay = $rawDailyOrders->groupBy(function (Order $order) {
            return $order->created_at->format('d.m');
        });

        foreach ($groupedByDay as $dateKey => $ordersCollection) {
            if (isset($dailySalesGrid[$dateKey])) {
                $daySalesCount = $ordersCollection
                    ->whereIn('status', self::SUCCESSFUL_STATUSES)
                    ->count();

                $dayRevenue = $ordersCollection
                    ->whereIn('status', self::SUCCESSFUL_STATUSES)
                    ->sum('total');

                $dailySalesGrid[$dateKey]['salesCount'] = $daySalesCount;
                $dailySalesGrid[$dateKey]['revenue'] = (float) $dayRevenue;
            }
        }

        return [
            'ordersCount'   => $ordersCount,
            'salesCount'    => $salesCount,
            'revenue'       => $revenue,
            'canceledCount' => $canceledCount,
            'dailySales'    => array_values($dailySalesGrid),
        ];
    }
}
