<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        /* Para mantener solo una sesión activa por dispositivo o client web,
        Con el method definido se cierran las sesiónes abiertas en otros dispositivos */
        Auth::logoutOtherDevices($request['password']);
        
        // Para evitar el acceso al sistema de los usuarios deshabilitados
        if($user->status == 0) {
            Auth::logout();
            abort(403, 'No puede ingresar al sistema su usuario fue deshabilitado, por favor ponerse en contacto con el administrador. liliana.guerra@bav.com.ve');
        }
    }
}
