<?php

namespace App\Http\Controllers;

use App\Models\User;
use AvtoDev\JsonRpc\Errors\InvalidParamsError;
use AvtoDev\JsonRpc\Requests\RequestInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(RequestInterface $request)
    {
        $params = $request->getParams();

        if($params){
            $params = json_decode(json_encode($params), true);
            $validator = Validator::make($params, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
            ]);
            if($validator->fails()){
                throw new InvalidParamsError("Invalid params", 200, $validator->errors()->getMessages());
            }
        }

        $user = User::create([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => Hash::make($params['password']),
        ]);

        //$token = $user->createToken('guest_token', ['guest:try_login'])->plainTextToken;

        event(new Registered($user));

        return $user->toArray();
    }

    public function login(RequestInterface $request)
    {
        $params = $request->getParams();

        if($params){
            $params = json_decode(json_encode($params), true);
            $validator = Validator::make($params, [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string',
            ]);
            if($validator->fails()){
                throw new InvalidParamsError("Invalid params", 200, $validator->errors()->getMessages());
            }
        }

        $user= User::where('email', $params["email"])->first();

        if (!$user || !Hash::check($params["password"], $user["password"])) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        //Delete old tokens
        $user->tokens()->delete();

        //Create new token
        $token = $user->createToken('auth_token', ['user:all'])->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(RequestInterface $request)
    {
        $params = $request->getParams();

        if($params){
            $params = json_decode(json_encode($params), true);
            $validator = Validator::make($params, [
                'user_id' => 'required|integer|exists:users,id'
            ]);
            if($validator->fails()){
                throw new InvalidParamsError("Invalid params", 200, $validator->errors()->getMessages());
            }
        }

        $user= User::find($params["user_id"]);

        //Delete old tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => $user->name.' ('.$user->email.') successfuly logged out!'
        ]);

    }

    public function showInfo(RequestInterface $request)
    {
        $this->checkToken();

        return [
            'params'       => $request->getParams(),
            'notification' => $request->isNotification(),
            'method_name'  => $request->getMethod(),
            'request ID'   => $request->getId(),
        ];
    }

}
