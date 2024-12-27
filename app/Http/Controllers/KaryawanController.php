<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KaryawanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $karyawans = Karyawan::all();
        $users = User::where('role', '!=', 'admin')
                        ->whereNotIn('id', function($query) {
                            $query->select('user_id')->from('karyawans');
                        })->get();
        return view('admin.karyawan.index', compact('karyawans', 'users'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.karyawan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required',
            'user_id' => 'required',
            'no_id' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'telepon' => 'required',
            'gaji_pokok' => 'required|numeric',
            'file' => 'required'
        ]);

        $file = $request->file('file');

        unset($validatedData['file']);
        $newEmploye = Karyawan::create($validatedData);

        if($newEmploye) {
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->withHeaders([
                'Accept' => 'application/json',
            ])->post('http://127.0.0.1:5000/upload', [
                'filename' => $validatedData['nama'],
            ]);
        }
        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan telah ditambahkan.');
    }

    public function show($id)

    {
        $karyawan = Karyawan::findOrFail($id);
        $users = User::all();
        return view('admin.karyawan.show', compact('karyawan', 'users'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $users = User::all();
        return view('admin.karyawan.edit', compact('karyawan', 'users'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'no_id' => 'required',
            'telepon' => 'required',
            'gaji_pokok' => 'required|numeric',
            'user_id' => 'required',
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update($validatedData);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan telah diperbarui');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->presensi()->delete();
        $karyawan->user()->delete();
        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan telah dihapus');
    }


    public function profile()

    {
        // Mendapatkan objek User yang sedang login
    $user = auth()->user();

    // Mendapatkan profil karyawan terkait dengan user yang login
    $karyawan = $user->karyawan;

    return view('karyawan.profile', compact('karyawan', 'user'));
    }



}

