<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest\KorwilStoreRequest;
use App\Http\Requests\UpdateRequest\KorwilUpdateRequest;
use App\Http\Resources\KorwilResource;
use App\Models\Korwil;
use Illuminate\Http\Request;

class KorwilController extends Controller
{

  public function index(Korwil $korwil)
  {
    $data = $korwil->get();
    return $this->sendResponse(KorwilResource::collection($data), "success");
  }
  public function store(KorwilStoreRequest $request, Korwil $korwil)
  {
    $data = $korwil->create($request->validated());
    return $this->sendResponse(KorwilResource::collection($data), "success");
  }

  public function update(KorwilStoreRequest $request, $id, Korwil $korwil)
  {
    $data = ($request->validated());

    Korwil::where('id', $id)->update($data);
    return $this->sendResponse(KorwilResource::collection($data), 'success');
  }

  public function destroy(Korwil $korwil, $id)
  {

    $data =  $korwil->where('id', $id)->delete($korwil);

    return $this->sendResponse($data, 'success');
  }
}
