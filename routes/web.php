<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CampController;
use App\Http\Controllers\CampPatientController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HeadController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\IncomeExpenseController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientProcedureController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PharmacyOrderController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ProductAccessoryController;
use App\Http\Controllers\ProductDamageController;
use App\Http\Controllers\ProductFrameController;
use App\Http\Controllers\ProductLensController;
use App\Http\Controllers\ProductPharmacyController;
use App\Http\Controllers\ProductServiceController;
use App\Http\Controllers\ProductSolutionController;
use App\Http\Controllers\PurchaseFrameController;
use App\Http\Controllers\PurchaseLensController;
use App\Http\Controllers\PurchasePharmacyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesReturnContoller;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SolutionOrderController;
use App\Http\Controllers\SpectacleController;
use App\Http\Controllers\StoreOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferFrameController;
use App\Http\Controllers\TransferLensController;
use App\Http\Controllers\TransferPharmacyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Models\CustomerAccount;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';*/

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return view('backend.login');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'signin')->name('user.login');
        Route::get('/logout', 'logout')->name('logout')->middleware('auth');
    });
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/backend/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/user/branch/update', [UserController::class, 'updateBranch'])->name('user.branch.update');
    Route::get('/closingbalance', [HelperController::class, 'closingBalance'])->name('closing.balance');
});

