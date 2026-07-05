<?php
use App\Http\Controllers\Company\CRM\BomController;
use App\Http\Controllers\Company\CRM\PartController;
use App\Http\Controllers\Company\CRM\ProductionStatusController;
use App\Http\Controllers\Company\CRM\ProductionStageController;
use App\Http\Controllers\Company\CRM\CompanyReportController;
use App\Http\Controllers\Company\CRM\Customer360Controller;
use App\Http\Controllers\Company\CRM\PaymentController;
use App\Http\Controllers\Company\CRM\PaymentPrintController;
use App\Http\Controllers\Company\CRM\JobcardController;
use App\Http\Controllers\Company\CRM\ProductionTrackerController;
use App\Http\Controllers\Company\CRM\RecipeController;
use App\Http\Controllers\Company\CRM\RfiController;
use App\Http\Controllers\Company\Store\AttendanceController;
use App\Http\Controllers\Company\Store\BrandController;
use App\Http\Controllers\Company\Store\DepartmentController;
use App\Http\Controllers\Company\Store\EmployeeController;
use App\Http\Controllers\Company\Store\PriorityController;
use App\Http\Controllers\Company\Store\ProjectController;
use App\Http\Controllers\Company\Store\PurchaseOrderController;
use App\Http\Controllers\Company\Store\ShiftController;
use App\Http\Controllers\Company\Store\SpecificationController;
use App\Http\Controllers\Company\Store\StockController;
use App\Http\Controllers\Company\Store\StockInController;
use App\Http\Controllers\TranslateController;
use App\Models\Company;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
//Home Controllers 
use App\Http\Controllers\Profile\ProfileController;
//Admin Controllers 
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyUserController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\ComponentController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\Company\CRM\ActionController;
use App\Http\Controllers\Company\CRM\LeadController;
use App\Http\Controllers\Company\CRM\FollowupController;
use App\Http\Controllers\Company\CRM\QuotationController;
use App\Http\Controllers\Company\CRM\OrderController;
use App\Http\Controllers\Company\Store\CategoryController;
use App\Http\Controllers\Company\Store\SubCategoryController;
use App\Http\Controllers\Company\Store\UnitController;
use App\Http\Controllers\Company\Store\ConditionController;
use App\Http\Controllers\Company\Store\ItemController;
use App\Http\Controllers\Company\Store\SupplierController;
use App\Http\Controllers\Company\Store\IssueController;
use App\Http\Controllers\Company\Store\LocationController as ItemLocationController;
use Spatie\Browsershot\Browsershot;


/*
|--------------------------------------------------------------------------
| System Utilities
|--------------------------------------------------------------------------
*/

Route::get('/test-pdf', function () {

    $html = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body{
                font-family: Arial, sans-serif;
            }
        </style>
    </head>
    <body>

        <h1>Quotation Test</h1>

        <p>कृषि यंत्र</p>
        <p>श्री गणेश</p>
        <p>प्रस्ताव पत्र</p>

    </body>
    </html>';

    $pdf = Browsershot::html($html)
        ->format('A4')
        ->pdf();

    return response($pdf)
        ->header('Content-Type', 'application/pdf');
});
Route::get('/optimize', function () {
    Artisan::call('optimize');
    return 'Optimization Completed!';
});
Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    return 'Cache Cleared!';
});
// web.php
Route::match(['get', 'post'], '/translate', [TranslateController::class, 'translate'])
    ->name('translate.text');

