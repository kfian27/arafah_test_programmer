@extends('layouts.app')
@section('title', 'Daftar Pemeriksaan')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Daftar Pemeriksaan</h4>
            <a href="{{ route('examinations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Pemeriksaan Baru
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
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
                            <th>Waktu Pemeriksaan</th>
                            <th>Pasien</th>
                            <th>Status Resep</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examinations as $index => $examination)
                            <tr>
                                <td>{{ $examinations->firstItem() + $index }}</td>
                                <td>{{ $examination->examination_time->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $examination->patient_name }}
                                </td>
                                <td>
                                    @if (!$examination->prescription)
                                        <span class="badge bg-secondary">Belum Ada Resep</span>
                                    @elseif($examination->prescription->status === 'pending')
                                        <span class="badge bg-warning">Menunggu Farmasi</span>
                                    @else
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('examinations.show', $examination->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>

                                        @if ($examination->prescription && $examination->prescription->status === 'pending')
                                            <a href="{{ route('prescriptions.edit', $examination->prescription->id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Edit Resep
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">Belum ada data pemeriksaan</div>
                                    <a href="{{ route('examinations.create') }}" class="btn btn-primary mt-2">
                                        Mulai Pemeriksaan Baru
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $examinations->links() }}
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
