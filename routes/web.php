<?php
Route::view('/', 'auth.guest');
Auth::routes();

Route::get('/login/admin', 'Auth\LoginController@showAdminLoginForm')->name('login.admin');
Route::get('/register/admin', 'Auth\RegisterController@showAdminRegisterForm')->name('register.admin');
Route::get('/login/sekolah', 'Auth\LoginController@showSekolahLoginForm')->name('login.sekolah');
Route::get('/register/sekolah', 'Auth\RegisterController@showSekolahRegisterForm')->name('register.sekolah');

Route::post('/login/admin', 'Auth\LoginController@adminLogin');
Route::post('/register/admin', 'Auth\RegisterController@createAdmin')->name('register.admin');
Route::post('/login/sekolah', 'Auth\LoginController@sekolahLogin');
Route::post('/register/sekolah', 'Auth\RegisterController@createSekolah')->name('register.sekolah');

Route::view('/home', 'home')->middleware('auth');

Route::group([
	'prefix'     => 'admin',
	'middleware' => 'auth:admin'
], function () {
    Route::view('/', 'admin.index')->name('admin');;

    // Start Kode Barang
    Route::resource('/kodebarang', 'Admin\Master\KodeBarangController', ['as' => 'admin'])
	->except([
	    'create', 'destroy'
	]);
	Route::get('/kodebarang/delete/{id}', 'Admin\Master\KodeBarangController@destroy');
    // End Kode Barang

	// Start Kode Program
    Route::resource('/kodeprogram', 'Admin\Master\KodeProgramController', ['as' => 'admin'])
	->except([
	    'create', 'destroy'
	]);
	Route::get('/kodeprogram/delete/{id}', 'Admin\Master\KodeProgramController@destroy');
    // End Kode Program

    // Start Kode Program
    Route::resource('/komponenpembiayaan', 'Admin\Master\KomponenPembiayaanController', ['as' => 'admin'])
	->except([
	    'create', 'destroy'
	]);
	Route::get('/komponenpembiayaan/delete/{id}', 'Admin\Master\KomponenPembiayaanController@destroy');
    // End Kode Program

    // Start Kode Rekening
    Route::resource('/koderekening', 'Admin\Master\KodeRekeningController', ['as' => 'admin']);
    // End Kode Rekening

    // Start Pagu
    Route::resource('/pagu', 'Admin\PaguController', ['as' => 'admin']);
    // End Pagu

    // Start Penerimaan
    Route::resource('/penerimaan', 'Admin\PenerimaanController', ['as' => 'admin'])
    ->except('destroy');
    Route::get('/penerimaan/delete/{id}', 'Admin\PenerimaanController@destroy')->name('admin.penerimaan.destroy');
    

    Route::get('/saldo', 'Admin\KasController@saldo_index')->name('admin.kas.saldo');

    Route::get('/saldolalu', 'Admin\KasController@saldolalu_index')->name('admin.kas.saldolalu');
    // End Penerimaan

    // Start Sekolah
    Route::resource('/sekolah', 'Admin\SekolahController', ['as' => 'admin'])
    ->except([
        'destroy'
    ]);
    Route::get('/sekolah/select', 'Admin\SekolahController@selectSekolah')->name('admin.sekolah.select');
    Route::get('/sekolah/delete/{id}', 'Admin\SekolahController@destroy')->name('admin.sekolah.destroy');
    Route::get('/sekolah/reset/{id}', 'Admin\SekolahController@reset')->name('admin.sekolah.reset');
    // End Sekolah

    // Start Rka
    Route::get('/rka', 'Admin\RkaController@index')->name('admin.rka.index');
    // End Rka

    // Start Persediaan
    Route::get('/stok', 'Admin\TransaksiPersediaanController@stok_index')->name('admin.persediaan.stok');
    Route::get('/penggunaan', 'Admin\TransaksiPersediaanController@penggunaan_index')->name('admin.persediaan.penggunaan');
    Route::get('/penyesuaian', 'Admin\TransaksiPersediaanController@penyesuaian_index')->name('admin.persediaan.penyesuaian');
    // End Persediaan
});

