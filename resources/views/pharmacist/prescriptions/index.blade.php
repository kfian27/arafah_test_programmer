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
                            <th>Dokter</th>
                            <th>Pasien</th>
                            <th>Status</th>
                            <th>Total Biaya</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prescriptions as $index => $prescription)
                            <tr>
                                <td>{{ $prescriptions->firstItem() + $index }}</td>
                                <td>{{ $prescription->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $prescription->examination->doctor->name }}</td>
                                <td>{{ $prescription->examination->patient_name }}</td>
                                <td>
                                    @if($prescription->status === 'pending')
                                        <span class="badge bg-warning">Menunggu Proses</span>
                                    @else
                                        <span class="badge bg-success">Sudah Diproses</span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $prescription->processed_at->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($prescription->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pharmacy.show', $prescription->id) }}"
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>

                                        @if($prescription->status === 'pending')
                                            <form action="{{ route('pharmacy.process', $prescription->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm"
                                                        onclick="return confirm('Proses resep ini?')">
                                                    <i class="fas fa-check"></i> Proses
                                                </button>
                                            </form>
                                        @endif

                                        @if($prescription->status === 'processed')
                                            <a href="{{ route('pharmacy.print-receipt', $prescription->id) }}"
                                               class="btn btn-success btn-sm" target="_blank">
                                                <i class="fas fa-print"></i> Cetak
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">Belum ada resep yang perlu diproses</div>
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
