<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resi Pembayaran #{{ $prescription->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 150px auto;
            gap: 10px;
            margin-bottom: 5px;
        }

        .label {
            font-weight: bold;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 200px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RS Delta Surya</h1>
        <p>Jl. Veteran No. 123, Sidoarjo</p>
        <p>Telp: (031) 8921234</p>
        <h2>RESI PEMBAYARAN RESEP</h2>
        <p>No. Resep: {{ str_pad($prescription->id, 8, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="info-section">
        <h3>Data Pasien</h3>
        <div class="info-grid">
            <span class="label">Nama Pasien</span>
            <span>: {{ $prescription->examination->patient_name }}</span>
        </div>
        <div class="info-grid">
            <span class="label">Alamat</span>
            <span>: {{ $prescription->examination->patient_address }}</span>
        </div>
        <div class="info-grid">
            <span class="label">Tanggal Resep</span>
            <span>: {{ $prescription->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-grid">
            <span class="label">Dokter</span>
            <span>: {{ $prescription->examination->doctor->name }}</span>
        </div>
    </div>

    <div class="info-section">
        <h3>Daftar Obat</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
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
                    <td align="right">Rp {{ number_format($medicine->unit_price, 0, ',', '.') }}</td>
                    <td align="right">Rp {{ number_format($medicine->unit_price * $medicine->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" align="right">Total Pembayaran</td>
                    <td align="right">Rp {{ number_format($prescription->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="info-section">
        <h3>Catatan Penggunaan Obat</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Aturan Pakai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->medicines as $medicine)
                <tr>
                    <td>{{ $medicineDetails[$medicine->medicine_id]['name'] ?? 'Unknown Medicine' }}</td>
                    <td>{{ $medicine->instructions }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="signature">
        <p>Sidoarjo, {{ $prescription->processed_at->format('d/m/Y') }}</p>
        <p>Apoteker</p>
        <div class="signature-line"></div>
        <p>{{ $prescription->pharmacist->name }}</p>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
