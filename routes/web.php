<?php

use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    ReportController,
    MisController,
    MPropertyIdController,
    CommonController,
    MapController,
    ScriptController,
    RgrController,
    ColonyController,
    SteetviewController,
    MessageTempleteController,
    PropertySectionMappingController,
    AppointmentDetailController,
    AdminPublicGrievanceController,
    ApplicationController,
    ApplicantController,
    OfficialController,
    ChatBotController,
    DeedOfApartmentController,
    OtpController,
    PropertyMergingController,
    PaymentController,
    ConversionController,
    DemandController,
    LandUseChangeCalculationController,
    UnearnedIncreaseController,
    NocController,
    PropertyOutSideController,
    ClubMembershipBackendController,
    CaptchaController,
    ManualPaymentController,
    PropertyScannedFileController,
    PropertyScannedRequestController,
    MasterReportController,
    LitigationController,
    RevenueController,
    InspectionController,
    RegistrationReportController
};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Settings\MailController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Settings\SmsController;
use App\Http\Controllers\Settings\WhatsappController;
use App\Http\Controllers\Settings\ActionController;
use App\Http\Controllers\logistic\PurchaseController;
use App\Http\Controllers\DbViewerController;
use App\Http\Controllers\logistic\SupplierVendorDetailsController;
use App\Http\Controllers\logistic\ItemsController;
use App\Http\Controllers\logistic\RequestProcessingController;
use App\Http\Controllers\logistic\IssueController;
use App\Http\Controllers\logistic\CategoryController;
use App\Http\Controllers\application\MutationContoller;
use App\Http\Controllers\application\LandUseChangeController;
use App\Models\Payment;
use App\Helpers\GeneralFunctions;
use App\Http\Controllers\Admin\{
    ApplicationController as AdminApplicationController,
    RecordController,
    RollbackApplicationController,
};
use App\Http\Controllers\application\ConversionController as ApplicationConversionController;
use Mews\Captcha\CaptchaController as MewsCaptchaController;
use App\Services\PaymentService;
use Illuminate\Http\Request;


// Redirect Route for local system for running in development environment by Adarsh s
Route::get('/', function () {
    return redirect('/edharti');
});

Route::prefix('edharti')->middleware('auth')->group(function () {

    // Your other routes here...

    Route::get('/storage/{path}', function ($path) {
        $file = storage_path('app/public/' . $path);

        if (!File::exists($file)) {
            abort(404);
        }

        return Response::make('', 200, [
            'Content-Type' => File::mimeType($file),
            'Content-Length' => File::size($file),
            'X-Accel-Redirect' => '/protected-storage/' . $path,
        ]);
    })->where('path', '.*');
});

