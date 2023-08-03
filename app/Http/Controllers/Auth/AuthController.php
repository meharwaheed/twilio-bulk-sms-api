<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;


class AuthController extends Controller
{
    /**
     * Register new user
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $data['user'] = User::create($validated);

        $data['token'] = $data['user']->createToken('web')->plainTextToken;

        return $this->respond($data, 'Register successfully');
    }



    /**
     * login new user
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (!auth()->attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return $this->respond([], 'Invalid Credentials', false, 401);
        }

        $data['user'] = auth()->user();
        $data['token'] = $data['user']->createToken('web')->plainTextToken;
        return $this->respond($data, 'Login successfully');
    }


    /**
     * Logout current user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return $this->respond([], 'Logout successfully');
    }
}
