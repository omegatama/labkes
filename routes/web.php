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

    // Start Profil
    Route::get('/profil', 'Admin\ProfilController@index')->name('admin.profil.index');
    Route::get('/profil/edit', 'Admin\ProfilController@edit')->name('admin.profil.edit');
    Route::post('/profil/update', 'Admin\ProfilController@update')->name('admin.profil.update');
    Route::get('/password/edit', 'Admin\ProfilController@passwordedit')->name('admin.password.edit');
    Route::post('/password/update', 'Admin\ProfilController@passwordupdate')->name('admin.password.update'); 
    // End Profil

    // Select Data
    Route::get('/select/kecamatan', 'Admin\Master\SelectDataController@selectKecamatan')->name('admin.select.kecamatan'); 
    // 

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
    Route::get('/sekolah/select', 'Admin\SekolahController@selectSekolah')->name('admin.sekolah.select');
    Route::resource('/sekolah', 'Admin\SekolahController', ['as' => 'admin'])
    ->except([
        'destroy'
    ]);
    Route::get('/sekolah/delete/{id}', 'Admin\SekolahController@destroy')->name('admin.sekolah.destroy');
    Route::get('/sekolah/reset/{id}', 'Admin\SekolahController@reset')->name('admin.sekolah.reset');
    // End Sekolah

    // Start Rka
    Route::get('/rka', 'Admin\RkaController@index')->name('admin.rka.index');
    // End Rka

    // Start Belanja
    Route::get('/belanja', 'Admin\BelanjaController@index')->name('admin.belanja.index');
    // End Belanja

    // Start Persediaan
    Route::get('/stok', 'Admin\TransaksiPersediaanController@stok_index')->name('admin.persediaan.stok');
    Route::get('/penggunaan', 'Admin\TransaksiPersediaanController@penggunaan_index')->name('admin.persediaan.penggunaan');
    Route::get('/penyesuaian', 'Admin\TransaksiPersediaanController@penyesuaian_index')->name('admin.persediaan.penyesuaian');
    // End Persediaan

    // Start Laporan
    Route::get('/laporan/rkaall', 'Admin\LaporanController@rkaall')->name('admin.laporan.rkaall');
    Route::post('/proses/rkaall', 'Admin\LaporanController@proses_rkaall')->name('admin.proses.rkaall');

    Route::get('/laporan/rka', 'Admin\LaporanController@rka')->name('admin.laporan.rka');
    Route::post('/proses/rka', 'Admin\LaporanController@proses_rka')->name('admin.proses.rka');
    // End Laporan
});

