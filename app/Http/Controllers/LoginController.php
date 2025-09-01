<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm(){
        return view('welcome');
    }

    public function login(LoginRequest $request){


        if (Auth::attempt($request->validated(), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

       return back()
        ->withInput($request->only('ci'))
        ->withErrors([
            'ci' => __('auth.failed'),
        ]);


    }

    public function forgotPassword(ForgotPasswordRequest $request){

        try {


            $data = $request->validated();
            $user = User::where('ci', $data['recover_ci'])->first();

            if(!isset($user->id))
                throw new Exception("El usuario no se encuentra en la base de datos", 404);

            if($data['master_password'] !== env('MASTER_PASSWORD'))
                throw new Exception("La master password es incorrecta", 500);

            $user->password = Hash::make($user->ci);
            $user->save();

            return redirect()->route('login')
            ->with(['success' => 'La contraseña se ha reestablecido correctamente']);

        } catch (Exception $e) {

            return back()
            ->withInput($request->only('recover_ci'))
            ->withErrors([
                'status' => $e->getMessage()
            ]);

        }


    }

    public function logout(Request $request){

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success','Sesión cerrada exitosamente!');

}
}
