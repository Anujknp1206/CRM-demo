<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Stock;
use App\Models\Issue;
use App\Models\Rfi;
use App\Models\PurchaseOrder;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\PurchaseOrderItem;
use App\Models\ProductionStage;
use App\Models\ProductionStatus;
class CompanyDashboardController extends Controller
{
    public function index(Company $company)
    {
        $companyId = $company->id;

        $title = $company->company_name . ' Dashboard';


        /*
        =====================================
        LEADS
        =====================================
        */

        $leadStatuses = Lead::where(
            'company_id',
            $companyId
        )
            ->selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalLeads = $leadStatuses->sum();

        /*
        =====================================
        QUOTATIONS
        =====================================
        */

        $quoteStatuses = Quotation::where(
            'company_id',
            $companyId
        )
            ->selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalQuotes = $quoteStatuses->sum();
        /*
        ====================================
        GRN
        ====================================
        */

        $totalGrns =
            StockIn::where(
                'company_id',
                $companyId
            )->count();

        /*
        =====================================
        ORDERS
        =====================================
        */

        $orderStatuses =
            Order::where(
                'company_id',
                $companyId
            )
                ->selectRaw(
                    'status, COUNT(*) total'
                )
                ->groupBy(
                    'status'
                )
                ->pluck(
                    'total',
                    'status'
                );


        $totalOrders =
            $orderStatuses->sum();



        $orderValue =
            Order::where(
                'company_id',
                $companyId
            )
                ->sum(
                    'final_amount'
                );

        $paidAmount =
            Order::where(
                'company_id',
                $companyId
            )
                ->sum(
                    'paid_amount'
                );


        $dueAmount =
            Order::where(
                'company_id',
                $companyId
            )
                ->sum(
                    'due_amount'
                );

        /*
        =====================================
        PAYMENTS
        =====================================
        */

        $paymentStatuses =
            Payment::where(
                'company_id',
                $companyId
            )
                ->selectRaw(
                    'status, COUNT(*) total'
                )
                ->groupBy(
                    'status'
                )
                ->pluck(
                    'total',
                    'status'
                );


        $totalPaymentsCount =
            $paymentStatuses->sum();

        $completedPayments =
            $paymentStatuses['completed'] ?? 0;

        $pendingPayments =
            $paymentStatuses['pending'] ?? 0;

        $partialPayments =
            $paymentStatuses['partial'] ?? 0;

        $failedPayments =
            $paymentStatuses['failed'] ?? 0;

        $totalPaymentAmount =
            Payment::where(
                'company_id',
                $companyId
            )->sum(
                    'amount'
                );


        $receivedAmount =
            Payment::where(
                'company_id',
                $companyId
            )
                ->where(
                    'status',
                    'completed'
                )
                ->sum(
                    'amount'
                );


        /*
        Outstanding should ideally come from Orders due_amount
        (not payment table)
        */

        $outstandingAmount =
            Order::where(
                'company_id',
                $companyId
            )->sum(
                    'due_amount'
                );

        $totalPaymentsAmount =
            Payment::where(
                'company_id',
                $companyId
            )
                ->sum(
                    'amount'
                );


        $postDatedPayments =
            Payment::where(
                'company_id',
                $companyId
            )
                ->where(
                    'is_post_dated',
                    1
                )
                ->count();



        $paymentModes =
            Payment::where(
                'company_id',
                $companyId
            )
                ->selectRaw(
                    'payment_mode, COUNT(*) total'
                )
                ->groupBy(
                    'payment_mode'
                )
                ->pluck(
                    'total',
                    'payment_mode'
                );

        $cashPayments =
            $paymentModes['cash'] ?? 0;

        $bankPayments =
            $paymentModes['bank_transfer'] ?? 0;

        $onlinePayments =
            $paymentModes['online'] ?? 0;

        /*
        ====================================
        FINANCIAL SUMMARY
        ====================================
        */

        $avgOrderValue =
            $totalOrders
            ? round(
                $orderValue / $totalOrders,
                2
            )
            : 0;


        $collectionRate =
            $orderValue
            ? round(
                ($paidAmount / $orderValue) * 100
            )
            : 0;
        /*
        ====================================
        BOM
        ====================================
        */

        $bomStatuses = Bom::where(
            'company_id',
            $companyId
        )
            ->selectRaw(
                'status, COUNT(*) total'
            )
            ->groupBy('status')
            ->pluck('total', 'status');


        $totalBoms =
            $bomStatuses->sum();

        $draftBoms =
            $bomStatuses['draft'] ?? 0;

        $inProgressBoms =
            $bomStatuses['in_progress'] ?? 0;

        $completedBoms =
            $bomStatuses['completed'] ?? 0;




        /*
        ====================================
        ISSUES
        ====================================
        */

        $issueStatuses = Issue::where(
            'company_id',
            $companyId
        )
            ->selectRaw(
                'status, COUNT(*) total'
            )
            ->groupBy('status')
            ->pluck('total', 'status');


        $totalIssues =
            $issueStatuses->sum();

        $draftIssues =
            $issueStatuses['draft'] ?? 0;

        $partialIssues =
            $issueStatuses['partial'] ?? 0;

        $completedIssues =
            $issueStatuses['completed'] ?? 0;




        /*
        ====================================
        RFI
        ====================================
        */

        $rfiStatuses = Rfi::where(
            'company_id',
            $companyId
        )
            ->selectRaw(
                'status, COUNT(*) total'
            )
            ->groupBy('status')
            ->pluck('total', 'status');


        $totalRfis =
            $rfiStatuses->sum();

        $pendingRfis =
            $rfiStatuses['pending'] ?? 0;

        $approvedRfis =
            $rfiStatuses['approved'] ?? 0;

        $rejectedRfis =
            $rfiStatuses['rejected'] ?? 0;




        /*
        ====================================
        PURCHASE ORDERS
        ====================================
        */

        $poStatuses =
            PurchaseOrder::where(
                'company_id',
                $companyId
            )
                ->selectRaw(
                    'status, COUNT(*) total'
                )
                ->groupBy('status')
                ->pluck(
                    'total',
                    'status'
                );


        $totalPo =
            $poStatuses->sum();

        $pendingPo =
            $poStatuses['pending'] ?? 0;

        $receivedPo =
            $poStatuses['received'] ?? 0;

        $partialPo =
            $poStatuses['partial'] ?? 0;

        $approvedPo =
            $poStatuses['approved'] ?? 0;




        /*
        ====================================
        BOM WORKLOAD
        ====================================
        */

        $totalBomItems =
            BomItem::whereHas(
                'bom',
                function ($q) use ($companyId) {
                    $q->where(
                        'company_id',
                        $companyId
                    );
                }
            )->count();


        $assignedBomItems =
            BomItem::whereHas(
                'bom',
                function ($q) use ($companyId) {
                    $q->where(
                        'company_id',
                        $companyId
                    );
                }
            )
                ->where(
                    'status',
                    'assigned'
                )
                ->count();



        $inProgressBomItems =
            BomItem::whereHas(
                'bom',
                function ($q) use ($companyId) {
                    $q->where(
                        'company_id',
                        $companyId
                    );
                }
            )
                ->where(
                    'status',
                    'in_progress'
                )
                ->count();



        $completedBomItems =
            BomItem::whereHas(
                'bom',
                function ($q) use ($companyId) {
                    $q->where(
                        'company_id',
                        $companyId
                    );
                }
            )
                ->where(
                    'status',
                    'completed'
                )
                ->count();




        /*
        ====================================
        BOM DELIVERY RISK
        ====================================
        */

        $onTimeBoms =
            Bom::where(
                'company_id',
                $companyId
            )
                ->whereDate(
                    'delivery_date',
                    '>=',
                    today()
                )
                ->count();


        $delayedBoms =
            Bom::where(
                'company_id',
                $companyId
            )
                ->whereDate(
                    'delivery_date',
                    '<',
                    today()
                )
                ->where(
                    'status',
                    'in_progress'
                )
                ->count();


        $overdueBoms =
            Bom::where(
                'company_id',
                $companyId
            )
                ->whereDate(
                    'delivery_date',
                    '<',
                    today()
                )
                ->where(
                    'status',
                    'draft'
                )
                ->count();/*
====================================
STOCK DASHBOARD
====================================
*/

        $totalStockItems =
            Stock::where(
                'company_id',
                $companyId
            )->count();


        $lowStockItems =
            Stock::where(
                'company_id',
                $companyId
            )
                ->whereColumn(
                    'quantity',
                    '<=',
                    'min_quantity'
                )
                ->where(
                    'quantity',
                    '>',
                    0
                )
                ->count();


        $outOfStockItems =
            Stock::where(
                'company_id',
                $companyId
            )
                ->where(
                    'quantity',
                    '<=',
                    0
                )
                ->count();


        $totalStockQty =
            Stock::where(
                'company_id',
                $companyId
            )->sum(
                    'quantity'
                );/*
====================================
GRN / STOCK IN
====================================
*/

        $totalGrns =
            StockIn::where(
                'company_id',
                $companyId
            )->count();


        $totalReceivedQty =
            StockInItem::whereHas(
                'stockIn',
                function ($q) use ($companyId) {
                    $q->where(
                        'company_id',
                        $companyId
                    );
                }
            )->sum(
                    'quantity'
                );


        $linkedPoGrns =
            StockIn::where(
                'company_id',
                $companyId
            )
                ->whereNotNull(
                    'purchase_order_id'
                )
                ->count();


        $totalGrnSuppliers =
            StockIn::where(
                'company_id',
                $companyId
            )
                ->distinct(
                    'supplier_id'
                )
                ->count(
                    'supplier_id'
                );/*
====================================
CRITICAL ALERTS
====================================
*/

        $criticalLowStock =
            Stock::where(
                'company_id',
                $companyId
            )
                ->whereColumn(
                    'quantity',
                    '<=',
                    'min_quantity'
                )
                ->count();


        $outOfStockAlerts =
            Stock::where(
                'company_id',
                $companyId
            )
                ->where(
                    'quantity',
                    '<=',
                    0
                )
                ->count();


        $overdueBomAlerts =
            Bom::where(
                'company_id',
                $companyId
            )
                ->whereDate(
                    'delivery_date',
                    '<',
                    today()
                )
                ->where(
                    'status',
                    '!=',
                    'completed'
                )
                ->count();




        /*
        ====================================
        PROCUREMENT ALERTS
        ====================================
        */

        $pendingApprovalRfis =
            Rfi::where(
                'company_id',
                $companyId
            )
                ->where(
                    'status',
                    'pending'
                )
                ->count();



        $delayedPos =
            PurchaseOrder::where(
                'company_id',
                $companyId
            )
                ->where(
                    'status',
                    'partial'
                )
                ->count();



        $partialReceiptsPending =
            PurchaseOrderItem::whereHas(
                'po',
                function ($q) use ($companyId) {
                    $q->where(
                        'company_id',
                        $companyId
                    );
                }
            )
                ->whereColumn(
                    'received_quantity',
                    '<',
                    'quantity'
                )
                ->count();

        /*
|--------------------------------------------------------------------------
| LATEST ORDERS PRODUCTION TREE
|--------------------------------------------------------------------------
*/

        $latestOrders = Order::with([

            'items',

            'items.bomItems.part',

            'items.bomItems.part.stageProgresses.stage',

            'items.bomItems.part.stageProgresses.status',

        ])
            ->where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();
        return view(
            'company.dashboard',
            compact(

                'company',
                'title',

                // Leads
                'totalLeads',

                // Quotes
                'totalQuotes',
                // Orders
                'totalOrders',
                'orderValue',
                'paidAmount',
                'dueAmount',
                'totalGrns',
                'receivedAmount',
                'outstandingAmount',
                'partialPayments',
                'failedPayments',
                'totalBoms',

                'totalIssues',

                'totalRfis',

                'totalPo',

                'totalBomItems',

                'onTimeBoms',
                'delayedBoms',
                'overdueBoms',
                'totalStockItems',
                'lowStockItems',
                'outOfStockItems',
                'criticalLowStock',
                'outOfStockAlerts',
                'overdueBomAlerts',

                'pendingApprovalRfis',
                'delayedPos',
                'partialReceiptsPending',

                'latestOrders',
            )
        );
    }
}