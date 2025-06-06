<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\TimeLog;
use App\Models\Project;

class TimeLogController extends Controller
{
    public function show($id)
    {
        $time_logs = TimeLog::find($id);

        if (!$time_logs) {
            return response()->json(['message' => 'Time log not found'], 404);
        }

        if ($time_logs->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized | Try another log'], 403);
        }

        return response()->json($time_logs, 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            "project_id" => "required|exists:projects,id",
            "description" => "required|string|max:255",
            "tag" => "required|in:billable,non-billable"
        ]);

        $project = Project::findOrFail($request->project_id);

        if ($project->client_id !== Auth::user()->client_id) {
            return response()->json(['message' => 'Unauthorized | You do not have access to this project'], 403);
        }

        $time_logs = TimeLog::create([
            "user_id" => Auth::id(),
            "project_id" => $request->project_id,
            "description" => $request->description,
            "tag" => $request->tag,
        ]);

        return response()->json($time_logs, 201);
    }

    public function update(Request $request, $id)
    {

        $time_log = TimeLog::findOrFail($id);

        $request->validate([
            "project_id" => "required|exists:projects,id",
            "start_time" => "date|nullable",
            "end_time" => "date|nullable",
            "description" => "required|string|max:255",
            "hours" => "numeric|nullable",
        ]);

        $project = Project::findOrFail($request->project_id);
        if ($project->client_id !== Auth::user()->client_id) {
            return response()->json(['message' => 'Unauthorized | You do not have access to this project'], 403);
        }

        if ($request->has('start_time') && $request->has('end_time')) {
            $start = Carbon::parse($request->start_time);
            $end = Carbon::parse($request->end_time);
            $time_log->hours = $start->floatDiffInHours($end);
        } elseif ($request->has('start_time')) {
            $time_log->start_time = Carbon::parse($request->start_time);
        } elseif ($request->has('end_time')) {
            $time_log->end_time = Carbon::parse($request->end_time);
        }

        if (!$time_log->start_time && !$time_log->end_time) {
            $time_log->hours = null;
        }

        $time_log->project_id = $request->project_id;
        $time_log->description = $request->description;
        $time_log->tag = $request->tag ?? $time_log->tag;
        $time_log->save();

        return response()->json($time_log, 200);

    }

    public function destroy($id)
    {
        
        $time_log = TimeLog::findOrFail($id);

        $project = Project::findOrFail($time_log->project_id);
        if ($project->client_id !== Auth::user()->client_id) {
            return response()->json(['message' => 'Unauthorized | You do not have access to this project'], 403);
        }
        $time_log->delete();

        return response()->json(["message" => "Log deleted"], 200);
    }

    public function start_time_logs(Request $request, $id)
    {
        $time_logs = TimeLog::where("id", $id)->first();
        $time_logs->start_time = Carbon::parse($request->start_time);

        $time_logs->save();

        return response()->json(["message" => "log started", $time_logs], 200);
    }

    public function end_time_logs(Request $request, $id)
    {
        $time_log = TimeLog::where("id", $id)->first();
        $time_log->end_time = Carbon::parse($request->end_time);

        if ($time_log->start_time && $time_log->end_time) {
            $start = Carbon::parse($time_log->start_time);
            $end = Carbon::parse($time_log->end_time);
            $time_log->hours = $start->floatDiffInHours($end);
        }


        return response()->json(["message" => "log ended", $time_log], 200);
    }

    // logs

    public function logsByProject($id)
    {
        $time_logs = TimeLog::where('project_id', $id)->orderBy("created_at", "desc")->paginate(10);
        return response()->json($time_logs);
    }

    public function logsByUser()
    {
        $time_logs = TimeLog::where('user_id', Auth::id())
            ->orderBy("created_at", "desc")
            ->paginate(10);
        return response()->json($time_logs);
    }

    public function logsByDay(Request $request)
    {
        $request->validate([
            "date" => "required|date",
        ]);

        $date = Carbon::parse($request->date)->startOfDay();
        $time_logs = TimeLog::whereDate('created_at', $date)
            ->where('user_id', Auth::id())
            ->orderBy("created_at", "desc")
            ->paginate(10);

        return response()->json($time_logs);
    }

    public function logsByWeek(Request $request)
    {
        $start = Carbon::parse($request->start_date)->startOfWeek();
        $end = Carbon::parse($request->start_date)->endOfWeek();

        $logs = TimeLog::where('user_id', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    public function logsBetweenDates(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $start = Carbon::parse($request->from)->startOfDay();
        $end = Carbon::parse($request->to)->endOfDay();

        $logs = TimeLog::where('user_id', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'logs' => $logs,
        ]);
    }

    // Total Hours

    public function totalHoursByProject($id)
    {
        $total_hours = TimeLog::where('project_id', $id)
            ->whereNotNull('hours')
            ->sum('hours');

        return response()->json(['project_id' => $id, 'total_hours' => $total_hours]);

    }

    public function totalHoursByDay(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = Carbon::parse($request->date);

        $total = TimeLog::whereDate('created_at', $date)
            ->where('user_id', Auth::id())
            ->sum('hours');

        return response()->json(['date' => $date->toDateString(), 'total_hours' => $total]);
    }

    public function totalHoursByClient($client_id)
    {
        $total = TimeLog::whereHas(
            'project',
            function ($query) use ($client_id) {
                $query->where('client_id', $client_id);
            }
        )
            ->sum('hours');

        return response()->json(['client_id' => $client_id, 'total_hours' => $total]);
    }

    public function exportSelectedLogsToPDF(Request $request)
    {
        // echo "Exporting selected logs to PDF...";
        $request->validate([
            'logs' => 'required|array',
            'logs.*' => 'required|integer|exists:time_logs,id',
        ]);

        $time_logs = TimeLog::with('project.client') // eager load project and client
            ->whereIn('id', $request->logs)
            ->where('user_id', Auth::id())
            ->get();



        if ($time_logs->isEmpty()) {
            return response()->json(['message' => 'No logs found for export'], 404);
        }

        $pdf = Pdf::loadView('pdf.time_logs', ['logs' => $time_logs, "user" => Auth::user()]);

        // echo json_encode($time_logs);
        $filePath = storage_path('app/public/time_logs_export.pdf');
        $pdf->save($filePath);


        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'time_logs_export.pdf');
    }

}
