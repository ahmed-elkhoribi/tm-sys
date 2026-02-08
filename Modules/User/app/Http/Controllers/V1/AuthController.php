<?php

namespace Modules\User\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Http\Requests\V1\LoginRequest;
use Modules\User\Http\Requests\V1\RegisterRequest;
use Modules\User\Http\Resources\V1\UserResource;
use Modules\User\Services\UserService;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->userService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
        ], 201);
    }

    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->userService->login($request->validated());

        if (!$result) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
        ]);
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->userService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
