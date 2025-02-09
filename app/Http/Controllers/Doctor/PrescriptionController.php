<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Services\MedicineApiService;

class PrescriptionController extends Controller
{
    protected $medicineApi;

    public function __construct(MedicineApiService $medicineApi)
    {
        $this->medicineApi = $medicineApi;
    }

    public function index()
    {
        $prescriptions = Prescription::whereHas('examination', function($query) {
            $query->where('doctor_id', auth()->id());
        })->latest()->paginate(10);

        return view('doctor.prescriptions.index', compact('prescriptions'));
    }

    public function show(Prescription $prescription)
    {
        // $this->authorize('view', $prescription);
        $medicineDetails = [];
        foreach ($prescription->medicines as $prescriptionMedicine) {
            try {
                $medicines = $this->medicineApi->getAllMedicines();
                $medicine = collect($medicines)->firstWhere('id', $prescriptionMedicine->medicine_id);
                if ($medicine) {
                    $medicineDetails[$prescriptionMedicine->medicine_id] = $medicine;
                }
            } catch (\Exception $e) {
                // Handle API error gracefully
                logger()->error('Failed to fetch medicine details: ' . $e->getMessage());
            }
        }
        return view('doctor.prescriptions.show', compact('prescription','medicineDetails'));
    }

    public function edit(Prescription $prescription)
    {
        // $this->authorize('update', $prescription);

        if ($prescription->status !== 'pending') {
            return redirect()->route('prescriptions.show', $prescription)
                ->with('error', 'Resep yang sudah diproses tidak dapat diubah.');
        }

        $medicines = $this->medicineApi->getAllMedicines();
        return view('doctor.prescriptions.edit', compact('prescription', 'medicines'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        // $this->authorize('update', $prescription);

        if ($prescription->status !== 'pending') {
            return redirect()->route('prescriptions.show', $prescription)
                ->with('error', 'Resep yang sudah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'medicines' => 'required|array',
            'quantities' => 'required|array',
            'instructions' => 'required|array',
        ]);

        // Delete existing medicines
        $prescription->medicines()->delete();

        // Add new medicines
        foreach ($validated['medicines'] as $index => $medicineId) {
            $price = $this->medicineApi->getMedicinePrice($medicineId);
            $prescription->medicines()->create([
                'medicine_id' => $medicineId,
                'quantity' => $validated['quantities'][$index],
                'instructions' => $validated['instructions'][$index],
                'unit_price' => $price,
            ]);
        }

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'Resep berhasil diperbarui.');
    }

    public function getMedicinePrices($medicineId)
    {
        $prices = $this->medicineApi->getMedicinePrice($medicineId);
        return response()->json($prices);
    }
}
