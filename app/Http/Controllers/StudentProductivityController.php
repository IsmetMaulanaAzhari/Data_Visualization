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
        $datasetMeta = $this->studentProductivityService->getDatasetMeta();
        $previewRows = $this->studentProductivityService->getPreviewRows(8);

        return view('student-productivity.dashboard', [
            'filters' => $filters,
            'availableFilters' => $availableFilters,
            'dashboardData' => $dashboardData,
            'datasetMeta' => $datasetMeta,
            'previewRows' => $previewRows,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'dataset_file' => ['required', 'file', 'mimes:csv,txt', 'max:51200'],
        ]);

        $this->studentProductivityService->replaceDataset($request->file('dataset_file'));

        return redirect()
            ->route('student-productivity.dashboard')
            ->with('success', 'Dataset CSV berhasil diupload dan cache sudah diperbarui.');
    }

    public function refresh()
    {
        $this->studentProductivityService->refreshCache();

        return redirect()
            ->route('student-productivity.dashboard')
            ->with('success', 'Cache dataset berhasil di-refresh.');
    }

    public function apiData(Request $request)
    {
        $filters = [
            'academic_level' => $request->query('academic_level', 'all'),
            'gender' => $request->query('gender', 'all'),
            'internet_quality' => $request->query('internet_quality', 'all'),
        ];

        return response()->json($this->studentProductivityService->getApiPayload($filters));
    }
}
