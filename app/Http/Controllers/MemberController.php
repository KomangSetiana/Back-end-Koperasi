<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest\MemberStoreRequest;
use App\Http\Resources\MemberResource;
use App\Models\Deposit;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberController extends Controller
{
  public function index(Member $member, Request $request)
  {
    $data = $member->fillter($request)->with('korwil')->get();
    return $this->sendResponse($data, "success");
  }
  public function countMember(Member $member)
  {
    $data = $member->count('id');
    return $this->sendResponse($data, "success");
  }
  public function store(MemberStoreRequest $request)
  {

    $data = Member::create($request->validated());
    $depoAwal = new Deposit();
    $depoAwal->member_id = $data->id;
    $depoAwal->date = Carbon::now();
    $depoAwal->debet = 120000;
    $depoAwal->saldo = 120000;
    $depoAwal->created_by = 1;
    $depoAwal->deposit_type_id = 3;
    $depoAwal->save();

    return $data;
  }


  public function update(MemberStoreRequest $request, $id)
  {
    $data = ($request->validated());

    Member::where('id', $id)->update($data);
    return $this->sendResponse($data, 'success');
  }

  public function show(Member $member)
  {
    // dd($member);
    // $data =  $member->where('id', $id)->get($member);

    // return $this->sendResponse($data, 'success');


    return new MemberResource($member);
  }

  public function destroy(Member $member, $id)
  {

    $data =  $member->where('id', $id)->delete($member);

    return $this->sendResponse($data, 'success');
  }

  public function search(Request $request)
  {
    $search = $request->search;

    $member = Member::where(function ($query) use ($search) {
      $query->where('name', 'like', "%$search%")->get();
    });

    return $this->sendResponse($member, 'success');
  }
}
