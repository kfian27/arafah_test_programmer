<?php

namespace App\Services;

use App\Models\Examination;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExaminationService
{
    public function storeAttachment(Examination $examination, UploadedFile $file)
    {
        $path = $file->store('examinations');

        if ($examination->attachment_path) {
            Storage::delete($examination->attachment_path);
        }

        $examination->update(['attachment_path' => $path]);
        return $path;
    }

    public function calculateBMI(Examination $examination)
    {
        $heightInMeters = $examination->height / 100;
        return round($examination->weight / ($heightInMeters * $heightInMeters), 2);
    }

    public function getVitalSignsStatus(Examination $examination)
    {
        return [
            'blood_pressure' => $this->evaluateBloodPressure($examination->systole, $examination->diastole),
            'heart_rate' => $this->evaluateHeartRate($examination->heart_rate),
            'temperature' => $this->evaluateTemperature($examination->temperature),
            'respiration_rate' => $this->evaluateRespirationRate($examination->respiration_rate)
        ];
    }

    protected function evaluateBloodPressure($systole, $diastole)
    {
        if ($systole < 90 || $diastole < 60) {
            return 'low';
        } elseif ($systole > 140 || $diastole > 90) {
            return 'high';
        }
        return 'normal';
    }

    protected function evaluateHeartRate($rate)
    {
        if ($rate < 60) return 'low';
        if ($rate > 100) return 'high';
        return 'normal';
    }

    protected function evaluateTemperature($temp)
    {
        if ($temp < 36.5) return 'low';
        if ($temp > 37.5) return 'high';
        return 'normal';
    }

    protected function evaluateRespirationRate($rate)
    {
        if ($rate < 12) return 'low';
        if ($rate > 20) return 'high';
        return 'normal';
    }
}
