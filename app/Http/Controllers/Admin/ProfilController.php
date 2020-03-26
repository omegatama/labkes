<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class ProfilController extends Controller
{
    public function index()
    {
    	$user = Auth::user();
    	return view('admin.profil.index', compact('user'));
    }

    public function edit()
    {
    	# code...
    }

    public function update()
    {
    	# code...
    }

    public function passwordedit()
    {
    	# code...
    }

    public function passwordupdate()
    {
    	# code...
    }
}
