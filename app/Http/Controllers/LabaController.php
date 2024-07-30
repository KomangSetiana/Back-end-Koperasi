<?php

namespace App\Http\Controllers;

use App\Models\Laba;
use Illuminate\Http\Request;

class LabaController extends Controller
{
    public function sumLaba()
    {
        $laba = Laba::sum("laba_amount");

        return $this->sendResponse($laba, 'success');
    }

    public function index()
    {
        $laba = Laba::all();

        return $this->sendResponse($laba, 'success');
    }
}
