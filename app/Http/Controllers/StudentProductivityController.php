<?php

namespace App\Http\Controllers;

use App\Services\StudentProductivityService;
use Illuminate\Http\Request;

class StudentProductivityController extends Controller
{
    public function __construct(protected StudentProductivityService $studentProductivityService)
    {
    }

    public function dashboard(Request $request)
    {
        $filters = [
            'academic_level' => $request->query('academic_level', 'all'),
            'gender' => $request->query('gender', 'all'),
            'internet_quality' => $request->query('internet_quality', 'all'),
        ];

        $dashboardData = $this->studentProductivityService->getDashboardData($filters);
        $availableFilters = $this->studentProductivityService->getAvailableFilters();

        return view('student-productivity.dashboard', [
            'filters' => $filters,
            'availableFilters' => $availableFilters,
            'dashboardData' => $dashboardData,
        ]);
    }
}
