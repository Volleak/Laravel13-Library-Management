<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    // Admin: View the system audit trail
    public function index(Request $request)
    {
        // Assuming a standard activity_logs table for audit tracking
        $logs = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->when($request->search, function ($q, $search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('activity_logs.action', 'like', "%{$search}%")
                  ->orWhere('activity_logs.description', 'like', "%{$search}%");
            })
            ->when($request->date, function ($q, $date) {
                $q->whereDate('activity_logs.created_at', $date);
            })
            ->orderBy('activity_logs.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('logs.index', compact('logs'));
    }
}