@extends('layouts.app')
@section('title', 'Pemeriksaan Baru')

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
        <div class="card-header">
            <h4>Pemeriksaan Pasien Baru</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('examinations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Data Pasien -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Data Pasien</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pasien</label>
                                <input type="text" name="patient_name" class="form-control" required
                                    value="{{ old('patient_name') }}">
                                @error('patient_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat Pasien</label>
                                <textarea name="patient_address" class="form-control" required>{{ old('patient_address') }}</textarea>
                                @error('patient_address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Waktu Pemeriksaan</label>
                        <input type="datetime-local" name="examination_time" class="form-control" required
                            value="{{ old('examination_time', now()->format('Y-m-d\TH:i')) }}">

                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" name="height" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" name="weight" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Suhu Tubuh (°C)</label>
                        <input type="number" step="0.1" name="temperature" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Systole</label>
                        <input type="number" name="systole" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Diastole</label>
                        <input type="number" name="diastole" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Heart Rate</label>
                        <input type="number" name="heart_rate" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Respiration Rate</label>
                        <input type="number" name="respiration_rate" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hasil Pemeriksaan</label>
                    <textarea name="examination_result" class="form-control" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Berkas Pemeriksaan</label>
                    <input type="file" name="examination_file" class="form-control">
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Resep Obat</h5>
                    </div>
                    <div class="card-body">
                        <div id="prescription-items">
                            <div class="prescription-item mb-3">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label class="form-label">Obat</label>
                                        <select name="medicines[]" class="form-control medicine-select" required>
                                            <option value="">Pilih Obat</option>
                                            @foreach ($medicines as $medicine)
                                                <option value="{{ $medicine['id'] }}">{{ $medicine['name'] }}</option>
                                            @endforeach
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
                                    <div class="col-md-1 d-flex align-items-end  button-remove-medicine"
                                        style="display: none !important;">
                                        <button type="button" class="btn btn-danger remove-medicine-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" id="add-medicine-btn">
                            + Tambah Obat
                        </button>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan Pemeriksaan</button>
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

                    // if (parent) {
                    //     $(parent).find('.medicine-select').select2(select2Options);
                    // } else {
                    $('.medicine-select').select2(select2Options);
                    // }
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
                    $('.button-remove-medicine').hide();
                    if ($('.prescription-item').length > 1) {
                        $('.button-remove-medicine').show();
                    }

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
                    } else {
                        alert('Minimal harus ada satu obat dalam resep');
                    }
                }
            });
        </script>
    @endpush
@endsection
