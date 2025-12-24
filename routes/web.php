<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Library\BankController;
use App\Http\Controllers\Library\CompanyAccountController;
use App\Http\Controllers\Library\CustomerController;
use App\Http\Controllers\Library\CustomerCategoryController;
use App\Http\Controllers\Library\ExpeditionController;
use App\Http\Controllers\Library\SalesController;
use App\Http\Controllers\Library\SupplierCategoryController;
use App\Http\Controllers\Library\SupplierController;
use App\Http\Controllers\Library\TermsAndConditionTemplateController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductBrandController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductUnitController;
use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Purchasing\ReceivingController;
use App\Http\Controllers\Purchasing\PurchaseInvoiceController;
use App\Http\Controllers\Purchasing\RekapPembelianController;
use App\Http\Controllers\Setting\ModuleController;
use App\Http\Controllers\Setting\ModuleAccessController;
use App\Http\Controllers\Setting\PreferenceController;
use App\Http\Controllers\Setting\UsersController;
use App\Http\Controllers\Sales\QuotationController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Sales\DeliveryController;
use App\Http\Controllers\Sales\SalesInvoiceCollectionController;
use App\Http\Controllers\Sales\SalesInvoiceController;
use App\Http\Controllers\Sales\RekapPenjualanController;
use App\Http\Controllers\Accounting\AccountReceiveableController;
use App\Http\Controllers\Accounting\AccountPayableController;
use App\Http\Controllers\Accounting\GLAccountLevelController;
use App\Http\Controllers\Accounting\GLAccountController;
use App\Http\Controllers\Accounting\GLAccountSettingsController;
use App\Http\Controllers\Accounting\GLJournalController;
use App\Http\Controllers\Accounting\GLKasBankController;
use App\Http\Controllers\Accounting\GLMotherAccountController;
use App\Http\Controllers\Accounting\GLSubAccountController;
use App\Http\Controllers\Accounting\TaxSerialNumberController;
use App\Http\Controllers\Accounting\TaxSettingsController;
use App\Http\Controllers\Library\CustomerGroupController;
use App\Http\Controllers\Library\DataIndexController;
use App\Http\Controllers\Product\ProductSpecificationController;
use App\Http\Controllers\Production\ProductionDeliveryController;
use App\Http\Controllers\Production\ProductionOrderController;
use App\Http\Controllers\Production\ProductionReceivingController;
use App\Http\Controllers\Purchasing\PurchaseInvoiceCollectionController;
use App\Http\Controllers\Purchasing\PurchaseReturnController;
use App\Http\Controllers\Purchasing\PurchaseReturnItemController;
use App\Http\Controllers\Report\DeliveryReportController;
use App\Http\Controllers\Report\PurchaseCollectionReportController;
use App\Http\Controllers\Report\SalesReportController;
use App\Http\Controllers\Report\SalesReportDetailController;
use App\Http\Controllers\Report\PurchaseReportController;
use App\Http\Controllers\Report\PurchaseReportDetailController;
use App\Http\Controllers\Report\ReceivingReportController;
use App\Http\Controllers\Sales\ExpeditionCostController;
use App\Http\Controllers\Sales\SalesOrderInternalController;
use App\Http\Controllers\Sales\SalesReturnController;
use App\Http\Controllers\Sales\SalesReturnItemController;
use App\Http\Controllers\Setting\TransactionPeriodController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Stock\StockIndexController;
use App\Http\Controllers\Stock\StockTransferController;
use App\Http\Controllers\Stock\StockConversionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	if (!Auth::check()) {
		return view('pages.login');
    }
    else {
		Session::put('currentParent', 'nav-dashboard');
	    return redirect('/dashboard');
    }
});

/* Login Controller Routes */

Route::get('/main', [MainController::class, 'index']);
Route::post('/main/checklogin', [MainController::class, 'checklogin']);
Route::get('/dashboard', [MainController::class, 'successlogin']);
Route::get('main/logout', [MainController::class, 'logout']);
Route::post('/chartSales', [MainController::class, 'createChartPenjualan']);
Route::post('/dataInvSaleRefresh', [MainController::class, 'getDataInvSale']);
Route::post('/dataSales', [MainController::class, 'getDataSales']);
Route::post('/DataSalesDetail', [MainController::class, 'getDataSalesDetail']);
Route::post('/chartOmzet', [MainController::class, 'getDataOmzet']);
Route::post('/chartProfit', [MainController::class, 'getDataProfit']);
Route::post('/GetTaxSerialNumber', [MainController::class, 'getTaxSerialNumberCount']);
Route::post('/GetDashboardData', [MainController::class, 'retrieveData']);
Route::post('/GetStockMonitor', [MainController::class, 'getStockMonitor']);
Route::post('/CustomProgramRun', [MainController::class, 'CustomProgram']);
/* End of Login Controller Routes */

//Menu
Route::get('/Modules', [ModuleController::class, 'index']);
Route::get('/Modules/Add', [ModuleController::class, 'create']);
Route::get('/Modules/GetData',[ModuleController::class, 'getDataIndex']);
Route::post('/Modules/Delete',[ModuleController::class, 'delete']);
Route::resource('Modules', ModuleController::class);

//Menu Akses
Route::get('/ModuleAccess', [ModuleAccessController::class, 'index']);
Route::post('/ModuleAccess/GetUsers', [ModuleAccessController::class, 'getUsers']);
Route::post('/ModuleAccess/GetMenu', [ModuleAccessController::class, 'getMenu']);
Route::post('/ModuleAccess/UpdateAkses', [ModuleAccessController::class, 'updateDataAksesMenu']);
Route::resource('ModuleAccess', ModuleAccessController::class);

//Periode Transaksi
Route::get('/TransactionPeriod', [TransactionPeriodController::class, 'index']);
Route::post('/TransactionPeriod/GetPeriod', [TransactionPeriodController::class, 'getDataIndex']);
Route::post('/TransactionPeriod/UpdateAkses', [TransactionPeriodController::class, 'updateDataAksesPeriode']);
Route::resource('TransactionPeriod', TransactionPeriodController::class);

//Users
Route::get('/Users', [UsersController::class, 'index']);
Route::get('/Users/Add', [UsersController::class, 'create']);
Route::get('/Users/GetData', [UsersController::class, 'getDataIndex']);
Route::get('/Users/Profile/{id}', [UsersController::class, 'display'])->name('Users.Profile');
Route::post('/Users/CheckUser', [UsersController::class, 'cekUsername']);
Route::post('/Users/ResetPassword', [UsersController::class, 'resetPassword']);
Route::post('/Users/UbahPassword', [UsersController::class, 'ubahPasswordUser'])->name('Users.UbahPass');
Route::post('/Users/Delete',[UsersController::class, 'delete'])->name('Users.Delete');;
Route::resource('Users', UsersController::class);

//Bank
Route::get('/Bank', [BankController::class, 'index']);
Route::get('/Bank/Add', [BankController::class, 'create']);
Route::get('/Bank/GetData', [BankController::class, 'getDataIndex']);
Route::post('/Bank/Delete',[BankController::class, 'delete'])->name('Bank.delete');
Route::resource('Bank', BankController::class);

//Company Account
Route::get('/CompanyAccount', [CompanyAccountController::class, 'index']);
Route::get('/CompanyAccount/Add', [CompanyAccountController::class, 'create']);
Route::get('/CompanyAccount/GetData', [CompanyAccountController::class, 'getDataIndex']);
Route::post('/CompanyAccount/Delete',[CompanyAccountController::class, 'delete'])->name('CompanyAccount.delete');
Route::resource('CompanyAccount', CompanyAccountController::class);

//Preferrence
Route::get('/Preference/GetData', [PreferenceController::class, 'getDataIndex']);
Route::post('/Preference/Delete',[PreferenceController::class, 'delete']);
Route::get('/Preference/Add', [PreferenceController::class, 'create']);
Route::get('/Preference/Detail/{id}', [PreferenceController::class, 'detail'])->name('Preference.Detail');
Route::resource('Preference', PreferenceController::class);

//Expedition
Route::get('/Expedition', [ExpeditionController::class, 'index']);
Route::get('/Expedition/GetData', [ExpeditionController::class, 'getDataIndex']);
Route::get('/Expedition/Add', [ExpeditionController::class, 'create']);
Route::post('/Expedition/StoreBranch', [ExpeditionController::class, 'StoreBranchExpedition']);
Route::post('/Expedition/EditBranch', [ExpeditionController::class, 'EditBranchExpedition']);
Route::post('/Expedition/DeleteBranch', [ExpeditionController::class, 'DeleteBranchData']);
Route::post('/Expedition/SetDefault', [ExpeditionController::class, 'SetDefaultBranch']);
Route::post('/Expedition/GetBranch', [ExpeditionController::class, 'GetBranchData']);
Route::get('/Expedition/Detail/{id}', [ExpeditionController::class, 'detail'])->name('Expedition.Detail');
Route::post('/Expedition/Delete',[ExpeditionController::class, 'delete'])->name('Expedition.delete');
Route::post('/Expedition/StoreTarif', [ExpeditionController::class, 'StoreTarifExpedition']);
Route::post('/Expedition/EditTarif', [ExpeditionController::class, 'EditTarifExpedition']);
Route::post('/Expedition/DeleteTarif', [ExpeditionController::class, 'DeleteTarifData']);
Route::post('/Expedition/GetTarif', [ExpeditionController::class, 'GetTarifData']);
Route::resource('Expedition', ExpeditionController::class);

//Supplier
Route::get('/Supplier/GetData', [SupplierController::class, 'getDataIndex']);
Route::post('/Supplier/GetDataItem', [SupplierController::class, 'supplierItems']);
Route::post('/Supplier/GetProduct', [SupplierController::class, 'getProduct']);
Route::post('/Supplier/DeleteProduct', [SupplierController::class, 'DeleteSupplierItem']);
Route::post('/Supplier/AddSupplierProduct', [SupplierController::class, 'addSupplierProduct']);
Route::get('/Supplier/Add', [SupplierController::class, 'create']);
Route::get('/Supplier/Detail/{id}', [SupplierController::class, 'detail'])->name('Supplier.Detail');
Route::get('/Supplier/DetailBarang/{id}', [SupplierController::class, 'detail_barang'])->name('Supplier.DetailBarang');
Route::get('/Supplier/History/{id}', [SupplierController::class, 'history'])->name('Supplier.History');
Route::post('/Supplier/StoreAddress', [SupplierController::class, 'StoreSupplierAddress']);
Route::post('/Supplier/DeleteAddress', [SupplierController::class, 'DeleteSupplierAddress']);
Route::post('/Supplier/EditAddress', [SupplierController::class, 'EditSupplierAddress']);
Route::post('/Supplier/SetDefault', [SupplierController::class, 'SetDefaultAddress']);
Route::post('/Supplier/GetAddress', [SupplierController::class, 'GetSupplierAddress']);
Route::post('/Supplier/Delete', [SupplierController::class, 'delete']);
Route::resource('Supplier', SupplierController::class);

//SupplierCategory
Route::get('/SupplierCategory/Add', [SupplierCategoryController::class, 'create']);
Route::get('/SupplierCategory/GetData', [SupplierCategoryController::class, 'getDataIndex']);
Route::post('/SupplierCategory/Update', [SupplierCategoryController::class, 'Update']);
Route::post('/SupplierCategory/Delete', [SupplierCategoryController::class, 'Delete']);
Route::resource('SupplierCategory', SupplierCategoryController::class);

//CustomerCategory
Route::get('/CustomerCategory/Add', [CustomerCategoryController::class, 'create']);
Route::get('/CustomerCategory/GetData', [CustomerCategoryController::class, 'getDataIndex']);
Route::post('/CustomerCategory/Update', [CustomerCategoryController::class, 'Update']);
Route::post('/CustomerCategory/Delete', [CustomerCategoryController::class, 'Delete']);
Route::resource('CustomerCategory', CustomerCategoryController::class);

//CustomerGroup
Route::get('/CustomerGroup/Add', [CustomerGroupController::class, 'create']);
Route::get('/CustomerGroup/GetData', [CustomerGroupController::class, 'getDataIndex']);
Route::get('/CustomerGroup/Detail/{id}', [CustomerGroupController::class, 'detail'])->name('CustomerGroup.Detail');
Route::post('/CustomerGroup/Update', [CustomerGroupController::class, 'Update']);
Route::post('/CustomerGroup/Delete', [CustomerGroupController::class, 'Delete']);

Route::resource('CustomerGroup', CustomerGroupController::class);

//ProductBrand
Route::get('/ProductBrand/Add', [ProductBrandController::class, 'create']);
Route::get('/ProductBrand/GetData', [ProductBrandController::class, 'getDataIndex']);
Route::post('/ProductBrand/Delete', [ProductBrandController::class, 'delete']);
Route::resource('ProductBrand', ProductBrandController::class);

//ProductCategory
Route::get('/ProductCategory/Add', [ProductCategoryController::class, 'create']);
Route::get('/ProductCategory/GetData', [ProductCategoryController::class, 'getDataIndex']);
Route::post('/ProductCategory/Delete', [ProductCategoryController::class, 'delete']);
Route::resource('ProductCategory', ProductCategoryController::class);

//ProductSpecification
Route::get('/ProductSpecification/Add', [ProductSpecificationController::class, 'create']);
Route::get('/ProductSpecification/GetData', [ProductSpecificationController::class, 'getDataIndex']);
Route::post('/ProductSpecification/Delete', [ProductSpecificationController::class, 'delete']);
Route::resource('ProductSpecification', ProductSpecificationController::class);

//ProductUnit
Route::get('/ProductUnit/Add', [ProductUnitController::class, 'create']);
Route::get('/ProductUnit/GetData', [ProductUnitController::class, 'getDataIndex']);
Route::post('/ProductUnit/Delete', [ProductUnitController::class, 'delete']);
Route::resource('ProductUnit', ProductUnitController::class);

//ProductSpecification
Route::get('/ProductSpecification/Add', [ProductSpecificationController::class, 'create']);
Route::get('/ProductSpecification/GetData', [ProductSpecificationController::class, 'getDataIndex']);
Route::post('/ProductSpecification/Delete', [ProductSpecificationController::class, 'delete']);
Route::resource('ProductSpecification', ProductSpecificationController::class);

