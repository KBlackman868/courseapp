<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\MoodleCourseSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MoodleCourseImportController extends Controller
{
    private MoodleCourseSync $syncService;
    
    public function __construct(MoodleCourseSync $syncService)
    {
        $this->syncService = $syncService;
    }
    
    /**
     * Show the import page
     */
    public function index()
    {
        $stats = [
            'local_courses' => Course::count(),
            'moodle_synced' => Course::whereNotNull('moodle_course_id')->count(),
            'not_synced' => Course::whereNull('moodle_course_id')->count(),
        ];
        
        return view('admin.moodle.course-import', compact('stats'));
    }
    
    /**
     * Handle Excel/CSV file upload and import
     */
    public function importFromFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
            'import_mode' => 'required|in:create_only,update_only,both',
        ]);
        
        try {
            $file = $request->file('file');
            $importMode = $request->import_mode;
            
            // Parse the file
            $courses = $this->parseFile($file);
            
            if (empty($courses)) {
                return back()->with('error', 'No valid courses found in the file');
            }
            
            // Validate the data
            $validation = $this->validateCourseData($courses);
            if (!$validation['valid']) {
                return back()->with('error', 'Validation failed')->with('validation_errors', $validation['errors']);
            }
            
            // Process import
            $results = $this->processImport($courses, $importMode);
            
            return back()->with('success', 'Import completed successfully')
                        ->with('import_results', $results);
            
        } catch (\Exception $e) {
            Log::error('Course import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Sync courses directly from Moodle API
     */
    public function syncFromMoodle(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|integer',
            'sync_enrollments' => 'boolean',
        ]);
        
        try {
            $stats = $this->syncService->syncAllCourses();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Sync completed',
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Download course data from Moodle as Excel
     */
    public function exportFromMoodle(Request $request)
    {
        try {
            $courses = $this->syncService->exportCoursesToArray();
            
            // Create CSV response
            $filename = 'moodle_courses_export_' . date('Y-m-d_His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($courses) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Add headers
                if (!empty($courses)) {
                    fputcsv($file, array_keys($courses[0]));
                    
                    // Add data
                    foreach ($courses as $course) {
                        fputcsv($file, $course);
                    }
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Parse uploaded file
     */
    private function parseFile($file): array
    {
        $extension = $file->getClientOriginalExtension();
        $courses = [];
        
        if ($extension === 'csv') {
            $courses = $this->parseCsv($file);
        } else {
            // For Excel files, you'll need to install: composer require maatwebsite/excel
            // $courses = $this->parseExcel($file);
            
            // Simple alternative using PhpSpreadsheet directly
            $courses = $this->parseExcelBasic($file);
        }
        
        return $courses;
    }
    
    /**
     * Parse CSV file
     */
    private function parseCsv($file): array
    {
        $courses = [];
        $handle = fopen($file->getPathname(), 'r');
        
        // Get headers
        $headers = fgetcsv($handle);
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);
        $headers = array_map(function($h) {
            return str_replace(' ', '_', $h);
        }, $headers);
        
        // Parse rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $course = array_combine($headers, $row);
                
                // Map to expected structure
                $courses[] = $this->mapImportedCourse($course);
            }
        }
        
        fclose($handle);
        
        return $courses;
    }
    
    /**
     * Basic Excel parsing (without Laravel Excel package)
     */
    private function parseExcelBasic($file): array
    {
        // This requires: composer require phpoffice/phpspreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        if (empty($rows)) {
            return [];
        }
        
        // First row is headers
        $headers = array_map('trim', $rows[0]);
        $headers = array_map('strtolower', $headers);
        $headers = array_map(function($h) {
            return str_replace(' ', '_', $h);
        }, $headers);
        
        $courses = [];
        
        // Parse data rows
        for ($i = 1; $i < count($rows); $i++) {
            if (count($rows[$i]) === count($headers)) {
                $course = array_combine($headers, $rows[$i]);
                $courses[] = $this->mapImportedCourse($course);
            }
        }
        
        return $courses;
    }
    
    /**
     * Map imported course data to expected structure
     */
    private function mapImportedCourse(array $data): array
    {
        return [
            'moodle_course_id' => $data['moodle_id'] ?? $data['id'] ?? null,
            'moodle_course_shortname' => $data['shortname'] ?? $data['short_name'] ?? null,
            'title' => $data['fullname'] ?? $data['full_name'] ?? $data['title'] ?? '',
            'description' => $data['summary'] ?? $data['description'] ?? '',
            'status' => isset($data['visible']) ? ($data['visible'] ? 'active' : 'inactive') : 'active',
            'category_id' => $data['categoryid'] ?? $data['category_id'] ?? $data['category'] ?? null,
            'format' => $data['format'] ?? 'topics',
            'startdate' => $data['startdate'] ?? $data['start_date'] ?? null,
            'enddate' => $data['enddate'] ?? $data['end_date'] ?? null,
        ];
    }
    
    /**
     * Validate course data
     */
    private function validateCourseData(array $courses): array
    {
        $errors = [];
        $valid = true;
        
        foreach ($courses as $index => $course) {
            $rowNumber = $index + 2; // Account for header row
            
            $validator = Validator::make($course, [
                'moodle_course_id' => 'required|integer',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
                'moodle_course_shortname' => 'nullable|string|max:255',
            ]);
            
            if ($validator->fails()) {
                $valid = false;
                $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
            }
        }
        
        return ['valid' => $valid, 'errors' => $errors];
    }
    
    /**
     * Process the import
     */
    private function processImport(array $courses, string $mode): array
    {
        $results = [
            'total' => count($courses),
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        DB::beginTransaction();
        
        try {
            foreach ($courses as $courseData) {
                try {
                    $existing = Course::where('moodle_course_id', $courseData['moodle_course_id'])->first();
                    
                    if ($existing) {
                        if ($mode === 'create_only') {
                            $results['skipped']++;
                            continue;
                        }
                        
                        // Update existing
                        $existing->update([
                            'title' => $courseData['title'],
                            'description' => $courseData['description'],
                            'status' => $courseData['status'],
                            'moodle_course_shortname' => $courseData['moodle_course_shortname'],
                        ]);
                        
                        $results['updated']++;
                        
                    } else {
                        if ($mode === 'update_only') {
                            $results['skipped']++;
                            continue;
                        }
                        
                        // Create new
                        Course::create([
                            'moodle_course_id' => $courseData['moodle_course_id'],
                            'moodle_course_shortname' => $courseData['moodle_course_shortname'],
                            'title' => $courseData['title'],
                            'description' => $courseData['description'],
                            'status' => $courseData['status'],
                        ]);
                        
                        $results['created']++;
                    }
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Course {$courseData['title']}: {$e->getMessage()}";
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        return $results;
    }
}