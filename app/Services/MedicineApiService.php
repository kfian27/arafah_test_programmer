<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MedicineApiService
{
    protected $baseUrl = 'http://recruitment.rsdeltasurya.com/api/v1';
    protected $token;

    public function __construct()
    {
        $this->authenticate();
    }

    protected function authenticate()
    {
        $response = Http::post($this->baseUrl . '/auth', [
            'email' => config('services.medicine_api.email'),
            'password' => config('services.medicine_api.password')
        ]);

        if ($response->successful()) {
            $this->token = $response->json('access_token');
        } else {
            throw new \Exception('Failed to authenticate with medicine API');
        }
    }

    public function getAllMedicines()
    {
        return Cache::remember('medicines', 3600, function () {
            $response = Http::withToken($this->token)
                ->get($this->baseUrl . '/medicines');

            if ($response->successful()) {
                return $response->json('medicines');
            }

            throw new \Exception('Failed to fetch medicines from API');
        });
    }

    public function getMedicinePrice($medicineId)
    {
        $cacheKey = "medicine_price_{$medicineId}";

        return Cache::remember($cacheKey, 300, function () use ($medicineId) {
            $response = Http::withToken($this->token)
                ->get($this->baseUrl . "/medicines/{$medicineId}/prices");

            if ($response->successful()) {
                $prices = $response->json('prices');
                $currentDate = now()->format('Y-m-d');

                // Find the valid price for current date
                foreach ($prices as $price) {
                    if ($currentDate >= $price['start_date']['value'] &&
                        $currentDate <= $price['end_date']['value']) {
                        return $price['unit_price'];
                    }
                }
            }

            throw new \Exception('Failed to fetch medicine price from API');
        });
    }
}