//Product
Route::get('/Product/GetData', [ProductController::class, 'getDataIndex']);
Route::post('/Product/StorePrice', [ProductController::class, 'storePriceData']);
Route::post('/Product/Delete', [ProductController::class, 'delete']);
Route::get('/Product/Add', [ProductController::class, 'create']);
Route::get('/Product/Detail/{id}', [ProductController::class, 'detail'])->name('Product.Detail');
Route::get('/Product/History/{id}', [ProductController::class, 'history'])->name('Product.History');
Route::post('/Product/GetDetailSatuan', [ProductController::class, 'getProductDetail']);
Route::post('/Product/GetDetailSpec', [ProductController::class, 'getProductSpec']);
Route::post('/Product/RestoreDetail', [ProductController::class, 'RestoreProductDetail']);
Route::post('/Product/StoreDetail', [ProductController::class, 'StoreProductDetail']);
Route::post('/Product/UpdateDetail', [ProductController::class, 'UpdateProductDetail']);
Route::post('/Product/DeleteDetail', [ProductController::class, 'DeleteProductDetail']);
Route::post('/Product/ResetDetail', [ProductController::class, 'ResetProductDetail']);
Route::post('/Product/EditDetail', [ProductController::class, 'EditProductDetail']);
Route::post('/Product/StoreSpec', [ProductController::class, 'StoreProductSpec']);
Route::post('/Product/UpdateSpec', [ProductController::class, 'UpdateProductSpec']);
Route::post('/Product/DeleteSpec', [ProductController::class, 'DeleteProductSpec']);
Route::post('/Product/EditSpec', [ProductController::class, 'EditProductSpec']);
Route::post('/Product/SetDefault', [ProductController::class, 'SetDefault']);
Route::post('/Product/SetMonitor', [ProductController::class, 'SetMonitor']);
Route::post('/Product/ExportProduct', [ProductController::class, 'exportDataProduct'])->name('Product.Export');
Route::post('/Product/GetDetailSpec', [ProductController::class, 'getProductSpec']);
Route::post('/Product/StoreSpec', [ProductController::class, 'StoreProductSpec']);
Route::post('/Product/UpdateSpec', [ProductController::class, 'UpdateProductSpec']);
Route::post('/Product/DeleteSpec', [ProductController::class, 'DeleteProductSpec']);
Route::post('/Product/EditSpec', [ProductController::class, 'EditProductSpec']);
Route::post('/Product/GetDataItem', [ProductController::class, 'DetailItems']);
Route::post('/Product/GetSuppAndCust', [ProductController::class, 'getSuppAndCustomer']);
Route::post('/Product/DeleteCustOrSupp', [ProductController::class, 'DeleteCustomerOrSupplierItem']);
Route::post('/Product/AddCustomerOrSupplier', [ProductController::class, 'addCustomerOrSupplier']);
Route::post('/Product/ExportProduct', [ProductController::class, 'exportDataProduct'])->name('Product.Export');
Route::resource('Product', ProductController::class);

//Terms And Condition Template
Route::get('/TermsAndCondTemplate/Add', [TermsAndConditionTemplateController::class, 'create']);
Route::get('/TermsAndCondTemplate/GetData', [TermsAndConditionTemplateController::class, 'getDataIndex']);
Route::post('/TermsAndCondTemplate/Delete', [TermsAndConditionTemplateController::class, 'delete']);
Route::get('/TermsAndCondTemplate/Detail/{id}', [TermsAndConditionTemplateController::class, 'detail'])->name('TermsAndCondTemplate.Detail');
Route::resource('TermsAndCondTemplate', TermsAndConditionTemplateController::class);

//Sales
Route::get('/Sales/Add', [SalesController::class, 'create']);
Route::get('/Sales/GetData', [SalesController::class, 'getDataIndex']);
Route::post('/Sales/Update', [SalesController::class, 'Update']);
Route::post('/Sales/Delete', [SalesController::class, 'Delete']);
Route::resource('Sales', SalesController::class);

//Customer
Route::get('/Customer/GetData', [CustomerController::class, 'getDataIndex']);
Route::post('/Customer/GetDataItem', [CustomerController::class, 'customerItems']);
Route::post('/Customer/GetProduct', [CustomerController::class, 'getProduct']);
Route::post('/Customer/DeleteProduct', [CustomerController::class, 'DeleteCustomerItem']);
Route::post('/Customer/AddCustomerProduct', [CustomerController::class, 'addCustomerProduct']);
Route::get('/Customer/Add', [CustomerController::class, 'create']);
Route::get('/Customer/Detail/{id}', [CustomerController::class, 'detail'])->name('Customer.Detail');
Route::get('/Customer/History/{id}', [CustomerController::class, 'history'])->name('Customer.History');
Route::get('/Customer/DetailBarang/{id}', [CustomerController::class, 'detail_barang'])->name('Customer.DetailBarang');
Route::post('/Customer/StoreAddress', [CustomerController::class, 'StoreCustomerAddress']);
Route::post('/Customer/DeleteAddress', [CustomerController::class, 'DeleteCustomerAddress']);
Route::post('/Customer/EditAddress', [CustomerController::class, 'EditCustomerAddress']);
Route::post('/Customer/SetDefault', [CustomerController::class, 'SetDefaultAddress']);
Route::post('/Customer/GetAddress', [CustomerController::class, 'GetCustomerAddress']);
Route::post('/Customer/Delete', [CustomerController::class, 'delete']);
Route::resource('Customer', CustomerController::class);

//PurchaseOrder
Route::get('/PurchaseOrder/GetData', [PurchaseOrderController::class, 'getDataIndex']);
Route::get('/PurchaseOrder/Add', [PurchaseOrderController::class, 'create']);
Route::post('/PurchaseOrder/GetProductBySupplier', [PurchaseOrderController::class, 'getProductSupplier']);
Route::post('/PurchaseOrder/GetProduct', [PurchaseOrderController::class, 'getProduct']);
Route::post('/PurchaseOrder/GetDataItem', [PurchaseOrderController::class, 'getDataItem']);
Route::post('/PurchaseOrder/GetSatuan', [PurchaseOrderController::class, 'getProductDetail']);
Route::post('/PurchaseOrder/GetListTerms', [PurchaseOrderController::class, 'getListTerms']);
Route::post('/PurchaseOrder/GetTerms', [PurchaseOrderController::class, 'getTerms']);
Route::post('/PurchaseOrder/GetDefaultAddress', [PurchaseOrderController::class, 'getDefaultAddress']);
Route::post('/PurchaseOrder/GetSupplierAddress', [PurchaseOrderController::class, 'getSupplierAddress']);
Route::post('/PurchaseOrder/AddSupllierProduct', [PurchaseOrderController::class, 'addSupllierProduct']);
Route::get('/PurchaseOrder/Detail/{id}', [PurchaseOrderController::class, 'detail'])->name('PurchaseOrder.Detail');
Route::get('/PurchaseOrder/Cetak/{id}', [PurchaseOrderController::class, 'cetak'])->name('PurchaseOrder.Cetak');
Route::post('/PurchaseOrder/Posting/{id}', [PurchaseOrderController::class, 'posting'])->name('PurchaseOrder.Posting');
Route::post('/PurchaseOrder/StoreDetail', [PurchaseOrderController::class, 'StorePurchaseOrderDetail']);
Route::post('/PurchaseOrder/UpdateDetail', [PurchaseOrderController::class, 'UpdatePurchaseOrderDetail']);
Route::post('/PurchaseOrder/DeleteDetail', [PurchaseOrderController::class, 'DeletePurchaseOrderDetail']);
Route::post('/PurchaseOrder/RestoreDetail', [PurchaseOrderController::class, 'RestorePurchaseOrderDetail']);
Route::post('/PurchaseOrder/EditDetail', [PurchaseOrderController::class, 'EditPurchaseOrderDetail']);
Route::post('/PurchaseOrder/GetDetail', [PurchaseOrderController::class, 'GetPurchaseOrderDetail']);
Route::post('/PurchaseOrder/GetDataFooter', [PurchaseOrderController::class, 'GetPurchaseOrderFooter']);
Route::post('/PurchaseOrder/ResetDetail', [PurchaseOrderController::class, 'ResetPurchaseOrderDetail']);
Route::post('/PurchaseOrder/Delete', [PurchaseOrderController::class, 'delete']);
Route::post('/PurchaseOrder/GetSupplierPreviousOrder', [PurchaseOrderController::class, 'getPreviousOrder']);
Route::post('/PurchaseOrder/GetProductHistory', [PurchaseOrderController::class, 'getProductHistory']);
Route::resource('PurchaseOrder', PurchaseOrderController::class);

//Receiving
Route::get('/Receiving/GetData', [ReceivingController::class, 'getDataIndex']);
Route::get('/Receiving/Add', [ReceivingController::class, 'create']);
Route::post('/Receiving/GetPurchaseOrder', [ReceivingController::class, 'getPurchaseOrder']);
Route::post('/Receiving/GetProductByPurchaseOrder', [ReceivingController::class, 'getProduct']);
Route::post('/Receiving/GetDataItem', [ReceivingController::class, 'getDataItem']);
Route::post('/Receiving/GetListTerms', [ReceivingController::class, 'getListTerms']);
Route::post('/Receiving/GetTerms', [ReceivingController::class, 'getTerms']);
Route::post('/Receiving/GetSatuan', [ReceivingController::class, 'getProductDetail']);
Route::post('/Receiving/GetTanggalPo', [ReceivingController::class, 'getTanggalPo']);
Route::post('/Receiving/GetDefaultAddress', [ReceivingController::class, 'getDefaultAddress']);
Route::post('/Receiving/GetSupplierAddress', [ReceivingController::class, 'getSupplierAddress']);
Route::get('/Receiving/Detail/{id}', [ReceivingController::class, 'detail'])->name('Receiving.Detail');
Route::get('/Receiving/Staging/{id}', [ReceivingController::class, 'staging'])->name('Receiving.Staging');
Route::post('/Receiving/Posting/{id}', [ReceivingController::class, 'posting'])->name('Receiving.Posting');
Route::post('/Receiving/PostStaging/{id}', [ReceivingController::class, 'postAllocation'])->name('Receiving.PostStaging');
Route::post('/Receiving/StoreDetail', [ReceivingController::class, 'StoreReceivingDetail']);
Route::post('/Receiving/UpdateDetail', [ReceivingController::class, 'UpdateReceivingDetail']);
Route::post('/Receiving/DeleteDetail', [ReceivingController::class, 'DeleteReceivingDetail']);
Route::post('/Receiving/EditDetail', [ReceivingController::class, 'EditReceivingDetail']);
Route::post('/Receiving/SetDetail', [ReceivingController::class, 'SetReceivingDetail']);
Route::post('/Receiving/GetDetail', [ReceivingController::class, 'GetReceivingDetail']);
Route::post('/Receiving/ResetDetail', [ReceivingController::class, 'ResetReceivingDetail']);
Route::post('/Receiving/RestoreDetail', [ReceivingController::class, 'RestoreReceivingDetail']);
Route::get('/Receiving/Cetak/{id}', [ReceivingController::class, 'cetak'])->name('Receiving.Cetak');
Route::post('/Receiving/GetDataFooter', [ReceivingController::class, 'GetReceivingFooter']);
Route::post('/Receiving/Delete', [ReceivingController::class, 'delete']);
Route::post('/Receiving/GetDataDetail', [ReceivingController::class, 'getDataDetail']);
Route::post('/Receiving/StoreAllocation', [ReceivingController::class, 'StoreReceivingAllocation']);
Route::post('/Receiving/GetDataAlokasi', [ReceivingController::class, 'GetReceivingAllocation']);
Route::post('/Receiving/DeleteAllocation', [ReceivingController::class, 'DeleteReceivingAllocation']);
Route::post('/Receiving/ExportReceiving', [ReceivingController::class, 'exportDataReceiving'])->name('Receiving.Export');
Route::resource('Receiving', ReceivingController::class);

//PurchaseInvoice
Route::get('/PurchaseInvoice/GetData', [PurchaseInvoiceController::class, 'getDataIndex']);
Route::get('/PurchaseInvoice/Add', [PurchaseInvoiceController::class, 'create']);
Route::post('/PurchaseInvoice/GetPurchaseOrder', [PurchaseInvoiceController::class, 'getPurchaseOrder']);
Route::post('/PurchaseInvoice/GetPurchaseOrderData', [PurchaseInvoiceController::class, 'getPurchaseOrderData']);
Route::post('/PurchaseInvoice/GetProductBySupplier', [PurchaseInvoiceController::class, 'getProductSupplier']);
Route::post('/PurchaseInvoice/GetReceiving', [PurchaseInvoiceController::class, 'getReceiving']);
Route::post('/PurchaseInvoice/GetDataReceiving', [PurchaseInvoiceController::class, 'getDataReceiving']);
Route::post('/PurchaseInvoice/GetListTerms', [PurchaseInvoiceController::class, 'getListTerms']);
Route::post('/PurchaseInvoice/GetTerms', [PurchaseInvoiceController::class, 'getTerms']);
Route::post('/PurchaseInvoice/GetDefaultAddress', [PurchaseInvoiceController::class, 'getDefaultAddress']);
Route::post('/PurchaseInvoice/AddSupllierProduct', [PurchaseInvoiceController::class, 'addSupllierProduct']);
Route::get('/PurchaseInvoice/Detail/{id}', [PurchaseInvoiceController::class, 'detail'])->name('PurchaseInvoice.Detail');
Route::post('/PurchaseInvoice/Posting/{id}', [PurchaseInvoiceController::class, 'posting'])->name('PurchaseInvoice.Posting');
Route::post('/PurchaseInvoice/StoreDetail', [PurchaseInvoiceController::class, 'StoreInvoiceDetail']);
Route::post('/PurchaseInvoice/DeleteDetail', [PurchaseInvoiceController::class, 'DeleteInvoiceDetail']);
Route::post('/PurchaseInvoice/SetDetail', [PurchaseInvoiceController::class, 'SetInvoiceDetail']);
Route::post('/PurchaseInvoice/GetDetail', [PurchaseInvoiceController::class, 'GetInvoiceDetail']);
Route::post('/PurchaseInvoice/RestoreDetail', [PurchaseInvoiceController::class, 'RestorePurchaseInvoiceDetail']);
Route::post('/PurchaseInvoice/GetDataFooter', [PurchaseInvoiceController::class, 'GetInvoiceFooter']);
Route::post('/PurchaseInvoice/GetDetailReceiving', [PurchaseInvoiceController::class, 'GetReceivingDetail']);
Route::post('/PurchaseInvoice/GetInvoiceDate', [PurchaseInvoiceController::class, 'GetDate']);
Route::post('/PurchaseInvoice/ResetDetail', [PurchaseInvoiceController::class, 'ResetPurchaseInvoiceDetail']);
Route::get('/PurchaseInvoice/Cetak/{id}', [PurchaseInvoiceController::class, 'cetak'])->name('PurchaseInvoice.Cetak');
Route::post('/PurchaseInvoice/Delete', [PurchaseInvoiceController::class, 'delete']);
Route::post('/PurchaseInvoice/ExportPurchaseInvoice', [PurchaseInvoiceController::class, 'exportDataPurchaseInvoice'])->name('PurchaseInvoice.Export');
Route::resource('PurchaseInvoice', PurchaseInvoiceController::class);

