<?php

namespace App\Http\Controllers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
 
 
use App\Models\TasFileHistory;
use App\Models\TasFile;
use App\Models\admitted;
use App\Models\AuditTrail;
use App\Models\ApprehendingOfficer;
use App\Models\TrafficViolation;
use App\Models\department;
use App\Models\G5ChatMessage;
use App\Models\archives;
class UserController extends Controller
{
    public function admitTrail(Request $request)
    {
        // Retrieve filter from request query parameters
        $filter = $request->query('filter', 'today');
        
        // Initialize activities query with eager loading of 'user' relationship
        $activitiesQuery = AuditTrail::with('user');
        
        // Apply filters based on $filter value
        switch ($filter) {
            case 'today':
                $activitiesQuery->whereDate('created_at', today());
                break;
            case 'this_month':
                $activitiesQuery->whereMonth('created_at', now()->month);
                break;
            case 'this_year':
                $activitiesQuery->whereYear('created_at', now()->year);
                break;
            case 'all':
                // No additional conditions for 'all'
                break;
            default:
                break;
        }
        
        // Determine how many items per page
        $perPage = 10;
        
        // Retrieve activities with pagination
        if ($filter !== 'all') {
            $activities = $activitiesQuery->orderBy('created_at', 'desc')->paginate($perPage);
        } else {
            $activities = $activitiesQuery->orderBy('created_at', 'desc')->paginate($perPage);
        }
        
        // Return view with activities, filter, and authenticated user's details
        $user = Auth::user(); // Retrieve authenticated user
        $fullname = $user->fullname; // Assuming 'fullname' and 'username' are fields in your User model
        $username = $user->username;
        
        return view('history', compact('activities', 'filter', 'fullname', 'username'));
    }
    
    public function getNotifications()
    {
        $notifications = AuditTrail::with('user')
            ->latest()
            ->take(5)
            ->get(['model', 'description', 'old_value','new_value','details', 'created_at', 'user_id']);
    
        // Format created_at field using Carbon
        $notifications->transform(function ($item) {
            $item->created_at = Carbon::parse($item->created_at)->format('Y-m-d H:i:s'); // Format as per your requirement
            return $item;
        });
    
        return response()->json($notifications);
    }

    public function getOfficersStatus(Request $request)
    {
        $department = $request->input('department');
    
        $query = ApprehendingOfficer::query();
    
        if ($department) {
            $query->where('department', $department);
        }
    
        // Log the query so we can debug if needed
        Log::info('Fetching officer status with query', [
            'department' => $department,
            'query' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
    
        // Get the count of inactive and active officers
        $inactiveCount = $query->where('isactive', 0)->count();
        Log::info('Inactive count', ['count' => $inactiveCount]);
    
        // Since we've already used where() on the same query, we need to clone it or create a new instance
        $activeQuery = ApprehendingOfficer::query();
        if ($department) {
            $activeQuery->where('department', $department);
        }
        $activeCount = $activeQuery->where('isactive', 1)->count();
        Log::info('Active count', ['count' => $activeCount]);
    
        // Log the final counts
        Log::info('Final officer status counts', [
            'department' => $department,
            'inactive_count' => $inactiveCount,
            'active_count' => $activeCount
        ]);
    
        return response()->json([
            'inactive' => $inactiveCount,
            'active' => $activeCount
        ]);
    }
    
    public function getRecentActivity()
    {
        // Fetch the most recent 5 activities with user details
        $latestActivity = AuditTrail::with('user:id,fullname,username') // Eager load user with specific fields
            ->orderBy('created_at', 'desc')
            ->take(3) // Fetch the latest 5 activities
            ->get();
    
        return response()->json($latestActivity);
    }
    
   

    


}
