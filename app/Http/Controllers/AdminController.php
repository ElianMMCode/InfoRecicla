<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function view_admin()
    {
        // Lógica para la vista de registro
        return view('Admin.admin');
    }
}