//PurchaseInvoiceCollection
Route::get('/PurchaseInvoiceCollection/GetData', [PurchaseInvoiceCollectionController::class, 'getDataIndex']);
Route::get('/PurchaseInvoiceCollection/Add', [PurchaseInvoiceCollectionController::class, 'create']);
Route::post('/PurchaseInvoiceCollection/GetInvoice', [PurchaseInvoiceCollectionController::class, 'getInvoice']);
Route::post('/PurchaseInvoiceCollection/GetInvoiceData', [PurchaseInvoiceCollectionController::class, 'getInvoiceData']);
Route::post('/PurchaseInvoiceCollection/GetDefaultAddress', [PurchaseInvoiceCollectionController::class, 'getDefaultAddress']);
Route::post('/PurchaseInvoiceCollection/GetSupplierAddress', [PurchaseInvoiceCollectionController::class, 'getSupplierAddress']);
Route::get('/PurchaseInvoiceCollection/Detail/{id}', [PurchaseInvoiceCollectionController::class, 'detail'])->name('PurchaseInvoiceCollection.Detail');
Route::post('/PurchaseInvoiceCollection/Posting/{id}', [PurchaseInvoiceCollectionController::class, 'posting'])->name('PurchaseInvoiceCollection.Posting');
Route::post('/PurchaseInvoiceCollection/StoreDetail', [PurchaseInvoiceCollectionController::class, 'StoreInvoiceDetail']);
Route::post('/PurchaseInvoiceCollection/DeleteDetail', [PurchaseInvoiceCollectionController::class, 'DeleteInvoiceDetail']);
Route::post('/PurchaseInvoiceCollection/RestoreDetail', [PurchaseInvoiceCollectionController::class, 'RestoreInvoiceDetail']);
Route::post('/PurchaseInvoiceCollection/SetDetail', [PurchaseInvoiceCollectionController::class, 'SetCollectionDetail']);
Route::post('/PurchaseInvoiceCollection/GetDetail', [PurchaseInvoiceCollectionController::class, 'GetInvoiceDetail']);
Route::post('/PurchaseInvoiceCollection/GetDataFooter', [PurchaseInvoiceCollectionController::class, 'GetInvoiceFooter']);
Route::post('/PurchaseInvoiceCollection/ConfirmCollection', [PurchaseInvoiceCollectionController::class, 'confirm']);
Route::post('/PurchaseInvoiceCollection/GetInvoiceDate', [PurchaseInvoiceCollectionController::class, 'GetDate']);
Route::post('/PurchaseInvoiceCollection/ResetDetail', [PurchaseInvoiceCollectionController::class, 'ResetPurchaseInvoiceCollectionDetail']);
Route::get('/PurchaseInvoiceCollection/CetakKwitansi/{id}', [PurchaseInvoiceCollectionController::class, 'cetakKwitansi'])->name('PurchaseInvoiceCollection.CetakKwitansi');
Route::get('/PurchaseInvoiceCollection/Cetak/{id}', [PurchaseInvoiceCollectionController::class, 'cetak'])->name('PurchaseInvoiceCollection.Cetak');
Route::post('/PurchaseInvoiceCollection/CetakPreview', [PurchaseInvoiceCollectionController::class, 'preview']);
Route::post('/PurchaseInvoiceCollection/Delete', [PurchaseInvoiceCollectionController::class, 'delete']);
Route::post('/PurchaseInvoiceCollection/GetTerms', [PurchaseInvoiceCollectionController::class, 'getTerms']);
Route::post('/PurchaseInvoiceCollection/GetListTerms', [PurchaseInvoiceCollectionController::class, 'getListTerms']);
Route::resource('PurchaseInvoiceCollection', PurchaseInvoiceCollectionController::class);

//Quotation
Route::get('/Quotation/GetData', [QuotationController::class, 'getDataIndex']);
Route::get('/Quotation/Add', [QuotationController::class, 'create']);
Route::post('/Quotation/GetProductByCustomer', [QuotationController::class, 'getProductCustomer']);
Route::post('/Quotation/GetProduct', [QuotationController::class, 'getProduct']);
Route::post('/Quotation/GetSatuan', [QuotationController::class, 'getProductDetail']);
Route::post('/Quotation/GetDataItem', [QuotationController::class, 'getDataItem']);
Route::post('/Quotation/GetListTerms', [QuotationController::class, 'getListTerms']);
Route::post('/Quotation/GetTerms', [QuotationController::class, 'getTerms']);
Route::post('/Quotation/GetDefaultAddress', [QuotationController::class, 'getDefaultAddress']);
Route::post('/Quotation/GetCustomerAddress', [QuotationController::class, 'getCustomerAddress']);
Route::post('/Quotation/AddCustomerProduct', [QuotationController::class, 'addCustomerProduct']);
Route::get('/Quotation/Detail/{id}', [QuotationController::class, 'detail'])->name('Quotation.Detail');
Route::get('/Quotation/Cetak/{id}', [QuotationController::class, 'cetak'])->name('Quotation.Cetak');
Route::post('/Quotation/Posting/{id}', [QuotationController::class, 'posting'])->name('Quotation.Posting');
Route::post('/Quotation/StoreDetail', [QuotationController::class, 'StoreQuotationDetail']);
Route::post('/Quotation/UpdateDetail', [QuotationController::class, 'UpdateQuotationDetail']);
Route::post('/Quotation/DeleteDetail', [QuotationController::class, 'DeleteQuotationDetail']);
Route::post('/Quotation/ResetDetail', [QuotationController::class, 'ResetQuotationDetail']);
Route::post('/Quotation/EditDetail', [QuotationController::class, 'EditQuotationDetail']);
Route::post('/Quotation/RestoreDetail', [QuotationController::class, 'RestoreQuotationDetail']);
Route::post('/Quotation/GetDetail', [QuotationController::class, 'GetQuotationDetail']);
Route::post('/Quotation/GetDataFooter', [QuotationController::class, 'GetQuotationFooter']);
Route::post('/Quotation/Delete', [QuotationController::class, 'delete']);
Route::resource('Quotation', QuotationController::class);

//SalesOrder
Route::get('/SalesOrder/GetData', [SalesOrderController::class, 'getDataIndex']);
Route::get('/SalesOrder/Add', [SalesOrderController::class, 'create']);
Route::post('/SalesOrder/GetProductByCustomer', [SalesOrderController::class, 'getProductCustomer']);
Route::post('/SalesOrder/GetProduct', [SalesOrderController::class, 'getProduct']);
Route::post('/SalesOrder/GetProductHistory', [SalesOrderController::class, 'getProductHistory']);
Route::post('/SalesOrder/GetDataItem', [SalesOrderController::class, 'getDataItem']);
Route::post('/SalesOrder/GetListTerms', [SalesOrderController::class, 'getListTerms']);
Route::post('/SalesOrder/GetTerms', [SalesOrderController::class, 'getTerms']);
Route::post('/SalesOrder/GetDefaultAddress', [SalesOrderController::class, 'getDefaultAddress']);
Route::post('/SalesOrder/GetCustomerAddress', [SalesOrderController::class, 'getCustomerAddress']);
Route::post('/SalesOrder/AddCustomerProduct', [SalesOrderController::class, 'addCustomerProduct']);
Route::get('/SalesOrder/Detail/{id}', [SalesOrderController::class, 'detail'])->name('SalesOrder.Detail');
Route::get('/SalesOrder/Print/{id}', [SalesOrderController::class, 'print'])->name('SalesOrder.Print'); //test tambahan
Route::get('/SalesOrder/Cetak/{id}', [SalesOrderController::class, 'cetak'])->name('SalesOrder.Cetak');
Route::get('/SalesOrder/CetakInvDp/{id}', [SalesOrderController::class, 'cetakInvDP'])->name('SalesOrder.CetakInvDP');
Route::get('/SalesOrder/CetakInvP/{id}', [SalesOrderController::class, 'cetakInvPelunasan'])->name('SalesOrder.CetakInvPelunasan');
Route::get('/SalesOrder/CetakInvPerforma/{id}', [SalesOrderController::class, 'cetakInvPerforma'])->name('SalesOrder.CetakInvPerforma');
Route::post('/SalesOrder/Posting/{id}', [SalesOrderController::class, 'posting'])->name('SalesOrder.Posting');
Route::post('/SalesOrder/RestoreDetail', [SalesOrderController::class, 'RestoreSalesOrderDetail']);
Route::post('/SalesOrder/StoreDetail', [SalesOrderController::class, 'StoreSalesOrderDetail']);
Route::post('/SalesOrder/UpdateDetail', [SalesOrderController::class, 'UpdateSalesOrderDetail']);
Route::post('/SalesOrder/DeleteDetail', [SalesOrderController::class, 'DeleteSalesOrderDetail']);
Route::post('/SalesOrder/ResetDetail', [SalesOrderController::class, 'ResetSalesOrderDetail']);
Route::post('/SalesOrder/EditDetail', [SalesOrderController::class, 'EditSalesOrderDetail']);
Route::post('/SalesOrder/GetDetail', [SalesOrderController::class, 'GetSalesOrderDetail']);
Route::post('/SalesOrder/GetDataFooter', [SalesOrderController::class, 'GetSalesOrderFooter']);
Route::post('/SalesOrder/Delete', [SalesOrderController::class, 'delete']);
Route::post('/SalesOrder/GetCustomerPreviousOrder', [SalesOrderController::class, 'getPreviousOrder']);
Route::post('/SalesOrder/GetSatuan', [SalesOrderController::class, 'getProductDetail']);
Route::post('/SalesOrder/ExportSalesOrder', [SalesOrderController::class, 'exportDataSalesOrder'])->name('SalesOrder.Export');
Route::post('/SalesOrder/SetDetail', [SalesOrderController::class, 'SetSalesOrderDetail']);
Route::resource('SalesOrder', SalesOrderController::class);

//Delivery
Route::get('/Delivery/GetData', [DeliveryController::class, 'getDataIndex']);
Route::get('/Delivery/Add', [DeliveryController::class, 'create']);
Route::post('/Delivery/GetSalesOrder', [DeliveryController::class, 'getSalesOrder']);
Route::post('/Delivery/GetProductBySalesOrder', [DeliveryController::class, 'getProduct']);
Route::post('/Delivery/GetRequestDate', [DeliveryController::class, 'getRequestDate']);
Route::post('/Delivery/GetDataItem', [DeliveryController::class, 'getDataItem']);
Route::post('/Delivery/GetListTerms', [DeliveryController::class, 'getListTerms']);
Route::post('/Delivery/GetTerms', [DeliveryController::class, 'getTerms']);
Route::post('/Delivery/GetTermsByOption', [DeliveryController::class, 'getTermsByOpt']);
Route::post('/Delivery/GetDefaultAddress', [DeliveryController::class, 'getDefaultAddress']);
Route::post('/Delivery/GetCustomerAddress', [DeliveryController::class, 'getCustomerAddress']);
Route::get('/Delivery/Detail/{id}', [DeliveryController::class, 'detail'])->name('Delivery.Detail');
Route::get('/Delivery/Staging/{id}', [DeliveryController::class, 'staging'])->name('Delivery.Staging');
Route::post('/Delivery/Posting/{id}', [DeliveryController::class, 'posting'])->name('Delivery.Posting');
Route::post('/Delivery/PostStaging/{id}', [DeliveryController::class, 'postAllocation'])->name('Delivery.PostStaging');
Route::post('/Delivery/StoreDetail', [DeliveryController::class, 'StoreDeliveryDetail']);
Route::post('/Delivery/UpdateDetail', [DeliveryController::class, 'UpdateDeliveryDetail']);
Route::post('/Delivery/DeleteDetail', [DeliveryController::class, 'DeleteDeliveryDetail']);
Route::post('/Delivery/EditDetail', [DeliveryController::class, 'EditDeliveryDetail']);
Route::post('/Delivery/RestoreDetail', [DeliveryController::class, 'RestoreDeliveryDetail']);
Route::post('/Delivery/ResetDetail', [DeliveryController::class, 'ResetDeliveryDetail']);
Route::get('/Delivery/Cetak/{id}', [DeliveryController::class, 'cetak'])->name('Delivery.Cetak');
Route::get('/Delivery/CetakOrder/{id}', [DeliveryController::class, 'cetakOrder'])->name('Delivery.CetakOrder');
Route::post('/Delivery/SetDetail', [DeliveryController::class, 'SetDeliveryDetail']);
Route::post('/Delivery/GetDetail', [DeliveryController::class, 'GetDeliveryDetail']);
Route::post('/Delivery/ConfirmDelivery', [DeliveryController::class, 'confirm']);
Route::post('/Delivery/GetDataFooter', [DeliveryController::class, 'GetDeliveryFooter']);
Route::post('/Delivery/GetDataStock', [DeliveryController::class, 'getStockItem']);
Route::post('/Delivery/Delete', [DeliveryController::class, 'delete']);
Route::post('/Delivery/GetSatuan', [DeliveryController::class, 'getProductDetail']);
Route::post('/Delivery/ExportDelivery', [DeliveryController::class, 'exportDataDelivery'])->name('Delivery.Export');
Route::post('/Delivery/GetIndexList', [DeliveryController::class, 'getIndexList']);
Route::resource('Delivery', DeliveryController::class);

