<?php

namespace app\Http\Controllers;

use app\Gaem;
use app\Mantenimiento;
use app\Obra;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use app\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $usuarios = count(User::select('users.*')
            ->join('datos_usuarios', 'datos_usuarios.user_id', '=', 'users.id')
            ->where('users.estado', 1)
            ->get());

        $mantenimientos = count(Mantenimiento::where('status', 1)->get());
        $obras = count(Obra::where('status', 1)->get());
        $gaems = count(Gaem::where('status', 1)->get());

        return view('home', compact('usuarios', 'mantenimientos', 'obras', 'gaems'));
    }
}
