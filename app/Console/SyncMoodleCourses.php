<?php

namespace App\Console\Commands;

use App\Services\MoodleCourseSync;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncMoodleCourses extends Command
{
    protected $signature = 'moodle:sync-courses 
                            {--category= : Sync only courses from specific category ID}
                            {--export : Export to Excel instead of syncing}
                            {--dry-run : Show what would be synced without making changes}';
    
    protected $description = 'Sync courses from Moodle to local database';
    
    private MoodleCourseSync $syncService;
    
    public function __construct(MoodleCourseSync $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }
    
    public function handle()
    {
        $this->info('Starting Moodle course synchronization...');
        
        // Export mode
        if ($this->option('export')) {
            return $this->handleExport();
        }
        
        // Dry run mode
        if ($this->option('dry-run')) {
            return $this->handleDryRun();
        }
        
        // Regular sync
        return $this->handleSync();
    }
    
    private function handleSync()
    {
        try {
            $categoryId = $this->option('category');
            
            if ($categoryId) {
                $this->info("Fetching courses from category ID: {$categoryId}");
                $courses = $this->syncService->fetchCoursesByCategory((int)$categoryId);
            } else {
                $this->info('Fetching all courses from Moodle...');
                $courses = $this->syncService->fetchMoodleCourses();
            }
            
            $this->info('Found ' . count($courses) . ' courses in Moodle');
            
            if (count($courses) === 0) {
                $this->warn('No courses found to sync');
                return 0;
            }
            
            // Show preview
            $this->table(
                ['ID', 'Short Name', 'Full Name', 'Visible'],
                array_map(function($course) {
                    return [
                        $course['id'],
                        $course['shortname'] ?? 'N/A',
                        substr($course['fullname'] ?? 'N/A', 0, 50),
                        $course['visible'] ? 'Yes' : 'No'
                    ];
                }, array_slice($courses, 0, 10))
            );
            
            if (count($courses) > 10) {
                $this->info('... and ' . (count($courses) - 10) . ' more courses');
            }
            
            // Ask for confirmation
            if (!$this->confirm('Do you want to sync these courses?')) {
                $this->info('Sync cancelled');
                return 0;
            }
            
            // Perform sync
            $this->info('Syncing courses...');
            $progressBar = $this->output->createProgressBar(count($courses));
            $progressBar->start();
            
            $stats = [
                'created' => 0,
                'updated' => 0,
                'failed' => 0,
                'errors' => []
            ];
            
            foreach ($courses as $moodleCourse) {
                try {
                    $existing = \App\Models\Course::where('moodle_course_id', $moodleCourse['id'])->exists();
                    
                    $this->syncService->syncCourse($moodleCourse);
                    
                    if ($existing) {
                        $stats['updated']++;
                    } else {
                        $stats['created']++;
                    }
                } catch (\Exception $e) {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'course' => $moodleCourse['fullname'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Display results
            $this->info('Sync completed!');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Created', $stats['created']],
                    ['Updated', $stats['updated']],
                    ['Failed', $stats['failed']],
                    ['Total', $stats['created'] + $stats['updated'] + $stats['failed']]
                ]
            );
            
            if (!empty($stats['errors'])) {
                $this->error('The following errors occurred:');
                foreach ($stats['errors'] as $error) {
                    $this->error("- {$error['course']}: {$error['error']}");
                }
            }
            
            Log::info('Moodle course sync completed', $stats);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('Moodle course sync failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
    
    private function handleDryRun()
    {
        $this->info('DRY RUN MODE - No changes will be made');
        
        try {
            $courses = $this->syncService->fetchMoodleCourses();
            $this->info('Found ' . count($courses) . ' courses in Moodle');
            
            $toCreate = [];
            $toUpdate = [];
            
            foreach ($courses as $moodleCourse) {
                $existing = \App\Models\Course::where('moodle_course_id', $moodleCourse['id'])->first();
                
                if ($existing) {
                    $toUpdate[] = [
                        'local_id' => $existing->id,
                        'moodle_id' => $moodleCourse['id'],
                        'shortname' => $moodleCourse['shortname'] ?? 'N/A',
                        'fullname' => $moodleCourse['fullname'] ?? 'N/A',
                        'action' => 'UPDATE'
                    ];
                } else {
                    $toCreate[] = [
                        'moodle_id' => $moodleCourse['id'],
                        'shortname' => $moodleCourse['shortname'] ?? 'N/A',
                        'fullname' => $moodleCourse['fullname'] ?? 'N/A',
                        'action' => 'CREATE'
                    ];
                }
            }
            
            $this->info("\nCourses to CREATE: " . count($toCreate));
            if (!empty($toCreate)) {
                $this->table(
                    ['Moodle ID', 'Short Name', 'Full Name'],
                    array_map(function($c) {
                        return [$c['moodle_id'], $c['shortname'], substr($c['fullname'], 0, 50)];
                    }, array_slice($toCreate, 0, 5))
                );
                if (count($toCreate) > 5) {
                    $this->info('... and ' . (count($toCreate) - 5) . ' more');
                }
            }
            
            $this->info("\nCourses to UPDATE: " . count($toUpdate));
            if (!empty($toUpdate)) {
                $this->table(
                    ['Local ID', 'Moodle ID', 'Short Name', 'Full Name'],
                    array_map(function($c) {
                        return [$c['local_id'], $c['moodle_id'], $c['shortname'], substr($c['fullname'], 0, 40)];
                    }, array_slice($toUpdate, 0, 5))
                );
                if (count($toUpdate) > 5) {
                    $this->info('... and ' . (count($toUpdate) - 5) . ' more');
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Dry run failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function handleExport()
    {
        $this->info('Exporting Moodle courses to Excel...');
        
        try {
            $courses = $this->syncService->exportCoursesToArray();
            
            if (empty($courses)) {
                $this->warn('No courses found to export');
                return 0;
            }
            
            // Create Excel file using Laravel Excel package
            // First install: composer require maatwebsite/excel
            
            $filename = 'moodle_courses_' . date('Y-m-d_His') . '.csv';
            $filepath = storage_path('app/exports/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/exports'))) {
                mkdir(storage_path('app/exports'), 0755, true);
            }
            
            // Create CSV
            $handle = fopen($filepath, 'w');
            
            // Add headers
            fputcsv($handle, array_keys($courses[0]));
            
            // Add data
            foreach ($courses as $course) {
                fputcsv($handle, $course);
            }
            
            fclose($handle);
            
            $this->info("Export completed! File saved to: {$filepath}");
            $this->info("Total courses exported: " . count($courses));
            
            // Display summary
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Courses', count($courses)],
                    ['Active Courses', count(array_filter($courses, fn($c) => $c['visible'] == 1))],
                    ['Inactive Courses', count(array_filter($courses, fn($c) => $c['visible'] == 0))],
                    ['File Location', $filepath]
                ]
            );
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Export failed: ' . $e->getMessage());
            return 1;
        }
    }
}