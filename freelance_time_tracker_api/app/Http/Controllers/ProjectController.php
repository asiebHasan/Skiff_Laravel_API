<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use Carbon\Carbon;


class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where("user_id", Auth::id())->get();

        return response()->json($projects);
    }

    public function show($id)
    {
        $project = Project::find($id)->where("user_id", Auth::id())->first();
        return response()->json($project, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|in:active,completed',
            'deadline' => 'required|date',
        ]);

        $project = Project::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'client_id' => $request->client_id,
            'status' => $request->status,
            'deadline' => Carbon::parse($request->deadline)->format('Y-m-d H:i:s'),
        ]);

        return response()->json($project, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "title" => "required|string|max:255",
            "description" => "required|string|max:255",
            "client_id" => "required|exists:clients,id",
            "status" => "required|in:active, completed",
            "deadline" => "required|date",
        ]);

        $project = Project::where('id', $id)->first();
        if (Auth::id() !== $project->user_id) {
            return response()->json(["message" => "Invalid project"], 403);
        }

        $project->update($request->only(["title", "description", "status", "deadline"]));

        return response()->json(["message" => "Project Updated", "project" => $project], 200);
    }

    public function destroy($id)
    {
        $project = Project::where('id', $id)->first();
        if (Auth::id() !== $project->user_id) {
            return response()->json(["message" => "Invalid project"], 403);
        }
        $project->delete();

        return response()->json(["message" => "Project Deleted"], 200);
    }


}