Route::group([
	'prefix'     => 'sekolah',
	'middleware' => 'auth:sekolah'
], function () {
    Route::view('/', 'sekolah.index')->name('sekolah');
    
    // Start Master Kegiatan
    Route::resource('/kegiatan', 'Sekolah\Master\KegiatanController', ['as' => 'sekolah'])
	->except([
	    'create', 'destroy'
	]);
	Route::get('/kegiatan/delete/{id}', 'Sekolah\Master\KegiatanController@destroy');
    // End Master Kegiatan

    // Start Master BarangPersediaan
    Route::resource('/barangpersediaan', 'Sekolah\Master\BarangPersediaanController', ['as' => 'sekolah'])
	->except([
	    'create', 'destroy'
	]);
	Route::get('/barangpersediaan/delete/{id}', 'Sekolah\Master\BarangPersediaanController@destroy');
    // End Master BarangPersediaan

    // Start Pagu
    Route::get('/pagu', 'Sekolah\PaguController@index')->name('sekolah.pagu.index');
    // End Pagu

    // Start RKA
    Route::resource('/rka', 'Sekolah\RkaController', ['as' => 'sekolah'])
    ->except([
        'destroy'
    ]);
    Route::get('/rka/delete/{id}', 'Sekolah\RkaController@destroy')->name('sekolah.rka.destroy');
    // End RKA

    // Start Select2 URL
    Route::get('/select/program', 'Sekolah\Master\SelectDataController@selectProgram')->name('sekolah.select.program');
    Route::get('/select/kegiatan', 'Sekolah\Master\SelectDataController@selectKegiatan')->name('sekolah.select.kegiatan');
    Route::get('/select/kp', 'Sekolah\Master\SelectDataController@selectKp')->name('sekolah.select.kp');
    Route::get('/select/rekening', 'Sekolah\Master\SelectDataController@selectRekening')->name('sekolah.select.rekening');
    Route::get('/select/barangpersediaan', 'Sekolah\Master\SelectDataController@selectBarangPersediaan')->name('sekolah.select.barangpersediaan');
    // End Select2 URL

    // Start Rka Limit
    Route::get('/rkalimit', 'Sekolah\RkaLimitController@index')->name('sekolah.rkalimit.index');
    // End Rka Limit

    // Start Kas / Saldo
    Route::get('/penerimaan', 'Sekolah\KasController@penerimaanDana_index')->name('sekolah.kas.penerimaan');

    Route::get('/saldo', 'Sekolah\KasController@saldo_index')->name('sekolah.kas.saldo');

    Route::get('/saldolalu', 'Sekolah\KasController@saldolalu_index')->name('sekolah.kas.saldolalu');

    Route::resource('/trxkas', 'Sekolah\TransaksiKasController', ['as' => 'sekolah'])
    ->except([
        'destroy'
    ]);
    Route::get('/trxkas/delete/{id}', 'Sekolah\TransaksiKasController@destroy')->name('sekolah.trxkas.destroy');
    // End Kas / Saldo

    // Start Persediaan
    Route::resource('/trxpersediaan', 'Sekolah\TransaksiPersediaanController', ['as' => 'sekolah'])
    ->except([
        'index','destroy'
    ]);
    Route::get('/trxpersediaan/delete/{id}', 'Sekolah\TransaksiPersediaanController@destroy')->name('sekolah.trxpersediaan.destroy');

    Route::get('/stok', 'Sekolah\TransaksiPersediaanController@stok_index')->name('sekolah.persediaan.stok');
    Route::get('/penggunaan', 'Sekolah\TransaksiPersediaanController@penggunaan_index')->name('sekolah.persediaan.penggunaan');
    Route::get('/penyesuaian', 'Sekolah\TransaksiPersediaanController@penyesuaian_index')->name('sekolah.persediaan.penyesuaian');
    // End Persediaan
});
