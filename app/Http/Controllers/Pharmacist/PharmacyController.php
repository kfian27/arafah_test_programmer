<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\MedicineApiService;
use Illuminate\Http\Request;
use PDF;

class PharmacyController extends Controller
{
    protected $medicineApi;

    public function __construct(MedicineApiService $medicineApi)
    {
        $this->medicineApi = $medicineApi;
    }

    public function index()
    {
        $prescriptions = Prescription::with(['examination'])
            ->whereHas('examination')
            ->latest()
            ->paginate(10);

        return view('pharmacist.prescriptions.index', compact('prescriptions'));
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['examination', 'medicines']);

        // Get medicine details from API
        $medicineDetails = [];
        foreach ($prescription->medicines as $prescriptionMedicine) {
            try {
                $medicines = $this->medicineApi->getAllMedicines();
                $medicine = collect($medicines)->firstWhere('id', $prescriptionMedicine->medicine_id);
                if ($medicine) {
                    $medicineDetails[$prescriptionMedicine->medicine_id] = $medicine;
                }
            } catch (\Exception $e) {
                logger()->error('Failed to fetch medicine details: ' . $e->getMessage());
            }
        }

        return view('pharmacist.prescriptions.show', compact('prescription', 'medicineDetails'));
    }

    public function process(Prescription $prescription)
    {
        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Resep ini sudah diproses sebelumnya.');
        }

        $prescription->update([
            'status' => 'processed',
            'processed_at' => now(),
            'pharmacist_id' => auth()->id()
        ]);

        return redirect()->route('pharmacy.show', $prescription)
            ->with('success', 'Resep berhasil diproses.');
    }

    public function printReceipt(Prescription $prescription)
    {
        if ($prescription->status !== 'processed') {
            return back()->with('error', 'Hanya resep yang sudah diproses yang dapat dicetak.');
        }

        $prescription->load(['examination', 'medicines', 'pharmacist']);

        // Get medicine details from API
        $medicineDetails = [];
        foreach ($prescription->medicines as $prescriptionMedicine) {
            try {
                $medicines = $this->medicineApi->getAllMedicines();
                $medicine = collect($medicines)->firstWhere('id', $prescriptionMedicine->medicine_id);
                if ($medicine) {
                    $medicineDetails[$prescriptionMedicine->medicine_id] = $medicine;
                }
            } catch (\Exception $e) {
                logger()->error('Failed to fetch medicine details: ' . $e->getMessage());
            }
        }

        // Format nama file: RECEIPT_[NAMA-PASIEN]_[TANGGAL].pdf
        $fileName = 'RECEIPT_' .
                    str_replace(' ', '-', $prescription->examination->patient_name) . '_' .
                    now()->format('Y-m-d') . '.pdf';

        $pdf = PDF::loadView('pharmacist.prescriptions.receipt', [
            'prescription' => $prescription,
            'medicineDetails' => $medicineDetails
        ]);

        return $pdf->download($fileName);
    }
}