//SalesInvoice
Route::get('/SalesInvoice/GetData', [SalesInvoiceController::class, 'getDataIndex']);
Route::get('/SalesInvoice/Add', [SalesInvoiceController::class, 'create']);
Route::post('/SalesInvoice/GetSalesOrder', [SalesInvoiceController::class, 'getSalesOrder']);
Route::post('/SalesInvoice/GetSalesOrderData', [SalesInvoiceController::class, 'getSalesOrderData']);
Route::post('/SalesInvoice/GetProductBySupplier', [SalesInvoiceController::class, 'getProductSupplier']);
Route::post('/SalesInvoice/GetDelivery', [SalesInvoiceController::class, 'getDelivery']);
Route::post('/SalesInvoice/GetDataDelivery', [SalesInvoiceController::class, 'getDataDelivery']);
Route::post('/SalesInvoice/GetListTerms', [SalesInvoiceController::class, 'getListTerms']);
Route::post('/SalesInvoice/GetTerms', [SalesInvoiceController::class, 'getTerms']);
Route::post('/SalesInvoice/GetTermsByOption', [SalesInvoiceController::class, 'getTermsByOpt']);
Route::post('/SalesInvoice/GetDefaultAddress', [SalesInvoiceController::class, 'getDefaultAddress']);
Route::get('/SalesInvoice/Detail/{id}', [SalesInvoiceController::class, 'detail'])->name('SalesInvoice.Detail');
Route::post('/SalesInvoice/Posting/{id}', [SalesInvoiceController::class, 'posting'])->name('SalesInvoice.Posting');
Route::post('/SalesInvoice/StoreDetail', [SalesInvoiceController::class, 'StoreInvoiceDetail']);
Route::post('/SalesInvoice/DeleteDetail', [SalesInvoiceController::class, 'DeleteInvoiceDetail']);
Route::post('/SalesInvoice/RestoreDetail', [SalesInvoiceController::class, 'RestoreInvoiceDetail']);
Route::post('/SalesInvoice/SetDetail', [SalesInvoiceController::class, 'SetInvoiceDetail']);
Route::post('/SalesInvoice/GetDetail', [SalesInvoiceController::class, 'GetInvoiceDetail']);
Route::post('/SalesInvoice/GetDetailDelivery', [SalesInvoiceController::class, 'GetDeliveryDetail']);
Route::post('/SalesInvoice/ResetDetail', [SalesInvoiceController::class, 'ResetSalesInvoiceDetail']);
Route::post('/SalesInvoice/GetDataFooter', [SalesInvoiceController::class, 'GetInvoiceFooter']);
Route::post('/SalesInvoice/GetInvoiceDate', [SalesInvoiceController::class, 'GetDate']);
Route::get('/SalesInvoice/Cetak/{id}', [SalesInvoiceController::class, 'cetak'])->name('SalesInvoice.Cetak');
Route::post('/SalesInvoice/Delete', [SalesInvoiceController::class, 'delete']);
Route::post('/SalesInvoice/ExportSalesInvoice', [SalesInvoiceController::class, 'exportDataSalesInvoice'])->name('SalesInvoice.Export');
Route::resource('SalesInvoice', SalesInvoiceController::class);

//SalesInvoiceCollection
Route::get('/SalesInvoiceCollection/GetData', [SalesInvoiceCollectionController::class, 'getDataIndex']);
Route::get('/SalesInvoiceCollection/Add', [SalesInvoiceCollectionController::class, 'create']);
Route::post('/SalesInvoiceCollection/GetInvoice', [SalesInvoiceCollectionController::class, 'getInvoice']);
Route::post('/SalesInvoiceCollection/GetInvoiceData', [SalesInvoiceCollectionController::class, 'getInvoiceData']);
Route::post('/SalesInvoiceCollection/GetDefaultAddress', [SalesInvoiceCollectionController::class, 'getDefaultAddress']);
Route::post('/SalesInvoiceCollection/GetCustomerAddress', [SalesInvoiceCollectionController::class, 'getCustomerAddress']);
Route::get('/SalesInvoiceCollection/Detail/{id}', [SalesInvoiceCollectionController::class, 'detail'])->name('SalesInvoiceCollection.Detail');
Route::post('/SalesInvoiceCollection/Posting/{id}', [SalesInvoiceCollectionController::class, 'posting'])->name('SalesInvoiceCollection.Posting');
Route::post('/SalesInvoiceCollection/StoreDetail', [SalesInvoiceCollectionController::class, 'StoreInvoiceDetail']);
Route::post('/SalesInvoiceCollection/DeleteDetail', [SalesInvoiceCollectionController::class, 'DeleteInvoiceDetail']);
Route::post('/SalesInvoiceCollection/RestoreDetail', [SalesInvoiceCollectionController::class, 'RestoreInvoiceDetail']);
Route::post('/SalesInvoiceCollection/SetDetail', [SalesInvoiceCollectionController::class, 'SetCollectionDetail']);
Route::post('/SalesInvoiceCollection/GetDetail', [SalesInvoiceCollectionController::class, 'GetInvoiceDetail']);
Route::post('/SalesInvoiceCollection/GetDataFooter', [SalesInvoiceCollectionController::class, 'GetInvoiceFooter']);
Route::post('/SalesInvoiceCollection/ConfirmCollection', [SalesInvoiceCollectionController::class, 'confirm']);
Route::post('/SalesInvoiceCollection/GetInvoiceDate', [SalesInvoiceCollectionController::class, 'GetDate']);
Route::post('/SalesInvoiceCollection/ResetDetail', [SalesInvoiceCollectionController::class, 'ResetSalesInvoiceCollectionDetail']);
Route::get('/SalesInvoiceCollection/CetakKwitansi/{id}', [SalesInvoiceCollectionController::class, 'cetakKwitansi'])->name('SalesInvoiceCollection.CetakKwitansi');
Route::get('/SalesInvoiceCollection/Cetak/{id}', [SalesInvoiceCollectionController::class, 'cetak'])->name('SalesInvoiceCollection.Cetak');
Route::get('/SalesInvoiceCollection/CetakKwitansi/{id}', [SalesInvoiceCollectionController::class, 'cetakKwitansi'])->name('SalesInvoiceCollection.CetakKwitansi');
Route::post('/SalesInvoiceCollection/CetakPreview', [SalesInvoiceCollectionController::class, 'preview']);
Route::post('/SalesInvoiceCollection/Delete', [SalesInvoiceCollectionController::class, 'delete']);
Route::resource('SalesInvoiceCollection', SalesInvoiceCollectionController::class);

//AccountReceiveable
Route::get('/AccountReceiveable/GetData', [AccountReceiveableController::class, 'getDataIndex']);
Route::post('/AccountReceiveable/GetInvoiceData', [AccountReceiveableController::class, 'getInvoiceData']);
Route::post('/AccountReceiveable/GetCostData', [AccountReceiveableController::class, 'getCostData']);
Route::post('/AccountReceiveable/GetPaymentData', [AccountReceiveableController::class, 'getPaymentData']);
Route::post('/AccountReceiveable/GetListCost', [AccountReceiveableController::class, 'getCostList']);
Route::get('/AccountReceiveable/Detail/{id}', [AccountReceiveableController::class, 'detail'])->name('AccountReceiveable.Detail');
Route::post('/AccountReceiveable/StoreDetail', [AccountReceiveableController::class, 'StoreAccountReceiveable']);
Route::post('/AccountReceiveable/StoreCost', [AccountReceiveableController::class, 'StoreAccountReceiveableCost']);
Route::post('/AccountReceiveable/GetDetail', [AccountReceiveableController::class, 'getDataTagihan']);
Route::post('/AccountReceiveable/GetDetailLunas', [AccountReceiveableController::class, 'getDataTagihanLunas']);
Route::post('/AccountReceiveable/GetTagihanByCustomer', [AccountReceiveableController::class, 'getDataTagihanCustomer']);
Route::post('/AccountReceiveable/GetDetailMass', [AccountReceiveableController::class, 'getDataTagihanMass']);
Route::post('/AccountReceiveable/SetDataMass', [AccountReceiveableController::class, 'setDataTagihanMass']);
Route::post('/AccountReceiveable/GetDataMass', [AccountReceiveableController::class, 'GetDataMass']);
Route::post('/AccountReceiveable/AlocatePayment', [AccountReceiveableController::class, 'AlocatePayment']);
Route::post('/AccountReceiveable/StoreDetailMass', [AccountReceiveableController::class, 'StoreAccountReceiveableMass']);
Route::post('/AccountReceiveable/GetDetailMass', [AccountReceiveableController::class, 'getDataTagihanMass']);
Route::post('/AccountReceiveable/SetDataMass', [AccountReceiveableController::class, 'setDataTagihanMass']);
Route::post('/AccountReceiveable/GetDataMass', [AccountReceiveableController::class, 'GetDataMass']);
Route::post('/AccountReceiveable/AlocatePayment', [AccountReceiveableController::class, 'AlocatePayment']);
Route::post('/AccountReceiveable/StoreDetailMass', [AccountReceiveableController::class, 'StoreAccountReceiveableMass']);
Route::post('/AccountReceiveable/CancelPayment', [AccountReceiveableController::class, 'CancelPayment']);
Route::get('/AccountReceiveable/GroupPayment', [AccountReceiveableController::class, 'indexGroup'])->name('AccountReceiveable.GroupPayment');
Route::get('/AccountReceiveable/GetDataGroup', [AccountReceiveableController::class, 'getDataIndexGroup']);
Route::get('/AccountReceiveable/DetailGroup/{id}', [AccountReceiveableController::class, 'detailGroup'])->name('AccountReceiveable.DetailGroup');
Route::post('/AccountReceiveable/GetTagihanByCustomerGroup', [AccountReceiveableController::class, 'getDataTagihanCustomerGroup']);
Route::post('/AccountReceiveable/GetDetailGroup', [AccountReceiveableController::class, 'getDataTagihanGroup']);
Route::post('/AccountReceiveable/GetDetailLunasGroup', [AccountReceiveableController::class, 'getDataTagihanLunasGroup']);
Route::post('/AccountReceiveable/StoreDetailMassGroup', [AccountReceiveableController::class, 'StoreAccountReceiveableMassGroup']);
Route::resource('AccountReceiveable', AccountReceiveableController::class);

//AccountReceiveable
Route::get('/AccountPayable/GetData', [AccountPayableController::class, 'getDataIndex']);
Route::post('/AccountPayable/GetInvoiceData', [AccountPayableController::class, 'getInvoiceData']);
Route::post('/AccountPayable/GetCostData', [AccountPayableController::class, 'getCostData']);
Route::post('/AccountPayable/GetPaymentData', [AccountPayableController::class, 'getPaymentData']);
Route::post('/AccountPayable/GetListCost', [AccountPayableController::class, 'getCostList']);
Route::get('/AccountPayable/Detail/{id}', [AccountPayableController::class, 'detail'])->name('AccountPayable.Detail');
Route::post('/AccountPayable/StoreDetail', [AccountPayableController::class, 'StoreAccountPayable']);
Route::post('/AccountPayable/StoreCost', [AccountPayableController::class, 'StoreAccountPayableCost']);
Route::post('/AccountPayable/GetDetailLunas', [AccountPayableController::class, 'getDataTagihanLunas']);
Route::post('/AccountPayable/GetDetail', [AccountPayableController::class, 'getDataTagihan']);
Route::post('/AccountPayable/GetDetailMass', [AccountPayableController::class, 'getDataTagihanMass']);
Route::post('/AccountPayable/GetTagihanBySupplier', [AccountPayableController::class, 'getDataTagihanSupplier']);
Route::post('/AccountPayable/SetDataMass', [AccountPayableController::class, 'setDataTagihanMass']);
Route::post('/AccountPayable/GetDataMass', [AccountPayableController::class, 'GetDataMass']);
Route::post('/AccountPayable/AlocatePayment', [AccountPayableController::class, 'AlocatePayment']);
Route::post('/AccountPayable/StoreDetailMass', [AccountPayableController::class, 'StoreAccountPayableMass']);
Route::post('/AccountPayable/CancelPayment', [AccountPayableController::class, 'CancelPayment']);
Route::resource('AccountPayable', AccountPayableController::class);

//TaxSettings
Route::post('/TaxSettings/GetPrefAddress', [TaxSettingsController::class, 'getAddress']);
Route::post('/TaxSettings/GetPPN', [TaxSettingsController::class, 'getPPn']);
Route::post('/TaxSettings/AddPPN', [TaxSettingsController::class, 'SavePPn']);
Route::resource('TaxSettings', TaxSettingsController::class);

//TaxSerialNumber
Route::get('/TaxSerialNumber/Add', [TaxSerialNumberController::class, 'create']);
Route::get('/TaxSerialNumber/Detail/{id}', [TaxSerialNumberController::class, 'detail'])->name('TaxSerialNumber.Detail');
Route::post('/TaxSerialNumber/Posting/{id}', [TaxSerialNumberController::class, 'posting'])->name('TaxSerialNumber.Posting');
Route::get('/TaxSerialNumber/GetData', [TaxSerialNumberController::class, 'getDataIndex']);
Route::get('/MassGenerateFP', [TaxSerialNumberController::class, 'indexMassGenerate'])->name('TaxSerialNumber.MassGenerateFP');
Route::post('/TaxSerialNumber/MassGenerate', [TaxSerialNumberController::class, 'MassGenerateTaxInvoice']);
Route::get('/FakturPajak', [TaxSerialNumberController::class, 'indexFP'])->name('TaxSerialNumber.indexFP');
Route::get('/FakturPajak/GetData', [TaxSerialNumberController::class, 'getDataIndexFP']);
Route::post('/FakturPajak/ExportFakturPajak', [TaxSerialNumberController::class, 'exportDataFP'])->name('FakturPajak.Export');
Route::post('/FakturPajak/ExportFakturPajakXML', [TaxSerialNumberController::class, 'exportDataFPXML'])->name('FakturPajakXML.Export');
Route::get('/FakturPajak/Detail/{id}', [TaxSerialNumberController::class, 'detailFP'])->name('FakturPajak.Detail');
Route::post('/FakturPajak/Posting/{id}', [TaxSerialNumberController::class, 'postingFP'])->name('FakturPajak.Posting');
Route::resource('TaxSerialNumber', TaxSerialNumberController::class);

