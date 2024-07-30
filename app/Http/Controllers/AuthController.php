<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResourse;
use App\Http\Resources\DepositResource;
use App\Http\Resources\MyDepositResource;
use App\Http\Resources\MyInstallResource;
use App\Http\Resources\MyLoanResource;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\Member;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:api", ['except' => ['login', 'register']]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $request["password"] = bcrypt($request->password);
        $user = User::create(

            $validator->validated()
        );

        return response()->json([
            "message" => "User successfully registered",
            "user" => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(["error" => "Unauthorized"], 401);
        }
        return $this->createNewToken($token);
    }
    public function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 86400,
            'user' => auth()->user()
        ]);
    }

    public function index(Request $request)
    {
        $user = User::fillter($request)->get();
        return $this->sendResponse($user, 'sucess');
    }

    public function update($id, UserRequest $request)
    {

        $dataReq = $request->all();



        $dataReq["password"] = bcrypt($request->password);
        $user = User::where('id', $id)->update($dataReq);
        $this->sendResponse($user, 'success');
    }
    public function profile()
    {
        // dd(auth()->user()->id);
        return $this->sendResponse(new UserResourse(auth()->user()), "success");
    }

    public function depositUser()
    {
        $myDeposit = Member::with('deposits.deposit_types')->where('user_id', auth()->user()->id)->latest()->get();



        return $this->sendResponse(MyDepositResource::collection($myDeposit), 'success');
    }

    public function loanUser()
    {
        $myLoan = Member::with('loans')->where('user_id', auth()->user()->id)->get();

        return $this->sendResponse(MyLoanResource::collection($myLoan), 'success');
    }

    public function myInstallments()
    {
        $myInstall = Member::where('user_id', auth()->user()->id)->with('loans')->first();

        return $this->sendResponse(new MyInstallResource($myInstall), 'success');
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => "User logout"
        ]);
    }
}
