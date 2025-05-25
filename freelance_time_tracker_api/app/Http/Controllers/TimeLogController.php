<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeLog;
use Carbon\Carbon;

class TimeLogController extends Controller
{
    

    public function store(Request $request)
    {
        $request->validate([
            "project_id" => "required|exists:projects,id",
            "description" => "required|string|max:255",
            "tag" => "required|in:billable,non-billable"
        ]);

        $time_logs = TimeLog::create($request->all());

        return response()->json($time_logs, 201);
    }

    public function update(Request $request, $id)
    {

        $time_log = TimeLog::findOrFail($id);

        $request->validate([
            "project_id" => "required|exists:projects,id",
            "start_time" => "date",
            "end_time" => "date",
            "description" => "required|string|max:255",
            "hours" => "numeric",
        ]);

        $time_log->update($request->only('start_time', 'end_time', 'description'));

        return response()->json($time_log, 200);

    }

    public function destroy($id)
    {
        $time_log = TimeLog::findOrFail($id);
        $time_log->delete();

        return response()->json(["message" => "Log deleted"], 200);
    }

    public function start_time_logs(Request $request, $id)
    {
        $time_logs = TimeLog::where("id", $id)->first();
        $time_logs->start_time = $request->start_time;

        $time_logs->save();

        return response()->json(["message" => "log started", $time_logs], 200);
    }

    public function end_time_logs(Request $request, $id)
    {
        $time_log = TimeLog::where("id", $id)->first();
        $time_log->end_time = $request->end_time;

        if ($time_log->start_time && $time_log->end_time) {
            $start = Carbon::parse($time_log->start_time);
            $end = Carbon::parse($time_log->end_time);
            $time_log->hours = $end->floatDiffInHours($start);
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
        $time_logs = TimeLog::where('user_id', Auth::id())->orderBy("created_at", "desc")->paginate(10);
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
        $total = TimeLog::whereHas('project', function ($query) use ($client_id) {
            $query->where('client_id', $client_id);
        })->sum('hours');

        return response()->json(['client_id' => $client_id, 'total_hours' => $total]);
    }

}
