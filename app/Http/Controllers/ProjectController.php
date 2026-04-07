<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function list(Request $request)
    {
        $projects = Project::with('creator')
            ->orderByDesc('last_used_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return response()->json($projects);
    }

    public function options()
    {
        $projects = Project::with('creator:id,name')
            ->withCount('tasks')
            ->orderByDesc('last_used_at')
            ->get(['id', 'name', 'created_by', 'last_used_at']);

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (!$request->user()->is_admin) {
            abort(403);
        }

        $project = Project::create([
            'name' => $request->name,
            'created_by' => Auth::id(),
            'last_used_at' => now(),
        ]);

        return response()->json([
            'message' => 'Proyecto creado correctamente.',
            'project' => $project,
        ]);
    }
}
