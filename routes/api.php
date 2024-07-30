<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DepositTypeController;
use App\Http\Controllers\InstallmentsController;
use App\Http\Controllers\KorwilController;
use App\Http\Controllers\LabaController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportDepositController;
use App\Http\Controllers\ReportLoanController;
use App\Models\Installments;
use App\Models\ReportLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();






Route::get('/fillter-loan', [LoanController::class, 'fillterLoan']);
Route::post('/preview', [LoanController::class, 'loan']);





// Route::group(["middlewae" => "api", "prefix" => "auth"], function ($router) {
Route::post('/register', [AuthController::class, 'register']);
Route::get('/users', [AuthController::class, 'index']);
Route::put('/user/{id}', [AuthController::class, 'update']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('laba', [LabaController::class, 'sumLaba']);
Route::get('laba-all', [LabaController::class, 'index']);




Route::group(["middleware" => ["auth", "checkRole:admin"]], function () {
    Route::post('/deposit', [DepositController::class, 'store']);
    Route::get('/deposit', [DepositController::class, 'index']);
    Route::put('/deposit/{id}', [DepositController::class, 'update']);
    Route::delete('/deposit/{id}', [DepositController::class, 'destroy']);
    Route::post('/report-deposit', [DepositController::class, 'report']);



    Route::post('/angsuran', [InstallmentsController::class, 'store']);
    Route::get('/angsuran', [InstallmentsController::class, 'index']);
    Route::put('/angsuran/{id}', [InstallmentsController::class, 'update']);
    Route::get('/angsuran/{id}', [InstallmentsController::class, 'show']);
    Route::delete('/angsuran/{id}', [InstallmentsController::class, 'destroy']);

    Route::post('/send-report-loan', [InstallmentsController::class, 'sendReportLoan']);
    Route::post('/send-report', [DepositController::class, 'sendReport']);
    Route::get('/report-deposit', [DepositController::class, 'indexLaporan']);
    Route::get('/report-loan', [InstallmentsController::class, 'indexLaporan']);

    Route::get('/admin-report', [DepositController::class, 'getLaporanAdmin']);
    Route::get('/sum-saldo-admin', [DepositController::class, 'sumSaldoAdmin']);
    Route::get('/sum-angsuran-admin', [LoanController::class, 'sumLoanAdmin']);
    Route::get('/jumlah-deposit-admin', [DepositController::class, 'countDepositAdmin']);
    Route::get('/jumlah-angsuran-admin', [InstallmentsController::class, 'countInstallmentsAdmin']);
});


Route::group(["middleware" => ["auth", "checkRole:leader"]], function () {
    Route::get('/deposit-leader', [DepositController::class, 'pimpinanDepo']);


    Route::put('/deposit_type/{id}', [DepositTypeController::class, 'update']);
    Route::delete('/deposit_type/{id}', [DepositTypeController::class, 'destroy']);
    Route::post('/deposit_type', [DepositTypeController::class, 'store']);


    Route::post('/pinjaman', [LoanController::class, 'store']);
    Route::put('/pinjaman/{id}', [LoanController::class, 'update']);
    Route::get('/pinjaman/{id}', [LoanController::class, 'show']);
    Route::delete('/pinjaman/{id}', [LoanController::class, 'destroy']);
    Route::put('/validasi/{id}', [DepositController::class, 'validasiLaporan']);
    Route::post('/simpanan-wajib-baru', [DepositController::class, 'simpananWajibBaru']);

    Route::get('/angsurans', [InstallmentsController::class, 'indexPimpinan']);
    Route::get('/leader-report', [DepositController::class, 'getLaporanLeader']);

    Route::post('/bunga-tabungan', [DepositController::class, 'depositInterest']);

    Route::post('/korwil', [KorwilController::class, 'store']);
    Route::put('/korwil/{id}', [KorwilController::class, 'update']);
    Route::delete('/korwil/{id}', [KorwilController::class, 'destroy']);
    Route::get('bunga', [DepositController::class, 'bunga']);
    Route::get('/jumlah-deposit', [DepositController::class, 'countDeposit']);
    Route::get('/jumlah-angsuran', [InstallmentsController::class, 'countInstallments']);
    Route::get('/sum-saldo', [DepositController::class, 'sumSaldoAll']);
    Route::get('/sum-loan', [LoanController::class, 'sumLoan']);
});

Route::group(["middleware" => ["auth", "checkRole:user"]], function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/my-deposit', [AuthController::class, 'depositUser']);
    Route::get('/my-loan', [AuthController::class, 'LoanUser']);
    Route::get('/my-angsuran', [AuthController::class, 'myInstallments']);
});


Route::group(["middleware" => ["auth", "checkRole:leader,admin"]], function () {
    Route::get('/pinjaman', [LoanController::class, 'index']);
    Route::get('/show-report/{id}', [DepositController::class, 'showLaporan']);
    Route::get('/show-report-loan/{id}', [InstallmentsController::class, 'showLaporanLoan']);
    Route::post('/member', [MemberController::class, 'store']);
    Route::put('/member/{id}', [MemberController::class, 'update']);
    Route::delete('/member/{id}', [MemberController::class, 'destroy']);
    Route::get('/member/{member}', [MemberController::class, 'show']);
    Route::get('/simpanan-wajib', [DepositController::class, 'simpananWajib']);
    Route::get('/detail-pinjaman/{id}', [InstallmentsController::class, 'show']);
    Route::get('/detail-simpanan-wajib/{id}', [DepositController::class, 'showSimpananwajib']);

    Route::get('/korwil', [KorwilController::class, 'index']);
    Route::get('/member', [MemberController::class, 'index']);
    // });

    Route::get('/deposit/{id}', [DepositController::class, 'show']);


    Route::get('/deposit_type', [DepositTypeController::class, 'index']);

    Route::get('/jumlah-pinjaman', [LoanController::class, 'countLoan']);
    Route::get('/jumlah-member', [MemberController::class, 'countMember']);
});
