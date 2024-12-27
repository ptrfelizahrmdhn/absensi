
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Data Karyawan Non Organik</div>

                <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                    </div>
                @endif

                    <div class="d-flex left-content-start">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKaryawanModal">
                            Tambah Karyawan
                        </button>
                    </div>
                    <br/>


                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No ID</th>
                                <th>Nama Karyawan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($karyawans as $karyawan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $karyawan->no_id }}</td>
                                    <td>{{ $karyawan->nama }}</td>
                                    <td>
                                        <a href="{{ route('admin.karyawan.show', $karyawan->id) }}" class="btn btn-danger">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">Tidak ada data karyawan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="modal fade" id="createKaryawanModal" tabindex="-1" aria-labelledby="createKaryawanModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-slide-right" role="document">
                            {{-- <div class="modal-content"> --}}
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createKaryawanModalLabel">Tambah Karyawan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.karyawan.store') }}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">User</label>
                                            <select class="form-select" id="user_id" name="user_id" required>
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="no_id" class="form-label">No ID</label>
                                            <input type="number" class="form-control" id="no_id" name="no_id" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama</label>
                                            <input type="text" class="form-control" id="nama" name="nama" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="agama" class="form-label">Agama</label>
                                            <input type="text" class="form-control" id="agama" name="agama" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="telepon" class="form-label">Telepon</label>
                                            <input type="number" class="form-control" id="telepon" name="telepon" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                                            <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="foto" class="form-label">Foto Selfie</label>
                                            <input type="file" class="form-control" name="file" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>






                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('styles')
    <style>
        /* Custom CSS */
        .modal-dialog-slide-right {
            width: 400px; /* Adjust the width as needed */
            transform: translateX(100%);
            transition: transform 0.3s ease-out;
        }

        .modal.fade.show .modal-dialog-slide-right {
            transform: translateX(0);
        }
    </style>
@endsection
