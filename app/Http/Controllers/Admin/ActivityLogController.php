<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin');
    }

    /**
     * Display activity logs with live updates
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }
        
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // For AJAX requests (live updates)
        if ($request->ajax()) {
            $logs = $query->take(50)->get();
            return response()->json([
                'logs' => $logs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'icon' => $log->action_icon,
                        'action' => $log->formatted_action,
                        'description' => $log->description,
                        'user' => $log->user_name ?? 'System',
                        'time' => $log->created_at->diffForHumans(),
                        'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                        'severity' => $log->severity,
                        'severity_color' => $log->severity_color,
                        'status' => $log->status,
                        'status_color' => $log->status_color,
                        'ip' => $log->ip_address,
                        'url' => $log->url,
                    ];
                }),
                'last_id' => $logs->first()?->id,
            ]);
        }
        
        $logs = $query->paginate(50);
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        
        // Get statistics
        $stats = [
            'total_today' => ActivityLog::whereDate('created_at', today())->count(),
            'failed_today' => ActivityLog::whereDate('created_at', today())->where('status', 'failed')->count(),
            'unique_users_today' => ActivityLog::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
            'critical_events' => ActivityLog::whereDate('created_at', today())->where('severity', 'critical')->count(),
        ];
        
        return view('admin.activity-logs.index', compact('logs', 'users', 'stats'));
    }

    /**
     * Get new logs for live updates
     */
    public function live(Request $request)
    {
        $lastId = $request->get('last_id', 0);
        
        $newLogs = ActivityLog::with('user')
            ->where('id', '>', $lastId)
            ->latest()
            ->take(20)
            ->get();
        
        return response()->json([
            'logs' => $newLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'icon' => $log->action_icon,
                    'action' => $log->formatted_action,
                    'description' => $log->description,
                    'user' => $log->user_name ?? 'System',
                    'time' => $log->created_at->diffForHumans(),
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'severity' => $log->severity,
                    'severity_color' => $log->severity_color,
                    'status' => $log->status,
                    'status_color' => $log->status_color,
                ];
            }),
            'count' => $newLogs->count(),
        ]);
    }

    /**
     * Show detailed log entry
     */
    public function show(ActivityLog $log)
    {
        return view('admin.activity-logs.show', compact('log'));
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user');
        
        // Apply same filters as index
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }
        
        $logs = $query->get();
        
        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 'Date/Time', 'User', 'Action', 'Description', 
                'Status', 'Severity', 'IP Address', 'URL'
            ]);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_name ?? 'System',
                    $log->action,
                    $log->description,
                    $log->status,
                    $log->severity,
                    $log->ip_address,
                    $log->url,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}