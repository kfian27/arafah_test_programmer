<?php

namespace App\Services;

use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    protected $medicineService;

    public function __construct(MedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    public function createPrescriptionItems(Prescription $prescription, array $items)
    {
        DB::beginTransaction();
        try {
            // Clear existing items
            $prescription->items()->delete();
            $totalAmount = 0;

            foreach ($items as $item) {
                $priceData = $this->medicineService->getMedicinePrice(
                    $item['medicine_id'],
                    $prescription->examination->examination_time
                );

                $medicineData = $this->medicineService->getMedicineDetails($item['medicine_id']);

                $totalPrice = $priceData['current_price'] * $item['quantity'];
                $totalAmount += $totalPrice;

                $prescription->items()->create([
                    'medicine_id' => $item['medicine_id'],
                    'medicine_name' => $medicineData['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $priceData['current_price'],
                    'total_price' => $totalPrice,
                    'notes' => $item['notes'] ?? null
                ]);
            }

            $prescription->update(['total_amount' => $totalAmount]);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function validatePrescriptionPrices(Prescription $prescription)
    {
        foreach ($prescription->items as $item) {
            $isValid = $this->medicineService->validatePriceForDate(
                $item->medicine_id,
                $item->unit_price,
                $prescription->examination->examination_time
            );

            if (!$isValid) {
                return false;
            }
        }

        return true;
    }
}
