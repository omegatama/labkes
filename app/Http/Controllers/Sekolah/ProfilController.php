<?php

namespace App\Http\Controllers\Sekolah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Hash;

class ProfilController extends Controller
{
    public function index()
    {
    	$user = Auth::user();
    	return view('sekolah.profil.index', compact('user'));
    }

    public function edit(Request $request)
    {
    	$user = Auth::user();
    	return view('sekolah.profil.edit', compact('user'));
    }

    public function update(Request $request)
    {
    	$user= Auth::user();

    	$user->name = $request->name;
    	$user->email = $request->email;
    	$user->telepon = $request->telepon;
    	$user->kecamatan_id = $request->kecamatan_id;
    	$user->desa = $request->desa;
    	$user->alamat = $request->alamat;
    	$user->nama_kepsek = $request->nama_kepsek;
    	$user->nip_kepsek = $request->nip_kepsek;
    	$user->nama_bendahara = $request->nama_bendahara;
    	$user->nip_bendahara = $request->nip_bendahara;
    	
    	try {
    		$user->save();
    		return redirect()->route('sekolah.profil.index');

    	} catch (\Exception $e) {
    		return redirect()->back()->withErrors($e->getMessage());
    	}
    	// return json_encode($request->all());
    }

    public function passwordedit(Request $request)
    {
    	return view('sekolah.profil.passwordedit');
    }

    public function passwordupdate(Request $request)
    {
    	try {
            $user = Auth::user();
            $passwordlama = $user->password;
            if ($request->password_baru == $request->konfirmasi) {
                if (Hash::check($request->password, $passwordlama)) {
                    $passwordbaru = Hash::make($request->password_baru);
                    $user->password = $passwordbaru;
                    $user->save();

                    return redirect()->route('sekolah.profil.index')->with(['success'=>'Password Berhasil diperbaharui']);

                }
                else{
                    return redirect()->back()->withErrors('Password Lama anda tidak sesuai!');
                }
            }
            else{
                return redirect()->back()->withErrors('Password baru tidak sama dengan konfirmasi password! Mohon periksa kembali!');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg'=> $e->getMessage()]);
        }
    }
}
