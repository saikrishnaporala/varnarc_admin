<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRMLeadsController;
use App\Http\Controllers\CRMCompaniesController;
use App\Http\Controllers\CRMContactsController;
use App\Http\Controllers\HREmployeeController;
use App\Http\Controllers\hr\settings\HRDepartmentController;
use App\Http\Controllers\hr\settings\HRDesignationController;
use App\Http\Controllers\hr\settings\HREmployeeTypeController;
use App\Http\Controllers\company\BranchController;
use App\Http\Controllers\hr\settings\HREmployeeGradeController;
use App\Http\Controllers\hr\HrAttendanceController;
use App\Http\Controllers\crm\master\LeadSourceController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('country','ResCountryController@index');
Route::get('country','ResCountryController@index');
Route::get('state','ResCountryStatesController@index');
Route::get('state/search','ResCountryStatesController@search');
Route::get('currency','ResCurrencyController@index');
Route::get('industry','ResPartnerIndustryController@index');
Route::get('lang','ResLangController@index');
Route::get('lang/count','ResLangController@CountActiveLang');
Route::get('lang/fetch','ResLangController@FetchLang');
Route::post('lang/search/{id}', 'ResLangController@GetLang');
Route::post('lang/activate','ResLangController@activate');
Route::get('tz','ResTimeZoneController@index');
Route::post('/leave', 'ManageSalaryController@getcount');
Route::post('employee/search', 'HrEmployeesController@searchapi');
Route::get('employee/list', 'HrEmployeesController@fetchEmployees');
// Route::get('/attendance', 'HrAttendanceController@checkatd');
// Route::post('/atd-count', 'HrAttendanceController@getcount');

// CRM - master - lead source
Route::get('/lead-sources', [LeadSourceController::class, 'index']); // Fetch all Lead Sources
Route::post('/lead-sources', [LeadSourceController::class, 'store']); // Create a Lead Source
Route::put('/lead-sources/{id}', [LeadSourceController::class, 'update']); // Update a Lead Source
Route::delete('/lead-sources/{id}', [LeadSourceController::class, 'destroy']); // Delete a Lead Source

// CRM - leads
Route::get('/leads', [CRMLeadsController::class, 'fetchLeads']);
Route::delete('leads/{id}', [CRMLeadsController::class, 'fetchLead']);
Route::post('/leads/store', [CRMLeadsController::class, 'store']);
Route::delete('leads/{id}', [CRMLeadsController::class, 'destroy'])->name('leads.destroy');

// CRM - companies
Route::get('/companies', [CRMCompaniesController::class, 'fetch']);
Route::post('/companies/store', [CRMCompaniesController::class, 'store']);
Route::delete('companies/{id}', [CRMCompaniesController::class, 'destroy']);

// CRM - contacts
Route::get('/contacts', [CRMContactsController::class, 'fetch']);
Route::post('/contacts/store', [CRMContactsController::class, 'store']);
Route::delete('contacts/{id}', [CRMContactsController::class, 'destroy']);

// HR - employees
Route::get('/employees', [HREmployeeController::class, 'fetch']);
Route::post('/employees/store', [HREmployeeController::class, 'store']);
Route::delete('employees/{id}', [HREmployeeController::class, 'destroy']);
Route::get('employees/byId/{id}', [HREmployeeController::class, 'fetchById']);

// HR - settings - Department
Route::get('/departments', [HRDepartmentController::class, 'fetch']);
Route::post('/departments/store', [HRDepartmentController::class, 'store']);
Route::delete('departments/{id}', [HRDepartmentController::class, 'destroy']);
Route::get('departments/byId/{id}', [HRDepartmentController::class, 'fetchById']);

// HR - settings - Designation
Route::get('/designations', [HRDesignationController::class, 'fetch']);
Route::post('/designations/store', [HRDesignationController::class, 'store']);
Route::delete('designations/{id}', [HRDesignationController::class, 'destroy']);
Route::get('designations/byId/{id}', [HRDesignationController::class, 'fetchById']);

// HR - settings - Employee Type
Route::get('/employeeTypes', [HREmployeeTypeController::class, 'fetch']);
Route::post('/employeeTypes/store', [HREmployeeTypeController::class, 'store']);
Route::delete('employeeTypes/{id}', [HREmployeeTypeController::class, 'destroy']);
Route::get('employeeTypes/byId/{id}', [HREmployeeTypeController::class, 'fetchById']);

// HR - settings - Employee Grades
Route::get('/employee_grades', [HREmployeeGradeController::class, 'fetch']);
Route::post('/employee_grades/store', [HREmployeeGradeController::class, 'store']);
Route::delete('employee_grades/{id}', [HREmployeeGradeController::class, 'destroy']);
Route::get('employee_grades/byId/{id}', [HREmployeeGradeController::class, 'fetchById']);

// HR - Employee Attendance
Route::post('/attendance/checkin', [HrAttendanceController::class, 'checkIn']);
Route::post('/attendance/checkout', [HrAttendanceController::class, 'checkOut']);
Route::get('/attendance/today/{employee}', [HrAttendanceController::class, 'getTodayAttendance']);
Route::get('/attendance/weekly/{employee}', [HrAttendanceController::class, 'getWeeklyAttendance']);
Route::get('/attendance/monthly/{employee}', [HrAttendanceController::class, 'getMonthlyAttendance']);
Route::post('/attendance/checkin-status', [HrAttendanceController::class, 'hasCheckedInToday']);


// company
Route::get('company','ResCompaniesController@companies');
Route::post('company/store','ResCompaniesController@store');
Route::get('company/count','ResCompaniesController@CountCompanies');