// Authentication Routes (No Login Required)
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/refreshCaptcha', [AuthController::class, 'refreshCaptcha'])->name('refreshCaptcha');
Route::post('/check-login', [AuthController::class, 'login'])->name('Checklogin');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Login Required)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    // Permissions & Roles
    Route::resource('permissions', PermissionController::class)->middleware('permission:manage permissions');
    Route::resource('roles', RoleController::class)->middleware('permission:manage roles');

    //Company
    Route::resource('companies', CompanyController::class)
        ->except(['show'])->middleware('permission:manage company');
    Route::post('/company/status', [CompanyController::class, 'changeStatus'])->name('company.changeStatus');
    // Assign Users to Company
    Route::get('companies/{company}/assign-users', [CompanyUserController::class, 'index'])->name('company.assignUsers');
    Route::post('companies/{company}/assign-users', [CompanyUserController::class, 'store'])->name('company.assignUsers.store');

    // Remove assigned user
    Route::delete('companies/{company}/remove-user/{user}', [CompanyUserController::class, 'removeUser'])->name('company.removeUser');

    // Settings
    // Route::delete(
    //     'setting/{setting}/photo/{type}',
    //     [SettingController::class, 'deletePhoto']
    // )
    //     ->name('setting.deletePhoto');
    Route::resource('setting', SettingController::class)
        ->except(['destroy']);
    // Profile
    Route::resource('profile', ProfileController::class);
    Route::post('/verify-password', [ProfileController::class, 'verifyPassword'])->name('verify.password');
    Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update.password');
    // User Management
    Route::resource('users', UserController::class)->middleware('permission:manage users');
    Route::get('users/{id}/permissions', [UserController::class, 'managePermissions'])->name('users.permissions');
    Route::post('users/{id}/permissions', [UserController::class, 'updatePermissions'])->name('users.update.permissions');
    Route::get('/deleteUserPhoto/{id}', [UserController::class, 'deletePhoto'])->name('user.deletePhoto');
    //Machine Management
    Route::resource('machines', MachineController::class)->middleware('permission:manage machines');
    //Component Management
    Route::resource('components', ComponentController::class)->middleware('permission:manage components');

    //Location 
    Route::get('states', [LocationController::class, 'getStates'])->name('getStates');
    Route::get('cities', [LocationController::class, 'getCities'])->name('getCities');
    // Country
    Route::post('/countries/store', [LocationController::class, 'storeCountry'])->name('countries.store');
    // State
    Route::post('/states/store', [LocationController::class, 'storeState'])->name('states.store');
    // City
    Route::post('/cities/store', [LocationController::class, 'storeCity'])->name('cities.store');
    Route::get('/ajax/generate-quotation-number', [QuotationController::class, 'generateNumber'])
        ->name('ajax.generate.quote.number');
    Route::get('/ajax/generate-order-number', [OrderController::class, 'generateNumber'])
        ->name('ajax.generate.order.number');
    Route::get('/ajax/generate-bom-number', [BomController::class, 'generateBomNumber'])
        ->name('ajax.generate.bom.number');

    // routes/web.php
    Route::get('/ajax/get-country-code', function (\Illuminate\Http\Request $request) {
        $name = $request->input('name');
        if (!$name)
            return response()->json(['phonecode' => null]);

        $country = \App\Models\Country::where('name', 'LIKE', $name)->first();

        return response()->json([
            'phonecode' => $country ? $country->phonecode : null
        ]);
    })->name('ajax.get.country.code');

    Route::prefix('company/{company}')
        ->group(function () {
            Route::get(
                '/customer-360/{customer}/{type}',
                [Customer360Controller::class, 'load']
            )->name('customer.360');

            //Company Dashboard
            Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('company.dashboard');
            /*
               Recipes
               */
            Route::get(
                '/recipes/by-order-item',
                [RecipeController::class, 'recipesForOrderItem']
            )->name(
                    'recipes.by.order.item'
                );
            Route::prefix('recipes')
                ->name('recipes.')
                ->controller(
                    RecipeController::class
                )
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/ajax', 'ajaxList')->name('ajax');
                    Route::get('/search', 'search')->name('search');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/store', 'store')->name('store');
                    Route::get('/{recipe}/edit', 'edit')->name('edit');
                    Route::put('/{recipe}', 'update')->name('update');
                    Route::delete('/{recipe}', 'destroy')->name('destroy');
                });
            //Actions
            Route::resource('actions', ActionController::class);
            //Leads
            Route::get('/leads/data', [LeadController::class, 'data'])->name('leads.data');
            Route::resource('leads', LeadController::class);
            Route::get('/ajax/lead-details', [LeadController::class, 'ajaxLeadDetails'])
                ->name('ajax.get.lead.details');
            Route::get(
                'followups/lead/{lead}',
                [FollowupController::class, 'leadInfo']
            )->name('followups.lead.info');

            Route::get(
                '/check-customer-by-mobile',
                [LeadController::class, 'checkCustomerByMobile']
            )->name('leads.checkCustomerByMobile');
            Route::get(
                '/ajax/lead-details/view',
                [LeadController::class, 'ajaxSingleLeadDetails']
            )->name('ajax.get.single.lead.details');

            Route::post(
                '/leads/ajax-store',
                [LeadController::class, 'storeAjax']
            )->name('leads.ajax.store');

            //Followups
            Route::get('followups/{lead_id}', [FollowupController::class, 'index'])->name('followups.index');
            Route::post('followups', [FollowupController::class, 'store'])->name('followups.store');
            Route::put('followups/{followup}', [FollowupController::class, 'update'])->name('followups.update');
            Route::delete('followups/{followup}', [FollowupController::class, 'destroy'])->name('followups.destroy');

            //Quotation
            Route::get('/quotations/data', [QuotationController::class, 'data'])->name('quotations.data');
            Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
            Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
            Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
            Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
            Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
            Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
            Route::get('/quotations/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('quotations.duplicate');
            Route::post(
                'quotations/save-translation',
                [QuotationController::class, 'saveTranslation']
            )->name('quotations.saveTranslation');
            Route::get(
                'quotations/get-translation',
                [QuotationController::class, 'getTranslation']
            )->name('quotations.getTranslation');
            Route::get('/quotations/ajax/search', [QuotationController::class, 'ajaxSearch'])
                ->name('ajax.quotations.search');
            Route::get('/ajax/quotation-details', [QuotationController::class, 'ajaxQuotationDetails'])
                ->name('ajax.get.quotation.details');
            Route::get(
                '/quotations/{quotation}/print',
                [QuotationController::class, 'print']
            )->name('quotations.print');
            Route::get(
                '/quotations/{quotation}/pdf',
                [QuotationController::class, 'pdf']
            )->name('quotations.pdf');
            // Orders
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/data', [OrderController::class, 'data'])->name('orders.data');
            Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
            Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
            Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
            Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
            Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
            Route::get('/orders/ajax/search', [OrderController::class, 'ajaxSearch'])->name('ajax.orders.search');
            Route::get('/ajax/order-details', [OrderController::class, 'ajaxOrderDetails'])->name('ajax.get.order.details');
            Route::get('/ajax/stock-details', [IssueController::class, 'stockDetails']);
            Route::get('/ajax/employee-boms', [EmployeeController::class, 'employeeBoms'])->name('ajax.employee.boms');
            Route::get('/ajax/order-items-for-bom', [OrderController::class, 'orderItemsForBom'])->name('ajax.order.items.bom');
            Route::post('/orders/save-translation', [OrderController::class, 'saveTranslation'])->name('orders.saveTranslation');
            Route::get('orders/get-translation', [OrderController::class, 'getTranslation'])->name('orders.getTranslation');
            Route::get(
                'ajax/quotation-for-order',
                [OrderController::class, 'getQuotationForOrder']
            )->name('ajax.quotation.for.order');
            Route::get(
                'orders/{order}/proforma-preview',
                [OrderController::class, 'proformaPreview']
            )->name('orders.proforma.preview');
            // Orders Print
            Route::get(
                'orders/{order}/print',
                [OrderController::class, 'print']
            )->name('orders.print');

            Route::get(
                '/orders/{order}/pdf',
                [OrderController::class, 'pdf']
            )->name('orders.pdf');
            Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
            Route::resource('subcategories', SubCategoryController::class)->except(['show', 'create', 'edit']);
            Route::get(
                '/subcategories/by-category/{category}',
                [SubcategoryController::class, 'byCategory']
            )->name('subcategories.byCategory');


            Route::resource('units', UnitController::class)->except(['show', 'create', 'edit']);
            Route::resource('conditions', ConditionController::class)->except(['show', 'create', 'edit']);
            Route::get(
                'items/data',
                [ItemController::class, 'data']
            )->name('items.data');
            Route::resource('items', ItemController::class)->except(['show']);
            Route::get(
                '/items/search',
                [ItemController::class, 'search']
            )->name('items.search');
            Route::get(
                'items/{item}/ajax',
                [ItemController::class, 'ajaxShow']
            )->name('items.ajax.show');

            Route::resource('locations', ItemLocationController::class)->except(['create', 'edit']);
            Route::get('locations-search', [ItemLocationController::class, 'search'])->name('locations.search');
            Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit']);
            Route::get('/departments/data', [DepartmentController::class, 'data'])
                ->name('departments.data');
            Route::resource('employees', EmployeeController::class)->except(['create', 'edit']);
            Route::post(
                '/employees/toggle-status/{employee}',
                [EmployeeController::class, 'toggleStatus']
            )->name('employees.toggle.status');
            Route::post('employees/reveal-password', [EmployeeController::class, 'revealPassword'])
                ->name('employees.revealPassword');
            Route::get('/ajax/departments/search', function (Company $company) {

                $search = request('search');

                return \App\Models\Department::where('company_id', $company->id)
                    ->when($search, function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->select('id', 'name')
                    ->limit(20)
                    ->get();

            })->name('ajax.departments.search');
            Route::get('/ajax/employees/search', function (Company $company) {

                $search = request('search');

                return \App\Models\Employee::with('department')
                    ->where('company_id', $company->id)
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($qq) use ($search) {
                            $qq->where('first_name', 'LIKE', "%$search%")
                                ->orWhere('last_name', 'LIKE', "%$search%");
                        });
                    })
                    ->get()
                    ->map(function ($e) {
                        return [
                            'id' => $e->id,
                            'text' => $e->first_name . ' ' . $e->last_name .
                                ' (' . ($e->department->name ?? '-') . ')'
                        ];
                    });

            })->name('ajax.employees.search');
            Route::get(
                '/ajax/employees/assigned-search',
                [EmployeeController::class, 'assignedSearch']
            )->name('ajax.employees.assigned.search');
            Route::get(
                '/ajax/employees/working-features',
                [EmployeeController::class, 'workingFeatures']
            )->name('ajax.employees.working.features');
            //Attandance
            Route::get('/check-attendance', [AttendanceController::class, 'checkTodayAttendance'])
                ->name('attendance.check');
            Route::get('/attendance', [AttendanceController::class, 'index'])
                ->name('attendance.index');

            Route::get('/attendance/data', [AttendanceController::class, 'data'])
                ->name('attendance.data');

            Route::post('/attendance/store', [AttendanceController::class, 'store'])
                ->name('attendance.store');
            Route::get(
                '/suppliers/search',
                [SupplierController::class, 'search']
            )->name('suppliers.search');
            Route::resource('suppliers', SupplierController::class)->except(['create', 'edit']);

            Route::get(
                '/projects/search',
                [ProjectController::class, 'search']
            )->name('projects.search');
            Route::resource('projects', ProjectController::class)->except(['create', 'edit']);
            Route::resource('brands', BrandController::class);
            Route::get('/stocks/initial', [StockController::class, 'initialcreate'])->name('initialstock');
            Route::post('/stocks/initial', [StockController::class, 'initialstore'])->name('addinitialstock');
            Route::get('/stocks', [StockController::class, 'index'])
                ->name('stocks.index');
            Route::get('stocks/data', [StockController::class, 'data'])
                ->name('stocks.data');

            Route::get('stock-ins', [StockInController::class, 'index'])
                ->name('stock-ins.index');

            Route::get('stock-ins/data', [StockInController::class, 'data'])
                ->name('stock-ins.data');
            Route::get(
                'stock-ins/search',
                [StockInController::class, 'ajaxSearch']
            )->name('stock-ins.search');
            Route::get(
                'stock-ins/{stockIn}/view',
                [StockInController::class, 'view']
            )->name('stock-ins.view');

            Route::get('stock-ins/create', [StockInController::class, 'create'])
                ->name('stock-ins.create');
            Route::get(
                '/stock-ins/create-po',
                [StockInController::class, 'createWithPO']
            )->name('stock-ins.create.po');
            Route::get(
                '/stock-ins/{stockIn}/edit-po',
                [StockInController::class, 'editWithPo']
            )->name('stock-ins.edit.po');
            Route::post('stock-ins', [StockInController::class, 'store'])
                ->name('stock-ins.store');

            Route::get('stock-ins/{stockIn}/edit', [StockInController::class, 'edit'])
                ->name('stock-ins.edit');

            Route::put('stock-ins/{stockIn}', [StockInController::class, 'update'])
                ->name('stock-ins.update');

            Route::delete('stock-ins/{stockIn}', [StockInController::class, 'destroy'])
                ->name('stock-ins.destroy');
            Route::get(
                'stock-ins/{stockIn}/print',
                [StockInController::class, 'print']
            )->name('stock-ins.print');
            //Issue
            Route::get('issues/check-stock', [IssueController::class, 'checkStock'])
                ->name('issues.check-stock');

            Route::post('issues/auto-fulfill', [IssueController::class, 'autoFulfill'])
                ->name('issues.auto-fulfill');
            Route::get('issues', [IssueController::class, 'index'])
                ->name('issues.index');

            Route::get('/issues/create', [IssueController::class, 'create'])
                ->name('issues.create');
            Route::post('issues', [IssueController::class, 'store'])
                ->name('issues.store');

            Route::get('/bom/{bom}/issues', [IssueController::class, 'getBomIssues'])
                ->name('bom.issues');
            Route::get('issues/{issue}', [IssueController::class, 'show'])
                ->name('issues.show');
            Route::get('/bom-items', [IssueController::class, 'getBomItems'])
                ->name('ajax.bom.items');
            Route::get('/issues/generate-number', [IssueController::class, 'generateIssueNo'])
                ->name('issues.generate.number');
            Route::get(
                'issues/{issue}/edit',
                [IssueController::class, 'edit']
            )->name('issues.edit');
            Route::put('issues/{issue}', [IssueController::class, 'update'])
                ->name('issues.update');
            Route::post('issues/return', [IssueController::class, 'returnItem'])
                ->name('issues.return');
            Route::get('issues/returns/all', [IssueController::class, 'allReturnsSummary'])
                ->name('issues.returns.all');


            Route::get('ajax/locations/by-item/', [IssueController::class, 'getLocationsByItem'])
                ->name('ajax.locations.by.item');
            Route::delete('issues/{issue}', [IssueController::class, 'destroy'])
                ->name('issues.destroy');
            Route::get('issues/{issue}/print', [IssueController::class, 'printIssue'])
                ->name('issues.print');
            Route::get(
                'issues/return/{id}/print',
                [IssueController::class, 'printReturnSlip']
            )
                ->name('issues.return.print');

            Route::get('ajax/employees-by-department', function (Company $company) {

                $departmentId = request('department_id');
                $today = \Carbon\Carbon::today();

                return \App\Models\Employee::where('company_id', $company->id)
                    ->where('department_id', $departmentId)
                    ->where('status', 1)

                    // 🔥 THIS IS THE FIX
                    ->whereHas('attendances', function ($q) use ($today) {
                        $q->whereDate('date', $today)
                            ->where('is_present', 1); // ✅ ONLY PRESENT
                    })

                    ->get()
                    ->map(function ($e) {
                        return [
                            'id' => $e->id,
                            'name' => $e->first_name . ' ' . $e->last_name,
                            'is_present' => 1 // always present now
                        ];
                    });

            })->name('ajax.employees.by.department');
            Route::post('issues/auto-fulfill', [IssueController::class, 'autoFulfill'])
                ->name('issues.auto-fulfill');


            //Payments 
    
            Route::get('payments', [PaymentController::class, 'index'])
                ->name('payments.index');
            Route::get('payments/data', [PaymentController::class, 'data'])
                ->name('company.payments.data');
            Route::get('payments/ajax/search', [PaymentController::class, 'ajaxSearch'])
                ->name('company.payments.ajaxSearch');
            Route::get(
                'ajax/payment/details',
                [PaymentController::class, 'details']
            )->name('ajax.get.payment.details');


            Route::post('payments', [PaymentController::class, 'store'])
                ->name('payments.store');

            Route::get('payments/{payment}', [PaymentController::class, 'show'])
                ->name('payments.show');
            Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])
                ->name('payments.edit');

            Route::put('payments/{payment}', [PaymentController::class, 'update'])
                ->name('payments.update');

            Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])
                ->name('payments.destroy');
            Route::get('ajax/generate-payment-number', [PaymentController::class, 'generateNumber'])->name('ajax.generate.payment.number');

            // Print
            Route::get(
                'payments/print/single/{order}/{payment}',
                [PaymentPrintController::class, 'single']
            )->name('payments.print.single');
            Route::get(
                '/payments/pdf/{order}/{payment}',
                [PaymentController::class, 'pdfSingle']
            )
                ->name('payments.pdf.single');
            Route::get(
                '/payments/pdf/full/{order}/{payment}',
                [PaymentController::class, 'pdfFull']
            )->name('payments.pdf.full');
            Route::get(
                'payments/print/full/{order}',
                [PaymentPrintController::class, 'full']
            )->name('payments.print.full');
            Route::get(
                'orders/{order}/proforma',
                [OrderController::class, 'proformaInvoice']
            )->name('orders.proforma');



            // Job CArd Routes
            Route::get('/jobcard', [JobcardController::class, 'index'])->name('jobcard.index');
            Route::get('/jobcard/data', [JobcardController::class, 'data'])->name('jobcard.data');
            Route::get('/jobcard/ajax/search', [JobcardController::class, 'ajaxSearch'])->name('ajax.jobcard.search');
            Route::get('/jobcard/details', [JobcardController::class, 'ajaxDetails'])
                ->name('ajax.get.jobcard.details');
            Route::get('/jobcard/create', [JobcardController::class, 'create'])->name('jobcard.create');
            Route::post('/jobcard', [JobcardController::class, 'store'])->name('jobcard.store');
            Route::get('/jobcard/{id}/edit', [JobcardController::class, 'edit'])->name('jobcard.edit');
            Route::put('/jobcard/{id}', [JobcardController::class, 'update'])->name('jobcard.update');
            Route::get('/ajax/generate-po', [JobcardController::class, 'generatePoAjax'])
                ->name('ajax.generate.po');
            Route::get('/jobcard/{id}/preview', [JobcardController::class, 'preview'])
                ->name('jobcard.preview');

            Route::get('/jobcard/{id}/pdf', [JobcardController::class, 'pdf'])
                ->name('jobcard.pdf');
            Route::delete('/jobcard/{id}', [JobcardController::class, 'destroy'])
                ->name('jobcard.destroy');
            // ✅ BOM ROUTES (CLEAN)
            Route::get('/boms', [BomController::class, 'index'])->name('boms.index');
            Route::get('/ajax/order-delivery-date', [BomController::class, 'getOrderDeliveryDate'])
                ->name('ajax.order.delivery.date');
            Route::get('/bom/manage/{orderId}', [BomController::class, 'createOrEditBom'])->name('bom.manage');
            Route::get('/boms/ajax/search', [BomController::class, 'ajaxSearch'])
                ->name('ajax.boms.search');
            Route::get('/boms/data', [BomController::class, 'data'])->name('boms.data');
            Route::get('/boms/{bom}/details', [BomController::class, 'details'])->name('boms.details');
            Route::get('/boms/create', [BomController::class, 'create'])->name('boms.create');

            Route::post('/boms', [BomController::class, 'store'])->name('boms.store');

            Route::get('/boms/{bom}/edit', [BomController::class, 'edit'])->name('boms.edit');

            // 🔥 USE ONLY ONE UPDATE ROUTE
            Route::post('/boms/{bom}/update', [BomController::class, 'update'])->name('boms.update');

            Route::delete('/boms/{bom}', [BomController::class, 'destroy'])->name('boms.destroy');
            Route::get('/boms/{bom}/print', [BomController::class, 'print'])
                ->name('boms.print');

            Route::get('/boms/{bom}/pdf', [BomController::class, 'pdf'])
                ->name('bom.pdf');
            // 🔥 BOM TRANSLATION ROUTES
            Route::post(
                'boms/save-translation',
                [BomController::class, 'saveBomTranslation']
            )->name('bom.saveTranslation');

            Route::get(
                'boms/get-translation',
                [BomController::class, 'getBomTranslation']
            )->name('bom.getTranslation');
            Route::resource('priorities', PriorityController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('/get-shifts', [ShiftController::class, 'getShifts'])->name('shifts.get');
            Route::resource('shifts', ShiftController::class)->except(['show', 'create', 'edit']);
            Route::get('/get-specifications', [SpecificationController::class, 'getSpecifications'])->name('specifications');
            Route::resource('specifications', SpecificationController::class)->except(['show', 'create', 'edit']);

            //RFI 
            Route::get('/rfis/data', [RfiController::class, 'data'])->name('rfis.data');

            Route::get('/rfis/low-stock', [RfiController::class, 'lowStockItems'])
                ->name('rfis.lowStock');

            Route::get('/items/last-rate', [RfiController::class, 'getLastRate'])
                ->name('items.lastRate');

            Route::post('/rfis/approve', [RfiController::class, 'approve'])
                ->name('rfis.approve');

            Route::post('/rfis/reject', [RfiController::class, 'reject'])
                ->name('rfis.reject');
            /*
            |--------------------------------------------------------------------------
            | RESOURCE ROUTE (AUTO HANDLES CRUD)
            |--------------------------------------------------------------------------
            */
            Route::resource('rfis', RfiController::class);
            /*
            |--------------------------------------------------------------------------
            | PURCHASE ORDER ROUTES
            |--------------------------------------------------------------------------
            */
            // PO LIST
            Route::get('/pos', [PurchaseOrderController::class, 'index'])->name('pos.index');
            Route::get('/pos/data', [PurchaseOrderController::class, 'data'])->name('pos.data');

            // VIEW & EDIT MODALS
            Route::get('/po/view/{id}', [PurchaseOrderController::class, 'view'])->name('po.view');
            Route::get('/po/edit/{id}', [PurchaseOrderController::class, 'edit'])->name('po.edit');
            Route::get('/po/search', [PurchaseOrderController::class, 'searchPO'])
                ->name('po.search');

            Route::get('/po/{po}/items', [PurchaseOrderController::class, 'getPOItems'])
                ->name('po.items');
            // UPDATE
            Route::post('/po/update/{id}', [PurchaseOrderController::class, 'update'])->name('po.update');
            Route::get('/pos/{po}/print', [PurchaseOrderController::class, 'print'])
                ->name('pos.print');
            Route::post('/po/{po}/pdf', [PurchaseOrderController::class, 'pdf'])
                ->name('po.pdf');
            Route::get('/reports/customer-search', [CompanyReportController::class, 'customerSearch'])->name('company.reports.customer.search');
            Route::get('/reports', [CompanyReportController::class, 'customerReport'])->name('company.reports.index');
            Route::get('/order/reports', [CompanyReportController::class, 'orderReport'])->name('order.reports.index');
            Route::get('/reports/customers', [CompanyReportController::class, 'customersAjax'])->name('company.reports.customers');

            Route::prefix('reports')->group(function () {

                Route::get(
                    '/orders',
                    [CompanyReportController::class, 'orderReport']
                )->name('company.reports.orders');
                Route::get(
                    '/orders/search',
                    [CompanyReportController::class, 'orderSearch']
                )->name('company.reports.orders.search');

                Route::get(
                    '/orders/ajax',
                    [CompanyReportController::class, 'ordersAjax']
                )->name('company.reports.orders.ajax');
                Route::get(
                    '/orders/{order}',
                    [CompanyReportController::class, 'orderDetails']
                )->name('company.reports.orders.details');
            });
            // Production Tracker Page
            Route::get(
                '/orders/{order}/production',
                [ProductionTrackerController::class, 'show']
            )->name('orders.production.detail');

            Route::post(
                'item-unit-conversions/ajax-store',
                [StockInController::class, 'ajaxStore']
            )->name('item-unit-conversions.ajaxStore');
            // Save Part Stage Progress
            Route::post(
                '/production/update-part-stage',
                [ProductionTrackerController::class, 'updatePartStage']
            )->name('production.part.update');
            // Stage Master
            Route::post(
                '/production-stage',
                [ProductionStageController::class, 'store']
            )->name('production.stage.store');

            Route::put(
                '/production-stage/{stage}',
                [ProductionStageController::class, 'update']
            )->name('production.stage.update');

            Route::delete(
                '/production-stage/{stage}',
                [ProductionStageController::class, 'destroy']
            )->name('production.stage.destroy');


            // Status Master
            Route::get('production-status', [ProductionStatusController::class, 'index'])
                ->name('production-status.index');
            Route::post(
                '/production-status',
                [ProductionStatusController::class, 'store']
            )->name('production.status.store');

            Route::put(
                '/production-status/{status}',
                [ProductionStatusController::class, 'update']
            )->name('production.status.update');

            Route::delete(
                '/production-status/{status}',
                [ProductionStatusController::class, 'destroy']
            )->name('production.status.destroy');
            Route::get(
                'parts-data',
                [PartController::class, 'data']
            )->name('parts.data');

            Route::get(
                'parts-search',
                [PartController::class, 'ajaxSearch']
            )->name('parts.ajaxSearch');
            Route::get(
                'search-items',
                [PartController::class, 'searchItems']
            )->name('parts.searchItems');
            Route::get(
                'parts/{part}/items',
                [PartController::class, 'getPartItems']
            )->name('parts.items');
            Route::get(
                'parts/{part}/details',
                [PartController::class, 'details']
            )->name('parts.details');
            Route::resource('parts', PartController::class);
        });
});