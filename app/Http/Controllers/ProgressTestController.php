<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ProgressUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProgressTestController extends Controller
{
    public function testProgress(Request $request)
    {
        $projectId = $request->input('project_id', 25);
        
        Log::info('=== TESTING PROGRESS BROADCAST ===', [
            'projectId' => $projectId,
            'userId' => Auth::id()
        ]);
        
        // Test different progress values
        $progressValues = [
            ['progress' => 10, 'status' => 'Starting', 'message' => 'Initializing...'],
            ['progress' => 25, 'status' => 'Processing', 'message' => 'Step 1 of 4'],
            ['progress' => 50, 'status' => 'Processing', 'message' => 'Step 2 of 4'],
            ['progress' => 75, 'status' => 'Processing', 'message' => 'Step 3 of 4'],
            ['progress' => 100, 'status' => 'Completed', 'message' => 'All done!'],
        ];
        
        foreach ($progressValues as $index => $data) {
            Log::info("Broadcasting progress step " . ($index + 1), $data);
            
            event(new ProgressUpdated(
                progress: $data['progress'],
                status: $data['status'],
                message: $data['message'],
                projectId: $projectId,
                userId: Auth::id()
            ));
            
            // Add delay between broadcasts
            if ($index < count($progressValues) - 1) {
                sleep(2);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Progress test completed',
            'projectId' => $projectId
        ]);
    }
    
    public function testSingleProgress(Request $request)
    {
        $projectId = $request->input('project_id', 25);
        $progress = $request->input('progress', 50);
        $status = $request->input('status', 'Testing');
        $message = $request->input('message', 'Test broadcast message');
        
        Log::info('=== SINGLE PROGRESS TEST ===', [
            'projectId' => $projectId,
            'progress' => $progress,
            'status' => $status,
            'message' => $message
        ]);
        
        event(new ProgressUpdated(
            progress: $progress,
            status: $status,
            message: $message,
            projectId: $projectId,
            userId: Auth::id()
        ));
        
        return response()->json([
            'success' => true,
            'message' => 'Single progress broadcast sent',
            'data' => [
                'projectId' => $projectId,
                'progress' => $progress,
                'status' => $status,
                'message' => $message
            ]
        ]);
    }
}