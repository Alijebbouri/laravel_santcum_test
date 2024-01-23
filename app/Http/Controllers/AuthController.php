<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;
    public function login(LoginUserRequest $request){
        $request->validated();
        $email = $request->input('email');
        $password = $request->input('password');
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return $this->error('', 'Credentials do not match', 401);
        }
        $user = User::where('email','=',$email)->firstOrFail();
        return $this->success([
            'user'=>$user,
            'token'=>$user->createToken('Api Token of'.$user->name)->plainTextToken,
        ]);
    }
    public function register(StoreUserRequest $request){
        $request->validated($request->all());
        $user = User::create([
            'name' => $request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        return $this->success([
            'user'=>$user,
            'token'=>$user->createToken('token '.$user->name)->plainTextToken
        ]);
    }
    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            'message'=> 'You logged out'
        ]);
    }
}
