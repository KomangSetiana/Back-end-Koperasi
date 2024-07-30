<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest\DepositTypeRequest;
use App\Http\Resources\DepositTypesResourse;
use App\Models\Deposit_types;
use Illuminate\Http\Request;

class DepositTypeController extends Controller
{
    public function index(Deposit_types $DepositType)
    {
        $data = $DepositType->get();
        return $this->sendResponse(DepositTypesResourse::collection($data), "success");
    }
    public function store(DepositTypeRequest $request, Deposit_types $DepositType)
    {
        $data = $DepositType->create($request->validated());
        return $this->sendResponse($data, "success");
    }

    public function update(DepositTypeRequest $request, $id, Deposit_types $DepositType)
    {
        $data = ($request->validated());

        Deposit_types::where('id', $id)->update($data);
        return $this->sendResponse($data, 'success');
    }

    public function destroy(Deposit_types $DepositType, $id)
    {

        $data =  $DepositType->where('id', $id)->delete($DepositType);

        return $this->sendResponse($data, 'success');
    }
}