//Stock
Route::get('/Stock/GetData', [StockController::class, 'getDataIndex']);
Route::post('/Stock/GetDataPerIndex', [StockController::class, 'getDataPerIndex']);
Route::get('/Stock/Detail/{id}/{idSatuan}', [StockController::class, 'detail'])->name('Stock.Detail');
Route::get('/Stock/DetailAdjustment/{id}', [StockController::class, 'detailAdjustment'])->name('StockAdjustment.Detail');
Route::get('/Stock/Mutasi', [StockController::class, 'indexMutasi'])->name('Stock.indexMutasi');
Route::get('/Stock/Adjustment', [StockController::class, 'indexAdjustment'])->name('Stock.indexAdjustment');
Route::get('/Stock/Adjustment/Add', [StockController::class, 'create']);
Route::post('/Stock/GetAdjustment', [StockController::class, 'getAdjustment']);
Route::get('/Stock/GetDataAdjustment', [StockController::class, 'getDataIndexAdjustment']);
Route::post('/Stock/GetStockDetailPerItem', [StockController::class, 'getStockDetailPerItem']);
Route::post('/Stock/GetMutasiStock', [StockController::class, 'getMutasiStock']);
Route::post('/Stock/GetDataProduct', [StockController::class, 'getDataProduct']);
Route::post('/Stock/GetStockTransaction', [StockController::class, 'getStockTransaction']);
Route::post('/Stock/StoreAdjustment', [StockController::class, 'StoreAdjustment']);
Route::post('/Stock/DeleteAdjustment', [StockController::class, 'DeleteAdjustment']);
Route::post('/Stock/ExportStockCard', [StockController::class, 'exportStockCard'])->name('StockCard.Export');
Route::get('/OutstandingSO', [StockController::class, 'indexOutstandingSO'])->name('Stock.indexOutstandingSO');
Route::get('/OutstandingSO/GetDataOutstandingSO', [StockController::class, 'getDataIndexOutstandingSO']);
Route::post('/OutstandingSO/ExportOutstanding', [StockController::class, 'exportOutstandingSO'])->name('OutstandingSO.Export');
Route::get('/OutstandingPO', [StockController::class, 'indexOutstandingPO'])->name('Stock.indexOutstandingPO');
Route::get('/OutstandingPO/GetDataOutstandingPO', [StockController::class, 'getDataIndexOutstandingPO']);
Route::post('/OutstandingPO/ExportOutstanding', [StockController::class, 'exportOutstandingPO'])->name('OutstandingPO.Export');
Route::resource('Stock', StockController::class);

//Stock Conversion
Route::get('/StockConversion/GetData', [StockConversionController::class, 'getDataIndex']);
Route::get('/StockConversion/Add', [StockConversionController::class, 'create']);
Route::get('/StockConversion/Detail/{id}', [StockConversionController::class, 'detail'])->name('StockConversion.Detail');
Route::post('/StockConversion/Posting/{id}', [StockConversionController::class, 'posting'])->name('StockConversion.Posting');
Route::post('/StockConversion/GetDetailFrom', [StockConversionController::class, 'GetConversionDetailFrom']);
Route::post('/StockConversion/GetDetailTo', [StockConversionController::class, 'GetConversionDetailTo']);
Route::post('/StockConversion/StoreDetail', [StockConversionController::class, 'StoreConversionDetail']);
Route::post('/StockConversion/UpdateDetail', [StockConversionController::class, 'UpdateConversionDetail']);
Route::post('/StockConversion/DeleteDetail', [StockConversionController::class, 'DeleteConversionDetail']);
Route::post('/StockConversion/EditDetail', [StockConversionController::class, 'EditConversionDetail']);
Route::post('/StockConversion/RestoreDetail', [StockConversionController::class, 'RestoreConversionDetail']);
Route::post('/StockConversion/ResetDetail', [StockConversionController::class, 'ResetConversionDetail']);
Route::resource('StockConversion', StockConversionController::class);

//Laporan
Route::post('/DeliveryReport/GetDeliveryReport', [DeliveryReportController::class, 'getDataDeliveryReport']);
Route::post('/DeliveryReport/ExportDeliveryReport', [DeliveryReportController::class, 'exportDataDeliveryReport'])->name('DeliveryReport.Export');
Route::resource('DeliveryReport', DeliveryReportController::class);

Route::post('/ReceivingReport/GetReceivingReport', [ReceivingReportController::class, 'getDataReceivingReport']);
Route::post('/ReceivingReport/ExportReceivingReport', [ReceivingReportController::class, 'exportDataReceivingReport'])->name('ReceivingReport.Export');
Route::resource('ReceivingReport', ReceivingReportController::class);

Route::post('/ReportSales/GetSalesReport', [SalesReportController::class, 'getDataSalesReport']);
Route::post('/ReportSales/ExportSalesReport', [SalesReportController::class, 'exportDataSalesReport'])->name('ReportSales.Export');
Route::resource('ReportSales', SalesReportController::class);

Route::post('/ReportSalesDetail/GetSalesReportDetail', [SalesReportDetailController::class, 'getDataSalesReportDetail']);
Route::post('/ReportSalesDetail/ExportSalesDetailReport', [SalesReportDetailController::class, 'exportDataSalesDetailReport'])->name('ReportSalesDetail.Export');
Route::resource('ReportSalesDetail', SalesReportDetailController::class);

Route::post('/ReportPurchasing/GetPurchasingReport', [PurchaseReportController::class, 'getDataPurchasingReport']);
Route::post('/ReportPurchasing/ExportPurchasingReport', [PurchaseReportController::class, 'exportDataPurchasingReport'])->name('ReportPurchasing.Export');
Route::resource('ReportPurchasing', PurchaseReportController::class);

Route::post('/ReportPurchasingDetail/GetPurchasingReportDetail', [PurchaseReportDetailController::class, 'getDataPurchasingReportDetail']);
Route::post('/ReportPurchasingDetail/ExportPurchasingDetailReport', [PurchaseReportDetailController::class, 'exportDataPurchasingDetailReport'])->name('ReportPurchasingDetail.Export');
Route::resource('ReportPurchasingDetail', PurchaseReportDetailController::class);

Route::post('/RekapPenjualanBarang/GetDetail', [RekapPenjualanController::class, 'getDetailRekapPenjualanBarang']);
Route::post('/RekapPenjualanBarang/GetDetailBarang', [RekapPenjualanController::class, 'getDetailRekapBarang']);
Route::post('/RekapPenjualanBarang/GetDetailLokasi', [RekapPenjualanController::class, 'getDetailRekapLokasi']);
Route::post('/RekapPenjualanBarang/ExportBarang', [RekapPenjualanController::class, 'exportDataRekap'])->name('RekapPenjualanBarang.Export');
Route::get('/RekapPenjualanBarang', [RekapPenjualanController::class, 'indexBarang'])->name('RekapPenjualan.indexBarang');

Route::post('/RekapPenjualanCustomer/GetDetail', [RekapPenjualanController::class, 'getDetailRekapPenjualanCustomer']);
Route::post('/RekapPenjualanCustomer/GetDetailCustomer', [RekapPenjualanController::class, 'getDetailRekapCustomer']);
Route::get('/RekapPenjualanCustomer', [RekapPenjualanController::class, 'indexCustomer'])->name('RekapPenjualan.indexCustomer');

Route::resource('RekapPenjualan', RekapPenjualanController::class);

Route::post('/RekapPembelianBarang/GetDetail', [RekapPembelianController::class, 'getDetailRekapPembelianBarang']);
Route::post('/RekapPembelianBarang/GetDetailBarang', [RekapPembelianController::class, 'getDetailRekapBarang']);
Route::get('/RekapPembelianBarang', [RekapPembelianController::class, 'indexBarang'])->name('RekapPembelian.indexBarang');

Route::post('/RekapPembelianSupplier/GetDetail', [RekapPembelianController::class, 'getDetailRekapPembelianSupplier']);
Route::post('/RekapPembelianSupplier/GetDetailSupplier', [RekapPembelianController::class, 'getDetailRekapSupplier']);
Route::get('/RekapPembelianSupplier', [RekapPembelianController::class, 'indexSupplier'])->name('RekapPembelian.indexSupplier');

Route::resource('RekapPembelian', RekapPembelianController::class);

Route::post('/ReportPurchaseCollection/GetCollectionReport', [PurchaseCollectionReportController::class, 'getDataCollectionReport']);
Route::post('/ReportPurchaseCollection/ExportCollectionReport', [PurchaseCollectionReportController::class, 'exportDataCollectionReport'])->name('ReportPurchaseCollection.Export');
Route::resource('ReportPurchaseCollection', PurchaseCollectionReportController::class);

//Data Index
Route::get('/DataIndex', [DataIndexController::class, 'index']);
Route::get('/DataIndex/Add', [DataIndexController::class, 'create']);
Route::get('/DataIndex/GetData',[DataIndexController::class, 'getDataIndex']);
Route::post('/DataIndex/Delete',[DataIndexController::class, 'delete']);
Route::resource('DataIndex', DataIndexController::class);

//Index Persediaan
Route::get('/StockIndex', [StockIndexController::class, 'index']);
Route::get('/StockIndex/Add', [StockIndexController::class, 'create']);
Route::get('/StockIndex/GetData',[StockIndexController::class, 'getStockIndex']);
Route::post('/StockIndex/GetParent',[StockIndexController::class, 'getParentIndex']);
Route::post('/StockIndex/Delete',[StockIndexController::class, 'delete']);
Route::resource('StockIndex', StockIndexController::class);

//ExpeditionCost
Route::get('/ExpeditionCost/GetData', [ExpeditionCostController::class, 'getDataIndex']);
Route::get('/ExpeditionCost/Add', [ExpeditionCostController::class, 'create']);
Route::post('/ExpeditionCost/GetDelivery', [ExpeditionCostController::class, 'getDelivery']);
Route::post('/ExpeditionCost/GetTarif', [ExpeditionCostController::class, 'getTarif']);
Route::post('/ExpeditionCost/GetNominalTarif', [ExpeditionCostController::class, 'getNominalTarif']);
Route::post('/ExpeditionCost/GetExpeditionAddress', [ExpeditionCostController::class, 'getExpeditionAddress']);
Route::get('/ExpeditionCost/Detail/{id}', [ExpeditionCostController::class, 'detail'])->name('ExpeditionCost.Detail');
Route::post('/ExpeditionCost/Posting/{id}', [ExpeditionCostController::class, 'posting'])->name('ExpeditionCost.Posting');
Route::post('/ExpeditionCost/StoreDetail', [ExpeditionCostController::class, 'StoreCostDetail']);
Route::post('/ExpeditionCost/UpdateDetail', [ExpeditionCostController::class, 'UpdateCostDetail']);
Route::post('/ExpeditionCost/EditDetail', [ExpeditionCostController::class, 'EditCostDetail']);
Route::post('/ExpeditionCost/DeleteDetail', [ExpeditionCostController::class, 'DeleteCostDetail']);
Route::post('/ExpeditionCost/RestoreDetail', [ExpeditionCostController::class, 'RestoreCostDetail']);
Route::post('/ExpeditionCost/GetDetail', [ExpeditionCostController::class, 'GetCostDetail']);
Route::post('/ExpeditionCost/GetDataDelivery', [ExpeditionCostController::class, 'GetDeliveryDetail']);
Route::post('/ExpeditionCost/ResetDetail', [ExpeditionCostController::class, 'ResetExpeditionCostDetail']);
Route::post('/ExpeditionCost/GetDataFooter', [ExpeditionCostController::class, 'GetCostFooter']);
Route::get('/ExpeditionCost/Cetak/{id}', [ExpeditionCostController::class, 'cetak'])->name('ExpeditionCost.Cetak');
Route::post('/ExpeditionCost/Delete', [ExpeditionCostController::class, 'delete']);
Route::post('/ExpeditionCost/InputResi', [ExpeditionCostController::class, 'InputResi']);
Route::resource('ExpeditionCost', ExpeditionCostController::class);

//StockTransfer
Route::get('/StockTransfer/GetData', [StockTransferController::class, 'getDataIndex']);
Route::get('/StockTransfer/Add', [StockTransferController::class, 'create']);
Route::get('/StockTransfer/Detail/{id}', [StockTransferController::class, 'detail'])->name('StockTransfer.Detail');
Route::post('/StockTransfer/Posting/{id}', [StockTransferController::class, 'posting'])->name('StockTransfer.Posting');
Route::post('/StockTransfer/StoreDetail', [StockTransferController::class, 'StoreTransferDetail']);
Route::post('/StockTransfer/UpdateDetail', [StockTransferController::class, 'UpdateTransferDetail']);
Route::post('/StockTransfer/EditDetail', [StockTransferController::class, 'EditTransferDetail']);
Route::post('/StockTransfer/DeleteDetail', [StockTransferController::class, 'DeleteTransferDetail']);
Route::post('/StockTransfer/RestoreDetail', [StockTransferController::class, 'RestoreTransferDetail']);
Route::post('/StockTransfer/GetDetail', [StockTransferController::class, 'GetTransferDetail']);
Route::post('/StockTransfer/ResetDetail', [StockTransferController::class, 'ResetStockTransferDetail']);
Route::post('/StockTransfer/Delete', [StockTransferController::class, 'delete']);
Route::post('/StockTransfer/GetDataProduct', [StockTransferController::class, 'getDataProduct']);
Route::post('/StockTransfer/GetStock', [StockTransferController::class, 'getStockItem']);
Route::post('/StockTransfer/GetIndexList', [StockTransferController::class, 'getIndexList']);
Route::resource('StockTransfer', StockTransferController::class);

