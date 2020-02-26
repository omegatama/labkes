<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Config;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:admin')->except('logout');
        $this->middleware('guest:sekolah')->except('logout');
    }

    public function logout(Request $request)
    {
        Auth::guard($request->guard)->logout();
        return redirect('/');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAdminLoginForm()
    {
        return view('auth.login', [
            'url' => Config::get('constants.guards.admin')
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSekolahLoginForm()
    {
        return view('auth.login', [
            'url' => Config::get('constants.guards.sekolah')
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function validator(Request $request)
    {
        return $this->validate($request, [
            'email'   => 'required',
            'password' => 'required|min:6',
            'ta'=> 'required'
        ]);
    }

    /**
     * @param Request $request
     * @param $guard
     * @return bool
     */
    protected function guardLogin(Request $request, $guard)
    {
        $this->validator($request);
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $fieldType = 'email';
        }
        else {
            if ($guard == 'sekolah') {
                $fieldType = 'npsn';
            }
            else{
                $fieldType = 'username';
            }
        }
        
        return Auth::guard($guard)->attempt(
            [
                $fieldType => $request->email,
                'password' => $request->password
            ],
            $request->get('remember')
        );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adminLogin(Request $request)
    {
        if ($this->guardLogin($request, Config::get('constants.guards.admin'))) {
            // $request->session()->put('ta', $request->ta);
            return redirect()->intended('/admin')->withCookie(cookie()->forever('ta', $request->ta));
        }

        return back()->withInput($request->only('email', 'remember'));
    }



    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sekolahLogin(Request $request)
    {
        if ($this->guardLogin($request,Config::get('constants.guards.sekolah'))) {
            // $request->session()->put('ta', $request->ta);
            return redirect()->intended('/sekolah')->withCookie(cookie()->forever('ta', $request->ta));
        }

        return back()->withInput($request->only('email', 'remember'));
    }
}
