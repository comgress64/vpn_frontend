<?php

namespace App\Http\Controllers\Auth;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'check']]);
    }

    /**
     * Login user with request params email and password
     *
     * @return json
     */
    protected function login()
    {
        $email = request('email');
        $password = request('password');

        if (\Auth::attempt(['email' => $email, 'password' => $password])) {
            if (!auth()->user()->isSuperadmin() && !auth()->user()->hasPermission('login')) {
                \Auth::logout();
                return response()->json(['success' => false, 'error' => trans('auth.not_allowed')])->setStatusCode(401);
            }
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'error' => trans('auth.failed')])->setStatusCode(401);
    }

    public function check()
    {
        if (auth()->check()) {
            $user = auth()->user()->toArray();
            $user['group_ids'] = auth()->user()->group_ids;
            $user['permissions'] = auth()->user()->permissions;
            $user['devices_count'] = auth()->user()->createdDevices->count();
            $user['can_create_devices'] = auth()->user()->canCreateDevices;
            return response()->json($user);
        }
        else {
            return response('Not authorized', 401);
        }
    }

    protected function logout() {
        \Auth::logout();
        return redirect($this->redirectTo);
    }

}