//SalesReturn
Route::get('/SalesReturnItem/GetData', [SalesReturnItemController::class, 'getDataIndex']);
Route::get('/SalesReturnItem/Add', [SalesReturnItemController::class, 'create']);
Route::post('/SalesReturnItem/GetProductByCustomer', [SalesReturnItemController::class, 'getProductCustomer']);
Route::post('/SalesReturnItem/GetProductHistory', [SalesReturnItemController::class, 'getProductHistory']);
Route::post('/SalesReturnItem/GetDataItem', [SalesReturnItemController::class, 'getDataItem']);
Route::get('/SalesReturnItem/Detail/{id}', [SalesReturnItemController::class, 'detail'])->name('SalesReturnItem.Detail');
Route::get('/SalesReturnItem/Print/{id}', [SalesReturnItemController::class, 'print'])->name('SalesReturnItem.Print'); //test tambahan
Route::get('/SalesReturnItem/Cetak/{id}', [SalesReturnItemController::class, 'cetak'])->name('SalesReturnItem.Cetak');
Route::post('/SalesReturnItem/Posting/{id}', [SalesReturnItemController::class, 'posting'])->name('SalesReturnItem.Posting');
Route::post('/SalesReturnItem/RestoreDetail', [SalesReturnItemController::class, 'RestoreSalesReturnItemDetail']);
Route::post('/SalesReturnItem/StoreDetail', [SalesReturnItemController::class, 'StoreSalesReturnItemDetail']);
Route::post('/SalesReturnItem/UpdateDetail', [SalesReturnItemController::class, 'UpdateSalesReturnItemDetail']);
Route::post('/SalesReturnItem/DeleteDetail', [SalesReturnItemController::class, 'DeleteSalesReturnItemDetail']);
Route::post('/SalesReturnItem/ResetDetail', [SalesReturnItemController::class, 'ResetSalesReturnItemDetail']);
Route::post('/SalesReturnItem/EditDetail', [SalesReturnItemController::class, 'EditSalesReturnItemDetail']);
Route::post('/SalesReturnItem/GetDetail', [SalesReturnItemController::class, 'GetSalesReturnItemDetail']);
Route::post('/SalesReturnItem/GetDataFooter', [SalesReturnItemController::class, 'GetSalesReturnItemFooter']);
Route::post('/SalesReturnItem/Delete', [SalesReturnItemController::class, 'delete']);
Route::post('/SalesReturnItem/GetCustomerPreviousOrder', [SalesReturnItemController::class, 'getPreviousOrder']);
Route::post('/SalesReturnItem/GetSatuan', [SalesReturnItemController::class, 'getProductDetail']);
Route::resource('SalesReturnItem', SalesReturnItemController::class);

Route::get('/SalesReturn/GetData', [SalesReturnController::class, 'getDataIndex']);
Route::get('/SalesReturn/Add', [SalesReturnController::class, 'create']);
Route::post('/SalesReturn/GetReturnByCustomer', [SalesReturnController::class, 'getCustomerReturn']);
Route::post('/SalesReturn/GetProductHistory', [SalesReturnController::class, 'getProductHistory']);
Route::post('/SalesReturn/GetDataItem', [SalesReturnController::class, 'getDataItem']);
Route::get('/SalesReturn/Detail/{id}', [SalesReturnController::class, 'detail'])->name('SalesReturn.Detail');
Route::get('/SalesReturn/Print/{id}', [SalesReturnController::class, 'print'])->name('SalesReturn.Print'); //test tambahan
Route::get('/SalesReturn/Cetak/{id}', [SalesReturnController::class, 'cetak'])->name('SalesReturn.Cetak');
Route::post('/SalesReturn/Posting/{id}', [SalesReturnController::class, 'posting'])->name('SalesReturn.Posting');
Route::post('/SalesReturn/RestoreDetail', [SalesReturnController::class, 'RestoreSalesReturnDetail']);
Route::post('/SalesReturn/StoreDetail', [SalesReturnController::class, 'StoreSalesReturnDetail']);
Route::post('/SalesReturn/UpdateDetail', [SalesReturnController::class, 'UpdateSalesReturnDetail']);
Route::post('/SalesReturn/DeleteDetail', [SalesReturnController::class, 'DeleteSalesReturnDetail']);
Route::post('/SalesReturn/ResetDetail', [SalesReturnController::class, 'ResetSalesReturnDetail']);
Route::post('/SalesReturn/EditDetail', [SalesReturnController::class, 'EditSalesReturnDetail']);
Route::post('/SalesReturn/GetDetail', [SalesReturnController::class, 'GetSalesReturnDetail']);
Route::post('/SalesReturn/GetDataFooter', [SalesReturnController::class, 'GetSalesReturnFooter']);
Route::post('/SalesReturn/Delete', [SalesReturnController::class, 'delete']);
Route::post('/SalesReturn/GetCustomerPreviousOrder', [SalesReturnController::class, 'getPreviousOrder']);
Route::post('/SalesReturn/GetSatuan', [SalesReturnController::class, 'getProductDetail']);
Route::post('/SalesReturn/SetDetail', [SalesReturnController::class, 'SetSalesReturnDetail']);
Route::resource('SalesReturn', SalesReturnController::class);

