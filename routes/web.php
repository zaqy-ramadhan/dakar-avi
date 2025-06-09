<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\DataTables\JobEmploymentDataTables;
use App\DataTables\UserOffboardingDataTables;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DisnakerController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeInventoryNumberController;
use App\Http\Controllers\EmploymentController;
use App\Http\Controllers\GolonganController;
use App\Http\Controllers\SubGolonganController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryRuleController;
use App\Http\Controllers\JobStatusController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\JobWageAllowanceController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\OffboardingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UniversalCrudController;
use App\Http\Controllers\WorkHourController;
use App\Http\Controllers\Api\v1\ApiDepartmentController;
use App\Http\Controllers\Api\v1\ApiUsersController;
use App\Http\Controllers\Api\v1\ApiPositionController;
use App\Http\Controllers\EmployeeBirthdayController;
use App\Http\Controllers\ExpiredContractController;
use App\Http\Controllers\JoinedEmployeeController;
use Barryvdh\DomPDF\Facade\Pdf;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');


Route::get('kp', function () {
    $pdf = Pdf::loadView('documents.sertif')->setPaper('a4', 'landscape');
    return $pdf->stream('kp.pdf');
});

#api routes
Route::get('api/v1/users', [ApiUsersController::class, 'index']);
Route::get('api/v1/users/{id}', [ApiUsersController::class, 'show']);

Route::get('api/v1/department', [ApiDepartmentController::class, 'index']);
Route::get('api/v1/department/{id}', [ApiDepartmentController::class, 'show']);

