<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthRequest;
use App\Models\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(StoreAuthRequest $request): Response|ResponseFactory
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['remember_token'] = Str::random(10);
        $user = User::query()->create($data);
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        return response(['token' => $token], 200);
    }

    public function login (StoreAuthRequest $request) {
        $data = $request->all();
        /*$user = User::query()->where('username', $data['username'])->first();
        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                $token = $user->createToken('Laravel Personal Access Client')->accessToken;
                return response([
                    'token_type' => 'Bearer',
                    'access_token' => $token], 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }*/
        $credentials = request(['username', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'fails',
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = $request->user();
        if ($user) {
            $token = $user->createToken('Laravel Personal Access Client')->accessToken;
            return response([
                'token_type' => 'Bearer',
                'access_token' => $token], 200);
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }
    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