//PurchaseReturn
Route::get('/PurchaseReturnItem/GetData', [PurchaseReturnItemController::class, 'getDataIndex']);
Route::get('/PurchaseReturnItem/Add', [PurchaseReturnItemController::class, 'create']);
Route::post('/PurchaseReturnItem/GetProductBySupplier', [PurchaseReturnItemController::class, 'getProductSupplier']);
Route::post('/PurchaseReturnItem/GetProductHistory', [PurchaseReturnItemController::class, 'getProductHistory']);
Route::post('/PurchaseReturnItem/GetDataItem', [PurchaseReturnItemController::class, 'getDataItem']);
Route::get('/PurchaseReturnItem/Detail/{id}', [PurchaseReturnItemController::class, 'detail'])->name('PurchaseReturnItem.Detail');
Route::get('/PurchaseReturnItem/Print/{id}', [PurchaseReturnItemController::class, 'print'])->name('PurchaseReturnItem.Print'); //test tambahan
Route::get('/PurchaseReturnItem/Cetak/{id}', [PurchaseReturnItemController::class, 'cetak'])->name('PurchaseReturnItem.Cetak');
Route::post('/PurchaseReturnItem/Posting/{id}', [PurchaseReturnItemController::class, 'posting'])->name('PurchaseReturnItem.Posting');
Route::post('/PurchaseReturnItem/RestoreDetail', [PurchaseReturnItemController::class, 'RestorePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/StoreDetail', [PurchaseReturnItemController::class, 'StorePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/UpdateDetail', [PurchaseReturnItemController::class, 'UpdatePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/DeleteDetail', [PurchaseReturnItemController::class, 'DeletePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/ResetDetail', [PurchaseReturnItemController::class, 'ResetPurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/EditDetail', [PurchaseReturnItemController::class, 'EditPurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/GetDetail', [PurchaseReturnItemController::class, 'GetPurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/GetDataFooter', [PurchaseReturnItemController::class, 'GetPurchaseReturnItemFooter']);
Route::post('/PurchaseReturnItem/Delete', [PurchaseReturnItemController::class, 'delete']);
Route::resource('PurchaseReturnItem', PurchaseReturnItemController::class);

Route::get('/PurchaseReturn/GetData', [PurchaseReturnController::class, 'getDataIndex']);
Route::get('/PurchaseReturn/Add', [PurchaseReturnController::class, 'create']);
Route::post('/PurchaseReturn/GetReturnBySupplier', [PurchaseReturnController::class, 'getSupplierReturn']);
Route::post('/PurchaseReturn/GetProductHistory', [PurchaseReturnController::class, 'getProductHistory']);
Route::post('/PurchaseReturn/GetDataItem', [PurchaseReturnController::class, 'getDataItem']);
Route::get('/PurchaseReturn/Detail/{id}', [PurchaseReturnController::class, 'detail'])->name('PurchaseReturn.Detail');
Route::get('/PurchaseReturn/Print/{id}', [PurchaseReturnController::class, 'print'])->name('PurchaseReturn.Print'); //test tambahan
Route::get('/PurchaseReturn/Cetak/{id}', [PurchaseReturnController::class, 'cetak'])->name('PurchaseReturn.Cetak');
Route::post('/PurchaseReturn/Posting/{id}', [PurchaseReturnController::class, 'posting'])->name('PurchaseReturn.Posting');
Route::post('/PurchaseReturn/RestoreDetail', [PurchaseReturnController::class, 'RestorePurchaseReturnDetail']);
Route::post('/PurchaseReturn/StoreDetail', [PurchaseReturnController::class, 'StorePurchaseReturnDetail']);
Route::post('/PurchaseReturn/UpdateDetail', [PurchaseReturnController::class, 'UpdatePurchaseReturnDetail']);
Route::post('/PurchaseReturn/DeleteDetail', [PurchaseReturnController::class, 'DeletePurchaseReturnDetail']);
Route::post('/PurchaseReturn/ResetDetail', [PurchaseReturnController::class, 'ResetPurchaseReturnDetail']);
Route::post('/PurchaseReturn/EditDetail', [PurchaseReturnController::class, 'EditPurchaseReturnDetail']);
Route::post('/PurchaseReturn/GetDetail', [PurchaseReturnController::class, 'GetPurchaseReturnDetail']);
Route::post('/PurchaseReturn/GetDataFooter', [PurchaseReturnController::class, 'GetPurchaseReturnFooter']);
Route::post('/PurchaseReturn/Delete', [PurchaseReturnController::class, 'delete']);
Route::post('/PurchaseReturn/GetSupplierPreviousOrder', [PurchaseReturnController::class, 'getPreviousOrder']);
Route::post('/PurchaseReturn/GetSatuan', [PurchaseReturnController::class, 'getProductDetail']);
Route::post('/PurchaseReturn/SetDetail', [PurchaseReturnController::class, 'SetPurchaseReturnDetail']);
Route::resource('PurchaseReturn', PurchaseReturnController::class);

//GLAccountLevel
Route::get('/GLAccountLevel/Add', [GLAccountLevelController::class, 'create']);
Route::get('/GLAccountLevel/GetData', [GLAccountLevelController::class, 'getDataIndex']);
Route::post('/GLAccountLevel/Delete', [GLAccountLevelController::class, 'delete']);
Route::resource('GLAccountLevel', GLAccountLevelController::class);

//GLMotherAccount
Route::get('/GLMotherAccount', [GLMotherAccountController::class, 'index']);
Route::get('/GLMotherAccount/Add', [GLMotherAccountController::class, 'create']);
Route::get('/GLMotherAccount/GetData',[GLMotherAccountController::class, 'getDataIndex']);
Route::post('/GLMotherAccount/Delete',[GLMotherAccountController::class, 'delete']);
Route::resource('GLMotherAccount', GLMotherAccountController::class);

//GLMotherAccount
Route::get('/GLMotherAccount', [GLMotherAccountController::class, 'index']);
Route::get('/GLMotherAccount/Add', [GLMotherAccountController::class, 'create']);
Route::get('/GLMotherAccount/GetData',[GLMotherAccountController::class, 'getDataIndex']);
Route::post('/GLMotherAccount/Delete',[GLMotherAccountController::class, 'delete']);
Route::resource('GLMotherAccount', GLMotherAccountController::class);

//GLAccount
Route::get('/GLAccount', [GLAccountController::class, 'index']);
Route::get('/GLAccount/Add', [GLAccountController::class, 'create']);
Route::get('/GLAccount/GetData',[GLAccountController::class, 'getDataIndex']);
Route::post('/GLAccount/Delete',[GLAccountController::class, 'delete']);
Route::resource('GLAccount', GLAccountController::class);

//GLSubAccount
Route::get('/GLSubAccount', [GLSubAccountController::class, 'index']);
Route::get('/GLSubAccount/Add', [GLSubAccountController::class, 'create']);
Route::get('/GLSubAccount/GetData',[GLSubAccountController::class, 'getDataIndex']);
Route::post('/GLSubAccount/Delete',[GLSubAccountController::class, 'delete']);
Route::post('/GLSubAccount/GetParentAccounts',[GLSubAccountController::class, 'getParents']);
Route::resource('GLSubAccount', GLSubAccountController::class);

//GLAccountSettings
Route::get('/GLAccountSettings', [GLAccountSettingsController::class, 'index']);
Route::get('/GLAccountSettings/Add', [GLAccountSettingsController::class, 'create']);
Route::get('/GLAccountSettings/GetData',[GLAccountSettingsController::class, 'getDataIndex']);
Route::post('/GLAccountSettings/Delete',[GLAccountSettingsController::class, 'delete']);
Route::post('/GLAccountSettings/GetParentAccounts',[GLAccountSettingsController::class, 'getParents']);
Route::post('/GLAccountSettings/StoreDetail', [GLAccountSettingsController::class, 'StoreDetail']);
Route::post('/GLAccountSettings/UpdateDetail', [GLAccountSettingsController::class, 'UpdateDetail']);
Route::post('/GLAccountSettings/DeleteDetail', [GLAccountSettingsController::class, 'DeleteDetail']);
Route::post('/GLAccountSettings/EditDetail', [GLAccountSettingsController::class, 'EditDetail']);
Route::post('/GLAccountSettings/GetDetail', [GLAccountSettingsController::class, 'GetDetail']);
Route::post('/GLAccountSettings/GetSubAccount', [GLAccountSettingsController::class, 'getSubAccount']);
Route::resource('GLAccountSettings', GLAccountSettingsController::class);

//GLJournal
Route::get('/GLJournal/Add', [GLJournalController::class, 'create']);
Route::get('/GLJournal/Detail/{id}', [GLJournalController::class, 'detail'])->name('GLJournal.Detail');
Route::post('/GLJournal/Posting/{id}', [GLJournalController::class, 'posting'])->name('GLJournal.Posting');
Route::get('/GLJournal/GetData', [GLJournalController::class, 'getDataIndex']);
Route::get('/GLJournal/Generate', [GLJournalController::class, 'indexGenerate']);
Route::post('/GLJournal/ExecuteGenerate', [GLJournalController::class, 'ExecuteGenerate']);
Route::get('/GeneralLedger', [GLJournalController::class, 'indexGL'])->name('GLJournal.indexGL');
Route::post('/GLJournal/GetLedger', [GLJournalController::class, 'getGeneralLedger']);
Route::post('/GeneralLedger/Export', [GLJournalController::class, 'exportGL'])->name('GLJournal.ExportGL');
Route::post('/GLJournal/GetDetailJournal', [GLJournalController::class, 'displayEntry']);
Route::get('/GLJournal/CetakGL/{id}', [GLJournalController::class, 'cetakGL'])->name('GLJournal.CetakGL');
Route::get('/TrialBalance', [GLJournalController::class, 'indexTB'])->name('GLJournal.indexTB');
Route::post('/TrialBalance/Export', [GLJournalController::class, 'exportTB'])->name('GLJournal.ExportTB');
Route::get('/BalanceSheet', [GLJournalController::class, 'indexBS'])->name('GLJournal.indexBS');
Route::post('/BalanceSheet/Export', [GLJournalController::class, 'exportBS'])->name('GLJournal.ExportBS');
Route::resource('GLJournal', GLJournalController::class);

//GLKasBank
Route::get('/GLKasBank/Kas', [GLKasBankController::class, 'index']);
Route::get('/GLKasBank/Bank', [GLKasBankController::class, 'index']);
Route::get('/GLKasBank/Kas/Add', [GLKasBankController::class, 'createKas']);
Route::get('/GLKasBank/Bank/Add', [GLKasBankController::class, 'createBank']);
Route::get('/GLKasBank/GetData',[GLKasBankController::class, 'getDataIndex']);
Route::post('/GLKasBank/Delete',[GLKasBankController::class, 'delete']);
Route::post('/GLKasBank/Delete',[GLKasBankController::class, 'delete']);
Route::post('/GLKasBank/StoreDetail', [GLKasBankController::class, 'StoreDetail']);
Route::post('/GLKasBank/UpdateDetail', [GLKasBankController::class, 'UpdateDetail']);
Route::post('/GLKasBank/DeleteDetail', [GLKasBankController::class, 'DeleteDetail']);
Route::post('/GLKasBank/EditDetail', [GLKasBankController::class, 'EditDetail']);
Route::post('/GLKasBank/GetDetail', [GLKasBankController::class, 'GetDetail']);
Route::post('/GLKasBank/GetDataFooter', [GLKasBankController::class, 'GetFooter']);
Route::get('/GLKasBank/Detail/{id}', [GLKasBankController::class, 'detail'])->name('GLKasBank.Detail');
Route::post('/GLKasBank/Posting/{id}', [GLKasBankController::class, 'posting'])->name('GLKasBank.Posting');
Route::post('/GLKasBank/GetSubAccount', [GLKasBankController::class, 'getSubAccount']);
Route::get('/GLKasBank/Cetak/{id}', [GLKasBankController::class, 'cetak'])->name('GLKasBank.Cetak');
Route::post('/GLKasBank/ExportEntry', [GLKasBankController::class, 'export'])->name('GLKasBank.Export');
Route::resource('GLKasBank', GLKasBankController::class);


Route::get('/SalesReturnItem/GetData', [SalesReturnItemController::class, 'getDataIndex']);
Route::get('/SalesReturnItem/Add', [SalesReturnItemController::class, 'create']);
Route::post('/SalesReturnItem/GetProductByCustomer', [SalesReturnItemController::class, 'getProductCustomer']);
Route::post('/SalesReturnItem/GetProductHistory', [SalesReturnItemController::class, 'getProductHistory']);
Route::post('/SalesReturnItem/GetDataItem', [SalesReturnItemController::class, 'getDataItem']);
Route::get('/SalesReturnItem/Detail/{id}', [SalesReturnItemController::class, 'detail'])->name('SalesReturnItem.Detail');
Route::get('/SalesReturnItem/Print/{id}', [SalesReturnItemController::class, 'print'])->name('SalesReturnItem.Print'); //test tambahan
Route::get('/SalesReturnItem/Cetak/{id}', [SalesReturnItemController::class, 'cetak'])->name('SalesReturnItem.Cetak');
Route::post('/SalesReturnItem/Posting/{id}', [SalesReturnItemController::class, 'posting'])->name('SalesReturnItem.Posting');
Route::post('/SalesReturnItem/RestoreDetail', [SalesReturnItemController::class, 'RestoreSalesReturnItemDetail']);
Route::post('/SalesReturnItem/StoreDetail', [SalesReturnItemController::class, 'StoreSalesReturnItemDetail']);
Route::post('/SalesReturnItem/UpdateDetail', [SalesReturnItemController::class, 'UpdateSalesReturnItemDetail']);
Route::post('/SalesReturnItem/DeleteDetail', [SalesReturnItemController::class, 'DeleteSalesReturnItemDetail']);
Route::post('/SalesReturnItem/ResetDetail', [SalesReturnItemController::class, 'ResetSalesReturnItemDetail']);
Route::post('/SalesReturnItem/EditDetail', [SalesReturnItemController::class, 'EditSalesReturnItemDetail']);
Route::post('/SalesReturnItem/GetDetail', [SalesReturnItemController::class, 'GetSalesReturnItemDetail']);
Route::post('/SalesReturnItem/GetDataFooter', [SalesReturnItemController::class, 'GetSalesReturnItemFooter']);
Route::post('/SalesReturnItem/Delete', [SalesReturnItemController::class, 'delete']);
Route::post('/SalesReturnItem/GetCustomerPreviousOrder', [SalesReturnItemController::class, 'getPreviousOrder']);
Route::post('/SalesReturnItem/GetSatuan', [SalesReturnItemController::class, 'getProductDetail']);
Route::resource('SalesReturnItem', SalesReturnItemController::class);

Route::get('/SalesReturn/GetData', [SalesReturnController::class, 'getDataIndex']);
Route::get('/SalesReturn/Add', [SalesReturnController::class, 'create']);
Route::post('/SalesReturn/GetReturnByCustomer', [SalesReturnController::class, 'getCustomerReturn']);
Route::post('/SalesReturn/GetProductHistory', [SalesReturnController::class, 'getProductHistory']);
Route::post('/SalesReturn/GetDataItem', [SalesReturnController::class, 'getDataItem']);
Route::get('/SalesReturn/Detail/{id}', [SalesReturnController::class, 'detail'])->name('SalesReturn.Detail');
Route::get('/SalesReturn/Print/{id}', [SalesReturnController::class, 'print'])->name('SalesReturn.Print'); //test tambahan
Route::get('/SalesReturn/Cetak/{id}', [SalesReturnController::class, 'cetak'])->name('SalesReturn.Cetak');
Route::post('/SalesReturn/Posting/{id}', [SalesReturnController::class, 'posting'])->name('SalesReturn.Posting');
Route::post('/SalesReturn/RestoreDetail', [SalesReturnController::class, 'RestoreSalesReturnDetail']);
Route::post('/SalesReturn/StoreDetail', [SalesReturnController::class, 'StoreSalesReturnDetail']);
Route::post('/SalesReturn/UpdateDetail', [SalesReturnController::class, 'UpdateSalesReturnDetail']);
Route::post('/SalesReturn/DeleteDetail', [SalesReturnController::class, 'DeleteSalesReturnDetail']);
Route::post('/SalesReturn/ResetDetail', [SalesReturnController::class, 'ResetSalesReturnDetail']);
Route::post('/SalesReturn/EditDetail', [SalesReturnController::class, 'EditSalesReturnDetail']);
Route::post('/SalesReturn/GetDetail', [SalesReturnController::class, 'GetSalesReturnDetail']);
Route::post('/SalesReturn/GetDataFooter', [SalesReturnController::class, 'GetSalesReturnFooter']);
Route::post('/SalesReturn/Delete', [SalesReturnController::class, 'delete']);
Route::post('/SalesReturn/GetCustomerPreviousOrder', [SalesReturnController::class, 'getPreviousOrder']);
Route::post('/SalesReturn/GetSatuan', [SalesReturnController::class, 'getProductDetail']);
Route::post('/SalesReturn/SetDetail', [SalesReturnController::class, 'SetSalesReturnDetail']);
Route::resource('SalesReturn', SalesReturnController::class);

Route::get('/PurchaseReturnItem/GetData', [PurchaseReturnItemController::class, 'getDataIndex']);
Route::get('/PurchaseReturnItem/Add', [PurchaseReturnItemController::class, 'create']);
Route::post('/PurchaseReturnItem/GetProductBySupplier', [PurchaseReturnItemController::class, 'getProductSupplier']);
Route::post('/PurchaseReturnItem/GetProductHistory', [PurchaseReturnItemController::class, 'getProductHistory']);
Route::post('/PurchaseReturnItem/GetDataItem', [PurchaseReturnItemController::class, 'getDataItem']);
Route::get('/PurchaseReturnItem/Detail/{id}', [PurchaseReturnItemController::class, 'detail'])->name('PurchaseReturnItem.Detail');
Route::get('/PurchaseReturnItem/Print/{id}', [PurchaseReturnItemController::class, 'print'])->name('PurchaseReturnItem.Print'); //test tambahan
Route::get('/PurchaseReturnItem/Cetak/{id}', [PurchaseReturnItemController::class, 'cetak'])->name('PurchaseReturnItem.Cetak');
Route::post('/PurchaseReturnItem/Posting/{id}', [PurchaseReturnItemController::class, 'posting'])->name('PurchaseReturnItem.Posting');
Route::post('/PurchaseReturnItem/RestoreDetail', [PurchaseReturnItemController::class, 'RestorePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/StoreDetail', [PurchaseReturnItemController::class, 'StorePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/UpdateDetail', [PurchaseReturnItemController::class, 'UpdatePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/DeleteDetail', [PurchaseReturnItemController::class, 'DeletePurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/ResetDetail', [PurchaseReturnItemController::class, 'ResetPurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/EditDetail', [PurchaseReturnItemController::class, 'EditPurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/GetDetail', [PurchaseReturnItemController::class, 'GetPurchaseReturnItemDetail']);
Route::post('/PurchaseReturnItem/GetDataFooter', [PurchaseReturnItemController::class, 'GetPurchaseReturnItemFooter']);
Route::post('/PurchaseReturnItem/Delete', [PurchaseReturnItemController::class, 'delete']);
Route::post('/PurchaseReturnItem/GetSupplierPreviousOrder', [PurchaseReturnItemController::class, 'getPreviousOrder']);
Route::post('/PurchaseReturnItem/GetSatuan', [PurchaseReturnItemController::class, 'getProductDetail']);
Route::resource('PurchaseReturnItem', PurchaseReturnItemController::class);

Route::get('/PurchaseReturn/GetData', [PurchaseReturnController::class, 'getDataIndex']);
Route::get('/PurchaseReturn/Add', [PurchaseReturnController::class, 'create']);
Route::post('/PurchaseReturn/GetReturnBySupplier', [PurchaseReturnController::class, 'getSupplierReturn']);
Route::post('/PurchaseReturn/GetProductHistory', [PurchaseReturnController::class, 'getProductHistory']);
Route::post('/PurchaseReturn/GetDataItem', [PurchaseReturnController::class, 'getDataItem']);
Route::get('/PurchaseReturn/Detail/{id}', [PurchaseReturnController::class, 'detail'])->name('PurchaseReturn.Detail');
Route::get('/PurchaseReturn/Print/{id}', [PurchaseReturnController::class, 'print'])->name('PurchaseReturn.Print'); //test tambahan
Route::get('/PurchaseReturn/Cetak/{id}', [PurchaseReturnController::class, 'cetak'])->name('PurchaseReturn.Cetak');
Route::post('/PurchaseReturn/Posting/{id}', [PurchaseReturnController::class, 'posting'])->name('PurchaseReturn.Posting');
Route::post('/PurchaseReturn/RestoreDetail', [PurchaseReturnController::class, 'RestorePurchaseReturnDetail']);
Route::post('/PurchaseReturn/StoreDetail', [PurchaseReturnController::class, 'StorePurchaseReturnDetail']);
Route::post('/PurchaseReturn/UpdateDetail', [PurchaseReturnController::class, 'UpdatePurchaseReturnDetail']);
Route::post('/PurchaseReturn/DeleteDetail', [PurchaseReturnController::class, 'DeletePurchaseReturnDetail']);
Route::post('/PurchaseReturn/ResetDetail', [PurchaseReturnController::class, 'ResetPurchaseReturnDetail']);
Route::post('/PurchaseReturn/EditDetail', [PurchaseReturnController::class, 'EditPurchaseReturnDetail']);
Route::post('/PurchaseReturn/GetDetail', [PurchaseReturnController::class, 'GetPurchaseReturnDetail']);
Route::post('/PurchaseReturn/GetDataFooter', [PurchaseReturnController::class, 'GetPurchaseReturnFooter']);
Route::post('/PurchaseReturn/Delete', [PurchaseReturnController::class, 'delete']);
Route::post('/PurchaseReturn/GetSupplierPreviousOrder', [PurchaseReturnController::class, 'getPreviousOrder']);
Route::post('/PurchaseReturn/GetSatuan', [PurchaseReturnController::class, 'getProductDetail']);
Route::post('/PurchaseReturn/SetDetail', [PurchaseReturnController::class, 'SetPurchaseReturnDetail']);
Route::resource('PurchaseReturn', PurchaseReturnController::class);


// Route::post('/FinanceForecast/ExportFinanceForecast', [FinanceForecastController::class, 'exportDataFinanceReport'])->name('FinanceForecast.Export');
// Route::resource('FinanceForecast', FinanceForecastController::class);

//ProductionOrder
Route::get('/ProductionOrder/GetData', [ProductionOrderController::class, 'getDataIndex']);
Route::get('/ProductionOrder/Add', [ProductionOrderController::class, 'create']);
Route::post('/ProductionOrder/GetProductBySupplier', [ProductionOrderController::class, 'getProductSupplier']);
Route::post('/ProductionOrder/GetProduct', [ProductionOrderController::class, 'getProduct']);
Route::post('/ProductionOrder/GetDataItem', [ProductionOrderController::class, 'getDataItem']);
Route::post('/ProductionOrder/GetSatuan', [ProductionOrderController::class, 'getProductDetail']);
Route::post('/ProductionOrder/GetListTerms', [ProductionOrderController::class, 'getListTerms']);
Route::post('/ProductionOrder/GetTerms', [ProductionOrderController::class, 'getTerms']);
Route::post('/ProductionOrder/GetDefaultAddress', [ProductionOrderController::class, 'getDefaultAddress']);
Route::post('/ProductionOrder/GetSupplierAddress', [ProductionOrderController::class, 'getSupplierAddress']);
Route::post('/ProductionOrder/AddSupllierProduct', [ProductionOrderController::class, 'addSupllierProduct']);
Route::get('/ProductionOrder/Detail/{id}', [ProductionOrderController::class, 'detail'])->name('ProductionOrder.Detail');
Route::get('/ProductionOrder/Cetak/{id}', [ProductionOrderController::class, 'cetak'])->name('ProductionOrder.Cetak');
Route::post('/ProductionOrder/Posting/{id}', [ProductionOrderController::class, 'posting'])->name('ProductionOrder.Posting');
Route::post('/ProductionOrder/StoreDetail', [ProductionOrderController::class, 'StoreProductionOrderDetail']);
Route::post('/ProductionOrder/UpdateDetail', [ProductionOrderController::class, 'UpdateProductionOrderDetail']);
Route::post('/ProductionOrder/DeleteDetail', [ProductionOrderController::class, 'DeleteProductionOrderDetail']);
Route::post('/ProductionOrder/RestoreDetail', [ProductionOrderController::class, 'RestoreProductionOrderDetail']);
Route::post('/ProductionOrder/EditDetail', [ProductionOrderController::class, 'EditProductionOrderDetail']);
Route::post('/ProductionOrder/GetDetail', [ProductionOrderController::class, 'GetProductionOrderDetail']);
Route::post('/ProductionOrder/GetDataFooter', [ProductionOrderController::class, 'GetProductionOrderFooter']);
Route::post('/ProductionOrder/ResetDetail', [ProductionOrderController::class, 'ResetProductionOrderDetail']);
Route::post('/ProductionOrder/Delete', [ProductionOrderController::class, 'delete']);
Route::post('/ProductionOrder/GetSupplierPreviousOrder', [ProductionOrderController::class, 'getPreviousOrder']);
Route::post('/ProductionOrder/GetProductHistory', [ProductionOrderController::class, 'getProductHistory']);
Route::resource('ProductionOrder', ProductionOrderController::class);

//ProductionReceiving
Route::get('/ProductionReceiving/GetData', [ProductionReceivingController::class, 'getDataIndex']);
Route::get('/ProductionReceiving/Add', [ProductionReceivingController::class, 'create']);
Route::post('/ProductionReceiving/GetProductionOrder', [ProductionReceivingController::class, 'getProductionOrder']);
Route::post('/ProductionReceiving/GetProductByProductionOrder', [ProductionReceivingController::class, 'getProduct']);
Route::post('/ProductionReceiving/GetDataItem', [ProductionReceivingController::class, 'getDataItem']);
Route::post('/ProductionReceiving/GetListTerms', [ProductionReceivingController::class, 'getListTerms']);
Route::post('/ProductionReceiving/GetTerms', [ProductionReceivingController::class, 'getTerms']);
Route::post('/ProductionReceiving/GetSatuan', [ProductionReceivingController::class, 'getProductDetail']);
Route::post('/ProductionReceiving/GetTanggalPo', [ProductionReceivingController::class, 'getTanggalPo']);
Route::post('/ProductionReceiving/GetDefaultAddress', [ProductionReceivingController::class, 'getDefaultAddress']);
Route::post('/ProductionReceiving/GetSupplierAddress', [ProductionReceivingController::class, 'getSupplierAddress']);
Route::get('/ProductionReceiving/Detail/{id}', [ProductionReceivingController::class, 'detail'])->name('ProductionReceiving.Detail');
Route::get('/ProductionReceiving/Staging/{id}', [ProductionReceivingController::class, 'staging'])->name('ProductionReceiving.Staging');
Route::post('/ProductionReceiving/Posting/{id}', [ProductionReceivingController::class, 'posting'])->name('ProductionReceiving.Posting');
Route::post('/ProductionReceiving/PostStaging/{id}', [ProductionReceivingController::class, 'postAllocation'])->name('ProductionReceiving.PostStaging');
Route::post('/ProductionReceiving/StoreDetail', [ProductionReceivingController::class, 'StoreProductionReceivingDetail']);
Route::post('/ProductionReceiving/UpdateDetail', [ProductionReceivingController::class, 'UpdateProductionReceivingDetail']);
Route::post('/ProductionReceiving/DeleteDetail', [ProductionReceivingController::class, 'DeleteProductionReceivingDetail']);
Route::post('/ProductionReceiving/EditDetail', [ProductionReceivingController::class, 'EditProductionReceivingDetail']);
Route::post('/ProductionReceiving/SetDetail', [ProductionReceivingController::class, 'SetProductionReceivingDetail']);
Route::post('/ProductionReceiving/GetDetail', [ProductionReceivingController::class, 'GetProductionReceivingDetail']);
Route::post('/ProductionReceiving/ResetDetail', [ProductionReceivingController::class, 'ResetProductionReceivingDetail']);
Route::post('/ProductionReceiving/RestoreDetail', [ProductionReceivingController::class, 'RestoreProductionReceivingDetail']);
Route::get('/ProductionReceiving/Cetak/{id}', [ProductionReceivingController::class, 'cetak'])->name('ProductionReceiving.Cetak');
Route::post('/ProductionReceiving/GetDataFooter', [ProductionReceivingController::class, 'GetProductionReceivingFooter']);
Route::post('/ProductionReceiving/Delete', [ProductionReceivingController::class, 'delete']);
Route::post('/ProductionReceiving/GetDataDetail', [ProductionReceivingController::class, 'getDataDetail']);
Route::post('/ProductionReceiving/StoreAllocation', [ProductionReceivingController::class, 'StoreProductionReceivingAllocation']);
Route::post('/ProductionReceiving/GetDataAlokasi', [ProductionReceivingController::class, 'GetProductionReceivingAllocation']);
Route::post('/ProductionReceiving/DeleteAllocation', [ProductionReceivingController::class, 'DeleteProductionReceivingAllocation']);
Route::post('/ProductionReceiving/ExportProductionReceiving', [ProductionReceivingController::class, 'exportDataProductionReceiving'])->name('ProductionReceiving.Export');
Route::resource('ProductionReceiving', ProductionReceivingController::class);

//ProductionDelivery
Route::get('/ProductionDelivery/GetData', [ProductionDeliveryController::class, 'getDataIndex']);
Route::get('/ProductionDelivery/Add', [ProductionDeliveryController::class, 'create']);
Route::post('/ProductionDelivery/GetProductBySupplier', [ProductionDeliveryController::class, 'getProductSupplier']);
Route::post('/ProductionDelivery/GetDataItem', [ProductionDeliveryController::class, 'getDataItem']);
Route::post('/ProductionDelivery/GetListTerms', [ProductionDeliveryController::class, 'getListTerms']);
Route::post('/ProductionDelivery/GetTerms', [ProductionDeliveryController::class, 'getTerms']);
Route::post('/ProductionDelivery/GetSatuan', [ProductionDeliveryController::class, 'getProductDetail']);
Route::post('/ProductionDelivery/GetDefaultAddress', [ProductionDeliveryController::class, 'getDefaultAddress']);
Route::post('/ProductionDelivery/GetSupplierAddress', [ProductionDeliveryController::class, 'getSupplierAddress']);
Route::get('/ProductionDelivery/Detail/{id}', [ProductionDeliveryController::class, 'detail'])->name('ProductionDelivery.Detail');
Route::get('/ProductionDelivery/Staging/{id}', [ProductionDeliveryController::class, 'staging'])->name('ProductionDelivery.Staging');
Route::post('/ProductionDelivery/Posting/{id}', [ProductionDeliveryController::class, 'posting'])->name('ProductionDelivery.Posting');
Route::post('/ProductionDelivery/PostStaging/{id}', [ProductionDeliveryController::class, 'postAllocation'])->name('ProductionDelivery.PostStaging');
Route::post('/ProductionDelivery/StoreDetail', [ProductionDeliveryController::class, 'StoreProductionDeliveryDetail']);
Route::post('/ProductionDelivery/UpdateDetail', [ProductionDeliveryController::class, 'UpdateProductionDeliveryDetail']);
Route::post('/ProductionDelivery/DeleteDetail', [ProductionDeliveryController::class, 'DeleteProductionDeliveryDetail']);
Route::post('/ProductionDelivery/EditDetail', [ProductionDeliveryController::class, 'EditProductionDeliveryDetail']);
Route::post('/ProductionDelivery/GetDetail', [ProductionDeliveryController::class, 'GetProductionDeliveryDetail']);
Route::post('/ProductionDelivery/ResetDetail', [ProductionDeliveryController::class, 'ResetProductionDeliveryDetail']);
Route::post('/ProductionDelivery/RestoreDetail', [ProductionDeliveryController::class, 'RestoreProductionDeliveryDetail']);
Route::get('/ProductionDelivery/Cetak/{id}', [ProductionDeliveryController::class, 'cetak'])->name('ProductionDelivery.Cetak');
Route::post('/ProductionDelivery/GetDataFooter', [ProductionDeliveryController::class, 'GetProductionDeliveryFooter']);
Route::post('/ProductionDelivery/Delete', [ProductionDeliveryController::class, 'delete']);
Route::post('/ProductionDelivery/GetDataDetail', [ProductionDeliveryController::class, 'getDataDetail']);
Route::post('/ProductionDelivery/StoreAllocation', [ProductionDeliveryController::class, 'StoreProductionDeliveryAllocation']);
Route::post('/ProductionDelivery/GetDataAlokasi', [ProductionDeliveryController::class, 'GetProductionDeliveryAllocation']);
Route::post('/ProductionDelivery/DeleteAllocation', [ProductionDeliveryController::class, 'DeleteProductionDeliveryAllocation']);
Route::post('/ProductionDelivery/ExportProductionDelivery', [ProductionDeliveryController::class, 'exportDataProductionDelivery'])->name('ProductionDelivery.Export');
Route::resource('ProductionDelivery', ProductionDeliveryController::class);

//SalesOrderInternal
Route::get('/SalesOrderInternal/GetData', [SalesOrderInternalController::class, 'getDataIndex']);
Route::get('/SalesOrderInternal/Add', [SalesOrderInternalController::class, 'create']);
Route::post('/SalesOrderInternal/GetProductByCustomer', [SalesOrderInternalController::class, 'getProductCustomer']);
Route::post('/SalesOrderInternal/GetProduct', [SalesOrderInternalController::class, 'getProduct']);
Route::post('/SalesOrderInternal/GetProductHistory', [SalesOrderInternalController::class, 'getProductHistory']);
Route::post('/SalesOrderInternal/GetDataItem', [SalesOrderInternalController::class, 'getDataItem']);
Route::post('/SalesOrderInternal/GetListTerms', [SalesOrderInternalController::class, 'getListTerms']);
Route::post('/SalesOrderInternal/GetTerms', [SalesOrderInternalController::class, 'getTerms']);
Route::post('/SalesOrderInternal/GetDefaultAddress', [SalesOrderInternalController::class, 'getDefaultAddress']);
Route::post('/SalesOrderInternal/GetCustomerAddress', [SalesOrderInternalController::class, 'getCustomerAddress']);
Route::post('/SalesOrderInternal/AddCustomerProduct', [SalesOrderInternalController::class, 'addCustomerProduct']);
Route::get('/SalesOrderInternal/Detail/{id}', [SalesOrderInternalController::class, 'detail'])->name('SalesOrderInternal.Detail');
Route::get('/SalesOrderInternal/Print/{id}', [SalesOrderInternalController::class, 'print'])->name('SalesOrderInternal.Print'); //test tambahan
Route::get('/SalesOrderInternal/Cetak/{id}', [SalesOrderInternalController::class, 'cetak'])->name('SalesOrderInternal.Cetak');
Route::get('/SalesOrderInternal/CetakInvDp/{id}', [SalesOrderInternalController::class, 'cetakInvDP'])->name('SalesOrderInternal.CetakInvDP');
Route::get('/SalesOrderInternal/CetakInvP/{id}', [SalesOrderInternalController::class, 'cetakInvPelunasan'])->name('SalesOrderInternal.CetakInvPelunasan');
Route::get('/SalesOrderInternal/CetakInvPerforma/{id}', [SalesOrderInternalController::class, 'cetakInvPerforma'])->name('SalesOrderInternal.CetakInvPerforma');
Route::post('/SalesOrderInternal/Posting/{id}', [SalesOrderInternalController::class, 'posting'])->name('SalesOrderInternal.Posting');
Route::post('/SalesOrderInternal/RestoreDetail', [SalesOrderInternalController::class, 'RestoreSalesOrderInternalDetail']);
Route::post('/SalesOrderInternal/StoreDetail', [SalesOrderInternalController::class, 'StoreSalesOrderInternalDetail']);
Route::post('/SalesOrderInternal/UpdateDetail', [SalesOrderInternalController::class, 'UpdateSalesOrderInternalDetail']);
Route::post('/SalesOrderInternal/DeleteDetail', [SalesOrderInternalController::class, 'DeleteSalesOrderInternalDetail']);
Route::post('/SalesOrderInternal/ResetDetail', [SalesOrderInternalController::class, 'ResetSalesOrderInternalDetail']);
Route::post('/SalesOrderInternal/EditDetail', [SalesOrderInternalController::class, 'EditSalesOrderInternalDetail']);
Route::post('/SalesOrderInternal/GetDetail', [SalesOrderInternalController::class, 'GetSalesOrderInternalDetail']);
Route::post('/SalesOrderInternal/GetDataFooter', [SalesOrderInternalController::class, 'GetSalesOrderInternalFooter']);
Route::post('/SalesOrderInternal/Delete', [SalesOrderInternalController::class, 'delete']);
Route::post('/SalesOrderInternal/GetCustomerPreviousOrder', [SalesOrderInternalController::class, 'getPreviousOrder']);
Route::post('/SalesOrderInternal/GetSatuan', [SalesOrderInternalController::class, 'getProductDetail']);
Route::post('/SalesOrderInternal/ExportSalesOrderInternal', [SalesOrderInternalController::class, 'exportDataSalesOrderInternal'])->name('SalesOrderInternal.Export');
Route::post('/SalesOrderInternal/SetDetail', [SalesOrderInternalController::class, 'SetSalesOrderInternalDetail']);
Route::resource('SalesOrderInternal', SalesOrderInternalController::class);
