<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\MedicineApiService;

class ExaminationController extends Controller
{
    protected $medicineApi;

    public function __construct(MedicineApiService $medicineApi)
    {
        $this->medicineApi = $medicineApi;
    }

    public function index()
    {
        $examinations = Examination::with('prescription')
            ->where('doctor_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('doctor.examination.index', compact('examinations'));
    }

    public function create()
    {
        $medicines = $this->medicineApi->getAllMedicines();
        $examination_time = now(); // Set default time to now
        return view('doctor.examination.create', compact('medicines', 'examination_time'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_address' => 'required|string',
            'examination_time' => 'required|date|before_or_equal:now',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'temperature' => 'required|numeric',
            'systole' => 'required|numeric',
            'diastole' => 'required|numeric',
            'heart_rate' => 'required|numeric',
            'respiration_rate' => 'required|numeric',
            'examination_result' => 'required|string',
            'examination_file' => 'nullable|file|max:2048',
            'medicines' => 'required|array',
            'quantities' => 'required|array',
            'instructions' => 'required|array',
        ]);

        if ($request->hasFile('examination_file')) {
            $filePath = $request->file('examination_file')->store('examinations');
            $validated['examination_file'] = $filePath;
        }

        $validated['examination_time'] = $request->examination_time ?? now();

        // Create examination record
        $examination = Examination::create([
            'doctor_id' => auth()->id(),
            'patient_name' => $validated['patient_name'],
            'patient_address' => $validated['patient_address'],
            'examination_time' => $validated['examination_time'],
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'temperature' => $validated['temperature'],
            'systole' => $validated['systole'],
            'diastole' => $validated['diastole'],
            'heart_rate' => $validated['heart_rate'],
            'respiration_rate' => $validated['respiration_rate'],
            'examination_result' => $validated['examination_result'],
            'examination_file' => $validated['examination_file'] ?? null,
        ]);

        // Create prescription
        $prescription = $examination->prescription()->create([
            'status' => 'pending'
        ]);

        // Add medicines to prescription
        $totalAmount = 0; // Inisialisasi total amount
        foreach ($validated['medicines'] as $index => $medicineId) {
            $price = $this->medicineApi->getMedicinePrice($medicineId);
            $quantity = $validated['quantities'][$index];
            $subtotal = $price * $quantity; // Hitung subtotal per obat
            $totalAmount += $subtotal; // Tambahkan ke total

            $prescription->medicines()->create([
                'medicine_id' => $medicineId,
                'quantity' => $quantity,
                'instructions' => $validated['instructions'][$index],
                'unit_price' => $price,
            ]);
        }

        $prescription->update([
            'total_amount' => $totalAmount
        ]);

        return redirect()->route('examinations.index')
            ->with('success', 'Pemeriksaan berhasil disimpan.');
    }

    public function show(Examination $examination)
    {
        // Authorize that this examination belongs to the logged-in doctor
        if ($examination->doctor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Load the related prescription and medicines data
        $examination->load(['prescription.medicines']);

        // If prescription exists, get the medicine details from API
        if ($examination->prescription) {
            $medicineDetails = [];
            foreach ($examination->prescription->medicines as $prescriptionMedicine) {
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
        }

        // Get vital signs status
        $vitalSigns = [
            'blood_pressure' => $this->evaluateBloodPressure($examination->systole, $examination->diastole),
            'heart_rate' => $this->evaluateHeartRate($examination->heart_rate),
            'temperature' => $this->evaluateTemperature($examination->temperature),
            'respiration_rate' => $this->evaluateRespirationRate($examination->respiration_rate),
            'bmi' => $this->calculateBMI($examination->height, $examination->weight)
        ];

        return view('doctor.examination.show', compact('examination', 'medicineDetails', 'vitalSigns'));
    }

    protected function evaluateBloodPressure($systole, $diastole)
    {
        if ($systole < 90 || $diastole < 60) {
            return ['status' => 'low', 'message' => 'Tekanan darah rendah'];
        } elseif ($systole >= 140 || $diastole >= 90) {
            return ['status' => 'high', 'message' => 'Tekanan darah tinggi'];
        }
        return ['status' => 'normal', 'message' => 'Tekanan darah normal'];
    }

    protected function evaluateHeartRate($rate)
    {
        if ($rate < 60) {
            return ['status' => 'low', 'message' => 'Detak jantung rendah'];
        } elseif ($rate > 100) {
            return ['status' => 'high', 'message' => 'Detak jantung tinggi'];
        }
        return ['status' => 'normal', 'message' => 'Detak jantung normal'];
    }

    protected function evaluateTemperature($temp)
    {
        if ($temp < 36.5) {
            return ['status' => 'low', 'message' => 'Suhu tubuh rendah'];
        } elseif ($temp > 37.5) {
            return ['status' => 'high', 'message' => 'Suhu tubuh tinggi'];
        }
        return ['status' => 'normal', 'message' => 'Suhu tubuh normal'];
    }

    protected function evaluateRespirationRate($rate)
    {
        if ($rate < 12) {
            return ['status' => 'low', 'message' => 'Laju pernapasan rendah'];
        } elseif ($rate > 20) {
            return ['status' => 'high', 'message' => 'Laju pernapasan tinggi'];
        }
        return ['status' => 'normal', 'message' => 'Laju pernapasan normal'];
    }

    protected function calculateBMI($height, $weight)
    {
        $heightInMeters = $height / 100;
        $bmi = $weight / ($heightInMeters * $heightInMeters);
        $bmi = round($bmi, 1);

        if ($bmi < 18.5) {
            return ['value' => $bmi, 'status' => 'low', 'message' => 'Berat badan kurang'];
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return ['value' => $bmi, 'status' => 'normal', 'message' => 'Berat badan normal'];
        } elseif ($bmi >= 25 && $bmi < 30) {
            return ['value' => $bmi, 'status' => 'high', 'message' => 'Berat badan berlebih'];
        } else {
            return ['value' => $bmi, 'status' => 'high', 'message' => 'Obesitas'];
        }
    }
}
