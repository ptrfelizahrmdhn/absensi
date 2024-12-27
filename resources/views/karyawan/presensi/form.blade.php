@extends('layouts.karyawanapp')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">Form Presensi Karyawan</div>
                    <div class="card-body">
                        <div class="container mt-4">
                            @if (session('success'))
                            <div class="alert alert-success">
                                  {{ session('success') }}
                             </div>
                         @elseif (session('error'))
                             <div class="alert alert-danger">
                              {{ session('error') }}
                             </div>
                         @endif
                            @if (!empty($konfigurasi) && $konfigurasi->status_presensi === 'buka')
                            <form id="presensiForm" action="{{ route('karyawan.presensi.store') }}" method="post">
                                @csrf
                                <p class="alert alert-info text-dark">Absen masuk telah dibuka. Silahkan absen dengan mengambil lokasi dahulu lalu klik Absen. Jika sudah, abaikan teks ini.</p><br/>
                                <!-- Hidden Latitude and Longitude -->
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <div class="w-25">
                                    <img style="width: 350px;" src="http://127.0.0.1:5000/video_feed" alt="Live Video" class="rounded-lg">
                                </div>
                                @if (!$presensiHariIni->isNotEmpty() || ($presensiHariIni->isNotEmpty() && $presensiHariIni->first()->jam_pulang))
                                    <label id="loc" for="lokasi">Lokasi Anda: </label><br/><br/>
                                    <button class="btn btn-primary" type="submit">Absen Masuk</button>
                                @else
                                    <p class="btn btn-danger">Anda sudah melakukan absen masuk hari ini.</p>
                                @endif
                            </form>


                           @else
                               <p class="btn btn-danger">Absen masuk ditutup oleh admin.</p>

                           @endif
                           <br/>

                           @if (!empty($konfigurasi) && $konfigurasi->status_presensi === 'buka_pulang')
                           <form id="presensiForm" action="{{ route('karyawan.presensi.update') }}" method="post">
                            <p class="alert alert-info text-dark">Absen pulang telah dibuka. Silahkan absen dengan mengambil lokasi dahulu lalu klik Absen. Jika sudah, abaikan teks ini..</p><br/>
                            @csrf
                            @method('PUT')
                            <label for="lokasi">Lokasi :</label><br/>
                            <select name="lokasi" id="lokasiSelect" required></select><br/><br/>
                            <button class="btn btn-secondary text-white" type="button" onclick="getLocation()">Ambil Lokasi</button>
                            <button class="btn btn-primary" type="submit">Absen Pulang</button>
                        </form>

                        @else
                           <p class="btn btn-danger">Absen pulang ditutup oleh admin.</p>
                        @endif

                          <br/>


                           <br/>

                           <table class="table">
                            <thead>
                                <tr>
                                    <th>No Badge</th>
                                    <th>Nama</th>
                                    <th>Periode / Tanggal</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>

                                <tbody>
                                    @if ($presensiHariIni->isNotEmpty())
                                        @php $presensi = $presensiHariIni->first(); @endphp
                                        <tr>
                                            <td>{{ $presensi->karyawan ? $presensi->karyawan->no_badge : 'Data tidak ditemukan' }}</td>
                                            <td>{{ $presensi->karyawan ? $presensi->karyawan->nama : 'Data tidak ditemukan' }}</td>

                                            <td>{{ $presensi->tanggal }}</td>

                                            <td>
                                                <a href="/karyawan/presensi/riwayat" class="btn btn-primary">Riwayat</a>

                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6">Data tidak ditemukan</td>
                                        </tr>
                                    @endif
                                </tbody>



                        </table>



                           </div>




                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            var label = document.getElementById('loc');
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    label.innerHTML = 'Lokasi Anda: ' + position.coords.latitude + ',' + position.coords.longitude;
                }, () => {
                    document.getElementById('location-alert').classList.remove('hidden');
                });
            } else {
                alert('Geolocation tidak didukung di browser Anda.');
            }
        });

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            var locationString = latitude + ', ' + longitude;

            // Mendapatkan elemen select
            var lokasiSelect = document.getElementById('lokasiSelect');

            // Menambahkan opsi baru ke dalam elemen select
            var option = document.createElement('option');
            option.value = locationString;
            option.text = locationString;
            lokasiSelect.add(option);
        }
    </script>









@endsection
