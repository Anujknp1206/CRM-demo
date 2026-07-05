<?php

use App\Models\Stock;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Models\Order;
use Carbon\Carbon;
function notifyAdmins($title, $message, $url = '#', $type = 'info')
{
    $admins = User::role(['Super Admin', 'Admin'], 'web')->get();

    foreach ($admins as $admin) {
        $admin->notify(new SystemNotification($title, $message, $url, $type));
    }
}
function checkLowStock(Stock $stock)
{
    if (!$stock->relationLoaded('item')) {
        $stock->load('item');
    }

    if ($stock->quantity <= $stock->min_quantity) {

        notifyAdmins(
            'Low Stock Alert',
            "{$stock->item->name} is running low (Qty: {$stock->quantity})",
            route('stocks.index', $stock->company_id),
            'danger'
        );
    }
}

function updateOrderProgress($orderId)
{
    $order = Order::with('plannings.planningItems')->find($orderId);

    if (!$order)
        return;

    $items = $order->plannings->flatMap->planningItems;

    if ($items->count() == 0) {
        $order->update([
            'status' => 'confirmed',
            'progress_percent' => 0
        ]);
        return;
    }

    $total = $items->count();
    $completed = $items->where('status', 'done')->count();
    $working = $items->where('status', 'working')->count();
    $hold = $items->where('status', 'hold')->count();

    $progress = ($completed / $total) * 100;

    $status = 'planning';

    if ($completed == $total) {
        $status = 'ready';
    } elseif ($hold > 0) {
        $status = 'on_hold';
    } elseif ($working > 0) {
        $status = 'in_production';
    }

    // 🔴 Delay Logic
    $isDelayed = false;

    $deliveryDate = $order->delivery_date
        ? Carbon::parse($order->delivery_date)->endOfDay()
        : null;

    if ($deliveryDate && now()->gt($deliveryDate) && $progress < 100) {
        $status = 'delayed';
        $isDelayed = true;
    }

    // ✅ Prevent completed orders from showing delayed
    if ($progress == 100) {
        $isDelayed = false;
    }
    $order->update([
        'status' => $status,
        'progress_percent' => round($progress),
        'is_delayed' => $isDelayed
    ]);
}
function formatMixedText($text)
{
    if (!$text)
        return '';

    return preg_replace(
        '/([\x{0900}-\x{097F}]+)/u',
        '<span class="hindi">$1</span>',
        $text
    );
}