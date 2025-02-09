@extends('layouts.app')
@section('title', 'Edit Resep')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Edit Resep</h4>
            <a href="{{ route('prescriptions.show', $prescription->id) }}" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <form action="{{ route('prescriptions.update', $prescription->id) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Obat -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Daftar Obat</h5>
                    </div>
                    <div class="card-body">
                        <div id="prescription-items">
                            @foreach ($prescription->medicines as $index => $medicine)
                                <div class="prescription-item mb-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Obat</label>
                                            <select name="medicines[]" class="form-control medicine-select" required>
                                                <option value="">Pilih Obat</option>
                                                @foreach ($medicines as $med)
                                                    <option value="{{ $med['id'] }}"
                                                        {{ $med['id'] == $medicine->medicine_id ? 'selected' : '' }}>
                                                        {{ $med['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Jumlah</label>
                                            <input type="number" name="quantities[]" class="form-control" required
                                                value="{{ $medicine->quantity }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Aturan Pakai</label>
                                            <input type="text" name="instructions[]" class="form-control" required
                                                value="{{ $medicine->instructions }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end button-remove-medicine">
                                            <button type="button" class="btn btn-danger remove-medicine-btn">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-secondary" id="add-medicine-btn">
                            + Tambah Obat
                        </button>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function() {
                const medicines = @json($medicines);

                // Initialize Select2 for existing selects
                initializeSelect2();

                // Add medicine row handler
                $('#add-medicine-btn').on('click', function() {
                    addMedicineRow();
                });

                // Remove medicine row handler using event delegation
                $('#prescription-items').on('click', '.remove-medicine-btn', function() {
                    removeMedicineRow($(this));
                });

                function initializeSelect2(parent = null) {
                    const select2Options = {
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Pilih Obat',
                        allowClear: true
                    };

                    if (parent) {
                        $(parent).find('.medicine-select').select2(select2Options);
                    } else {
                        $('.medicine-select').select2(select2Options);
                    }
                }

                function addMedicineRow() {
                    const $container = $('#prescription-items');

                    // Create HTML manually
                    const newRow = `
            <div class="prescription-item mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Obat</label>
                        <select name="medicines[]" class="form-control medicine-select" required>
                            <option value="">Pilih Obat</option>
                            ${medicines.map(medicine => `
                                        <option value="${medicine.id}">${medicine.name}</option>
                                    `).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="quantities[]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Aturan Pakai</label>
                        <input type="text" name="instructions[]" class="form-control" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end button-remove-medicine">
                        <button type="button" class="btn btn-danger remove-medicine-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

                    $container.append(newRow);

                    // Show/hide remove buttons
                    toggleRemoveButtons();

                    // Initialize Select2 for new row
                    initializeSelect2();
                }

                function removeMedicineRow($button) {
                    const $items = $('.prescription-item');
                    if ($items.length > 1) {
                        const $row = $button.closest('.prescription-item');
                        // Destroy Select2 before removing the row
                        $row.find('.medicine-select').select2('destroy');
                        $row.remove();
                        // Update remove buttons visibility
                        toggleRemoveButtons();
                    } else {
                        alert('Minimal harus ada satu obat dalam resep');
                    }
                }

                function toggleRemoveButtons() {
                    const $items = $('.prescription-item');
                    if ($items.length > 1) {
                        $('.button-remove-medicine').show();
                    } else {
                        $('.button-remove-medicine').hide();
                    }
                }

                // Initial toggle of remove buttons
                toggleRemoveButtons();
            });
        </script>
    @endpush
@endsection
