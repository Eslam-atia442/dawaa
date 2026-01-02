<?php

namespace App\Http\Controllers\Dashboard\Admin\Auth;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Admin\CreateRequest;
use App\Http\Requests\Admin\Admin\UpdateRequest;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\Admin;
use Exception;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;


class AuthController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array  $relations;

    public function __construct(
        AdminService $service,
                     $table = 'admins',
                     $guard = 'admin'
    ){
        $this->service   = $service;
        $this->table     = $table;
        $this->guard     = $guard;
        $this->relations = [];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'admin');
    }

    public function login(){
        return view('dashboard.admin.auth.login');
    }


    public function auth(LoginRequest $request){
        $data = $request->validated();
        if (auth()->guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember'] ?? null)) {

            if (auth()->guard('admin')->user()->is_blocked == 1 || auth()->guard('admin')->user()->is_active == 0) {
                auth()->guard('admin')->logout();
                throw ValidationException::withMessages([
                    'email' => [trans('auth.failed2')],
                ]);
            }
           
            $roles = auth()->guard('admin')->user()->roles;
            foreach ($roles as $role) {
                if(!$role->is_active){
                    auth()->guard('admin')->logout();
                    throw ValidationException::withMessages([
                        'email' => [trans('auth.failed3')],
                    ]);
                }
        
            }
        
            $request->session()->regenerate();
            return response()->json(['url' => route('admin.home')]);
        } else {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }
    }

    public function logout(Request $request){
        Auth::guard('admin')->logout();
        return redirect()->route('login');
    }

}
