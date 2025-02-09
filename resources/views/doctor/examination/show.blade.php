@extends('layouts.app')
@section('title', 'Detail Pemeriksaan')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Detail Pemeriksaan</h4>
        <a href="{{ route('examinations.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <!-- Data Pasien -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Data Pasien</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150">Nama Pasien</td>
                                <td>: {{ $examination->patient_name }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: {{ $examination->patient_address }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Pemeriksaan</td>
                                <td>: {{ $examination->examination_time->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <h5>Tanda Vital</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>Tekanan Darah</h6>
                                <p class="mb-0">{{ $examination->systole }}/{{ $examination->diastole }} mmHg</p>
                                <small class="text-{{ $vitalSigns['blood_pressure']['status'] == 'normal' ? 'success' : 'danger' }}">
                                    {{ $vitalSigns['blood_pressure']['message'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>Detak Jantung</h6>
                                <p class="mb-0">{{ $examination->heart_rate }} bpm</p>
                                <small class="text-{{ $vitalSigns['heart_rate']['status'] == 'normal' ? 'success' : 'danger' }}">
                                    {{ $vitalSigns['heart_rate']['message'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>Suhu Tubuh</h6>
                                <p class="mb-0">{{ $examination->temperature }}°C</p>
                                <small class="text-{{ $vitalSigns['temperature']['status'] == 'normal' ? 'success' : 'danger' }}">
                                    {{ $vitalSigns['temperature']['message'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>BMI</h6>
                                <p class="mb-0">{{ $vitalSigns['bmi']['value'] }}</p>
                                <small class="text-{{ $vitalSigns['bmi']['status'] == 'normal' ? 'success' : 'danger' }}">
                                    {{ $vitalSigns['bmi']['message'] }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Hasil Pemeriksaan</h5>
            </div>
            <div class="card-body">
                <p>{{ $examination->examination_result }}</p>
                @if($examination->examination_file)
                <div class="mt-3">
                    <strong>Berkas Pemeriksaan:</strong>
                    <a href="{{ Storage::url($examination->examination_file) }}" class="btn btn-sm btn-info" target="_blank">
                        Lihat Berkas
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Resep Obat</h5>
                @if($examination->prescription && $examination->prescription->isPending())
                <a href="{{ route('prescriptions.edit', $examination->prescription->id) }}" class="btn btn-primary btn-sm">
                    Edit Resep
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($examination->prescription)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Jumlah</th>
                            <th>Aturan Pakai</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($examination->prescription->medicines as $medicine)
                        <tr>
                            <td>{{ $medicineDetails[$medicine->medicine_id]['name'] ?? 'Unknown Medicine' }}</td>
                            <td>{{ $medicine->quantity }}</td>
                            <td>{{ $medicine->instructions }}</td>
                            <td>Rp {{ number_format($medicine->unit_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($medicine->unit_price * $medicine->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>Rp {{ number_format($examination->prescription->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="mt-3">
                    <strong>Status Resep:</strong>
                    @if($examination->prescription->status === 'pending')
                        <span class="badge bg-warning">Menunggu Farmasi</span>
                    @else
                        <span class="badge bg-success">Sudah Diproses</span>
                    @endif
                </div>
                @else
                <p>Belum ada resep untuk pemeriksaan ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