Route::middleware(['web', 'auth', 'branch', 'mobile'])->group(function () {

    Route::prefix('/backend')->controller(HelperController::class)->group(function () {
        Route::get('/transfer/product/{category}/{branch}', 'transferProductBulk')->name('transfer.product.bulk');
    });

    Route::prefix('/ajax')->controller(AjaxController::class)->group(function () {
        Route::get('/chart/order', 'getOrderData')->name('ajax.chart.order');
        Route::post('/appointment/time', 'getAppointmentTime')->name('ajax.appointment.time');
        Route::get('/product/{category}', 'getProductsByCategory')->name('ajax.product.get');
        Route::get('/productprice/{product}/{category}/{batch}', 'getProductPrice')->name('ajax.productprice.get');
        Route::get('/product/batch/{branch}/{product}/{category}', 'getProductBatch')->name('ajax.productbatch.get');
        Route::get('/product/type/{category}/{attribute}', 'getProductTypes')->name('ajax.product.type');
        Route::get('/product/by/type/{type}', 'getProductsByType')->name('ajax.product.type.get');
        Route::get('/daybook/details', 'getDaybookDetailed')->name('ajax.daybook.detailed');
        Route::get('/frame/details', 'getFrameDetailed')->name('ajax.frame.detailed');

        Route::get('/payment/details/{consultation}', 'getPaymentDetailsByConsultation')->name('ajax.payment.by.consultation');

        Route::get('/power', 'powers')->name('ajax.power.get');
        Route::get('/get/availablecredit/{cid}', 'getAvailableCredit')->name('ajax.available.credit.get');

        Route::get('/prescription/{source}/{val}', 'getPrescription')->name('ajax.get.prescription');
    });

    Route::prefix('/backend')->controller(HelperController::class)->group(function () {
        Route::get('/pending/transfer', 'pendingTransfer')->name('pending.transfer');
        Route::get('/pending/transfer/edit/{id}', 'pendingTransferEdit')->name('pending.transfer.edit');
        Route::post('/pending/transfer/edit/{id}', 'pendingTransferUpdate')->name('pending.transfer.update');
        Route::get('/pending/damage/transfer', 'pendingDamageTransfer')->name('pending.damage.transfer');
        Route::get('/pending/damage/transfer/edit/{id}', 'pendingDamageTransferEdit')->name('pending.damage.transfer.edit');
        Route::post('/pending/damage/transfer/edit/{id}', 'pendingDamageTransferUpdate')->name('pending.damage.transfer.update');
        Route::get('/order/status/update/{id}', 'orderStatus')->name('order.status');
        Route::post('/order/status/update/{id}', 'orderStatusUpdate')->name('order.status.update');

        Route::get('/search/order', 'searchOrder')->name('search.order');
        Route::post('/search/order', 'searchOrderFetch')->name('search.order.fetch');

        Route::get('/search/customer', 'searchCustomer')->name('search.customer');
        Route::post('/search/customer', 'searchCustomerFetch')->name('search.customer.fetch');
    });

    Route::prefix('/backend/export')->controller(ImportExportController::class)->group(function () {
        Route::get('/appointments/today', 'exportTodayAppointments')->name('export.today.appointments');
        Route::get('/camp/patient/list/{id}', 'exportCampPatientList')->name('export.camp.patient');
        Route::get('/product/pharmacy', 'exportProductPharmacy')->name('export.product.pharmacy');
        Route::get('/product/lens', 'exportProductLens')->name('export.product.lens');
        Route::get('/product/frame', 'exportProductFrame')->name('export.product.frame');
        Route::get('/product/solution', 'exportProductFrame')->name('export.product.solution');
        Route::get('/product/accessory', 'exportProductFrame')->name('export.product.accessory');
    });

    Route::prefix('/backend/import')->controller(ImportExportController::class)->group(function () {
        Route::get('/failed/uploads', 'uploadFailed')->name('upload.failed');
        Route::get('/failed/uploads/export', 'uploadFailedExport')->name('upload.failed.export');
        Route::get('/product/purchase', 'importProductPurchase')->name('import.product.purchase');
        Route::post('/product/purchase', 'importProductPurchaseUpdate')->name('import.product.purchase.update');

        Route::get('/frames', 'importFrames')->name('import.frames');
        Route::post('/frames', 'importFramesUpdate')->name('import.frames.update');
        Route::get('/lenses', 'importLenses')->name('import.lenses');
        Route::post('/lenses', 'importLensesUpdate')->name('import.lenses.update');
        Route::get('/transfer', 'importTransfer')->name('import.transfer');
        Route::post('/transfer', 'importTransferUpdate')->name('import.transfer.update');
    });

    Route::prefix('/backend/pdf')->controller(PdfController::class)->group(function () {
        Route::get('/opt/{id}', 'opt')->name('pdf.opt');
        Route::get('/prescription/{id}', 'prescription')->name('pdf.prescription'); // Order
        Route::get('/consultation/receipt/{id}', 'cReceipt')->name('pdf.consultation.receipt');
        Route::get('/mrecord/{id}', 'medicalRecord')->name('pdf.mrecord');
        Route::get('/appointment', 'exportTodaysAppointment')->name('pdf.appointment');
        Route::get('/camp/patient/list/{id}', 'exportCampPatientList')->name('pdf.camp.patient');
        Route::get('/camp/patient/mrecord/{id}', 'exportCampPatientMedicalRecord')->name('pdf.camp.patient.mrecord');
        Route::get('/product/pharmacy', 'exportProductPharmacy')->name('pdf.product.pharmacy');
        Route::get('/product/lens', 'exportProductLens')->name('pdf.product.lens');
        Route::get('/product/frame', 'exportProductFrame')->name('pdf.product.frame');
        Route::get('/product/solution', 'exportProductLens')->name('pdf.product.solution');
        Route::get('/product/accessory', 'exportProductFrame')->name('pdf.product.accessory');
        Route::get('/payment/receipt/{id}', 'exportPaymentReceipt')->name('pdf.payment.receipt');
        Route::get('/order/invoices', 'invoices')->name('invoice.register');
        Route::get('/order/notgenerated/invoices', 'invoicesNotGenerated')->name('not.generated.invoice.register');
        Route::get('/order/invoice/{id}', 'exportOrderInvoice')->name('store.order.invoice');
        Route::get('/order/invoice/generate/{id}', 'generateInvoice')->name('store.order.invoice.generate');
        Route::get('/order/receipt/{id}', 'exportOrderReceipt')->name('store.order.receipt');
        Route::get('/product/transfer/{id}', 'exportProductTransfer')->name('pdf.product.transfer');
        Route::get('/order/prescription/{id}', 'exportOrderPrescription')->name('store.order.prescription');
        Route::get('/customer/prescription/{id}', 'exportCustomerPrescription')->name('customer.order.prescription');
    });

    Route::prefix('/backend/bank/transfer')->controller(BankTransferController::class)->group(function () {
        Route::get('/', 'index')->name('bank.transfers');
        Route::get('/create', 'create')->name('bank.transfer.create');
        Route::post('/save', 'store')->name('bank.transfer.save');
        Route::get('/edit/{id}', 'edit')->name('bank.transfer.edit');
        Route::post('/edit/{id}', 'update')->name('bank.transfer.update');
        Route::get('/delete/{id}', 'destroy')->name('bank.transfer.delete');
    });

    Route::prefix('/backend/user')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('users');
        Route::get('/create', 'create')->name('user.create');
        Route::post('/save', 'store')->name('user.save');
        Route::get('/edit/{id}', 'edit')->name('user.edit');
        Route::post('/edit/{id}', 'update')->name('user.update');
        Route::get('/delete/{id}', 'destroy')->name('user.delete');
    });

    Route::prefix('/backend/role')->controller(RoleController::class)->group(function () {
        Route::get('/', 'index')->name('roles');
        Route::get('/create', 'create')->name('role.create');
        Route::post('/save', 'store')->name('role.save');
        Route::get('/edit/{id}', 'edit')->name('role.edit');
        Route::post('/edit/{id}', 'update')->name('role.update');
        Route::get('/delete/{id}', 'destroy')->name('role.delete');
    });

    Route::prefix('/backend/branch')->controller(BranchController::class)->group(function () {
        Route::get('/', 'index')->name('branches');
        Route::get('/create', 'create')->name('branch.create');
        Route::post('/save', 'store')->name('branch.save');
        Route::get('/edit/{id}', 'edit')->name('branch.edit');
        Route::post('/edit/{id}', 'update')->name('branch.update');
        Route::get('/delete/{id}', 'destroy')->name('branch.delete');
    });

    Route::prefix('/backend/doctor')->controller(DoctorController::class)->group(function () {
        Route::get('/', 'index')->name('doctors');
        Route::get('/create', 'create')->name('doctor.create');
        Route::post('/save', 'store')->name('doctor.save');
        Route::get('/edit/{id}', 'edit')->name('doctor.edit');
        Route::post('/edit/{id}', 'update')->name('doctor.update');
        Route::get('/delete/{id}', 'destroy')->name('doctor.delete');
    });

    Route::prefix('/backend/procedure')->controller(ProcedureController::class)->group(function () {
        Route::get('/', 'index')->name('procedures');
        Route::get('/create', 'create')->name('procedure.create');
        Route::post('/save', 'store')->name('procedure.save');
        Route::get('/edit/{id}', 'edit')->name('procedure.edit');
        Route::post('/edit/{id}', 'update')->name('procedure.update');
        Route::get('/delete/{id}', 'destroy')->name('procedure.delete');
    });

    Route::prefix('/backend/patients/procedure')->controller(PatientProcedureController::class)->group(function () {
        Route::get('/', 'index')->name('patient.procedures');
        Route::post('/', 'fetch')->name('patient.procedure.fetch');
        Route::get('/proceed', '')->name('patient.procedure.proceed');
        Route::get('/create/{id}', 'create')->name('patient.procedure.create');
        Route::post('/create', 'store')->name('patient.procedure.save');
        Route::get('/edit/{id}', 'edit')->name('patient.procedure.edit');
        Route::post('/edit/{id}', 'update')->name('patient.procedure.update');
        Route::get('/delete/{id}', 'destroy')->name('patient.procedure.delete');
    });

    Route::prefix('/backend/patient')->controller(PatientController::class)->group(function () {
        Route::get('/', 'index')->name('patients');
        Route::get('/create/{type}/{type_id}', 'create')->name('patient.create');
        Route::post('/save', 'store')->name('patient.save');
        Route::get('/edit/{id}', 'edit')->name('patient.edit');
        Route::post('/edit/{id}', 'update')->name('patient.update');
        Route::get('/delete/{id}', 'destroy')->name('patient.delete');
    });

    Route::prefix('/backend/consultation')->controller(ConsultationController::class)->group(function () {
        Route::get('/', 'index')->name('consultations');
        Route::get('/create/{pid}', 'create')->name('consultation.create');
        Route::post('/save', 'store')->name('consultation.save');
        Route::get('/edit/{id}', 'edit')->name('consultation.edit');
        Route::post('/edit/{id}', 'update')->name('consultation.update');
        Route::get('/delete/{id}', 'destroy')->name('consultation.delete');
    });

    Route::prefix('/backend/medical-record')->controller(MedicalRecordController::class)->group(function () {
        Route::get('/', 'index')->name('mrecords');
        Route::get('/create/{id}', 'create')->name('mrecord.create');
        Route::post('/save', 'store')->name('mrecord.save');
        Route::get('/edit/{id}', 'edit')->name('mrecord.edit');
        Route::post('/edit/{id}', 'update')->name('mrecord.update');
        Route::get('/delete/{id}', 'destroy')->name('mrecord.delete');
    });

    Route::prefix('/backend/appointment')->controller(AppointmentController::class)->group(function () {
        Route::get('/register', 'index')->name('appointments');
        Route::get('/list', 'show')->name('appointment.list');
        Route::get('/create', 'create')->name('appointment.create');
        Route::post('/save', 'store')->name('appointment.save');
        Route::get('/edit/{id}', 'edit')->name('appointment.edit');
        Route::post('/edit/{id}', 'update')->name('appointment.update');
        Route::get('/delete/{id}', 'destroy')->name('appointment.delete');
    });

    Route::prefix('/backend/camps')->controller(CampController::class)->group(function () {
        Route::get('/', 'index')->name('camps');
        Route::get('/create', 'create')->name('camp.create');
        Route::post('/save', 'store')->name('camp.save');
        Route::get('/edit/{id}', 'edit')->name('camp.edit');
        Route::post('/edit/{id}', 'update')->name('camp.update');
        Route::get('/delete/{id}', 'destroy')->name('camp.delete');
    });

    Route::prefix('/backend/camp/patient')->controller(CampPatientController::class)->group(function () {
        Route::get('/list/{id}', 'index')->name('camp.patients');
        Route::get('/create/{id}', 'create')->name('camp.patient.create');
        Route::post('/save', 'store')->name('camp.patient.save');
        Route::get('/edit/{id}', 'edit')->name('camp.patient.edit');
        Route::post('/edit/{id}', 'update')->name('camp.patient.update');
        Route::get('/delete/{id}', 'destroy')->name('camp.patient.delete');
    });

    Route::prefix('/backend/document')->controller(DocumentController::class)->group(function () {
        Route::get('/', 'index')->name('documents');
        Route::post('/', 'fetch')->name('document.fetch');
        Route::get('/proceed', '')->name('document.proceed');
        Route::get('/create/{id}', 'show')->name('document.create');
        Route::post('/create', 'store')->name('document.save');
        Route::get('/delete/{id}', 'destroy')->name('document.delete');
    });

    Route::prefix('/backend/supplier')->controller(SupplierController::class)->group(function () {
        Route::get('/', 'index')->name('suppliers');
        Route::get('/create', 'create')->name('supplier.create');
        Route::post('/create', 'store')->name('supplier.save');
        Route::get('/edit/{id}', 'edit')->name('supplier.edit');
        Route::post('/edit/{id}', 'update')->name('supplier.update');
        Route::get('/delete/{id}', 'destroy')->name('supplier.delete');
    });

    Route::prefix('/backend/manufacturer')->controller(ManufacturerController::class)->group(function () {
        Route::get('/', 'index')->name('manufacturers');
        Route::get('/create', 'create')->name('manufacturer.create');
        Route::post('/create', 'store')->name('manufacturer.save');
        Route::get('/edit/{id}', 'edit')->name('manufacturer.edit');
        Route::post('/edit/{id}', 'update')->name('manufacturer.update');
        Route::get('/delete/{id}', 'destroy')->name('manufacturer.delete');
    });

    Route::prefix('/backend/product/pharmacy')->controller(ProductPharmacyController::class)->group(function () {
        Route::get('/', 'index')->name('product.pharmacy');
        Route::get('/create', 'create')->name('product.pharmacy.create');
        Route::post('/create', 'store')->name('product.pharmacy.save');
        Route::get('/edit/{id}', 'edit')->name('product.pharmacy.edit');
        Route::post('/edit/{id}', 'update')->name('product.pharmacy.update');
        Route::get('/delete/{id}', 'destroy')->name('product.pharmacy.delete');
    });

    Route::prefix('/backend/product/lens')->controller(ProductLensController::class)->group(function () {
        Route::get('/', 'index')->name('product.lens');
        Route::get('/create', 'create')->name('product.lens.create');
        Route::post('/create', 'store')->name('product.lens.save');
        Route::get('/edit/{id}', 'edit')->name('product.lens.edit');
        Route::post('/edit/{id}', 'update')->name('product.lens.update');
        Route::get('/delete/{id}', 'destroy')->name('product.lens.delete');
    });

    Route::prefix('/backend/product/frame')->controller(ProductFrameController::class)->group(function () {
        Route::get('/', 'index')->name('product.frame');
        Route::get('/create', 'create')->name('product.frame.create');
        Route::post('/create', 'store')->name('product.frame.save');
        Route::get('/edit/{id}', 'edit')->name('product.frame.edit');
        Route::post('/edit/{id}', 'update')->name('product.frame.update');
        Route::get('/delete/{id}', 'destroy')->name('product.frame.delete');
    });

    Route::prefix('/backend/product/solution')->controller(ProductSolutionController::class)->group(function () {
        Route::get('/', 'index')->name('product.solution');
        Route::get('/create', 'create')->name('product.solution.create');
        Route::post('/create', 'store')->name('product.solution.save');
        Route::get('/edit/{id}', 'edit')->name('product.solution.edit');
        Route::post('/edit/{id}', 'update')->name('product.solution.update');
        Route::get('/delete/{id}', 'destroy')->name('product.solution.delete');
    });

    Route::prefix('/backend/product/accessory')->controller(ProductAccessoryController::class)->group(function () {
        Route::get('/', 'index')->name('product.accessory');
        Route::get('/create', 'create')->name('product.accessory.create');
        Route::post('/create', 'store')->name('product.accessory.save');
        Route::get('/edit/{id}', 'edit')->name('product.accessory.edit');
        Route::post('/edit/{id}', 'update')->name('product.accessory.update');
        Route::get('/delete/{id}', 'destroy')->name('product.accessory.delete');
    });

    Route::prefix('/backend/product/collection')->controller(CollectionController::class)->group(function () {
        Route::get('/', 'index')->name('collections');
        Route::get('/create', 'create')->name('collection.create');
        Route::post('/create', 'store')->name('collection.save');
        Route::get('/edit/{id}', 'edit')->name('collection.edit');
        Route::post('/edit/{id}', 'update')->name('collection.update');
        Route::get('/delete/{id}', 'destroy')->name('collection.delete');
    });

    Route::prefix('/backend/product/service')->controller(ProductServiceController::class)->group(function () {
        Route::get('/', 'index')->name('product.service');
        Route::get('/create', 'create')->name('product.service.create');
        Route::post('/create', 'store')->name('product.service.save');
        Route::get('/edit/{id}', 'edit')->name('product.service.edit');
        Route::post('/edit/{id}', 'update')->name('product.service.update');
        Route::get('/delete/{id}', 'destroy')->name('product.service.delete');
    });

    Route::prefix('/backend/store/order')->controller(StoreOrderController::class)->group(function () {
        Route::get('/', 'index')->name('store.order');
        Route::post('/', 'fetch')->name('store.order.fetch');
        Route::get('/proceed', '')->name('store.order.proceed');
        Route::get('/create/{id}/{type}', 'create')->name('store.order.create');
        Route::post('/create', 'store')->name('store.order.save');
        Route::get('/edit/{id}', 'edit')->name('store.order.edit');
        Route::post('/edit/{id}', 'update')->name('store.order.update');
        Route::get('/delete/{id}', 'destroy')->name('store.order.delete');
    });

    Route::prefix('/backend/solution/order')->controller(SolutionOrderController::class)->group(function () {
        Route::get('/create/{id}/{type}', 'create')->name('solution.order.create');
        Route::post('/create', 'store')->name('solution.order.save');
        Route::get('/edit/{id}', 'edit')->name('solution.order.edit');
        Route::post('/edit/{id}', 'update')->name('solution.order.update');
        Route::get('/delete/{id}', 'destroy')->name('solution.order.delete');
    });

    Route::prefix('/backend/pharmacy/order')->controller(PharmacyOrderController::class)->group(function () {
        Route::get('/', 'index')->name('pharmacy.order');
        Route::post('/', 'fetch')->name('pharmacy.order.fetch');
        Route::get('/proceed', '')->name('pharmacy.order.proceed');
        Route::get('/create/{id}', 'create')->name('pharmacy.order.create');
        Route::post('/create', 'store')->name('pharmacy.order.save');
        Route::get('/edit/{id}', 'edit')->name('pharmacy.order.edit');
        Route::post('/edit/{id}', 'update')->name('pharmacy.order.update');
        Route::get('/delete/{id}', 'destroy')->name('pharmacy.order.delete');
    });

    Route::prefix('/backend/purchase/pharmacy')->controller(PurchasePharmacyController::class)->group(function () {
        Route::get('/', 'index')->name('pharmacy.purchase');
        Route::get('/create', 'create')->name('pharmacy.purchase.create');
        Route::post('/create', 'store')->name('pharmacy.purchase.save');
        Route::get('/edit/{id}', 'edit')->name('pharmacy.purchase.edit');
        Route::post('/edit/{id}', 'update')->name('pharmacy.purchase.update');
        Route::get('/delete/{id}', 'destroy')->name('pharmacy.purchase.delete');
    });

    Route::prefix('/backend/purchase/lens')->controller(PurchaseLensController::class)->group(function () {
        Route::get('/', 'index')->name('lens.purchase');
        Route::get('/create', 'create')->name('lens.purchase.create');
        Route::post('/create', 'store')->name('lens.purchase.save');
        Route::get('/edit/{id}', 'edit')->name('lens.purchase.edit');
        Route::post('/edit/{id}', 'update')->name('lens.purchase.update');
        Route::get('/delete/{id}', 'destroy')->name('lens.purchase.delete');
    });

    Route::prefix('/backend/purchase/frame')->controller(PurchaseFrameController::class)->group(function () {
        Route::get('/', 'index')->name('frame.purchase');
        Route::get('/create', 'create')->name('frame.purchase.create');
        Route::post('/create', 'store')->name('frame.purchase.save');
        Route::get('/edit/{id}', 'edit')->name('frame.purchase.edit');
        Route::post('/edit/{id}', 'update')->name('frame.purchase.update');
        Route::get('/delete/{id}', 'destroy')->name('frame.purchase.delete');
    });

    Route::prefix('/backend/transfer/pharmacy')->controller(TransferPharmacyController::class)->group(function () {
        Route::get('/', 'index')->name('pharmacy.transfer');
        Route::get('/create', 'create')->name('pharmacy.transfer.create');
        Route::post('/create', 'store')->name('pharmacy.transfer.save');
        Route::get('/edit/{id}', 'edit')->name('pharmacy.transfer.edit');
        Route::post('/edit/{id}', 'update')->name('pharmacy.transfer.update');
        Route::get('/delete/{id}', 'destroy')->name('pharmacy.transfer.delete');
    });

    Route::prefix('/backend/transfer/lens')->controller(TransferLensController::class)->group(function () {
        Route::get('/', 'index')->name('lens.transfer');
        Route::get('/create', 'create')->name('lens.transfer.create');
        Route::post('/create', 'store')->name('lens.transfer.save');
        Route::get('/edit/{id}', 'edit')->name('lens.transfer.edit');
        Route::post('/edit/{id}', 'update')->name('lens.transfer.update');
        Route::get('/delete/{id}', 'destroy')->name('lens.transfer.delete');
    });

    Route::prefix('/backend/transfer/frame')->controller(TransferFrameController::class)->group(function () {
        Route::get('/', 'index')->name('frame.transfer');
        Route::get('/create', 'create')->name('frame.transfer.create');
        Route::post('/create', 'store')->name('frame.transfer.save');
        Route::get('/edit/{id}', 'edit')->name('frame.transfer.edit');
        Route::post('/edit/{id}', 'update')->name('frame.transfer.update');
        Route::get('/delete/{id}', 'destroy')->name('frame.transfer.delete');
    });

    Route::prefix('/backend/head')->controller(HeadController::class)->group(function () {
        Route::get('/', 'index')->name('heads');
        Route::get('/create', 'create')->name('head.create');
        Route::post('/create', 'store')->name('head.save');
        Route::get('/edit/{id}', 'edit')->name('head.edit');
        Route::post('/edit/{id}', 'update')->name('head.update');
        Route::get('/delete/{id}', 'destroy')->name('head.delete');
    });

    Route::prefix('/backend/iande')->controller(IncomeExpenseController::class)->group(function () {
        Route::get('/', 'index')->name('iande');
        Route::get('/create/{category}', 'create')->name('iande.create');
        Route::post('/create', 'store')->name('iande.save');
        Route::get('/edit/{id}', 'edit')->name('iande.edit');
        Route::post('/edit/{id}', 'update')->name('iande.update');
        Route::get('/delete/{id}', 'destroy')->name('iande.delete');
    });

    Route::prefix('/backend/payment')->controller(PaymentController::class)->group(function () {
        Route::get('/', 'index')->name('patient.payments');
        Route::post('/', 'fetch')->name('patient.payment.fetch');
        Route::get('/proceed', '')->name('patient.payment.proceed');
        Route::get('/create/{id}', 'create')->name('patient.payment.create');
        Route::post('/create', 'store')->name('patient.payment.save');
        Route::get('/edit/{id}', 'edit')->name('patient.payment.edit');
        Route::post('/edit/{id}', 'update')->name('patient.payment.update');
        Route::get('/delete/{id}', 'destroy')->name('patient.payment.delete');
    });

    Route::prefix('/backend/sreturn')->controller(SalesReturnContoller::class)->group(function () {
        Route::get('/', 'index')->name('sales.return');
        Route::post('/', 'fetch')->name('sales.return.fetch');
        Route::get('/list/{id}', 'list')->name('sales.return.list');
        Route::post('/list/save/{id}', 'store')->name('sales.return.list.save');
        Route::get('/list/detail/{id}', 'show')->name('sales.return.list.detail');
    });

    Route::prefix('/backend/product/damage')->controller(ProductDamageController::class)->group(function () {
        Route::get('/', 'index')->name('product.damage.register');
        Route::get('/create/{category}', 'create')->name('product.damage.create');
        Route::post('/create', 'store')->name('product.damage.save');
        Route::get('/edit/{id}', 'edit')->name('product.damage.edit');
        Route::post('/edit/{id}', 'update')->name('product.damage.update');
        Route::get('/delete/{id}', 'destroy')->name('product.damage.delete');
    });

    Route::prefix('/backend/spectacle')->controller(SpectacleController::class)->group(function () {
        Route::get('/', 'index')->name('spectacles');
        Route::get('/create/{id}/{type}', 'create')->name('spectacle.create');
        Route::post('/create', 'store')->name('spectacle.save');
        Route::get('/edit/{id}', 'edit')->name('spectacle.edit');
        Route::post('/edit/{id}', 'update')->name('spectacle.update');
        Route::get('/delete/{id}', 'destroy')->name('spectacle.delete');
    });

    Route::prefix('/backend/customer')->controller(CustomerController::class)->group(function () {
        Route::get('/', 'index')->name('customer.register');
        /*Route::get('/spectacles', 'spectacles')->name('customer.spectacles');*/
        Route::post('/', 'fetch')->name('customer.fetch');
        Route::get('/proceed', '')->name('customer.proceed');
        Route::get('/create/{id}/{source}', 'create')->name('customer.create');
        Route::post('/create', 'store')->name('customer.save');
        Route::get('/edit/{id}', 'edit')->name('customer.edit');
        Route::post('/edit/{id}', 'update')->name('customer.update');
        Route::get('/delete/{id}', 'destroy')->name('customer.delete');

        /*Route::get('/spectacle/edit/{id}/{type}', 'editSpectacle')->name('customer.spectacle.edit');
        Route::post('/spectacle/edit/{id}', 'updateSpectacle')->name('customer.spectacle.update');*/

        Route::get('/customer/registration/delete/{id}', 'destroy')->name('customer.registration.delete');
    });

    Route::prefix('/backend/report')->controller(ReportController::class)->group(function () {
        Route::get('/daybook', 'daybook')->name('report.daybook');
        Route::post('/daybook', 'fetchDaybook')->name('report.daybook.fetch');
        Route::get('/consultation', 'consultation')->name('report.consultation');
        Route::post('/consultation', 'fetchConsultation')->name('report.consultation.fetch');
        Route::get('/lab', 'lab')->name('report.lab');
        Route::post('/lab', 'fetchLab')->name('report.lab.fetch');
        Route::get('/sales', 'sales')->name('report.sales');
        Route::post('/sales', 'fetchSales')->name('report.sales.fetch');
        Route::get('/stock/status', 'stockStatus')->name('report.stock.status');
        Route::post('/stock/status', 'fetchStockStatus')->name('report.stock.status.fetch');
    });

    Route::prefix('/backend/voucher')->controller(VoucherController::class)->group(function () {
        Route::get('/', 'index')->name('voucher');
        Route::get('/create/{category}', 'create')->name('voucher.create');
        Route::post('/create', 'store')->name('voucher.save');
        Route::get('/edit/{id}', 'edit')->name('voucher.edit');
        Route::post('/edit/{id}', 'update')->name('voucher.update');
        Route::get('/delete/{id}', 'destroy')->name('voucher.delete');
    });

    Route::prefix('/backend/lab')->controller(LabController::class)->group(function () {
        Route::get('/', 'index')->name('labs');
        Route::get('/create', 'create')->name('lab.create');
        Route::post('/save', 'store')->name('lab.save');
        Route::get('/edit/{id}', 'edit')->name('lab.edit');
        Route::post('/edit/{id}', 'update')->name('lab.update');
        Route::get('/delete/{id}', 'destroy')->name('lab.delete');

        Route::get('/assign-orders', 'assignOrders')->name('lab.assign.orders');
        Route::post('/assign-orders', 'assignOrdersSave')->name('lab.assign.orders.save');

        Route::get('/lab-orders', 'labOrders')->name('lab.view.orders');
        Route::post('/lab-orders', 'labOrdersUpdateStatus')->name('lab.order.update.status');
        Route::get('/lab-orders/delete/{id}', 'delete')->name('lab.order.delete');
    });

    Route::prefix('/backend/settings')->controller(SettingController::class)->group(function () {
        Route::get('/account', 'accountSetting')->name('account.setting');
        Route::get('/fetch', 'accountSettingFetch')->name('account.setting.fetch');
        Route::post('/update', 'accountSettingUpdate')->name('account.setting.update');

        Route::get('/stock', 'stockAdjustmentSetting')->name('setting.stock.adjustment');
        Route::post('/stock', 'stockAdjustmentSettingFetch')->name('setting.stock.adjustment.fetch');
        Route::post('/stock/update', 'stockAdjustmentSettingUpdate')->name('setting.stock.adjustment.update');
    });
});
