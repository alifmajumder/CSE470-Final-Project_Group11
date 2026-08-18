<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Task;

class ImpactController extends Controller
{
    public function dashboard(): \Illuminate\View\View
    {
        $jobsCreated = Task::count();
        $incomeGenerated = Payment::sum('amount');
        $familiesSupported = Payment::distinct('worker_id')->count('worker_id');

        return view('impact.dashboard', [
            'jobsCreated' => $jobsCreated,
            'incomeGenerated' => $incomeGenerated,
            'familiesSupported' => $familiesSupported,
        ]);
    }
}