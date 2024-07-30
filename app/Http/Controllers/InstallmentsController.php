<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest\InstallmentRequest;
use App\Http\Requests\StoreRequest\LoanRequest;
use App\Models\Installments;
use App\Models\Laba;
use App\Models\Loan;
use App\Models\ValidasiLaporan;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

class InstallmentsController extends Controller
{
    public function index(Installments $Installments, Request $request)
    {
        $dateStart = null;
        if ($request->dateStart) {
            $dateStart = Carbon::parse($request->dateStart)->format('Y-m-d');
        }
        $dateEnd = null;
        if ($request->dateEnd) {
            $dateEnd = Carbon::parse($request->dateEnd)->format('Y-m-d');
        }
        $data = $Installments->fillterDate($dateStart, $dateEnd)->fillter($request)->with('loans.members')->where('created_by', auth()->user()->id)->get();
        return $this->sendResponse($data, "success");
    }
    public function indexPimpinan(Installments $Installments, Request $request)
    {
        $dateStart = null;
        if ($request->dateStart) {
            $dateStart = Carbon::parse($request->dateStart)->format('Y-m-d');
        }
        $dateEnd = null;
        if ($request->dateEnd) {
            $dateEnd = Carbon::parse($request->dateEnd)->format('Y-m-d');
        }

        $data = $Installments->fillterDate($dateStart, $dateEnd)->fillter($request)->with('loans.members')->orderBy('id', 'DESC')->get();
        return $this->sendResponse($data, "success");
    }

