<?php
namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        return AuditLog::with('user')->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $log = AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $request->action,
            'table_name' => $request->table_name,
            'description' => $request->description,
        ]);

        return response()->json($log, 201);
    }
}