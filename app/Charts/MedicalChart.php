<?php

declare(strict_types=1);

namespace App\Charts;

use App\Diagnose;
use App\Symptom;
use App\User;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;

class MedicalChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     * @param Request $request
     * @return Chartisan
     */
    public function handler(Request $request): Chartisan
    {
        return Chartisan::build()
            ->labels(['Patient(s)', 'Symptom(s)', 'Diagnosis'])
            ->dataset('Sample', [count(User::query()->get()), count(Symptom::query()->where('is_processed', true)->get()), count(Diagnose::query()->where('is_valid', true)->get())]);
    }
}
