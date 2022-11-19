<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
    use ApiHelpers;
    private UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * register super admin
     */
    public function registerSuperAdmin(UserRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userRepository->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 4,
            'fcm_token' => $validated['fcm_token']
        ]);

        $token = $user->createToken(env('HASH_TOKEN'), config('fortamil.ability_api.super_admin'))->plainTextToken;

        $result = [
            'user' => $user,
            'access_token' => $token
        ];

        return $this->onSuccess($result, 'Super admin registered');
    }

    /**
     * register admin
     */
    public function registerAdmin(UserRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userRepository->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 1,
            'fcm_token' => $validated['fcm_token']
        ]);

        $token = $user->createToken(env('HASH_TOKEN'), config('fortamil.ability_api.admin'))->plainTextToken;

        $result = [
            'user' => $user,
            'access_token' => $token
        ];

        return $this->onSuccess($result, 'Admin registered');
    }

    /**
     * register warehouse officer
     */
    public function registerWarehouseOfficer(UserRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userRepository->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 2,
            'fcm_token' => $validated['fcm_token']
        ]);

        $token = $user->createToken(env('HASH_TOKEN'), config('fortamil.ability_api.warehouse_officer'))->plainTextToken;
        $result = [
            'user' => $user,
            'access_token' => $token
        ];
        return $this->onSuccess($result, 'Warehouse officer registered');
    }

    /**
     * register dropshipper
     */
    public function registerDropshipper(UserRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userRepository->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 3,
            'fcm_token' => $validated['fcm_token']
        ]);

        $token = $user->createToken(env('HASH_TOKEN'), config('fortamil.ability_api.dropshipper'))->plainTextToken;
        $result = [
            'user' => $user,
            'access_token' => $token
        ];
        return $this->onSuccess($result, 'Dropshipper registered');
    }

    /**
     * register cashier
     */
    public function registerCashier(UserRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $user = $this->userRepository->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 5,
            'fcm_token' => $validated['fcm_token']
        ]);

        $token = $user->createToken(env('HASH_TOKEN'), config('fortamil.ability_api.cashier'))->plainTextToken;
        $result = [
            'user' => $user,
            'access_token' => $token
        ];
        return $this->onSuccess($result, 'Cashier registered');
    }

    /**
     * login.
     */
    public function login(UserLoginRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        if (!Auth::attempt($request->only('email', 'password')))
        {
            return $this->onError('Unauthorized', 401);
        }

        $user = User::where('email', $validated['email'])->with(['role' => function($q) {
            $q->select('id', 'name', 'slug');
        }])->firstOrFail();
        if (!$user) {
            return $this->onError('Failed login');
        }
        $user->update([
            'fcm_token' => $validated['fcm_token']
        ]);
        $acl = config('fortamil.ability_api.'.$user->role->slug);
        $token = $user->createToken(env('HASH_TOKEN'), $acl)->plainTextToken;
        $user->ability = config('fortamil.ability_menu.'.$user->role->slug);
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

    /**
     * get profile
     */
    public function getProfile(Request $request)
    {
        return $this->onSuccess($request->user());
    }
}
