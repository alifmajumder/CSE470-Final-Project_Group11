<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class ForecastController extends Controller
{
    public function index()
    {
        $currentMonth = (int) Date::now()->format('n'); // 1 to 12
        $monthName = Date::now()->format('F');

        // Feature 15: Rule-Based Engine for Bangladeshi Agricultural Seasons
        $seasons = [
            1 => ['season' => 'Boro Planting & Winter Crops', 'demand' => 'High', 'skills' => ['Tractor Driving', 'Seedling Planting', 'Irrigation']],
            2 => ['season' => 'Boro Maintenance', 'demand' => 'Medium', 'skills' => ['Weeding', 'Pesticide Spraying', 'Irrigation']],
            3 => ['season' => 'Early Boro Harvest & Aus Planting', 'demand' => 'High', 'skills' => ['Crop Harvesting', 'Field Clearing', 'Seedling Planting']],
            4 => ['season' => 'Main Boro Harvest', 'demand' => 'Peak', 'skills' => ['Crop Harvesting', 'Carrying/Transport', 'Threshing']],
            5 => ['season' => 'Aus Maintenance & Jute Planting', 'demand' => 'Medium', 'skills' => ['Jute Sowing', 'Weeding']],
            6 => ['season' => 'Monsoon Prep & Aman Seedbeds', 'demand' => 'Medium', 'skills' => ['Drainage Clearing', 'Seedbed Prep']],
            7 => ['season' => 'Aman Planting & Aus Harvest', 'demand' => 'High', 'skills' => ['Crop Harvesting', 'Seedling Planting', 'Mudding']],
            8 => ['season' => 'Aman Maintenance (Monsoon)', 'demand' => 'Low', 'skills' => ['Weeding', 'Fertilizer Application']],
            9 => ['season' => 'Late Aman Maintenance', 'demand' => 'Low', 'skills' => ['Pesticide Spraying', 'Field Patrol']],
            10 => ['season' => 'Pre-Harvest Prep', 'demand' => 'Medium', 'skills' => ['Equipment Repair', 'Storage Cleaning']],
            11 => ['season' => 'Main Aman Harvest', 'demand' => 'Peak', 'skills' => ['Crop Harvesting', 'Carrying/Transport', 'Threshing']],
            12 => ['season' => 'Winter Crop Planting & Boro Seedbeds', 'demand' => 'High', 'skills' => ['Soil Tilling', 'Vegetable Planting', 'Seedbed Prep']],
        ];

        $currentData = $seasons[$currentMonth];
        
        // Predict the next 2 months
        $nextMonth1 = $currentMonth === 12 ? 1 : $currentMonth + 1;
        $nextMonth2 = $nextMonth1 === 12 ? 1 : $nextMonth1 + 1;
        
        $upcoming = [
            Date::now()->addMonth()->format('F') => $seasons[$nextMonth1],
            Date::now()->addMonths(2)->format('F') => $seasons[$nextMonth2],
        ];

        return view('forecast', compact('monthName', 'currentData', 'upcoming'));
    }
}