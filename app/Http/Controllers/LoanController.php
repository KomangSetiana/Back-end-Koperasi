<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest\LoanRequest;
use App\Http\Requests\StoreRequest\PreviewLoanRequest;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\Deposit;
use App\Models\Installments;
use App\Models\Laba;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Loan $Loan, Request $request)
    {
        $data = $Loan->fillter($request)->with('users')->get();
        return $this->sendResponse(LoanResource::collection($data), "success");
    }
    public function countLoan(Loan $Loan)
    {
        $data = $Loan->count('id');
        return $this->sendResponse($data, "success");
    }

    public function store(LoanRequest $request, Loan $Loan)
    {
        $dataReq = $request->all();
        $dataReq["created_by"] = auth()->user()->id;
        $data = $Loan->create($dataReq);

        $laba = new Laba();
        $laba->laba_amount = $dataReq["loan_amount"] * 0.015;
        $laba->from_laba = "Potongan Pinjaman Admin";
        $laba->save();
        $deposit  = [
            'member_id' => $dataReq["member_id"],
            'date' => Carbon::now(),
            'debet' => $dataReq['loan_amount'] * 0.01,
            'saldo' => $dataReq['loan_amount'] * 0.01,
            'deposit_type_id' => 2,
            'created_by' => auth()->user()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
        Deposit::insert($deposit);


        return $this->sendResponse($data, "success");
    }


    public function update(LoanRequest $request, $id, Loan $Loan)
    {
        $data = $request->all();
        $data["time_period"] = $request->time_period * 12;
        $dataReq["created_by"] = auth()->user()->id;

        Loan::where('id', $id)->update($data);
        return $this->sendResponse($data, 'success');
    }

    public function destroy(Loan $Loan, $id)
    {

        $data =  $Loan->where('id', $id)->delete($Loan);

        return $this->sendResponse($data, 'success');
    }

    public function loan(PreviewLoanRequest $request)
    {
        $bunga = $request->loan_amount * 0.015;
        $pokok = $request->loan_amount / $request->time_period;
        $tabunganW = $request->loan_amount * 0.01;
        return response()->json([
            'besar_pinjaman' => $request->loan_amount,
            'jangka_waktu' => $request->time_period,
            'suku_bunga' => "1 %",
            'besar_angsuran' => $pokok + $bunga,
            'biaya_admin' => $bunga,
            'tabungan_wajib' => $tabunganW,
            'pinjaman_didapat' => $request->loan_amount - ($bunga + $tabunganW)
        ], 200);
    }

    public function sumLoan()
    {
        $loan = Loan::latest()->sum('loan_amount');
        $remaining_debt = Installments::latest()->sum('principal_loan');


        $data = $loan - $remaining_debt;

        return $this->sendResponse($data, 'success');
    }

    public function sumLoanAdmin()
    {
        $data = Installments::where('created_by', auth()->user()->id)->whereMonth('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->latest()->sum('amount_payment');



        return $this->sendResponse($data, 'success');
    }




    // public function fillterLoan(Request $request)
    // {
    //     $start_date = $request->start_date;
    //     $end_date = $request->end_date;
    //     $data = Loan::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->get();

    //     return $this->sendResponse($data, 'success');
    // }
}