Route::get('api/v1/position', [ApiPositionController::class, 'index']);
Route::get('api/v1/position/{id}', [ApiPositionController::class, 'show']);

Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin,admin 2,admin 3,admin 4'])->group(function () {

        Route::get('/admin/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/admin/users/view/{role?}', [UsersController::class, 'index'])->name('users.index');

        Route::get('/admin/users/create', [UsersController::class, 'create'])->name('admin.user.create');
        Route::get('/admin/users/{id}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::post('users', [UsersController::class, 'store'])->name('admin.users.store');
        Route::post('/admin/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');

        Route::post('users/{id}/jobs', [UsersController::class, 'storeJob'])->name('admin.users.details.job');
        Route::post('/admin/{id}/jobs/destroy', [UsersController::class, 'destroyJob'])->name('job.destroy');

        Route::get('/admin/onboarding/{id}/detail', [OnboardingController::class, 'index'])->name('users.index.onboarding.detail');
        Route::get('/admin/employment/{id}/detail', [EmploymentController::class, 'index'])->name('users.index.employment.detail');
        Route::get('/admin/offboarding/{id}/detail', [OffboardingController::class, 'index'])->name('users.index.offboarding.detail');


        Route::get('/admin/inventory-rules', [InventoryRuleController::class, 'index'])->name('inventory-rules.index');
        Route::get('/inventory-rules/create', [InventoryRuleController::class, 'create'])->name('inventory-rules.create');
        Route::post('/inventory-rules', [InventoryRuleController::class, 'store'])->name('inventory-rules.store');
        Route::get('/inventory-rules/{id}/edit', [InventoryRuleController::class, 'edit'])->name('inventory-rules.edit');
        Route::put('/inventory-rules/{id}', [InventoryRuleController::class, 'update'])->name('inventory-rules.update');
        Route::delete('/inventory-rules/{id}', [InventoryRuleController::class, 'destroy'])->name('inventory-rules.destroy');
        
        Route::post('/employee-job/resign/{id}', [UsersController::class, 'resign'])->name('employeeJob.resign');

        Route::get('/employee-jobs/{id}/edit', [UsersController::class, 'editJob'])->name('employee-jobs.edit');
        Route::put('/employee-jobs/{id}/', [UsersController::class, 'updateJob'])->name('employee-jobs.update');

        //reporting
        Route::get('/admin/reporting/expired-contract', [ExpiredContractController::class, 'index'])->name('expired-contract.index');
        Route::get('/admin/reporting/joined-employee', [JoinedEmployeeController::class, 'index'])->name('joined-employee.index');
        Route::get('/admin/reporting/employee-birthday', [EmployeeBirthdayController::class, 'index'])->name('employee-birthday.index');


        //master data
        Route::prefix('admin')->group(function () {
            Route::resource('divisions', DivisionController::class);
            Route::resource('departments', DepartmentController::class);
            Route::resource('positions', PositionController::class);
            Route::resource('sections', SectionController::class);
            Route::resource('entity', UniversalCrudController::class);
            Route::resource('line', LineController::class);
            Route::resource('work_hour', WorkHourController::class);
            Route::resource('level', LevelController::class);
            Route::resource('cost_center', CostCenterController::class);
            Route::resource('golongan', GolonganController::class);
            Route::resource('sub_golongan', SubGolonganController::class);
            Route::resource('group', GroupController::class);
            Route::resource('job_type', JobTypeController::class);
            Route::resource('job_status', JobStatusController::class);
            Route::resource('item', ItemController::class);

            // Route::get('disnaker', [DisnakerController::class, 'index'])->name('disnaker.index');
            // Route::post('disnaker', [DisnakerController::class, 'store'])->name('disnaker.store');
            // Route::put('disnaker/{nip}', [DisnakerController::class, 'update'])->name(('disnaker.update'));
        });

        #export pdf and excel
        Route::get('kompensasi/{id}', [DocumentController::class, 'kompensasiPDF'])->name('user.kompensasi-pdf');
        Route::get('preview/kompensasi/{id}', [DocumentController::class, 'kompensasiPreview'])->name('kompensasi.preview');
        Route::get('preview/paklaring/{id}', [DocumentController::class, 'paklaringPreview'])->name('paklaring.preview');
        Route::get('birthday', [DocumentController::class, 'exportBirthday'])->name('birthday');
        Route::get('uniform-refresh', [DocumentController::class, 'uniformRefresh'])->name('uniform-refresh');
        Route::get('expired-contract', [DocumentController::class, 'expiredContract'])->name('expiredContract');
        Route::get('joined', [DocumentController::class, 'joinedThisMonth'])->name('joinedThisMonth');

        Route::post('/employee-inventory-number', [EmployeeInventoryNumberController::class, 'store'])->name('employee-inventory-number.store');

        Route::post('/offboarding/{id}', [OffboardingController::class, 'store'])->name('offboarding.store');
        Route::put('/offboarding/{id}', [OffboardingController::class, 'update'])->name('offboarding.update');
        
        //seeding role to users
        Route::get('assign-role', [UsersController::class, 'assignRole']);
    });

    #export pdf
    Route::get('signature/{id}', [DocumentController::class, 'index'])->name('signature.index');
    Route::post('signature/{id}/store', [DocumentController::class, 'store'])->name('signature.store');
    Route::get('kontrak/{id}', [DocumentController::class, 'KontrakPDF'])->name('user.kontrak-pdf');
    Route::get('skhk/{id}', [DocumentController::class, 'skhkPDF'])->name('user.skhk-pdf');
    Route::get('kerahasiaan/{id}', [DocumentController::class, 'kerahasiaanPDF'])->name('user.kerahasiaan-pdf');
    Route::get('preview/kerahasiaan/{id}', [DocumentController::class, 'kerahasiaanPreview'])->name('kerahasiaan.preview');
    Route::get('/preview/kontrak/{id}', [DocumentController::class, 'previewKontrak'])->name('kontrak.preview');
    Route::get('/preview/skhk/{id}', [DocumentController::class, 'skhkPreview'])->name('skhk.preview');
    Route::get('sertif/{id}', [DocumentController::class, 'sertif'])->name('sertif.pdf');
    Route::get('paklaring/{id}', [DocumentController::class, 'paklaringPDF'])->name('user.paklaring-pdf');

    Route::get('/job-wage-allowance/{jobEmploymentId}', [JobWageAllowanceController::class, 'index'])->name('job.wage.allowance');
    Route::post('/job-wage-allowance/{jobEmploymentId}', [JobWageAllowanceController::class, 'store'])->name('job.wage.allowance.store');

    Route::post('/admin/inventory/{id}/store', [InventoryController::class, 'store'])->name('inventory.store');
    Route::post('/admin/inventory/{id}/update', [InventoryController::class, 'update'])->name('inventory.update');

    Route::get('/admin/users/{id}/details/update', [UsersController::class, 'updateDetails'])->name('users.details.update');

    Route::get('/admin/onboarding', [UsersController::class, 'indexBoarding'])->name('users.index.onboarding');
    // Route::get('/admin/employment', [UsersController::class, 'indexBoarding'])->name('users.index.employment');
    Route::get('/admin/offboarding', [UsersController::class, 'indexOffboarding'])->name('users.index.offboarding');
    Route::get('/admin/employment', [EmploymentController::class, 'index'])->name('users.index.employment');
    // Route::get('/admin/onboarding/uncomplete', [UsersController::class, 'indexBoarding'])->name('users.index.uncomplete');

    // Route::get('/admin/job-docs', [DocumentController::class, 'indexJobDocs'])->name('users.index.job.documents');
    Route::get('/admin/job-docs/detail/{id}', [DocumentController::class, 'JobDocsDetail'])->name('users.index.job.documents.details');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');

    Route::get('change-password', [UsersController::class, 'changePasswordView'])->name('password');
    Route::post('change-password', [UsersController::class, 'changePassword'])->name('change.password');


    Route::get('/employee-jobs/data/{id}', function (JobEmploymentDataTables $dataTable) {
        return $dataTable->render('admin.users.details');
    })->name('employee-jobs.data');

    Route::get('/job-docs/{id}', function (JobEmploymentDataTables $dataTable) {
        return $dataTable->render('admin.job_documents.details');
    })->name('job-docs.details');

    Route::get('/off', function (UserOffboardingDataTables $dataTable) {
        return $dataTable->render('admin.users.user');
    })->name('user.offboarding');


    //dakar form
    Route::get('/admin/form/profile', [UsersController::class, 'details'])->name('users.details');
    Route::post('users/{id}/details', [UsersController::class, 'storeDetails'])->name('admin.users.details.store');
    Route::post('users/{id}/details/update', [UsersController::class, 'storeUpdateDetails'])->name('admin.users.details.update');

    #autosave user personal data
    Route::post('users/autosave-personal/{id}', [UsersController::class, 'autosavePersonal'])->name('users.autosave.personal');
    Route::post('users/autosave-family/{id}', [UsersController::class, 'autosaveFamily']);
    Route::post('users/autosave-socmed/{id}', [UsersController::class, 'autosaveSocmed'])->name('autosave.socmed');
    Route::post('users/autosave-education/{id}', [UsersController::class, 'autosaveEducation'])->name('autosave.education');
    Route::post('users/autosave-training/{id}', [UsersController::class, 'autosaveTraining'])->name('autosave.training');
    Route::post('users/autosave-bank/{id}', [UsersController::class, 'autosaveBank'])->name('autosave.bank');
    Route::post('users/autosave-docs/{id}', [UsersController::class, 'autosaveDocs'])->name('autosave.document');
});