// company - Branches
Route::get('/company/branches', [BranchController::class, 'fetch']);
Route::post('/company/branches/store', [BranchController::class, 'store']);
Route::delete('/company/branches/{id}', [BranchController::class, 'destroy']);
Route::get('/company/branches/byId/{id}', [BranchController::class, 'fetchById']);

// ==== Inventory ==== 
Route::group(['namespace' => '\App\Addons\Inventory\Controllers'], function()
{
    Route::get('Products','ProductController@Products');
    Route::post('Product/store','ProductController@store');
    Route::post('Product/update','ProductController@update');
    Route::get('Products/sale','ProductController@ProductSale');
    Route::get('Products/Buy','ProductController@ProductBuy');
    Route::get('getProduct','ProductController@getProduct');
    Route::get('getProduct/id','ProductController@getProductById');

    Route::post('Products/quant/store','ProductQuantController@store');
    Route::post('Products/warehouse/quant/store','ProductQuantController@store_product');
    Route::post('Products/quant/update','ProductQuantController@UpdateQuant');

    Route::get('Products/category','CategoryController@fetchCategory');
    Route::post('Product/category/store','CategoryController@store');
    Route::post('Product/category/update','CategoryController@update');
    Route::get('Product/Category/{id}','CategoryController@getCategory');

    Route::get('warehouse','ProductWarehouseController@FetchWarehouse');
    Route::get('warehouse/{id}','ProductWarehouseController@getWarehouse');
    Route::post('warehouse/store','ProductWarehouseController@store');
    Route::post('warehouse/update','ProductWarehouseController@update');

    Route::post('stock_pickings/store','StockPickingsController@store');
    Route::post('stock_pickings/todo','StockPickingsController@todo');
    Route::post('stock_pickings/validate','StockPickingsController@validate_picking');
    Route::get('stock_pickings/search/{id}', 'StockPickingsController@getStockPicking');
    Route::get('stock_pickings/receipts/{id}','StockPickingsController@fetchReceiptPicking');
    Route::get('stock_pickings/delivere','StockPickingsController@fetchDeliverePicking');

    Route::get('Removal/Strategy','ProductRemovalController@fetchRemovalStrategy');
    
});

Route::get('/chart', 'HomeController@getChart');

// ==== Sales ====
Route::group(['namespace' => '\App\Addons\Accounting\Controllers'], function()
{
    Route::get('/fetchaccount/accounts', 'AccountAccountController@fetchAccountAccounts');
    Route::get('/fetchaccount/journal', 'AccountJournalController@fetchAccountJournals');
});
// ==== Sales ====
Route::group(['namespace' => '\App\Addons\Sales\Controllers'], function()
{
    Route::get('sale/list', 'SalesOrdersController@fetchSalesOrder');
    Route::post('sale/store', 'SalesOrdersController@store');
    Route::post('sale/update', 'SalesOrdersController@update');
    Route::post('sale/update/delivery', 'SalesOrdersController@updateDelivery');
    Route::post('sale/confirm', 'SalesOrdersController@confirm');
    Route::post('sale/delivere', 'SalesOrdersController@delivere');
    Route::get('sale/analysis', 'SalesOrdersController@sales_analysis');
    Route::get('sale/search/{id}', 'SalesOrdersController@getSalesOrder');
});

// ==== Purchases ====
Route::group(['namespace' => '\App\Addons\Purchase\Controllers'], function()
{
    Route::get('/purchase/list', 'PurchasesOrdersController@fetchPurchasesOrder');
    Route::post('/purchase/store', 'PurchasesOrdersController@store');
    Route::post('/purchase/update', 'PurchasesOrdersController@update');
    Route::post('/purchase/confirm', 'PurchasesOrdersController@confirm');
    Route::post('/purchase/receipts', 'PurchasesOrdersController@receipts');
    Route::get('/purchase/analysis', 'PurchasesOrdersController@purchases_analysis');
    Route::get('/purchase/search/{id}', 'PurchasesOrdersController@getPurchasesOrder');
});
// ==== Contact ====
Route::group(['namespace' => '\App\Addons\Contact\Controllers'], function()
{
    Route::post('customer/list', 'ResCustomersController@fetchCustomer');
    Route::post('customer/company/list', 'ResCustomersController@fetchCompany');
    Route::post('customer/search/{id}', 'ResCustomersController@searchapi');
    Route::post('customer/store', 'ResCustomersController@store');
    Route::post('customer/update', 'ResCustomersController@update');

    Route::post('vendor/list', 'ResPartnersController@fetchVendor');
    Route::post('vendor/company/list', 'ResPartnersController@fetchCompany');
    Route::post('vendor/search/{id}', 'ResPartnersController@searchapi');
    Route::post('vendor/store', 'ResPartnersController@store');
    Route::post('vendor/update', 'ResPartnersController@update');
});

// ==== UOM ====
Route::group(['namespace' => '\App\Addons\Uom\Controllers'], function()
{
    Route::get('/uom/list', 'UomController@fetchUom');
    Route::get('/uom/category/list','UomController@fetchUomCategory');
    Route::get('/uom/type/list','UomController@fetchUomType');
    Route::get('/uom/get_uom/{id}','UomController@getUom');
    Route::get('/uom/get_uom/category/{id}','UomController@fetchUomByCategory');
    Route::Post('/uom/store','UomController@store');
    Route::Post('/uom/update','UomController@update');
});

// get Access Rights
Route::get('/user/Access/{id}','UserController@getAccessRight');
Route::get('/user/{id}','UserController@getUser');
Route::get('/user/internal/count','UserController@CountInternalUser');
Route::get('/user/portal/count','UserController@CountportalUser');

// Addons
Route::get('/Addons/Check/{id}','AppsController@check_installed');

