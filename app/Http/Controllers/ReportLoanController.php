<?php

namespace App\Http\Controllers;

use App\Models\Installments;
use App\Models\ReportLoan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportLoanController extends Controller
{
    public function report()
    {



        $loans = Installments::where('created_by', auth()->user()->id)
            ->where('created_at', Carbon::now()->endOfMonth())->with(['loans.members', 'users'])->get();

        $reports = [];
        return $loans;
        foreach ($loans as $loan) {
            $reports[] = [
                'name' => $loan->loans->member_id,
                'date' => $loan->date,
                'interest' => $loan->interest,
                'principal' => $loan->principal,
                'mulct' => $loan->mulct,
                'amount' => $loan->amount,
                'remaining' => $loan->remaining,
                'is_acc' => false,
                'crated_by' => auth()->user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()

            ];
        }
        ReportLoan::insert($reports);
        return $this->sendResponse($reports, "success");
    }
}
