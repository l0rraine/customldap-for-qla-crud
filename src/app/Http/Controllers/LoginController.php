<?php
/**
 * Created by PhpStorm.
 * User: idn-lee
 * Date: 17-11-1
 * Time: 上午9:47
 */

namespace Qla\LdapLogin\app\Http\Controllers;



use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->redirectTo = route(config('qla.ldap_login.after_login_route_name'));
    }

    public function showLoginForm()
    {
        return view('ldaplogin::auth.login');
    }
}