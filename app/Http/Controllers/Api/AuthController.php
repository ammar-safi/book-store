<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    use Response;
    public function login(LoginRequest $request)
    {
        try {
            DB::beginTransaction();
            $credentials = $request->only('email', 'password');

            if (auth()->attempt($credentials)) {
                $user = auth()->user();
                $token = $user->createToken('authToken')->plainTextToken;

                $data['token'] = $token;
                DB::commit();
                return $this->data($data, 'Login successful');
            }
            DB::rollBack();
            return $this->unauthorize("email or password not correct");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->serverError($th->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $request->user("api")->currentAccessToken()->delete();
        return $this->success("Logout successful");
    }

    public function register(RegisterRequest $request)
    {
        try {
            $info = $request->validated();
            DB::beginTransaction();
            $user = User::create($info);

            $data["user"] = UserResource::make($user);
            DB::commit();
            return $this->data($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->serverError($th->getMessage());
        }
    }
    public function getregister(Request $request)
    {
        return User::create([
            "name" => "test",
            "email" => "test",
            "password" => "test"
        ]);
    }
}
