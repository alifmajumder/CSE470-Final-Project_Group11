<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchemeController extends Controller
{
    public function index()
    {
        return view('schemes');
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'age' => 'required|integer|min:18|max:120',
            'gender' => 'required|in:male,female,other',
            'income' => 'required|numeric|min:0',
            'land_size' => 'required|numeric|min:0',
        ]);

        $age = $validated['age'];
        $gender = $validated['gender'];
        $income = $validated['income'];
        $landSize = $validated['land_size']; // In acres

        $eligibleSchemes = [];

        // Rule 1: Vulnerable Women Benefit (VWB / formerly VGD)
        if ($gender === 'female' && $age >= 20 && $age <= 50 && $income <= 3000 && $landSize < 0.15) {
            $eligibleSchemes[] = [
                'name' => 'Vulnerable Women Benefit (VWB)',
                'description' => 'Provides 30kg of rice per month and training for destitute rural women.',
                'agency' => 'Ministry of Women and Children Affairs'
            ];
        }

        // Rule 2: Agricultural Input Assistance Card (Krishi Card)
        if ($landSize >= 0.05 && $landSize <= 2.49) {
            $eligibleSchemes[] = [
                'name' => 'Agricultural Input Assistance (Krishi Card)',
                'description' => 'Cash subsidies for diesel, fertilizer, and electricity for marginal and small farmers.',
                'agency' => 'Department of Agricultural Extension'
            ];
        }

        // Rule 3: Employment Generation Program for the Poorest (EGPP)
        if ($income <= 4000 && $landSize < 0.5) {
            $eligibleSchemes[] = [
                'name' => 'Employment Generation Program for the Poorest (EGPP)',
                'description' => 'Provides up to 80 days of guaranteed wage employment during lean agricultural seasons.',
                'agency' => 'Ministry of Disaster Management and Relief'
            ];
        }

        // Rule 4: Old Age Allowance (Boyosko Bhata)
        if (($gender === 'male' && $age >= 65) || ($gender === 'female' && $age >= 62)) {
            if ($income <= 10000) {
                $eligibleSchemes[] = [
                    'name' => 'Old Age Allowance (Boyosko Bhata)',
                    'description' => 'A monthly cash allowance to support the livelihood of elderly citizens.',
                    'agency' => 'Department of Social Services'
                ];
            }
        }

        $checked = true;

        return view('schemes', compact('eligibleSchemes', 'checked'));
    }
}