<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Traits\ResponseApi;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    use ResponseApi;
    
    /**
     * Register new user.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:100|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);

            $token = JWTAuth::fromUser($user);

            return $this->responseData([
                'user' => $user,
                'token' => $this->respondWithToken($token),
            ], 'Se ha registrado el usuario correctamente.', 201);
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo registrar el usuario.');
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8',
            ]);

            if (! $token = JWTAuth::attempt($validatedData)) {
                throw new UnauthorizedHttpException('Unauthorized', 'El email o la contraseña son incorrectos.');
            }

            return $this->responseData([
                'token' => $this->respondWithToken($token),
            ], 'Usuario autenticado correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'Credenciales incorrectas.');
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return $this->responseData(JWTAuth::user(), 'Usuario autenticado.');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->responseData([], 'Se ha cerrado la sesión correctamente.');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->responseData([
            'token' => $this->respondWithToken(JWTAuth::refresh()),
        ], 'Token de actualización.');
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @return DataToken
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }
}
