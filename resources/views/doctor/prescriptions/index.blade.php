@extends('layouts.app')
@section('title', 'Daftar Resep')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Daftar Resep</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Pasien</th>
                            <th>Jumlah Obat</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prescriptions as $index => $prescription)
                            <tr>
                                <td>{{ $prescriptions->firstItem() + $index }}</td>
                                <td>{{ $prescription->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $prescription->examination->patient_name }}</td>
                                <td>{{ $prescription->medicines->count() }} item</td>
                                <td>Rp {{ number_format($prescription->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($prescription->status === 'pending')
                                        <span class="badge bg-warning">Menunggu Farmasi</span>
                                    @elseif($prescription->status === 'processed')
                                        <span class="badge bg-success">Sudah Diproses</span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $prescription->processed_at->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('prescriptions.show', $prescription->id) }}"
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>

                                        @if($prescription->status === 'pending')
                                            <a href="{{ route('prescriptions.edit', $prescription->id) }}"
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">Belum ada data resep</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $prescriptions->links() }}
            </div>
        </div>
    </div>

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .btn-group .btn {
        margin-right: 5px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush

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