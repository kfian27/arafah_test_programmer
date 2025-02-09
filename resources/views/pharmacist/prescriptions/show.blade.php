@extends('layouts.app')
@section('title', 'Detail Resep')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Detail Resep</h4>
            <div>
                <a href="{{ route('pharmacy.index') }}" class="btn btn-secondary">Kembali</a>
                @if($prescription->status === 'pending')
                    <form action="{{ route('pharmacy.process', $prescription->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Proses resep ini?')">
                            <i class="fas fa-check"></i> Proses Resep
                        </button>
                    </form>
                @else
                    <a href="{{ route('pharmacy.print-receipt', $prescription->id) }}"
                       class="btn btn-success" target="_blank">
                        <i class="fas fa-print"></i> Cetak Resi
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Data Resep -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Informasi Resep</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150">Nama Pasien</td>
                                    <td>: {{ $prescription->examination->patient_name }}</td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{ $prescription->examination->patient_address }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu Pemeriksaan</td>
                                    <td>: {{ $prescription->examination->examination_time->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td>Dokter</td>
                                    <td>: {{ $prescription->examination->doctor->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150">Status</td>
                                    <td>:
                                        @if($prescription->status === 'pending')
                                            <span class="badge bg-warning">Menunggu Proses</span>
                                        @else
                                            <span class="badge bg-success">Sudah Diproses</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($prescription->status === 'processed')
                                <tr>
                                    <td>Waktu Diproses</td>
                                    <td>: {{ $prescription->processed_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td>Apoteker</td>
                                    <td>: {{ $prescription->pharmacist->name }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Obat -->
            <div class="card">
                <div class="card-header">
                    <h5>Daftar Obat</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th>Jumlah</th>
                                    <th>Aturan Pakai</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescription->medicines as $index => $medicine)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
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
                                    <td colspan="5" class="text-end"><strong>Total Pembayaran:</strong></td>
                                    <td><strong>Rp {{ number_format($prescription->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto close alert after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
    @endpush
    @endsection
