<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest\DepositRequest;
use App\Http\Resources\DepositResource;
use App\Http\Resources\LaporanDepositResource;
use App\Models\Deposit;
use App\Models\Deposit_types;
use App\Models\Member;
use App\Models\ReportDeposit;
use App\Models\ValidasiLaporan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepositController extends Controller
{
    public function index(Deposit $Deposit, Request $request)
    {
        $dateStart = null;
        if ($request->dateStart) {
            $dateStart = Carbon::parse($request->dateStart)->format('Y-m-d');
        }
        $dateEnd = null;
        if ($request->dateEnd) {
            $dateEnd = Carbon::parse($request->dateEnd)->format('Y-m-d');
        }
        // dd($search);
        // $current_page = isset($request->current_page) ? $request->current_page : 1;
        $data = $Deposit->fillterDate($dateStart, $dateEnd)->fillter($request)->where('created_by', auth()->user()->id)->with(['members', 'deposit_types'])->orderBy('id', 'DESC')->get();
        return $data;
        // paginate(20, ['*'], 'page', $current_page)
    }
    public function pimpinanDepo(Request $request)
    {
        $dateStart = null;
        if ($request->dateStart) {
            $dateStart = Carbon::parse($request->dateStart)->format('Y-m-d');
        }
        $dateEnd = null;
        if ($request->dateEnd) {
            $dateEnd = Carbon::parse($request->dateEnd)->format('Y-m-d');
        }
        $fillDeposit = $request->fillDeposit;

        // dd(Deposit::all());
        $data = Deposit::fillterDate($dateStart, $dateEnd)->fillterDepo($fillDeposit)->fillter($request)->where('interest', null)->with(['members', 'deposit_types'])->orderBy('id', 'DESC')->get();
        return $this->sendResponse($data, 'success');
    }
    public function countDeposit()
    {
        $data = Deposit::whereMonth('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count('id');
        return $this->sendResponse($data, "success");
    }
    public function countDepositAdmin()
    {
        $data = Deposit::whereMonth('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('created_by', auth()->user()->id)->count('id');
        return $this->sendResponse($data, "success");
    }
    public function store(DepositRequest $request, Deposit $Deposit)
    {
        // $totalDebet = DB::table('deposits')->where('member_id', $request['member_id'])->where('deposit_type_id', $request['deposit_type_id'])->sum('debet');

        // $totalKredit = DB::table('deposits')->where('member_id', $request['member_id'])->where('deposit_type_id', $request['deposit_type_id'])->sum('kredit');


        $saldoAwal = DB::table('deposits')->where('member_id', $request['member_id'])->where('deposit_type_id', $request['deposit_type_id'])->select('saldo')->latest()->first() ?? 0;
        $saldoTerakhir = $saldoAwal->saldo;
        $dataReq = $request->all();


        if ($request["debet"] !== null && $request["kredit"] !== null) {
            return response()->json(["error" => "Wrong Transaksi"], 422);
        } else if ($request["debet"] !== null) {
            $dataReq["saldo"] = $request["debet"] + $saldoTerakhir;
        } else if ($request["kredit"] !== null) {
            if ($request["kredit"] > $saldoTerakhir) {
                return response()->json(["error" => "Saldo Kurang"], 422);
            } else {

                $dataReq["saldo"] = $saldoTerakhir - $request["kredit"];
            }
        }
        // dd($dataReq["saldo"]);


        $dataReq["created_by"] = auth()->user()->id;
        // dd($dataReq);
        $data = $Deposit->create($dataReq);
        return $this->sendResponse($data, "success");
    }

    public function update(DepositRequest $request, $id, Deposit $Deposit)
    {
        $totalDebet = DB::table('deposits')->where('member_id', $request['member_id'])->where('deposit_type_id', $request['deposit_type_id'])->latest()->skip(1)->sum('debet');
        dd($totalDebet);

        $totalKredit = DB::table('deposits')->where('member_id', $request['member_id'])->where('deposit_type_id', $request['deposit_type_id'])->latest()->skip(1)->sum('kredit');
        $totalSaldo = $totalDebet - $totalKredit;

        $dataReq = $request->all();
        if ($request["debet"] !== null) {
            $dataReq["saldo"] = $request["debet"] + $totalSaldo;
        } else if ($request["kredit"] !== null) {
            $dataReq["saldo"] = $totalSaldo - $request["kredit"];
        }
        $dataReq["created_by"] = auth()->user()->id;

        Deposit::where('id', $id)->update($dataReq);
        return $this->sendResponse($dataReq, 'success');
    }

    public function show(Deposit $deposit, $id)
    {
        $data = $deposit->with(['members', 'deposit_types'])->where('member_id', $id)->get();
        return $this->sendResponse($data, 'success');
    }

    public function destroy(Deposit $Deposit, $id)
    {

        $data =  $Deposit->where('id', $id)->delete($Deposit);

        return $this->sendResponse($data, 'success');
    }

    public function depositInterest()
    {
        // $member_id =  Deposit::select(["member_id", "saldo"])->get();

        $members = Member::all();
        // return $members;
        $tabungans = [];

        foreach ($members as $member) {
            // $depoBunga = Deposit_types::where('id', 'simpanan sukarela')->get();
            $saldo = Deposit::where('member_id', $member->id)->where('deposit_type_id', 1)->latest()->first();
            if ($saldo) {
                $bunga =  $saldo->saldo * 0.0025;
                // return $bunga;
                $tabungans[] = [
                    'member_id' => $saldo->member_id,
                    'saldo' => $saldo->saldo + $bunga,
                    'interest' => $bunga,
                    'debet' => null,
                    'kredit' => null,
                    'date' => Carbon::now(),
                    'created_by' => auth()->user()->id,
                    'deposit_type_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()


                ];
            }
        }

        Deposit::insert($tabungans);
        $deposit = Deposit::where('interest', "!=", null)->get();
        return $this->sendResponse($deposit, 'success');
    }

    public function simpananWajibBaru()
    {
        // $member_id =  Deposit::select(["member_id", "saldo"])->get();

        $members = Member::all();
        // return $members;
        $tabungans = [];

        foreach ($members as $member) {
            // $depoBunga = Deposit_types::where('id', 'simpanan sukarela')->get();
            $saldo = Deposit::where('member_id', $member->id)->latest()->first();
            if ($saldo) {
                $bunga =  $saldo->saldo * 0.0025;
                // return $bunga;
                $tabungans[] = [
                    'member_id' => $saldo->member_id,
                    'saldo' => 0,
                    'interest' => null,
                    'debet' => null,
                    'kredit' => null,
                    'date' => Carbon::now(),
                    'created_by' => auth()->user()->id,
                    'deposit_type_id' => 3,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()


                ];
            }
        }

        Deposit::insert($tabungans);
        $deposit = Deposit::where('interest', "!=", null)->get();
        return $this->sendResponse($deposit, 'success');
    }



    public function report(Deposit $Deposit)
    {



        $deposits = $Deposit
            ->where('created_by', auth()->user()->id)->with(['members', 'deposit_types'])->get();

        $reports = [];

        foreach ($deposits as $deposit) {
            $reports[] = [
                'name' => $deposit->members->name,
                'date' => $deposit->date,
                'debet' => $deposit->debet,
                'kredit' => $deposit->kredit,
                'saldo' => $deposit->saldo,
                'deposit_type' => $deposit->deposit_types->deposit_name,
                'is_acc' => false,
                'crated_by' => auth()->user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()

            ];
        }
        ReportDeposit::insert($reports);
        return $this->sendResponse($reports, "success");
    }


    public function sumSaldoAll()
    {
        $debet = Deposit::latest()->sum('debet');
        $kredit = Deposit::latest()->sum('kredit');

        $saldo = $debet - $kredit;
        return $this->sendResponse($saldo, 'success');
    }
    public function sumSaldoAdmin()
    {
        $debet = Deposit::where('created_by', auth()->user()->id)->whereMonth('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('debet');
        $kredit = Deposit::whereMonth('date', [Carbon::now()->startOfMonth(1), Carbon::now()->endOfMonth(1)])->where('created_by', auth()->user()->id)->sum('kredit');

        $saldo = $debet - $kredit;
        return $this->sendResponse($saldo, 'success');
    }


    public function bunga(Request $request)
    {
        $dateStart = null;
        if ($request->dateStart) {
            $dateStart = Carbon::parse($request->dateStart)->format('Y-m-d');
        }
        $dateEnd = null;
        if ($request->dateEnd) {
            $dateEnd = Carbon::parse($request->dateEnd)->format('Y-m-d');
        }
        $data = Deposit::fillterDate($dateStart, $dateEnd)->fillter($request)->where('interest', '!=', null)->get();
        return $this->sendResponse(DepositResource::collection($data), "success");
    }

    public function sendReport(Request $request)
    {

        $date = Carbon::parse($request->date);

        $dateStart = $date->copy()->firstOfMonth()->format('Y-m-d');
        $dateEnd = $date->copy()->lastOfMonth()->format('Y-m-d');

        $data = new ValidasiLaporan();
        $data->date = $dateStart;
        $data->type = 'deposit';
        $data->created_by = auth()->user()->id;
        $data->save();


        $dataDepo = Deposit::whereDate('date', '>=', $dateStart)->whereDate('date', '<=', $dateEnd)
            ->where('validasi_laporan_id', null)
            ->where('created_by', auth()->user()->id);


        $upateLaporans = $dataDepo;

        $upateLaporans->update([
            'validasi_laporan_id' => $data->id
        ]);
        $getData = Deposit::whereDate('date', '>=', $dateStart)->whereDate('date', '<=', $dateEnd)->get();

        return $getData;
    }


    public function getLaporanAdmin(Request $request)
    {
        $data = ValidasiLaporan::fillter($request)->where('created_by', auth()->user()->id)->get();
        return $this->sendResponse($data, 'succes');
    }

    public function getLaporanLeader(Request $request)
    {
        $data = ValidasiLaporan::fillter($request)->get();
        return $this->sendResponse($data, 'succes');
    }

    public function showLaporan($id, Request $request)
    {
        $data = Deposit::fillter($request)->where('validasi_laporan_id', $id)->get();

        return $this->sendResponse(DepositResource::collection($data), 'success');
    }

    public function validasiLaporan($id, Request $request)
    {

        $data = ValidasiLaporan::where('id', $id);
        $data->update([
            'is_validasi' => true
        ]);
        $dataUpdate = ValidasiLaporan::where('id', $id)->get();

        return $this->sendResponse($dataUpdate, 'success');
    }

    public function indexLaporan(Deposit $Deposit)
    {
        $data = $Deposit->where('created_by', auth()->user()->id)
            ->whereMonth('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->where('validasi_laporan_id', '!=', null)
            ->get();
        return $this->sendResponse(DepositResource::collection($data), "success");
    }


    public function simpananWajib(Request $request)
    {
        $dateStart = null;
        if ($request->dateStart) {
            $dateStart = Carbon::parse($request->dateStart)->format('Y-m-d');
        }
        $dateEnd = null;
        if ($request->dateEnd) {
            $dateEnd = Carbon::parse($request->dateEnd)->format('Y-m-d');
        }

        $data = Deposit::fillterDate($dateStart, $dateEnd)->fillter($request)->where('deposit_type_id', 3)->with(['members', 'deposit_types'])->orderBy('id', 'DESC')->get();

        // $total = $data->sum('debet');
        // dd($total);
        return $this->sendResponse($data, 'success');
    }
    public function showSimpananWajib(Request $request, $id)
    {


        $data = Deposit::where('member_id', $id)->where('deposit_type_id', 3)->with(['members', 'deposit_types'])->orderBy('id', 'DESC')->get();

        return $this->sendResponse($data, 'success');
    }
}
