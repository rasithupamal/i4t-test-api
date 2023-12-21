<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(AuthRegisterRequest $request)
    {
        try {
            $request->validated();

            $user = new User([
                'name'  => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            if ($user->save()) {
                $tokenResult = $user->createToken($request->email);
                $token = $tokenResult->plainTextToken;

                return response()->json([
                    'message' => 'Successfully registered',
                    'accessToken' => $token,
                    'token_type' => 'Bearer',
                ], 201);
            } else {
                return response()->json(['error' => 'Please fill mandatory details'], 422);
            }
        } catch (ValidationException $e) {
            return response()->json(["message" => "Please fill all fields", 'error' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function login(AuthLoginRequest $request)
    {
        try {
            $request->validated();
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user = $request->user();
            $tokenResult = $user->createToken($request->email);
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'accessToken' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json(["message" => "Please fill all fields", 'error' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