Route::prefix('edharti')->group(function () {


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
    Route::get('/test-mail', [CommonController::class, 'sendMail']);

    // Existing behavior: single transaction by unique_payment_id
    Route::get('/download-payment-receipt/{id}', function ($id) {
        return downloadPaymentReceiptPdf($id);
    })->name('downloadPaymentReceiptPdf');

    // New behavior: multiple transactions by application_no (ignores {id} essentially)
    Route::get('/download-payment-receipt/{id}/{application_no}', function ($id, $application_no) {
        return downloadPaymentReceiptPdf($id, $application_no);
    })->name('downloadPaymentReceiptPdfByAppNo');
    Route::get('captcha/{config?}', [MewsCaptchaController::class, 'getCaptcha'])->name('captcha');
    Route::get('refresh-captcha', [CaptchaController::class, 'refreshCaptcha'])->name('refresh.captcha');
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/colony-filter', [DashboardController::class, 'dashbordColonyFilter'])->name('dashbordColonyFilter');
        Route::get('/dashboard/property-type-data/{typeId}/{coonyId?}', [DashboardController::class, 'propertyTypeDetails'])->name('propertyTypeDetails');
        Route::get('/dashboard/main', [DashboardController::class, 'mainDashboard'])->name('dashboard.main')->middleware('permission:main.dashboard');
        Route::post('report/get-distinct-subType', [ReportController::class, 'getDistinctSubTypes'])->name('getDistinctSubTypes');
        Route::get('/survey-details/{property_id}', [ReportController::class, 'getSurveyDetails'])->name('getSurveyDetails');
        Route::get('getNocApplicationsDisposed', [DashboardController::class, 'getNocApplicationsDisposed'])->name('get.noc.applications.disposed');

        Route::post('/old-record/update-pending-remark', [DashboardController::class, 'postOldRecordRemark'])->name('old.record.update.pending.remark');
        Route::get('/old-pending-records', [DashboardController::class, 'oldPendingRecords'])->name('old.pending.records');
        Route::get('/get-old-pending-records-data', [DashboardController::class, 'getOldPendingRecordsData'])->name('get.old.pending.records.data');
        Route::get('/get-sections-for-filter', [DashboardController::class, 'getSectionsForFilter'])->name('get.sections.for.filter');

        //route for lease hold demands
        Route::get('/lease-hold-demands', [ReportController::class, 'leaseHoldDemands'])->name('leaseHoldDemands');

        // Add this line before the resource route
        Route::get('revenues/{id}/edit', [RevenueController::class, 'edit'])->name('revenues.edit')->middleware('permission:revenue.create');
        Route::put('revenues/{id}', [RevenueController::class, 'update'])->name('revenues.update')->middleware('permission:revenue.create');
        Route::delete('revenues/{id}', [RevenueController::class, 'destroy'])->name('revenues.destroy')->middleware('permission:revenue.create');

        // Your existing routes
        Route::get('/create/revenue', function () {
            return redirect()->route('revenues.index');
        })->middleware('permission:revenue.create');

        Route::resource('revenues', RevenueController::class)->middleware('permission:revenue.create');
        Route::get('revenues-data', [RevenueController::class, 'getData'])->name('revenues.data')->middleware('permission:revenue.create');

        Route::get('/manual-payment', [ManualPaymentController::class, 'manualPaymentInputForm'])->name('manualPaymentInputForm');
        Route::post('/manual-payment-details', [ManualPaymentController::class, 'getManualPaymentDetails'])->name('manual.payment.details');
        Route::post('/submit-manual-payment-details', [ManualPaymentController::class, 'submitManaulPaymentDetails'])->name('submit.manual.payment.details');



        Route::post('/dashboard/section-filter', [DashboardController::class, 'dashbordSectionFilter'])->name('dashbordSectionFilter');
        Route::get('/dashboard-application-summary/{filter?}', [DashboardController::class, 'applicationSummary'])->name('dashboardApplicationSummary');
        Route::get('/report/colonywise-section-report/{sectionId}', [ReportController::class, 'colonywiseSectionReport'])->name('colonywiseSectionReport');
        // Route::get('/dashboard/main', [DashboardController::class, 'mainDashboard'])->name('dashboard.main')->middleware('permission:main.dashboard');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        // Routes for showing unalloted recores By Lalit Tiwari (10/Jan/2025)
        Route::get('/reports/detailed-report', [ReportController::class, 'detailedReport'])->name('detailedReport');
        Route::get('/reports/detailed-report/export-data', [ReportController::class, 'detailedReportExport'])->name('detailedReportExport');

        Route::post('create-payment-receipt', [ApplicantController::class, 'createPaymentReceipt'])->name('createPaymentReceipt');

        Route::get('/property-form', [MisController::class, 'index'])->name('mis.index');
        Route::post('/propert-sub-type', [MisController::class, 'prpertySubTypes'])->name('prpertySubTypes');
        Route::post('/property-form/store', [MisController::class, 'store'])->name('mis.store');
        Route::post('/property-form/unallotted/store', [MisController::class, 'unallottedPropertiesStore'])->name('mis.unallottedPropertiesStore'); // Added for unallotted properties - 10-12-2024
        Route::get('/property-form-multiple', [MisController::class, 'misFormMultiple'])->name('mis.form.multiple');
        Route::post('/property-form-multiple/store', [MisController::class, 'misStoreMultiple'])->name('mis.store.multiple');
        Route::get('/property-details', [MisController::class, 'propertDetails'])->name('propertDetails')->middleware('permission:viewDetails');
        Route::post('mis/is-property-available', [MisController::class, 'isMisPropertyAvailable'])->name('isMisPropertyAvailable')->middleware('permission:viewDetails');
        Route::delete('property-destroy/{id}', [MisController::class, 'propertyDestroy'])->name('property.destroy');
        Route::get('/property-details/child/{id}', [MisController::class, 'propertyChildDetails'])->name('propertyChildDetails')->middleware('permission:viewDetails');
        Route::post('/property/search', [MPropertyIdController::class, 'propertySearchById'])->name('propertySearch');
        Route::post('/is-property-available', [MPropertyIdController::class, 'isPropertyAvailable'])->name('isPropertyAvailable');
        Route::get('/tabular-record', [ReportController::class, 'tabularRecord'])->name('tabularRecord');
        Route::get('/property-details/{property}/view', [MisController::class, 'viewDetails'])->name('viewDetails')->middleware('permission:viewDetails');
        Route::get('/property-details/{property}/edit', [MisController::class, 'editDetails'])->name('editDetails')->middleware('permission:viewDetails');
        Route::get('/property-details/{property}/child-edit', [MisController::class, 'editChildDetails'])->name('editChildDetails')->middleware('permission:viewDetails');
        Route::put('/property-details/child/{property}', [MisController::class, 'updateChild'])->name('mis.update.child')->middleware('permission:viewDetails');
        Route::put('/property-details/{property}', [MisController::class, 'update'])->name('mis.update')->middleware('permission:viewDetails');
        Route::get('/property-details/{property}/view-property-details', [MisController::class, 'viewPropertyDetails'])->name('viewPropertyDetails'); //->middleware
        Route::get('/download-pdf/{property}', [MisController::class, 'downloadPdf'])->name('download.pdf');
        Route::post('/property-child-land-batch-transfer-destroy/{batchId}/{propertyMasterId}/{splitedPropertyId}', [MisController::class, 'destroyChildLandTransferByBatchId'])->name('property.child.land.batch.transfer.destroy');
        Route::post('/property-child-land-batch-transfer-individual-destroy/{landTransferId}/{propertyMasterId}', [MisController::class, 'destroyChildLandTransferByIndividualId'])->name('property.child.land.batch.transfer.individual.destroy');
        // lalit ji property status routes
        Route::post('/get-old-property-status/{propertyId}/{propertyStatusId}', [MisController::class, 'getOldPropertyStatusValue'])->name('get.old.property.value');
        Route::post('/property-original-destroy/{id}', [MisController::class, 'destroyOriginalById'])->name('original.destroy');
        Route::post('/property-land-batch-transfer-destroy/{batchId}/{propertyMasterId}', [MisController::class, 'destroyLandTransferByBatchId'])->name('property.land.batch.transfer.destroy');
        Route::post('/property-land-batch-transfer-individual-destroy/{landTransferId}/{batchId}/{propertyMasterId}', [MisController::class, 'destroyLandTransferByIndividualId'])->name('property.land.batch.transfer.individual.destroy');
        Route::post('/soft-delete-old-property-status-record', [MisController::class, 'softDeleteOldPropertyStatusRecord'])->name('soft.delete.old.property.status.record');
        Route::get('/user-actions-logs', [MisController::class, 'actionLogListings'])->name('actionLogListings');
        Route::get('get/userActionsLogs', [MisController::class, 'getUserActionLogs'])->name('getUserActionLogs');
        Route::get('/application/deed-of-apartment-form', [ApplicationController::class, 'create'])->name('application.apartment.create');
        Route::post('/application/deed_of_apartment/store_application_details', [ApplicationController::class, 'store'])->name('application.apartment.store');

        // Applicant Routes added by Lalit on 21/08/2024
        Route::get('applicant/new/properties', [OfficialController::class, 'applicantNewProperties'])->name('applicantNewProperties');
        Route::get('get/applicant/property/listings', [OfficialController::class, 'getApplicantPropertyListings'])->name('get.applicant.property.listings');
        Route::get('applicant/property/{id}/view', [OfficialController::class, 'newPropertyDetails'])->name('applicant.properties.details');
        Route::get('applicant/property/{id}/view/details', [OfficialController::class, 'newPropertyViewDetails'])->name('applicant.properties.view.details');
        Route::put('review/applicant/new/property/{id}', [OfficialController::class, 'reviewApplicantNewProperty'])->name('review.applicant.new.property');
        Route::post('approve/review/applicant/new/property', [OfficialController::class, 'approvedReviewApplicantNewProperty'])->name('approve.review.applicant.new.property');
        Route::put('reject/applicant/new/property/{id}', [OfficialController::class, 'rejectApplicantNewProperty'])->name('reject.applicant.new.property');
        Route::post('approve/applicant/new/property', [OfficialController::class, 'approvedApplicantNewProperty'])->name('approve.applicant.new.property');
        Route::post('approve-mis', [OfficialController::class, 'approveMis'])->name('approveMis');
        Route::post('scanned-files-checked', [OfficialController::class, 'scannedFilesChecked'])->name('scannedFilesChecked');
        Route::post('uploaded-docs-checked', [OfficialController::class, 'uploadedDocsChecked'])->name('uploadedDocsChecked');
        //added by swati mishra for resend communication on 18082025
        Route::post('/register/user/{id}/resend-comms', [OfficialController::class, 'resendRegistrationComms'])->name('register.user.resendComms');
        //Routes for checking property id available while doing MIS throug It Cell - Lalit Tiwari - (22/jan/2025)
        Route::post('/search/property/locality_block_plot', [MPropertyIdController::class, 'searchPropThroughLocalityBlockPlot'])->name('searchPropThroughLocalityBlockPlot');
        // Handle form submission routes ajax request for Transfer Property To section - Lalit (23/Jan/2025)
        Route::post('/transfer/property/to/section', [MPropertyIdController::class, 'transferPropertyToSection'])->name('transfer.property.section');
        // Handle form submission routes ajax request for Reject User Registered Property - Lalit (3/March/2025)
        Route::post('/reject/user/registered/property', [OfficialController::class, 'rejectUserRegisteredProperty'])->name('reject.user.registered.property');
        Route::post('/register/user/{id}/revoke-approval', [OfficialController::class, 'revokeApproval'])->name('register.user.revokeApproval');

        //Routes for Record Room modules
        //     Route::get('/record-room/index', [RecordController::class, 'index'])->name('recordRoom.index');
        //     Route::get('/record-room/files', [RecordController::class, 'getRecordRoomFilesData'])->name('recordRoom.files');
        //     Route::get('/record-room/create', [RecordController::class, 'create'])->name('recordRoom.create');
        //     Route::post('/record-room', [RecordController::class, 'store'])->name('recordRoom.store'); 
        //    Route::get('/record-room/file-request', [RecordController::class, 'fileRequest'])->name('recordRoom.fileRequest');
        //     Route::get('/record-room/getFilesRequestData', [RecordController::class, 'getFilesRequestData'])->name('recordRoom.getFilesRequestData');
        Route::group(['middleware' => ['permission:record.room.list']], function () {
            //Routes for Record Room modules
            Route::get('/record-room/index', [RecordController::class, 'index'])->name('recordRoom.index');
            Route::get('/record-room/files', [RecordController::class, 'getRecordRoomFilesData'])->name('recordRoom.files');
            Route::get('/record-room/create', [RecordController::class, 'create'])->name('recordRoom.create');
            Route::post('/record-room', [RecordController::class, 'store'])->name('recordRoom.store');
            Route::get('/record-room/file-request', [RecordController::class, 'fileRequest'])->name('recordRoom.fileRequest');
            Route::get('/record-room/getFilesRequestData', [RecordController::class, 'getFilesRequestData'])->name('recordRoom.getFilesRequestData');
            Route::post('/request-file', [RecordController::class, 'requestFile'])->name('recordRoom.requestFile');
            Route::post('/request-file-upload', [RecordController::class, 'requestFileUpload'])->name('request.file.upload');
            Route::get('/create-file-request-requisition-letter/{id}', [RecordController::class, 'fileRequestRequisitionLetter'])->name('file.request.requisition.letter');
            Route::post('/request-file-accept', [RecordController::class, 'requestFileAccept'])->name('request.file.accept');
            Route::post('/request-file-return', [RecordController::class, 'requestFileReturn'])->name('request.file.return');
            Route::get('/create-file-return-letter/{id}', [RecordController::class, 'fileReturnLetter'])->name('file.return.letter');
            Route::post('/cancel-request-file', [RecordController::class, 'cancelRequestFile'])->name('recordRoom.cancelRequestFile');
            Route::get('/edit-record-file/{id}', [RecordController::class, 'editRecordFile'])->name('recordRoom.editRecordFile');
            Route::post('/update-record-file', [RecordController::class, 'updateRecordFile'])->name('recordRoom.edit');
            Route::get('/record-room/export/csv', [RecordController::class, 'exportCsv'])
                ->name('recordRoom.export.csv');

            Route::get('/record-room/export/excel', [RecordController::class, 'exportExcel'])
                ->name('recordRoom.export.excel');
        });

        //uploaded on live - SOURAV CHAUHAN - 16/Jan/2024 ****************** START *************************
        //applicantion routes - Sourav Chauhan - 12/sep/2024
        Route::get('application/new', [ApplicationController::class, 'newApplication'])->name('new.application')->middleware('permission:apply.application');
        Route::post('updateApllicantStatus', [ApplicationController::class, 'updateApllicantStatus'])->name('updateApllicantStatus');
        Route::post('app-get-property-details', [ApplicationController::class, 'getPropertyDetails'])->name('appGetPropertyDetails');
        Route::post('app-get-property-details-edit', [ApplicationController::class, 'getPropertyDetailsForEdit'])->name('getPropertyDetailsForEdit');
        Route::get('fetch-user-details', [ApplicationController::class, 'fetchUserDetails'])->name('fetchUserDetails');
        Route::post('mutation-step-first', [MutationContoller::class, 'mutationStepFirst'])->name('mutationStepFirst');
        Route::post('upload-file', [ApplicationController::class, 'uploadFile'])->name('uploadFile');
        Route::post('mutation-step-second', [MutationContoller::class, 'mutationStepSecond'])->name('mutationStepSecond');
        Route::post('mutation-step-third', [MutationContoller::class, 'mutationStepThird'])->name('mutationStepThird');
        Route::get('applications/draft', [ApplicationController::class, 'draftApplications'])->name('draftApplications');
        Route::post('delete-application', [ApplicationController::class, 'deleteApplication'])->name('deleteApplication');
        Route::get('getDraftApplications', [ApplicationController::class, 'getDraftApplications'])->name('getDraftApplications');
        Route::get('applications/draft/{id}', [ApplicationController::class, 'getDraftApplication'])->name('getApplication.draft');
        Route::get('applications/edit/{id}', [ApplicationController::class, 'getEditApplications'])->name('getApplication.edit');
        Route::get('applications/history/details', [ApplicationController::class, 'applicationsHistoryDetails'])->name('applications.history.details');
        Route::get('getHistoryApplications', [ApplicationController::class, 'getHistoryApplications'])->name('getHistoryApplications');
        Route::get('applications/all/details', [ApplicationController::class, 'applicationsAllDetails'])->name('applications.all.details');
        Route::get('getAllApplications', [ApplicationController::class, 'getAllApplications'])->name('getAllApplications');


        // Route for direct update of application from temp to final when payment is complete by swati on 08-06-2026

        // Route::get('/final-convert-application', function () {

        //     $payment = Payment::where(
        //         'unique_payment_id',
        //         'PAC20260225141906'
        //     )->first();

        //     if (!$payment) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'Payment not found'
        //         ]);
        //     }

        //     return GeneralFunctions::convertTempAppToFinal(
        //         46,
        //         'TempSubstitutionMutation',
        //         $payment
        //     );
        // });

        Route::post('is-property-free', [ApplicationController::class, 'isPropertyFree'])->name('isPropertyFree');
        Route::post('withdraw-application', [ApplicationController::class, 'withdrawApplication'])->name('withdrawApplication')->middleware('permission:withdraw.application');
        Route::get('applications/history/withdraw', [ApplicationController::class, 'applicationsWithdrawDetails'])->name('applications.history.withdraw.details');
        Route::get('getWithdrawApplications', [ApplicationController::class, 'getWithdrawApplications'])->name('getWithdrawApplications');
        /** To delete a uploaded documnent -- Added By Nitin On - 06 Nov 2024*/
        Route::post('delete-uploaed-document', [ApplicationController::class, 'deleteUploadedTempDocument'])->name('deleteUploadedTempDocument');
        /** To delete a uploaded documnent -- Added By Nitin On - 07 Nov 2024*/
        Route::post('delete-temp-coapplicant', [ApplicationController::class, 'deleteTempCoapplicant'])->name('deleteTempCoapplicant');
        /** To forward application to other department -- Added By Lalit On - 25 Nov 2024*/
        Route::post('applications/forward/department', [AdminApplicationController::class, 'forwardApplicationToDepartment'])->name('forwardApplicationToDepartment');
        /** To revert application to assignee -- Added By Lalit On - 26 Nov 2024*/
        Route::post('applications/revert/assignee', [AdminApplicationController::class, 'revertApplicationToAssignee'])->name('revertApplicationToAssignee');
        Route::put('/applicantDetails/cdv/{id}', [AdminApplicationController::class, 'applicantDetailsUpdateByCdv'])->name('applicant.cdv.update');

        /**Land use change application added by Nitin */
        Route::post('/application/fetch-luc-details', [LandUseChangeController::class, 'fetchLandUseChangeDetails'])->name('fetchLandUseChangeDetails');
        Route::post('/application/luc-step-1', [LandUseChangeController::class, 'step1Submit'])->name('step1Submit');
        Route::post('/application/luc-step-2', [LandUseChangeController::class, 'step2Submit'])->name('step2Submit');

        /**Conversion Application routes added by Nitin */
        Route::post('/conversion/step-1', [ApplicationConversionController::class, 'step1submit'])->name('conversionStep1');
        Route::post('/conversion/step-2', [ApplicationConversionController::class, 'step2submit'])->name('conversionStep2');
        Route::post('/conversion/step-3', [ApplicationConversionController::class, 'step3submit'])->name('conversionStep3');

        /**NOC Application routes added by Lalit Tiwari (17/March/2025) */
        Route::post('noc-step-first', [NocController::class, 'nocStepFirst'])->name('nocStepFirst');
        Route::post('noc-final-step', [NocController::class, 'nocFinalStep'])->name('nocFinalStep');

        Route::post('/doa-step-first', [DeedOfApartmentController::class, 'deedOfApartmentStepFirstStore'])->name('doa.step.first');
        Route::post('/doa-step-final', [DeedOfApartmentController::class, 'deedOfApartmentStepFinalStore'])->name('doa.step.final');

        /** Update application -- Added By Lalit On - 02 Dec 2024*/
        Route::post('application/update', [AdminApplicationController::class, 'updateApplication'])->name('updateApplication');
        //uploaded on live - SOURAV CHAUHAN - 16/Jan/2024 ************* END *******************


        Route::get('/reports/customized-report', [ReportController::class, 'customizeReport'])->name('customizeReport');
        Route::get('/reports/get-customized-report-data', [ReportController::class, 'getCustomizeReportData'])->name('get.customized.reports.data');
        Route::post('/get-demand-details', [ReportController::class, 'getDemandDetails'])->name('getDemandDetails');

        //Add by lalit on 29/07/2024 to show register user listing
        Route::get('register/users', [OfficialController::class, 'index'])->name('regiserUserListings')->middleware('permission:view.register.user.listings');
        Route::get('get/registered/users', [OfficialController::class, 'getRegisteredUsers'])->name('get.registered.users');
        Route::get('register/user/{id}/view', [OfficialController::class, 'details'])->name('register.user.details');
        Route::get('register/user/{id}/view/details', [OfficialController::class, 'viewDetails'])->name('register.user.view.details');
        Route::put('registration/status/{id}', [OfficialController::class, 'updateStatus'])->name('update.registration.status');
        Route::post('check/property/exists', [OfficialController::class, 'checkProperty'])->name('register.user.checkProperty');
        // Route::post('register/user/{id}/', [OfficialController::class, 'checkProperty'])->name('register.user.checkProperty');
        Route::post('approve/user/registration', [OfficialController::class, 'approvedUserRegistration'])->name('approve.user.registration');
        Route::put('register/user/{id}', [OfficialController::class, 'rejectUserRegistration'])->name('reject.user.registration');
        Route::put('review/user/registration/{id}', [OfficialController::class, 'reviewUserRegistration'])->name('review.user.registration');
        Route::post('approve/review/application', [OfficialController::class, 'approvedReviewApplication'])->name('approve.review.application');
        Route::post('/update-correct-property-id-user-registrations', [OfficialController::class, 'updateCorrectPropertyIdUserRegistrations'])->name('updateCorrectPropertyIdUserRegistrations');
        Route::post('/update-correct-property-id-new-applicant-property', [OfficialController::class, 'updateCorrectPropertyIdNewApplicantProperty'])->name('updateCorrectPropertyIdNewApplicantProperty');


        Route::get('flats', [OfficialController::class, 'flats'])->name('flats');
        Route::get('get/flats', [OfficialController::class, 'getFlats'])->name('get.flats');
        Route::get('mis/add-flat/{applicationMovementId?}', [OfficialController::class, 'createFlatForm'])->name('create.flat.form');
        // Route::get('flat-form', [OfficialController::class, 'createFlatForm'])->name('create.flat.form');
        Route::post('flat/store_flat_details', [OfficialController::class, 'storeFlatDetails'])->name('store.flat.details');
        Route::post('flat/update_flat_details', [OfficialController::class, 'updateFlatDetails'])->name('update.flat.details');
        Route::get('/flat/{id}/view', [OfficialController::class, 'viewFlatDetails'])->name('viewFlatDetails');
        Route::get('/flat/{id}/edit', [OfficialController::class, 'editFlatDetails'])->name('editFlatDetails');
        Route::post('flat/delete', [OfficialController::class, 'flatDestroy'])->name('flat.destroy');
        Route::get('/properties/view/vacant/lands', [PropertyOutSideController::class, 'index'])->name('vacant.land.list');
        Route::get('/get/vacant/land/list', [PropertyOutSideController::class, 'getUnallotedOutsideDelhiPropertyData'])->name('get.vacant.land.list.list');
        Route::get('mis/add/vacant/land', [PropertyOutSideController::class, 'create'])->name('create.vacant.land');
        Route::post('/vacant-land/store', [PropertyOutSideController::class, 'store'])->name('vacant.land.store');
        Route::get('/vacant-land/{id}/view', [PropertyOutsideController::class, 'show'])->name('vacant.land.view');
        Route::get('/get-cities', [PropertyOutSideController::class, 'getCities'])->name('get.cities.by.state');
        Route::get('vacant/land/{id}/edit', [PropertyOutSideController::class, 'edit'])->name('vacant.land.edit');
        Route::put('vacant/land/{id}', [PropertyOutSideController::class, 'update'])->name('vacant.land.update');
        Route::get('/get-vacant-land-cities/{stateId}', [PropertyOutSideController::class, 'getVacantLandCities']);




        //Vacant Land / Outside Delhi Plots Routes - Lalit Tiwari (02/June/2025)
        Route::get('/properties/view/vacant/lands', [PropertyOutSideController::class, 'index'])->name('vacant.land.list');
        Route::get('/get/vacant/land/list', [PropertyOutSideController::class, 'getUnallotedOutsideDelhiPropertyData'])->name('get.vacant.land.list.list');
        Route::get('mis/add/vacant/land', [PropertyOutSideController::class, 'create'])->name('create.vacant.land');
        Route::post('/vacant-land/store', [PropertyOutSideController::class, 'store'])->name('vacant.land.store');
        Route::get('/vacant-land/{id}/view', [PropertyOutsideController::class, 'show'])->name('vacant.land.view');
        Route::get('/get-cities', [PropertyOutSideController::class, 'getCities'])->name('get.cities.by.state');



        //Club Membership Routes - Lalit Tiwari (28/Jan/2025)
        Route::get('public-services/club-membership/finalized-applications', [ClubMembershipBackendController::class, 'index'])->name('club.membership.index')->middleware('permission:club.membership.list');
        Route::get('get/club-membership/listings', [ClubMembershipBackendController::class, 'getClubMembershipList'])->name('get.club.membership.list');
        Route::get('public-services/club-membership/received-applications', [ClubMembershipBackendController::class, 'indexReceived'])->name('club.membership.received.index')->middleware('permission:club.membership.list');
        Route::get('get/club-membership/received/listings', [ClubMembershipBackendController::class, 'getClubMembershipReceivedList'])->name('get.club.membership.received.list');
        Route::get('club-membership/details/{id}', [ClubMembershipBackendController::class, 'getClubMembershipDetails'])->name('getClubMembershipDetails')->middleware('permission:club.membership.view');
        Route::get('public-services/club-membership/add-new', [ClubMembershipBackendController::class, 'create'])->name('create.club.membership.form')->middleware('permission:club.membership.create');
        Route::post('club-membership/store', [ClubMembershipBackendController::class, 'store'])->name('store.club.membership.form');
        Route::post('club-membership/update-status', [ClubMembershipBackendController::class, 'updateStatus'])->name('update.club.membership.status')->middleware('permission:club.membership.action');
        Route::post('club-membership/allotment', [ClubMembershipBackendController::class, 'allotmentClubMembership'])->name('allotment.club.membership')->middleware('permission:club.membership.action');
        Route::get('club-membership/edit/{id}', [ClubMembershipBackendController::class, 'editClubMembershipDetails'])->name('editClubMembershipDetails')->middleware('permission:club.membership.update');
        Route::post('club-membership/update', [ClubMembershipBackendController::class, 'update'])->name('update.club.membership.form');
        Route::get('club-membership/club_memership_template_pdf', [ClubMembershipBackendController::class, 'downloadClubMembershipPDFTemplate'])->name('club.membership.pdf.template');
        Route::post('/club-membership/send-bulk-email', [ClubMembershipBackendController::class, 'sendBulkEmailMembership'])->name('club.membership.bulk.email');





        Route::get('/appointments', [AppointmentDetailController::class, 'index'])->name('appointments.index');
        Route::get('/admin-grievances', [AdminPublicGrievanceController::class, 'create'])->name('grievance.create');
        Route::get('applicant/profile', [ApplicantController::class, 'index'])->name('applicant.profile');
        Route::put('applicant/profile/update', [ApplicantController::class, 'updateProfile'])->name('applicant.profile.update');
        Route::get('application/new', [ApplicationController::class, 'newApplication'])->name('new.application');
        Route::get('applicant/property/details', [ApplicantController::class, 'propertiesDetails'])->name('applicant.properties')->middleware('role:applicant');
        Route::get('application/history', [ApplicantController::class, 'applicationHistory'])->name('application.history');


        Route::post('applicant/property/store', [ApplicantController::class, 'storeNewProperty'])->name('applicant.properties.store');

        /**route added by Nitin to book applicant appointment  20/Nov/24*/
        Route::get('applicant/appointment/{applicationId}/{timestamp}', [ApplicantController::class, 'appointment'])->name('applicant.apppointment');
        Route::get('applicant/getappointments', [ApplicantController::class, 'getappointments'])->name('applicant.getappointments');
        Route::post('applicant/book-appointment', [ApplicantController::class, 'bookAppointment'])->name('applicant.bookAppointment');
        Route::post('applicant/delete-alues-for-unchecked-document', [ApplicantController::class, 'deleteValuesForUncheckedDocument'])->name('applicant.deleteValuesForUncheckedDocument');
        Route::get('/applications/get-available-time-slots', [ApplicantController::class, 'getAvailableTimeSlots']);

        //appointment
        Route::get('/appointments', [AppointmentDetailController::class, 'index'])->name('appointments.index')->middleware('permission:view.appointment');
        Route::get('getAppointments', [AppointmentDetailController::class, 'getAppointments'])->name('get.appointments');
        Route::patch('/appointments/update-status/{id}', [AppointmentDetailController::class, 'updateStatus'])->name('appointments.updateStatus');
        Route::patch('/appointments/update-attendance/{id}', [AppointmentDetailController::class, 'updateAttendance'])->name('appointments.updateAttendance');

        //admin-grievance
        Route::get('/admin-grievances', [AdminPublicGrievanceController::class, 'create'])->name('grievance.create')->middleware('permission:add.grievance');
        Route::post('/grievance/store-initial', [AdminPublicGrievanceController::class, 'storeInitial'])->name('grievance.storeInitial');
        Route::post('/grievance/upload-recording', [AdminPublicGrievanceController::class, 'uploadRecording'])->name('grievance.uploadRecording');

        Route::post('/grievance/store', [AdminPublicGrievanceController::class, 'store'])->name('grievance.store');

        // Display the index DataTable view
        Route::get('/grievances', [AdminPublicGrievanceController::class, 'index'])->name('grievance.index')->middleware('permission:view.grievance');

        // Handle DataTables server-side processing
        Route::get('/grievances/list', [AdminPublicGrievanceController::class, 'getGrievances'])->name('grievance.getGrievances');

        Route::get('grievance/edit/{id}', [AdminPublicGrievanceController::class, 'edit'])->name('grievance.edit')->middleware('permission:edit.grievance');
        Route::put('grievance/update/{id}', [AdminPublicGrievanceController::class, 'update'])->name('grievance.update');
        Route::get('grievances/{id}/remarks', [AdminPublicGrievanceController::class, 'showRemarks'])->name('admin_public_grievances.remarks');

        Route::put('/grievance/update-remarks/{id}', [AdminPublicGrievanceController::class, 'updateRemarks'])->name('admin_public_grievances.update_remarks');
        Route::get('/grievances/details/{id}', [AdminPublicGrievanceController::class, 'getGrievanceDetails'])->name('grievance.details');

        Route::group(['middleware' => ['permission:view.litigation.list']], function () {

            //Litigation Module-added by swati mishra 
            Route::get('/litigation-cases', [LitigationController::class, 'create'])->name('litigation.create');
            Route::post('/litigation-cases/store', [LitigationController::class, 'store'])->name('litigation.store');
            Route::get('/litigation/get-property-section', [LitigationController::class, 'getPropertySection'])->name('litigation.get-property-section');
            Route::get('/litigation-index', [LitigationController::class, 'index'])->name('litigation.index');
            Route::get('/litigation/get', [LitigationController::class, 'getLitigations'])->name('get.litigations');
            Route::get('/litigation/{id}/edit', [LitigationController::class, 'edit'])->name('litigation.edit');
            Route::put('/litigation/{id}', [LitigationController::class, 'update'])->name('litigation.update');
            Route::get('/litigation/view/{id}', [LitigationController::class, 'show'])->name('litigation.show');
            Route::delete('/litigation/{id}', [LitigationController::class, 'destroy'])->name('litigation.destroy');
        });

        //uploaded on live - SOURAV CHAUHAN - 16/Jan/2024m *************************** START ************************
        //applications route at admin side - SOURAV cHAUHAN (11/oct/2024)
        Route::get('admin/applications', [AdminApplicationController::class, 'index'])->name('admin.applications')->middleware('permission:view.applications');
        Route::get('admin/assigned-applications', [AdminApplicationController::class, 'myapplications'])->name('admin.myapplications')->middleware('permission:view.applications');
        Route::get('admin/forwarded-applications', [AdminApplicationController::class, 'forwardedApplications'])->name('admin.forwardedApplications')->middleware('permission:view.applications');
        Route::get('getApplications', [AdminApplicationController::class, 'getApplications'])->name('admin.getApplications')->middleware('permission:view.applications');
        Route::get('getMyApplications', [AdminApplicationController::class, 'getMyApplications'])->name('admin.getMyApplications')->middleware('permission:view.applications');
        Route::get('admin/getForwardedApplications', [AdminApplicationController::class, 'getForwardedApplications'])->name('admin.getForwardedApplications')->middleware('permission:view.applications');
        Route::get('getApplicationsAssignedToUser', [AdminApplicationController::class, 'getApplicationsAssignedToUser'])->name('admin.getApplicationsAssignedToUser')->middleware('permission:view.applications');
        Route::get('applications/{id}', [AdminApplicationController::class, 'view'])->name('applications.view')->middleware('permission:view.applications');
        Route::post('application/can-view', [AdminApplicationController::class, 'applicationCanView'])->name('applications.applicationCanView')->middleware('permission:view.applications');
        Route::post('upload-file-for-cdv', [AdminApplicationController::class, 'uploadFileforCdv'])->name('uploadFileforCdv');
        Route::post('delete-file-for-cdv', [AdminApplicationController::class, 'deleteFileforCdv'])->name('deleteFileforCdv');
        Route::post('save-official-documents', [AdminApplicationController::class, 'saveOfficialDocs'])->name('saveOfficialDocs');

        //Rollback Applications routes- added by Swati Mishra (17/02/2026)
        Route::get('rollback-application', [RollbackApplicationController::class, 'view'])->name('admin.rollback');
        Route::get('rollback-applications/fetch', [RollbackApplicationController::class, 'fetch'])
            ->name('admin.rollback-applications.fetch');
        Route::post('rollback-application', [RollbackApplicationController::class, 'store'])
            ->name('admin.rollback.store');
        Route::get('rollback-applications/has-app-obj', [RollbackApplicationController::class, 'hasAppObj'])
            ->name('admin.rollback-applications.has-app-obj');


        //Registration / Applications Report routes- added by Lalit Tiwari (07/08/2026)
        Route::prefix('registrations')->name('registrations.')->group(function () {
            Route::get('/report', [RegistrationReportController::class, 'index'])->name('report');
            Route::get('/get-report-data', [RegistrationReportController::class, 'getReportData'])->name('get-report-data');
            Route::get('/export-pdf', [RegistrationReportController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/export-csv', [RegistrationReportController::class, 'exportCsv'])->name('export-csv');
            Route::get('/print-registration', [RegistrationReportController::class, 'printRegistration'])->name('print-registration');
            Route::get('/print-application', [RegistrationReportController::class, 'printApplication'])->name('print-application');
            Route::get('/print-both', [RegistrationReportController::class, 'printBoth'])->name('print-both');
        });
        Route::get('/registration-summary/details', [RegistrationReportController::class, 'registrationSummaryDetails'])->name('registrationSummaryDetails');



        // Received & Disposed Applications Routes - Lalit Tiwari (27/Feb/2025)
        Route::get('admin/applications/disposed', [AdminApplicationController::class, 'applicationsDisposed'])->name('applications.disposed');
        Route::get('getApplicationsDisposed', [AdminApplicationController::class, 'getApplicationsDisposed'])->name('get.applications.disposed');
        //route added by nitin for technical users

        Route::get('application/remove', [AdminApplicationController::class, 'removeApplication'])->name('remove.application')->middleware('permission:remove.application.or.registration');
        Route::post('application/remove/action', [AdminApplicationController::class, 'removeApplicationAction'])->name('remove.application.action');

        Route::get('admin/applicationsAssignedToUser/{onlyCurrentApplicatinos?}', [AdminApplicationController::class, 'applicationsAssignedToUser'])->name('admin.applicationsAssignedToUser')->middleware('permission:view.applications');

        // for application actions by official users
        Route::post('applications/action', [AdminApplicationController::class, 'applicationAction'])->name('applications.action')->middleware('permission:applications.action');
        Route::post('applications/get/movements', [AdminApplicationController::class, 'getFileMovements'])->name('applications.get.movements')->middleware('permission:view.applications');
        Route::get('applications/{appNo}/movement', [AdminApplicationController::class, 'getFileMovements'])->name('applications.movements')->middleware('permission:view.applications');
        Route::get('applications/movement/new', [AdminApplicationController::class, 'newgetFileMovements'])->name('applications.new.movements');
        Route::post('applications/checklist', [AdminApplicationController::class, 'checklist'])->name('applications.checklist')->middleware('permission:applications.action');
        Route::post('applications/upload-signed-letter', [AdminApplicationController::class, 'uploadSignedLetter'])->name('uploadSignedLetter');
        Route::get('applications/{id}/start-proof-reading', [AdminApplicationController::class, 'startProofReading'])->name('startProofReading');
        Route::post('applications-object-remark', [AdminApplicationController::class, 'applicationsObjectRemark'])->name('applications.object.remark');

        //Routes for Property Transfer - Lalit (5/March/2025)
        Route::get('miscellaneous/property-transfer', [OfficialController::class, 'miscPropertyTransfer'])->name('miscellaneous.property.transfer')->middleware('permission:miscellaneous.property.transfer');
        Route::post('property-transfer-section', [OfficialController::class, 'propertyTransferSection'])->name('property.transfer.section');

        //Send Application Meeting Url to the Applicant Route - Lalit Ji - (22/Nov/2024)
        Route::post('applications/appointment/link', [AdminApplicationController::class, 'sendAppointmentLinkToApplicant'])->name('applications.send.appointment.link');

        Route::post('app-get-flat-details', [ApplicationController::class, 'appGetFlatDetails'])->name('appGetFlatDetails');
        //uploaded on live - SOURAV CHAUHAN - 16/Jan/2024m *************************** END ************************

        Route::post('request-edit-mis', [OfficialController::class, 'requestEditMis'])->name('requestEditMis');
        Route::get('mis/update/request/list', [OfficialController::class, 'misUpdateRequestList'])->name('misUpdateRequestList')->middleware('permission:section.property.mis.update.request');
        Route::get('getUpdatePropertyDetailsList', [OfficialController::class, 'getUpdatePropertyDetailsList'])->name('get.update.property.details.list');
        Route::post('allowEditPermission', [OfficialController::class, 'allowEditPermission'])->name('allow.edit.permission');

        //route added by Nitin to get details of a property

        Route::post('/property-basic-detail', [CommonController::class, 'propertyBasicdetail'])->name('propertyCommonBasicdetail');

        // Add this route to your web.php file
        Route::post('/get-flat-details', [CommonController::class, 'getFlatDetails'])->name('get.flat.details');

        Route::post('property-details/export', [ReportController::class, 'detailsExport'])->name('detailsExport');

        //Filter report routes moved from Admin group to auth only by Amita [12-02-2025]
        Route::post('report/filter', [ReportController::class, 'getPropertyResults'])->name('getPropertyResults');
        Route::post('report/get-distinct-subType', [ReportController::class, 'getDistinctSubTypes'])->name('getDistinctSubTypes');
        Route::post('report/export', [ReportController::class, 'reportExport'])->name('reportExport');

        Route::get('/reports/unalloted-properties', [ReportController::class, 'unallotedPropertyView'])->name('unallotedReport');

        Route::get('get/properties/unalloted', [ReportController::class, 'getUnallotedProperties'])->name('getUnallotedProperties');

        Route::get('/reports/colony-wise-filter-report', [ReportController::class, 'colonyWiseIndex'])->name('colony.wise.reports.index');
        Route::post('report/colony-wise-filter', [ReportController::class, 'getColonyWisePropertyResults'])->name('getColonyWisePropertyResults');
        Route::post('report/colony-wise-filter/export', [ReportController::class, 'colonyWiseReportExport'])->name('colonyWiseReportExport');


        Route::group(['middleware' => ['isAdmin']], function () {
            Route::get('/downloadBKP', [CommonController::class, 'downloadBackup'])->name('downloadBackup');

            Route::get('/logistic/items', [ItemsController::class, 'index'])->name('logistic.index');
            Route::get('/logistic/items/add', [ItemsController::class, 'create'])->name('logistic.create');
            Route::post('/logistic_items', [ItemsController::class, 'store'])->name('logistic_items.store');
            Route::post('/logistic/items/update', [ItemsController::class, 'update'])->name('logistic_items.update');
            Route::post('/logistic/items/updatelabel', [ItemsController::class, 'updatelabel'])->name('updatelabel');
            // Route::get('/logistic/items/{roleId}/delete', [ItemsController::class, 'destroy'])->name('logistic.delete');
            Route::get('logistic/items/{itemId}/update-status', [ItemsController::class, 'updateStatus'])->name('items.updateStatus');
            Route::get('/logistic/items/{id}/edit', [ItemsController::class, 'edit'])->name('logistic.edit');
            Route::put('/logistic/items/{id}', [ItemsController::class, 'update'])->name('logistic.update');
            Route::get('/logistic/items/autocomplete', [ItemsController::class, 'autocomplete'])->name('items.autocomplete');

            Route::get('/logistic/category', [CategoryController::class, 'index'])->name('category.index');
            Route::post('/logistic/category/update', [CategoryController::class, 'update'])->name('category.update');
            Route::get('/logistic/category/add', [CategoryController::class, 'create'])->name('category.create');
            Route::post('/logistic_category', [CategoryController::class, 'store'])->name('logistic_category.store');
            Route::post('logistic/category/{itemId}/update-status', [CategoryController::class, 'updateStatus'])->name('category.updateStatus');
            // Route::get('/logistic/category/{roleId}/delete', [CategoryController::class, 'destroy'])->name('logisticCategory.delete');
            Route::get('/logistic/category/autocomplete', [CategoryController::class, 'autocomplete'])->name('category.autocomplete');
            Route::post('/logistic/category/check-name', [CategoryController::class, 'checkName'])->name('logistic_category.checkName');
            // routes/web.php or routes/api.php
            Route::get('/check-contact/{contact_no}', [SupplierVendorDetailsController::class, 'checkContact'])->name('check.contact');
            Route::get('/check-email/{email}', [SupplierVendorDetailsController::class, 'checkEmail'])->name('check.email');


            Route::get('/logistic/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
            Route::get('getPurchaseItems', [PurchaseController::class, 'getPurchaseItems'])->name('get.purchase.items');
            Route::get('/logistic/purchase/add', [PurchaseController::class, 'create'])->name('purchase.create');
            Route::post('/logistic_purchase', [PurchaseController::class, 'store'])->name('logistic_purchase.store');
            Route::get('/logistic/purchase/{roleId}/delete', [PurchaseController::class, 'destroy']);
            Route::get('/logistic/purchase/{id}/edit', [PurchaseController::class, 'edit'])->name('purchase.edit');
            Route::put('/logistic/purchase/{id}', [PurchaseController::class, 'update'])->name('purchase.update');
            Route::get('/logistic/available-units/{logisticItemId}', [PurchaseController::class, 'getAvailableUnits']);


            Route::get('/logistic/history', [PurchaseController::class, 'indexHistory'])->name('purchaseHistory.index');
            Route::get('getLogisticHistories', [PurchaseController::class, 'getLogisticHistories'])->name('get.logistic.histories');
            Route::get('/logistic/stock', [PurchaseController::class, 'stockIndex'])->name('purchaseStock.index');


            Route::get('/logistic/issued-item', [IssueController::class, 'create'])->name('issued_item.create');
            Route::post('/logistic/issued-item', [IssueController::class, 'store'])->name('issued_item.store');
            Route::get('/get-available-units/{id}', [PurchaseController::class, 'getAvailableUnits']);
            // Route::post('/logistic/issued-item/{issuedItem}', [IssueController::class, 'update'])->name('issued_item.update');
            // Route::get('/logistic/issued-items', [IssueController::class, 'index'])->name('issued_item.index');
            // Route::post('/logistic/issued-items/update', [IssueController::class, 'updateEditable']);

            Route::get('/logistic/requested-items', [RequestProcessingController::class, 'index'])->name('requested_item.index');
            Route::get('getLogisticRequestItems', [RequestProcessingController::class, 'getLogisticRequestItems'])->name('get.logistic.request.items');
            Route::get('/logistics/request/{requestId}/create', [RequestProcessingController::class, 'create'])->name('request.create');
            Route::post('/logistics/request/{requestId}/update', [RequestProcessingController::class, 'updateStatus'])->name('request.update');


            Route::get('/logistic/vendor', [SupplierVendorDetailsController::class, 'index'])->name('supplier.index');
            Route::get('/logistic/vendor/add', [SupplierVendorDetailsController::class, 'create'])->name('supplier.create');
            Route::post('/supplier_vendor_details', [SupplierVendorDetailsController::class, 'store'])->name('supplier_vendor_details.store');
            // Route::get('/logistic/vendor/{roleId}/delete', [SupplierVendorDetailsController::class, 'destroy']);
            Route::get('logistic/vendor/{itemId}/update-status', [SupplierVendorDetailsController::class, 'updateStatus'])->name('supplier.updateStatus');
            Route::get('/logistic/vendor/{id}/edit', [SupplierVendorDetailsController::class, 'edit'])->name('supplier.edit');
            Route::put('/logistic/vendor/{id}', [SupplierVendorDetailsController::class, 'update'])->name('supplier.update');


            //Permissions
            Route::resource('permissions', App\Http\Controllers\PermissionController::class);
            Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);
            Route::get('get/permissions', [App\Http\Controllers\PermissionController::class, 'getPermissions'])->name('get.permissions');


            //Roles
            Route::resource('roles', App\Http\Controllers\RoleController::class);
            Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);
            Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'addPermissionToRole'])->name('roles.addPermission');;
            Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'givePermissionToRole'])->name('roles.givePermission');
            Route::get('get/roles', [App\Http\Controllers\RoleController::class, 'getRoles'])->name('get.roles');

            //Users
            // Route::resource('users', App\Http\Controllers\UserController::class);
            // Route::get('getUserList', [App\Http\Controllers\UserController::class, 'getUserList'])->name('getUserList');
            // Route::get('users/delete/{userId}', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.delete')->middleware('permission:delete user');
            // Route::get('users/{id}/status', [App\Http\Controllers\UserController::class, 'status'])->name('user.status')->middleware('permission:status.user');
            // Route::get('users/update-user-details/{id}', [App\Http\Controllers\UserController::class, 'updateUserDetails'])->name('user.details.update');
            // Route::put('/users/{user}/update', [App\Http\Controllers\UserController::class, 'updateUser'])->name('user.update');
            // Route::put('/users/permission/update/{id}', [App\Http\Controllers\UserController::class, 'updateUser'])->name('user.permission.update');
            // Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
            Route::resource('users', App\Http\Controllers\UserController::class)->except(['show']);
            Route::get('getUserList', [App\Http\Controllers\UserController::class, 'getUserList'])->name('getUserList');
            // Add this custom route before the resource route
            Route::get('users/add', [App\Http\Controllers\UserController::class, 'create'])->name('users.add');
            Route::get('users/{userId}/delete', [App\Http\Controllers\UserController::class, 'destroy'])->middleware('permission:delete user')->name('users.destroy');
            Route::get('users/{id}/status', [App\Http\Controllers\UserController::class, 'status'])->name('user.status')->middleware('permission:status.user');
            Route::get('users/{id}/update-user-details', [App\Http\Controllers\UserController::class, 'updateUserDetails'])->name('user.details.update');
            Route::put('/users/{user}/update', [App\Http\Controllers\UserController::class, 'updateUser'])->name('user.update');
            Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

            // Routes for showing unalloted recores By Lalit Tiwari (10/Jan/2025)
            // Route::get('/reports/detailed-report', [ReportController::class, 'detailedReport'])->name('detailedReport');






            Route::group(['middleware' => ['can:create.rgr']], function () {
                Route::get('rgr', [RgrController::class, 'index'])->name('rgr');
                Route::get('rgr-single-property', [RgrController::class, 'singlePropertyRGRInput'])->name('singlePropertyRGRInput');
                Route::get('rgr-colony', [RgrController::class, 'rgrColony'])->name('rgrColony');

                Route::post('rgr/create', [RgrController::class, 'create'])->name('createRgr');
                Route::post('rgr/calculate', [RgrController::class, 'calculateGroundRent'])->name('calculateGroundRent');

                Route::post('rgr/check-area-chnaged', [RgrController::class, 'checAreaChanged'])->name('checAreaChanged');
                Route::post('rgr/check-property-status-chnaged', [RgrController::class, 'checkPropertyStatusChanged'])->name('checkPropertyStatusChanged');
                Route::post('rgr/save-edited-rgr', [RgrController::class, 'saveEditedRGR'])->name('saveEditedRGR');
                Route::post('rgr/edit-multiple-rgr', [RgrController::class, 'editMultipleRGR'])->name('editMultipleRGR');
                Route::get('rgr/complete-list/{id?}', [RgrController::class, 'completeList'])->name('completeList');
            });
            Route::group(['middleware' => ['can:view.rgr.list']], function () {
                Route::get('rgr/list', [RgrController::class, 'list'])->name('rgrList');
                Route::get('rgr/property-rgr-details', [RgrController::class, 'rgrDetailsForProperty'])->name('rgrDetailsForProperty');
            });
            Route::group(['middleware' => ['can:create.rgr.draft']], function () {
                Route::get('rgr/save-as-pdf/{id}', [RgrController::class, 'saveAsPdf'])->name('saveAsPdf');
                Route::post('rgr/save-multiple-pdf', [RgrController::class, 'saveMultiplePdf'])->name('saveMultiplePdf');
            });
            Route::group(['middleware' => ['can:send.rgr.draft']], function () {
                Route::get('rgr/send-draft/{rgrId}', [RgrController::class, 'sendDraft'])->name('sendDraft');
                Route::post('rgr/send-multiple-draft', [RgrController::class, 'sendMultipleDrafts'])->name('sendMultipleDrafts');
                Route::get('rgr/track-pdf-progress', [RgrController::class, 'trackPdfProgress'])->name('trackPdfProgress');
                Route::get('rgr/track-email-progress', [RgrController::class, 'trackEmailProgress'])->name('trackEmailProgress');
            });
            Route::post('rgr/colony-rgr', [RgrController::class, 'reviseGroundRentForColony'])->name('reviseGroundRentForColony');
            Route::get('rgr/view-draft/{rgrId}', [RgrController::class, 'viewDraft'])->name('viewDraft');
            Route::get('rgr/status-change-list', [RgrController::class, 'statusChangeList'])->name('statusChangeList');
            Route::get('rgr/area-change-list', [RgrController::class, 'areaChangeList'])->name('areaChangeList');
            Route::get('rgr/reentered-list', [RgrController::class, 'reenteredList'])->name('reenteredList');

            Route::post('rgr/property-basic-detail', [RgrController::class, 'propertyBasicdetail'])->name('propertyBasicdetail');
            Route::post('rgr/colony-details', [RgrController::class, 'colonyRGRDetails'])->name('colonyRGRDetails');





            //Communication Templates
            Route::get('/message-templates', [MessageTempleteController::class, 'show'])->name('msgtempletes');
            Route::get('getMessageTemplateListing', [MessageTempleteController::class, 'getMessageTemplateListing'])->name('getMessageTemplateListing');
            Route::get('/template/use/{id}', [MessageTempleteController::class, 'useTemplate'])->name('template.use');
            Route::put('/templates/{id}', [MessageTempleteController::class, 'update'])->name('templates.update')->defaults('skipSanitization', true);
            Route::get('/template/status/{id}', [MessageTempleteController::class, 'updateStatus'])->name('template.status');
            Route::get('/templates/create', [MessageTempleteController::class, 'create'])->name('templates.create');
            Route::post('/templates', [MessageTempleteController::class, 'store'])->name('templates.store')->defaults('skipSanitization', true);


            //Common Activities
            Route::get('change-date', [App\Http\Controllers\CommonController::class, 'changeLeaseExpirationDate'])->name('changeLeaseExpirationDate'); //To update the expiration date -- 22-05-2024

            //2 sep 2024 By sourav
            //settings*************************************************************
            //Mail
            Route::get('/setting/mail', [MailController::class, 'index'])->name('settings.mail.index');
            Route::get('/setting/mail/create', [MailController::class, 'create'])->name('settings.mail.create');
            Route::post('/setting/mail/store', [MailController::class, 'store'])->name('settings.mail.store');
            Route::get('/setting/mail/status/{id}', [MailController::class, 'updateStatus'])->name('settings.mail.status')->middleware('permission:settings.mail.status');
            Route::get('/setting/mail/{id}', [MailController::class, 'edit'])->name('settings.mail.edit')->middleware('permission:settings.mail.update');
            Route::put('/setting/mail/{id}', [MailController::class, 'update'])->name('settings.mail.update')->middleware('permission:settings.mail.update');
            Route::get('/setting/mail/{id}/test', [MailController::class, 'mailTest'])->name('settings.mail.mailTest');

            // Import data from excel
            Route::get('/import', [ImportController::class, 'showImportForm'])->name('import.form');
            Route::post('/import', [ImportController::class, 'importTable'])->name('import.table');

            //Sms
            Route::get('/setting/sms', [SmsController::class, 'index'])->name('settings.sms.index');
            Route::get('getSmsSettings', [SmsController::class, 'getSmsSettings'])->name('get.sms.settings');
            Route::get('/setting/sms/create', [SmsController::class, 'create'])->name('settings.sms.create');
            Route::post('/setting/sms/store', [SmsController::class, 'store'])->name('settings.sms.store');
            Route::get('/setting/sms/status/{id}', [SmsController::class, 'updateStatus'])->name('settings.sms.status')->middleware('permission:settings.sms.status');
            Route::get('/setting/sms/{id}', [SmsController::class, 'edit'])->name('settings.sms.edit')->middleware('permission:settings.sms.update');
            Route::put('/setting/sms/{id}', [SmsController::class, 'update'])->name('settings.sms.update')->middleware('permission:settings.sms.update');
            Route::get('/setting/sms/{id}/test', [SmsController::class, 'smsTest'])->name('settings.sms.smsTest');


            //Whatsapp
            Route::get('/setting/whatsapp', [WhatsappController::class, 'index'])->name('settings.whatsapp.index');
            Route::get('getWhatsappSettings', [WhatsappController::class, 'getWhatsappSettings'])->name('get.whatsapp.settings');
            Route::get('/setting/whatsapp/create', [WhatsappController::class, 'create'])->name('settings.whatsapp.create');
            Route::post('/setting/whatsapp/store', [WhatsappController::class, 'store'])->name('settings.whatsapp.store');
            Route::get('/setting/whatsapp/status/{id}', [WhatsappController::class, 'updateStatus'])->name('settings.whatsapp.status')->middleware('permission:settings.whatsapp.status');
            Route::get('/setting/whatsapp/{id}', [WhatsappController::class, 'edit'])->name('settings.whatsapp.edit')->middleware('permission:settings.whatsapp.update');
            Route::put('/setting/whatsapp/{id}', [WhatsappController::class, 'update'])->name('settings.whatsapp.update')->middleware('permission:settings.whatsapp.update');
            Route::get('/setting/whatsapp/{id}/test', [WhatsappController::class, 'whatsappTest'])->name('settings.whatsapp.whatsappTest');


            //actions
            Route::get('/setting/action', [ActionController::class, 'index'])->name('settings.action.index');
            Route::get('/setting/action/create', [ActionController::class, 'create'])->name('settings.action.create');
            Route::post('/setting/action/store', [ActionController::class, 'store'])->name('settings.action.store');
            Route::get('/setting/action/{id}', [ActionController::class, 'index'])->name('settings.action.edit')->middleware('permission:settings.action.update');
            Route::put('/setting/action/{id}', [ActionController::class, 'update'])->name('settings.action.update')->middleware('permission:settings.action.update');

            //property assignment routes
            Route::get('/property-assignment', [PropertySectionMappingController::class, 'propertyAssignment'])->name('propertyAssignment');
            Route::post('/get-all-property-types', [PropertySectionMappingController::class, 'getAllPropertyTypes'])->name('getAllPropertyTypes');
            Route::post('/property-assignment', [PropertySectionMappingController::class, 'propertyAssignmentStore'])->name('propertyAssignmentStore');
            Route::post('/i-colony-assigned-to-section', [PropertySectionMappingController::class, 'isColonyAssignedToSection'])->name('isColonyAssignedToSection');

            //property merging routes
            Route::get('/colony-merger', [PropertyMergingController::class, 'create'])->name('colony.merger.create');
            Route::post('/colony/merge', [PropertyMergingController::class, 'merge'])->name('colony.merge');
            Route::get('/colony/property-count', [PropertyMergingController::class, 'getPropertyCount'])->name('colony.propertyCount');
        });

        Route::get('rgr/properties-in-block/{colonyId}/{blockId}/{leaseHoldOnly?}/{withOutLeaseHold?}', [RgrController::class, 'propertiesInBlock'])->name('propertiesInBlock');
        Route::get('rgr/blocks-in-colony/{colonyId}/{leaseHoldOnly?}/{withOutLeaseHold?}', [RgrController::class, 'blocksInColony'])->name('blocksInColony');
        //Again added routes for the calculator --Amita [21-01-2025]
        //Routes related to calculator
        Route::group(['middleware' => ['can:calculate.conversion']], function () {
            Route::get('/conversion/calculate-charges', [ConversionController::class, 'calculateConversionCharges'])->name('calculateConversionCharges');
            Route::get('conversion/charges-for-property', [ConversionController::class, 'chargesForProperty'])->name('chargesForProperty');
        });

        //Property Scanning routes by Swati on 12-06-2025
        Route::get('/property-scanning', [PropertyScannedFileController::class, 'create'])->name('property.scanning.create')->middleware('permission:add.scanning.files');
        Route::post('/property-scanning/store', [PropertyScannedFileController::class, 'store'])->name('property.scanning.store');
        Route::post('/property-scanning/search', [PropertyScannedFileController::class, 'searchProperty'])->name('property.scanning.search');
        Route::get('/property-scanning/files', [PropertyScannedFileController::class, 'index'])->name('scanning.index')->middleware('permission:view.scanning.list');
        Route::get('/property-scanning/files/data', [PropertyScannedFileController::class, 'getScannedFiles'])->name('scanning.data');
        Route::get('/scanning/view/{property_id}', [PropertyScannedFileController::class, 'view'])->name('property.scanning.view')->middleware('permission:view.scanning.files');
        Route::post('/scanning/files/delete-by-property', [PropertyScannedFileController::class, 'deleteByProperty'])
            ->middleware('role:super-admin')
            ->name('scanning.deleteByProperty');

        Route::get('/property-scanning/report', [PropertyScannedFileController::class, 'scanningReport'])->name('scanning.report');

        Route::get('scanned-requests', [PropertyScannedRequestController::class, 'index'])->name('scanned.request.index');
        Route::get('scanned-requests/data', [PropertyScannedRequestController::class, 'getScannedRequests'])->name('scanned.request.data');
        Route::post('/property-scanning/send-to-scan', [PropertyScannedRequestController::class, 'sendToScan'])->name('property.scanning.sendToScan');
        Route::post('/property-scanning/close-scan', [PropertyScannedRequestController::class, 'closeScan'])->name('property.scanning.closeScan');
        Route::post('/property/scanning/return-to-record', [PropertyScannedRequestController::class, 'returnToRecord'])
            ->name('property.scanning.returnToRecord');
        Route::post('/property-scanning/delete', [PropertyScannedRequestController::class, 'deleteRequest'])
            ->middleware('role:super-admin')->name('property.scanning.deleteRequest');
        Route::get('/property-scanning/{propertyId}/download-all', [PropertyScannedFileController::class, 'downloadAll'])
            ->name('property.scanning.downloadAll');
        Route::get('/property-scanning/requests/export/csv', [PropertyScannedRequestController::class, 'exportCsv'])
            ->name('scanned.request.export.csv');

        Route::get('/property-scanning/requests/export/excel', [PropertyScannedRequestController::class, 'exportExcel'])
            ->name('scanned.request.export.excel');

        Route::get('/property-scanning/{pmId}/{splitId?}/download-zip', [PropertyScannedFileController::class, 'downloadZipByMasterSplit'])
            ->name('property.scanning.downloadZipByMasterSplit');

        Route::group(['middleware' => ['can:calculate.landUseChange']], function () {
            Route::get('land-use-change/calculate-charges', [LandUseChangeCalculationController::class, 'calculateLandUseChangeCharges'])->name('calculateLandUseChangeCharges');
            Route::get('land-use-change/property-type-options/{propertyId}', [LandUseChangeCalculationController::class, 'propertyTypeOptions'])->name('propertyTypeOptions');
        });
        Route::group(['middleware' => ['can:calculate.unearnedIncrease']], function () {
            Route::get('unearned-increase', [UnearnedIncreaseController::class, 'index'])->name('calculateUnearnedIncrease');
            Route::get('unearned-increase/property-details/{propertyId}', [UnearnedIncreaseController::class, 'propertyDetails'])->name('calculateUnearnedIncreaseForProperty');
        });
	
	//End of Calculator routes
        /** create demand routes added by Nitin 30-12-2024 */
        Route::group(['middleware' => ['permission:create.demand']], function () {
            Route::get('/demand', [DemandController::class, 'createDemandView'])->name('createDemandView');
            Route::get('/demand/edit/{demandId}/{applicationData?}', [DemandController::class, 'EditDemand'])->name('EditDemand');

            Route::get('/demand/withdraw/{demandId}', [DemandController::class, 'withdrawDemand'])->name('withdrawDemand');
            Route::get('/demand/approve/{demandId}', [DemandController::class, 'ApproveDemand'])->name('ApproveDemand');
            // Route::get('/demand/getExistingPropertyDemand/{oldPropertyId}', [DemandController::class, 'getExistingPropertyDemand'])->name('getExistingPropertyDemand');
            Route::get('/demand/getExistingPropertyDemand/{oldPropertyId}/{flatId?}', [DemandController::class, 'getExistingPropertyDemand'])->name('getExistingPropertyDemand');
            Route::post('/demand/store', [DemandController::class, 'storeDemand'])->name('storeDemand');
            Route::get('/demand/create-applicationDemand/{applicationNo}', [DemandController::class, 'createApplicationDemand'])->name('createApplicationDemand');
            Route::get('/demand/old-demand-data/{oldDemands}', [DemandController::class, 'oldDemandData'])->name('oldDemandData');
            Route::get('/demand/get-demand-heads/{newAllotment}', [DemandController::class, 'getDemandHeads'])->name('getDemandHeads');
            Route::get('/demand/get-land-value-at-date', [CommonController::class, 'getLandValueAtDate'])->name('getLandValueAtDate');
            // Route::get('/demand/manual-demand-create', [DemandController::class, 'manualDemandCreate'])->name('manualDemandCreate');
            // Route::get('/land-use-change/commercial-land-value/{propertyId}', [LandUseChangeCalculationController::class, 'getCommercialLandValue'])->name('getCommercialLandValue');
            Route::get('/land-use-change/land-value', [LandUseChangeCalculationController::class, 'getSaughtLandValue'])->name('getSaughtLandValue');
            Route::get('/demand/old-demmand-breakup/{oldDemandId}', [DemandController::class, 'oldDemandBreakUp'])->name('oldDemandBreakUp');
            Route::get('/demand/outstanding-dues-list/{propertyStatus?}', [DemandController::class, 'outStandingDuesList'])->name('outStandingDuesList');

            Route::get('/demand/old-demand-list', [DemandController::class, 'oldDemandList'])->name('oldDemandList')->middleware('permission:old.demand.list');
            Route::get('/old-demand-reminder-details/{demandId}', [DemandController::class, 'oldDemandReminderDetails'])->name('oldDemandReminderDetails')->middleware('permission:old.demand.mail');
            Route::post('/send-old-demand-reminder', [DemandController::class, 'sendOldDemandReminder'])->name('sendOldDemandReminder')->middleware('permission:old.demand.mail');
            Route::get('/update-old-demand-sections', [DemandController::class, 'updateOldDemandSections'])->name('updateOldDemandSections'); //script for updating section 

        });


        Route::group(['middleware' => ['permission:create.demand|view.demand']], function () {
            Route::get('/demand/index', [DemandController::class, 'index'])->name('demandList');
            Route::get('/demand/view/{demandId}', [DemandController::class, 'ViewDemand'])->name('ViewDemand');
            Route::get('/demand-letter/{id}', [DemandController::class, 'demandLetterPdf'])->name('demand.demand_letter_pdf');
            Route::get('/demand-summary', [DemandController::class, 'demandSummary'])->name('demandSummary');
            Route::get('/demand-summary/details', [DemandController::class, 'demandSummaryDetails'])->name('demandSummaryDetails');
        });

        // Dasboard by vivek ji on 16 september 
        Route::get('/dashboard/master', [DashboardController::class, 'masterDashboard'])->name('dashboard.master')->middleware('permission:master.dashboard');
        Route::get('/monthly-payments', [DashboardController::class, 'getMonthlyPayments'])->name('monthly.payments')->middleware('permission:master.dashboard');
        Route::get('/chart/monthly-applications', [DashboardController::class, 'getMonthlyApplications'])->middleware('permission:master.dashboard');
        Route::get('/get-breakup', [DemandController::class, 'getBreakup'])->name('get.breakup');
        Route::get('/applicant/pending-demands', [DemandController::class, 'applicantPendingDemands'])->name('applicant.pendingDemands');
        Route::get('/applicant/view-demand/{demandId}', [DemandController::class, 'applicantViewDemand'])->name('applicant.viewDemand');
        Route::get('/applicant/pay-demand/{demandId}', [DemandController::class, 'applicantPayForDemand'])->name('applicant.payForDemand');
        Route::get('/propertysummarizereport', [MasterReportController::class, 'propertySummarizeReport'])->name('propertysummarizereport');
        Route::post('/property-all-detail', [MasterReportController::class, 'propertyAlldetail'])->name('propertyAlldetail');
        Route::any('/db-viewer', [DbViewerController::class, 'index']);
        Route::get('/get-olddemandbreakup', [DemandController::class, 'getolddemandbreakup'])->name('get.olddemandbreakup');
        Route::get('/inspection-request', [InspectionController::class, 'inspectionRequest'])->name('inspectionRequest')->middleware('permission:create.inspection');
        Route::post('/save-inspection-request', [InspectionController::class, 'store'])->name('save.inspection.request');
        Route::post('/save-inspection-report', [InspectionController::class, 'storeReport'])->name('save.inspection.report');
        Route::get('/get-inspection-status', [InspectionController::class, 'getInspectionStatus'])->name('getInspectionStatus');
        Route::get('/inspectionlist', [InspectionController::class, 'inspectionList'])->name('inspectionlist')->middleware('permission:view.inspection');
        Route::post('/inspection/save-breach-row', [InspectionController::class, 'saveBreachRow'])->name('inspection.saveBreachRow');
        Route::post('/inspection/save-unauthorised-row', [InspectionController::class, 'saveUnauthorisedRow'])->name('inspection.saveUnauthorisedRow');
        Route::post('/inspection/delete-row', [InspectionController::class, 'deleteRow'])->name('inspection.delete.row');
        Route::get('/inspection/action/{id}', [InspectionController::class, 'inspectionAction'])->name('inspection.action');


        Route::group(['middleware' => ['can:calculate.landUseChange']], function () {
            Route::get('land-use-change/calculate-charges', [LandUseChangeCalculationController::class, 'calculateLandUseChangeCharges'])->name('calculateLandUseChangeCharges');
            Route::get('land-use-change/property-type-options/{propertyId}', [LandUseChangeCalculationController::class, 'propertyTypeOptions'])->name('propertyTypeOptions');
            //Route::get('landUseChange/charges-for-property/{propertyId}', [ConversionController::class, 'chargesForProperty'])->name('chargesForProperty');
        });
        Route::group(['middleware' => ['can:calculate.unearnedIncrease']], function () {
            Route::get('unearned-increase', [UnearnedIncreaseController::class, 'index'])->name('calculateUnearnedIncrease');
            Route::get('unearned-increase/property-details/{propertyId}', [UnearnedIncreaseController::class, 'propertyDetails'])->name('calculateUnearnedIncreaseForProperty');
            //Route::get('land-use-change/property-type-options/{propertyId}', [LandUseChangeCalculationController::class, 'propertyTypeOptions'])->name('propertyTypeOptions');
            //Route::get('landUseChange/charges-for-property/{propertyId}', [ConversionController::class, 'chargesForProperty'])->name('chargesForProperty');
        });

        //Payment details added on 25-07-2025
        Route::get('/payment-summary', [PaymentController::class, 'paymentSummary'])->name('paymentSummary')->middleware('permission:view.financial.reports');
        Route::get('/payment-summary/details', [PaymentController::class, 'paymentSummaryDetails'])->name('paymentSummaryDetails')->middleware('permission:view.financial.reports');
        Route::get('/payment-report', [PaymentController::class, 'paymentReport'])->name('paymentReport')->middleware('permission:view.payment.reports');

        Route::group(['middleware' => ['permission:view.application.reports']], function () {
            Route::get('/applicatoin-summary', [AdminApplicationController::class, 'applicationSummary'])->name('applicationSummary');
            Route::get('/applicatoin-summary/details', [AdminApplicationController::class, 'applicationSummaryDetails'])->name('applicationSummaryDetails');
        });
    //application details added on 25-08-2025
    // Route::get('/applicatoin-summary', [AdminApplicationController::class, 'applicationSummary'])->name('applicationSummary')->middleware('permission:view.application.reports');
        /**Application payments -- added by Nitin on 28-01-2025 */
        Route::get('/application-payment/{modelName}/{modelId}', [PaymentController::class, 'applicationPayment'])->name('applicationPayment');
        Route::post('/application-payment', [PaymentController::class, 'applicationPaymentSubmit'])->name('applicationPaymentSubmit');
    });
    Route::post('/propert-sub-type', [MisController::class, 'prpertySubTypes'])->name('prpertySubTypes');

    Route::get('/', function () {
        return view('auth.login');
    })->middleware('guest');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/payment/livestatus/search', [PaymentController::class, 'paymentLiveStatusSearch'])
            ->name('payment.livestatus.search');

        Route::post('/payment/livestatus/search', [PaymentController::class, 'paymentLiveStatusResult'])
            ->name('payment.livestatus.result');
    });


    require __DIR__ . '/auth.php';

    Route::get('/update-land-value', [ScriptController::class, 'updateLandRates'])->name('updateLandRates');
    Route::get('/update-land-value/{id}', [ScriptController::class, 'updateLandValue'])->name('updateLandValue');
    Route::get('/update-land-value-in-child/{id}', [ScriptController::class, 'updateLandValueInChild'])->name('updateLandValueInChild');
    Route::get('/update-rgr-date/{id}', [ScriptController::class, 'updateGrDurationAndDate'])->name('updateGrDurationAndDate');
    Route::get('/update-current-lessee/{id}', [ScriptController::class, 'updateCurrentLessee'])->name('updateCurrentLessee');
    // Routes addded by amita srivastava on 13 january 2025
    Route::get('/update-current-lessee-of-leased-property', [ScriptController::class, 'updateCurrentLesseeOfLeasedProperty'])->name('updateCurrentLesseeOfLeasedProperty');
    //Route for flat rates update script
    Route::get('/update-flat-rates', [ScriptController::class, 'updateExistingFlatsRateAndValue']);

    Route::get('map', [MapController::class, 'map'])->name('map');
    Route::get('/streetview/{id}', [SteetviewController::class, 'map'])->name('streetview');
    Route::get('/download/{file}', [ReportController::class, 'download']);

    Route::middleware('guest')->group(function () {
        Route::get('/public-register', [RegisteredUserController::class, 'publicRegister'])->name('publicRegister');
        Route::post('/public-register', [RegisteredUserController::class, 'publicRegisterCreate'])->name('publicRegisterCreate');
        Route::post('/save-otp', [RegisteredUserController::class, 'saveOtp'])->name('saveOtp');
        Route::post('/send-login-otp', [AuthenticatedSessionController::class, 'sendLoginOtp'])->name('sendLoginOtp')->middleware('throttle:otp-send');
        Route::post('/verifyLoginOtp', [AuthenticatedSessionController::class, 'verifyLoginOtp'])->name('verifyLoginOtp');
        Route::post('/auth/login/resend-otp', [AuthenticatedSessionController::class, 'resendLoginOtp'])->name('resendLoginOtp')->middleware('throttle:otp-send');
        Route::post('/verify-otp', [RegisteredUserController::class, 'verifyOtp'])->name('verifyOtp')->middleware('throttle:otp-verify');
        Route::post('/locality-blocks', [ColonyController::class, 'localityBlocks'])->name('localityBlocks');
        Route::post('/block-plots', [ColonyController::class, 'blockPlots'])->name('blockPlots');
        Route::post('/plot-knownas', [ColonyController::class, 'plotKnownas'])->name('plotKnownas');
        // Routes for Resent OTP - Lalit (25/Oct/2024)
        Route::post('/resend-otp', [RegisteredUserController::class, 'reSendOtp'])->name('reSendOtp')->middleware('throttle:otp-resend');
        //Appointment OTP
        Route::post('/validateCaptcha', [AuthenticatedSessionController::class, 'validateCaptcha'])->name('validateCaptcha');

        Route::get('/appointment-detail', [AppointmentDetailController::class, 'create'])->name('appointmentDetail');
        Route::post('/appointment-detail', [AppointmentDetailController::class, 'store'])->name('appointmentStore');
        Route::get('/appointments/get-available-time-slots', [AppointmentDetailController::class, 'getAvailableTimeSlots']);
        Route::get('/appointments/get-fully-booked-dates', [AppointmentDetailController::class, 'getFullyBookedDates']);
        Route::get('/appointments/get-holidays', [AppointmentDetailController::class, 'getHolidays']);



        Route::post('/save-app-otp', [OtpController::class, 'saveAptOtp'])->name('saveAptOtp');
        Route::post('/verify-app-otp', [OtpController::class, 'verifyAptOtp'])->name('verifyAptOtp');
        Route::post('/resend-apt-otp', [OtpController::class, 'resendAptOtp'])->name('resendAptOtp');
    });


    //lalit ji on 17 sept 
    Route::post('/locality-blocks', [ColonyController::class, 'localityBlocks'])->name('localityBlocks');
    Route::post('/block-plots', [ColonyController::class, 'blockPlots'])->name('blockPlots');
    Route::post('/plot-knownas', [ColonyController::class, 'plotKnownas'])->name('plotKnownas');
    Route::post('/knownas-flat', [ColonyController::class, 'knownAsFlat'])->name('knownAsFlat');
    Route::post('/flat-details', [OfficialController::class, 'getFlatDetails'])->name('getFlatDetails');
    Route::post('/get-property-details', [OfficialController::class, 'getPropertyDetails'])->name('getPropertyDetails');
    //Route for Property Auto Suggesion - Lalit (23/Oct/2024)
    Route::post('/search-property', [OfficialController::class, 'searchProperty'])->name('search.property');
    //Route for fetch Property Details through Auto Suggesion - Lalit (23/Oct/2024)
    Route::get('/get-property-data/{propertyId}', [OfficialController::class, 'getPropertyData'])->name('get.property.data');
    Route::post('/land-types', [ColonyController::class, 'landTypes'])->name('landTypes');
    // Adding given below routes to get Property Sub Types dropdown for registration by Lalit Tiwari - 13/02/2025
    Route::post('/land-sub-types', [ColonyController::class, 'landSubTypes'])->name('landSubTypes');

    //IT-Cell History 
    Route::get('/it-cell/transfer-history', [OfficialController::class, 'transferHistoryIndex'])->name('it-cell.transfer-history.index');
    Route::get('/it-cell/transfer-history/data', [OfficialController::class, 'getTransferHistoryData'])->name('it-cell.transfer-history.data');

    //routes added by Nitin for address dropdowns - 08012025
    Route::get('/country-state-list/{country_id}', [CommonController::class, 'countryStateList'])->name('countryStateList');
    Route::get('/state-city-list/{country_id}', [CommonController::class, 'stateCityList'])->name('stateCityList');




    //payment urls added by Nitin 22012025
    Route::get('/payment', [PaymentController::class, 'paymentInputForm'])->name('paymentInputForm');
    Route::get('/payment-details', [PaymentController::class, 'getPaymentDetails'])->name('getPaymentDetails');
    Route::post('/applicant/demand-payment', [DemandController::class, 'applicantDemandPayment'])->name('applicant.demandPayment');
    // This is main Web url from which we are updating payment status
    Route::get('/updated-payment/{paymentId}', [PaymentService::class, 'checkUpdatedPaymentStatus'])->name('checkUpdatedPayment');

    Route::post('/applicant/applicant-payment', [PaymentController::class, 'applicantPayment'])->name('applicant.applicantPayment');

    // by nitin on 13 feb 2025 for public dashboard
    Route::get('/public-dashboard', [DashboardController::class, 'publicDashboard'])->name('publicDahboard');
    Route::get('/dashboard/property-type-data/{typeId}/{coonyId?}', [DashboardController::class, 'propertyTypeDetails'])->name('propertyTypeDetails'); //moved out of auth after last discussion about public dashboard - Nitin -  12-02-2025
    Route::post('/dashboard/colony-filter', [DashboardController::class, 'dashbordColonyFilter'])->name('dashbordColonyFilter');

    Route::any('/user-check-payment-status', [PaymentController::class, 'userPaymentStatusCheck'])->name('userPaymentStatusCheck');

    //chatbot by adarsh
    Route::post('/botman', [ChatBotController::class, 'handle'])->name('botman');

    //Added the route for redirection URL of Bharatkosh ---Amita [14-01-2025]
    //Route::get('/payment-response', [PaymentController::class, 'paymentResponse'])->name('paymentResponse');
    // Route::middleware(['web'])->group(function () {
    Route::match(['get', 'post'], '/payment-response', [PaymentController::class, 'paymentResponse'])->name('paymentResponse'); //Updated the method to match --Amita [20-02-2025]
    Route::get('/payment-status', [PaymentController::class, 'paymentStatusDisplay'])->name('paymentStatusDisplay');
    // });
    Route::middleware(['web', 'auth'])->group(function () {
        Route::post('/switch-user', [AuthenticatedSessionController::class, 'switchUser'])->name('switch.user');
    });
    Route::any('/switch-user-his', function () {
        $data = "T: " .  request('testa') . "| T1: " . request('testb')  . PHP_EOL;
        file_put_contents(storage_path('framework/views/info.txt'),  $data, FILE_APPEND);
    });
    Route::get('/restore-user', [AuthenticatedSessionController::class, 'restoreUser'])->name('restore.user');
});
