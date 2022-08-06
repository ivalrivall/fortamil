<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Http\Library\ApiHelpers;

class AuthController extends Controller
{
    use ApiHelpers;

    /**
     * register admin
     */
    public function registerAdmin(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return $this->onError($validator->errors()->first(), 400);
        }

        if ($this->isAdmin($request->user())) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 1
            ]);

            $token = $user->createToken(env('HASH_TOKEN'), ['admin'])->plainTextToken;

            $result = [
                'user' => $user,
                'access_token' => $token
            ];
            return $this->onSuccess($result, 'Admin registered');
        }
        return $this->onError('Unauthorized', 401);
    }

    /**
     * register warehouse officer
     */
    public function registerWarehouseOfficer(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()){
            return $this->onError($validator->errors()->first(), 400);
        }
        if ($this->isAdmin($request->user())) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 2
            ]);

            $token = $user->createToken(env('HASH_TOKEN'), ['warehouse_officer'])->plainTextToken;
            $result = [
                'user' => $user,
                'access_token' => $token
            ];
            return $this->onSuccess($result, 'Warehouse officer registered');
        }
        return $this->onError('Unauthorized', 401);
    }

    /**
     * register dropshipper
     */
    public function registerDropshipper(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()){
            return $this->onError($validator->errors()->first(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3
        ]);

        $token = $user->createToken(env('HASH_TOKEN'), ['dropshipper'])->plainTextToken;
        $result = [
            'user' => $user,
            'access_token' => $token
        ];
        return $this->onSuccess($result, 'Dropshipper registered');
    }

    /**
     * login.
     */
    public function login(Request $request) : JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password')))
        {
            return $this->onError('Unauthorized', 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        if (!$user) {
            return $this->onError('Failed login', 400);
        }
        $token = $user->createToken(env('HASH_TOKEN'))->plainTextToken;
        $result = [
            'user' => $user,
            'access_token' => $token
        ];
        return $this->onSuccess($result, 'Login successfully');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request) : JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return $this->onSuccess(null, 'Logout successfully');
    }

    /**
     * Log out all session of current user.
     */
    public function logoutAll(Request $request) : JsonResponse
    {
        $user = $request->user();
        $user->tokens()->delete();
        return $this->onSuccess(null, 'Logout successfully');
    }
}