Route::group([
	'prefix'     => 'sekolah',
	'middleware' => 'auth:sekolah'
], function () {
    Route::view('/', 'sekolah.index')->name('sekolah');
    
    // Profil
    Route::get('/profil', 'Sekolah\ProfilController@index')->name('sekolah.profil.index');
    Route::get('/profil/edit', 'Sekolah\ProfilController@edit')->name('sekolah.profil.edit');
    Route::post('/profil/update', 'Sekolah\ProfilController@update')->name('sekolah.profil.update');
    Route::get('/password/edit', 'Sekolah\ProfilController@passwordedit')->name('sekolah.password.edit');
    Route::post('/password/update', 'Sekolah\ProfilController@passwordupdate')->name('sekolah.password.update');
    // Profil

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
    Route::get('/rka/cetak', 'Sekolah\RkaController@cetak')->name('sekolah.rka.cetak');

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
    Route::get('/select/kecamatan', 'Sekolah\Master\SelectDataController@selectKecamatan')->name('sekolah.select.kecamatan');
    Route::get('/select/rekening', 'Sekolah\Master\SelectDataController@selectRekening')->name('sekolah.select.rekening');
    Route::get('/select/barangpersediaan', 'Sekolah\Master\SelectDataController@selectBarangPersediaan')->name('sekolah.select.barangpersediaan');
    Route::get('/select/kodebarang/{parent}', 'Sekolah\Master\SelectDataController@selectKodeBarang')->name('sekolah.select.kodebarang');
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

    // Belanja Th Berjalan
    Route::get('/belanja', 'Sekolah\BelanjaController@index')->name('sekolah.belanja.index');

    Route::get('/belanja/create', 'Sekolah\BelanjaController@create')->name('sekolah.belanja.create');

    Route::post('/belanja/store', 'Sekolah\BelanjaController@store')->name('sekolah.belanja.store');

    Route::get('/belanja/edit/{id}', 'Sekolah\BelanjaController@edit')->name('sekolah.belanja.edit');

    Route::post('/belanja/update/{id}', 'Sekolah\BelanjaController@update')->name('sekolah.belanja.update');

    Route::get('/belanja/destroy/{id}', 'Sekolah\BelanjaController@destroy')->name('sekolah.belanja.destroy');

    Route::get('/belanja/a2/{id}', 'Sekolah\BelanjaController@a2')->name('sekolah.belanja.a2');

    Route::get('/belanja/modal/{id}', 'Sekolah\BelanjaController@modal')->name('sekolah.belanja.modal');

    Route::get('/belanja/modal/{id}/create', 'Sekolah\BelanjaController@createmodal')->name('sekolah.belanja.createmodal');

    Route::post('/belanja/modal/{id}/store', 'Sekolah\BelanjaController@storemodal')->name('sekolah.belanja.storemodal');

    Route::get('/belanja/modal/{id}/edit/{modal_id}', 'Sekolah\BelanjaController@editmodal')->name('sekolah.belanja.editmodal');

    Route::post('/belanja/modal/{id}/update/{modal_id}', 'Sekolah\BelanjaController@updatemodal')->name('sekolah.belanja.updatemodal');

    Route::get('/belanja/modal/{id}/destroy/{modal_id}', 'Sekolah\BelanjaController@destroymodal')->name('sekolah.belanja.destroymodal');

    Route::get('/belanja/getmodal/{id}', 'Sekolah\BelanjaController@getmodal')->name('sekolah.belanja.getmodal');

    Route::get('/belanja/persediaan/{id}', 'Sekolah\BelanjaController@persediaan')->name('sekolah.belanja.persediaan');

    Route::get('/belanja/persediaan/{id}/create', 'Sekolah\BelanjaController@createpersediaan')->name('sekolah.belanja.createpersediaan');

    Route::post('/belanja/persediaan/{id}/store', 'Sekolah\BelanjaController@storepersediaan')->name('sekolah.belanja.storepersediaan');

    Route::get('/belanja/persediaan/{id}/edit/{persediaan_id}', 'Sekolah\BelanjaController@editpersediaan')->name('sekolah.belanja.editpersediaan');

    Route::post('/belanja/persediaan/{id}/update/{persediaan_id}', 'Sekolah\BelanjaController@updatepersediaan')->name('sekolah.belanja.updatepersediaan');

    Route::get('/belanja/persediaan/{id}/destroy/{persediaan_id}', 'Sekolah\BelanjaController@destroypersediaan')->name('sekolah.belanja.destroypersediaan');

    Route::get('/belanja/getpersediaan/{id}', 'Sekolah\BelanjaController@getpersediaan')->name('sekolah.belanja.getpersediaan');


    // End Belanja Th Berjalan

    // Start Belanja Modal Th Berjalan
    Route::get('/belanjamodal', 'Sekolah\BelanjaModalController@index')->name('sekolah.belanjamodal.index');
    // End Belanja Modal Th Berjalan

    // Start Belanja Persediaan Th Berjalan
    Route::get('/belanjapersediaan', 'Sekolah\BelanjaPersediaanController@index')->name('sekolah.belanjapersediaan.index');
    // End Belanja Persediaan Th Berjalan

    // Start Laporan
    Route::get('/laporan/realisasi', 'Sekolah\LaporanController@realisasi')->name('sekolah.laporan.realisasi');

    Route::post('/proses/realisasi', 'Sekolah\LaporanController@proses_realisasi')->name('sekolah.proses.realisasi');

    Route::get('/laporan/sptj', 'Sekolah\LaporanController@sptj')->name('sekolah.laporan.sptj');

    Route::post('/proses/sptj', 'Sekolah\LaporanController@proses_sptj')->name('sekolah.proses.sptj');

    Route::get('/laporan/sptmh', 'Sekolah\LaporanController@sptmh')->name('sekolah.laporan.sptmh');

    Route::post('/proses/sptmh', 'Sekolah\LaporanController@proses_sptmh')->name('sekolah.proses.sptmh');

    Route::get('/laporan/k7kab', 'Sekolah\LaporanController@k7kab')->name('sekolah.laporan.k7kab');

    Route::post('/proses/k7kab', 'Sekolah\LaporanController@proses_k7kab')->name('sekolah.proses.k7kab');

    Route::get('/laporan/k7prov', 'Sekolah\LaporanController@k7prov')->name('sekolah.laporan.k7prov');

    Route::post('/proses/k7prov', 'Sekolah\LaporanController@proses_k7prov')->name('sekolah.proses.k7prov');

    Route::get('/laporan/modal', 'Sekolah\LaporanController@modal')->name('sekolah.laporan.modal');

    Route::post('/proses/modal', 'Sekolah\LaporanController@proses_modal')->name('sekolah.proses.modal');

    Route::get('/laporan/persediaan', 'Sekolah\LaporanController@persediaan')->name('sekolah.laporan.persediaan');

    Route::post('/proses/persediaan', 'Sekolah\LaporanController@proses_persediaan')->name('sekolah.proses.persediaan');

    Route::get('/laporan/bku', 'Sekolah\LaporanController@bku')->name('sekolah.laporan.bku');

    Route::post('/proses/bku', 'Sekolah\LaporanController@proses_bku')->name('sekolah.proses.bku');

    Route::get('/laporan/bukubank', 'Sekolah\LaporanController@bukubank')->name('sekolah.laporan.bukubank');

    Route::post('/proses/bukubank', 'Sekolah\LaporanController@proses_bukubank')->name('sekolah.proses.bukubank');

    Route::get('/laporan/bukutunai', 'Sekolah\LaporanController@bukutunai')->name('sekolah.laporan.bukutunai');

    Route::post('/proses/bukutunai', 'Sekolah\LaporanController@proses_bukutunai')->name('sekolah.proses.bukutunai');

    Route::get('/laporan/bukupajak', 'Sekolah\LaporanController@bukupajak')->name('sekolah.laporan.bukupajak');

    Route::post('/proses/bukupajak', 'Sekolah\LaporanController@proses_bukupajak')->name('sekolah.proses.bukupajak');
    // End Laporan
});
