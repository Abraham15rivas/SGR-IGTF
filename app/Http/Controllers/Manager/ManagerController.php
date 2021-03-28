<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index()
    {
        $title = 'Gestión de calendario';
        return view('manager.calendar', compact('title'));
    }
}