    public function countInstallments()
    {
        $data = Installments::whereMonth('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count('id');
        return $this->sendResponse($data, "success");
    }
    public function countInstallmentsAdmin()
    {
        $data = Installments::whereMonth('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('created_by', auth()->user()->id)->count('id');
        return $this->sendResponse($data, "success");
    }
    public function store(InstallmentRequest $request, Installments $Installments)
    {
        $dataReq = $request->all();

        $hutangAwal = Loan::where('id', $request->loan_id)->sum('loan_amount');
        $jangkaWaktu = Loan::where('id', $request->loan_id)->sum('time_period');
        $sisaHutang = $Installments->where('loan_id', $request->loan_id)->select('remaining_debt')->latest()->first() ? null : Loan::where('id', $request->loan_id)->sum('loan_amount');
        // $sisaHutang2 = $Installments->where('loan_id', $request->loan_id)->select('remaining_debt')->latest()->skip(1)->first();
        // dd($sisaHutang2);
        // $sisaHutang = $sisaHutang1;
        // dd($sisaHutang);
        $denda = ($hutangAwal / $jangkaWaktu + $hutangAwal * 0.01) * 0.05;
        $tglDendaa = $Installments->where('loan_id', $request->loan_id)->select('date')->latest()->first() ? null :  Loan::where('id', $request->loan_id)->select('date')->latest()->first();

        $tglDenda = $tglDendaa["date"];
        $tgl = explode("-", $tglDenda);
        // $dateTime = CarbonPeriod::between(Carbon::now()->subMonth(1)->day($tgl[2]), Carbon::now()->endOfMonth()->day($tgl[2]));


        $tglPinjaman = new Carbon($tglDenda);
        // dd($tglPinjaman);
        $tglBayarPinjaman = new Carbon($dataReq["date"]);



        $fillterTgl =  function ($date) {
            return $date->day();
        };
        $fillRequestDenda = CarbonPeriod::between($tglPinjaman, $tglBayarPinjaman)->filter($fillterTgl)->count();
        // dd($fillRequestDenda);

        $fillDenda = CarbonPeriod::between(Carbon::now()->subMonth(1)->day($tgl[2]), Carbon::now()->endOfMonth()->day($tgl[2]))->filter($fillterTgl)->count();
        // dd($fillDenda);

        // dd($fillRequestDenda);
        if ($fillRequestDenda >= $fillDenda) {
            $dataReq["mulct"] = $denda;
            // return "kenda denda";
        } else {
            $dataReq["mulct"] = null;
            // return "tidak kena denda";
        }

        // dd($pp);
        $dataReq["bunga_pinjaman"] =  ($sisaHutang == null) ? $hutangAwal * (1 / 100) : $sisaHutang * (1 / 100);

        $dataReq["principal_loan"] = $request->amount_payment - $dataReq["bunga_pinjaman"] - $dataReq["mulct"];
        // dd($request->amount_payment - $dataReq['bunga_pinjaman']);


        $sisaHutangFinal = $sisaHutang;
        if ($sisaHutang == null) {

            $dataReq["remaining_debt"] = $sisaHutangFinal - $dataReq["principal_loan"];
        } else {
            $dataReq["remaining_debt"] = $sisaHutang - $dataReq["principal_loan"];
        }


        $laba = new Laba();
        $laba->laba_amount =  ($sisaHutang == null) ? $hutangAwal * (1 / 100) : $sisaHutang * (1 / 100);
        $laba->from_laba = "Bunga Pinjaman";
        $laba->save();
        // dd($dataReq);
        $dataReq["created_by"] = auth()->user()->id;
        $data = $Installments->create($dataReq);
        return $this->sendResponse($data, "success");
    }

    public function update(InstallmentRequest $request, $id, Installments $Installments)
    {
        $dataReq = $request->all();

        $hutangAwal = Loan::where('id', $request->loan_id)->sum('loan_amount');
        $jangkaWaktu = Loan::where('id', $request->loan_id)->sum('time_period');
        $sisaHutang1 = $Installments->where('loan_id', $request->loan_id)->select('remaining_debt')->latest()->skip(1)->first();
        // $sisaHutang2 = $Installments->where('loan_id', $request->loan_id)->select('remaining_debt')->latest()->skip(1)->first();
        // dd($sisaHutang2);
        $sisaHutang = $sisaHutang1["remaining_debt"];

        $denda = ($hutangAwal / $jangkaWaktu + $hutangAwal * 0.01) * 0.05;
        $tglDendaa = $Installments->where('loan_id', $request->loan_id)->select('date')->latest()->skip(1)->first();

        if ($tglDendaa["date"] == null) {
            $tglDendaa = Loan::where('id', $request->loan_id)->select('date')->latest()->skip(1)->first();
        }
        $tglDenda = $tglDendaa["date"];
        $tgl = explode("-", $tglDenda);
        // $dateTime = CarbonPeriod::between(Carbon::now()->subMonth(1)->day($tgl[2]), Carbon::now()->endOfMonth()->day($tgl[2]));


        $tglPinjaman = new Carbon($tglDenda);
        // dd($tglPinjaman);
        $tglBayarPinjaman = new Carbon($dataReq["date"]);



        $fillterTgl =  function ($date) {
            return $date->day();
        };
        $fillRequestDenda = CarbonPeriod::between($tglPinjaman, $tglBayarPinjaman)->filter($fillterTgl)->count();
        // dd($fillRequestDenda);

        $fillDenda = CarbonPeriod::between(Carbon::now()->subMonth(1)->day($tgl[2]), Carbon::now()->endOfMonth()->day($tgl[2]))->filter($fillterTgl)->count();
        // dd($fillDenda);

        // dd($fillRequestDenda);
        if ($fillRequestDenda >= $fillDenda) {
            $dataReq["mulct"] = $denda;
            // return "kenda denda";
        } else {
            $dataReq["mulct"] = null;
            // return "tidak kena denda";
        }

        // dd($pp);
        $dataReq["bunga_pinjaman"] =  ($sisaHutang == null) ? $hutangAwal * (1 / 100) : $sisaHutang * (1 / 100);

        $dataReq["principal_loan"] = $request->amount_payment - $dataReq["bunga_pinjaman"] - $dataReq["mulct"];
        // dd($request->amount_payment - $dataReq['bunga_pinjaman']);


        $sisaHutangFinal = $hutangAwal - $sisaHutang;
        if ($sisaHutang == null) {

            $dataReq["remaining_debt"] = $sisaHutangFinal - $dataReq["principal_loan"];
        } else {
            $dataReq["remaining_debt"] = $sisaHutang - $dataReq["principal_loan"];
        }

        // dd($dataReq);
        $dataReq["created_by"] = auth()->user()->id;
        $data = $Installments->where('id', $id)->update($dataReq);
        return $this->sendResponse($data, "success");
    }

    public function destroy(Installments $Installments, $id)
    {

        $data =  $Installments->where('id', $id)->delete($Installments);

        return $this->sendResponse($data, 'success');
    }


    // public function loan(LoanRequest $request)
    // {
    //     $bunga = $request->loan_amount * 0.01;
    //     $pokok = $request->loan_amount / ($request->time_period * 12);
    //     $tabunganW = $request->loan_amount * 0.015;
    //     return response()->json([
    //         'besar_pinjaman' => $request->loan_amount,
    //         'jangka_waktu' => $request->time_period * 12,
    //         'suku_bunga' => "1%",
    //         'besar_angsuran' => $pokok + $bunga,
    //         'biaya_admin' => $bunga,
    //         'tabungan_wajib' => $tabunganW,
    //         'pinjaman_didapat' => $request->loan_amount - ($bunga + $tabunganW)
    //     ], 200);
    // }


    public function sendReportLoan(Request $request)
    {

        $date = Carbon::parse($request->date);

        $dateStart = $date->copy()->firstOfMonth()->format('Y-m-d');
        $dateEnd = $date->copy()->lastOfMonth()->format('Y-m-d');

        $data = new ValidasiLaporan();
        $data->date = $dateStart;
        $data->type = 'angsuran';
        $data->created_by = auth()->user()->id;
        $data->save();


        $dataDepo = Installments::whereDate('date', '>=', $dateStart)->whereDate('date', '<=', $dateEnd)
            ->where('validasi_laporan_id', null)
            ->where('created_by', auth()->user()->id);


        $dataDepo->update([
            'validasi_laporan_id' => $data->id
        ]);
        $getData = Installments::whereDate('date', '>=', $dateStart)->whereDate('date', '<=', $dateEnd)->get();

        return $this->sendResponse($getData, 'success');
    }

    public function indexLaporan(Installments $Installments)
    {
        $data = $Installments->with('loans.members')->where('created_by', auth()->user()->id)
            ->whereMonth('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->get();
        return $this->sendResponse($data, "success");
    }

    public function showLaporanLoan($id)
    {
        $data = Installments::with('loans.members')->where('validasi_laporan_id', $id)->get();

        return $this->sendResponse($data, 'success');
    }

    public function fillterInstallments(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $data = Installments::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->get();

        return $this->sendResponse($data, 'success');
    }

    public function show(Installments $Installments, $id)
    {
        $data = $Installments->with(['loans.members'])->where('loan_id', $id)->get();
        return $this->sendResponse($data, 'success');
    }
}
