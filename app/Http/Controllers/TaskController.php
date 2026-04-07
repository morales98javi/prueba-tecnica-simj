<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Tasks/Index');
    }

    public function list(Request $request)
    {
        $query = Task::with(['project', 'user']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('started_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('ended_at', '<=', $request->input('to'));
        }

        $tasks = $query
            ->orderBy('started_at')
            ->get();

        return response()->json($tasks->map(function (Task $task) {
            return [
                'id' => $task->id,
                'title' => $task->project->name,
                'start' => $task->started_at->format('Y-m-d\TH:i:s'),
                'end' => $task->ended_at->format('Y-m-d\TH:i:s'),
                'project' => [
                    'id' => $task->project->id,
                    'name' => $task->project->name,
                ],
                'user' => [
                    'id' => $task->user->id,
                    'name' => $task->user->name,
                ],
                'duration_hours' => $task->duration_hours,
                'notes' => $task->notes,
            ];
        }));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'user_id' => ['required', 'exists:users,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $startDateTime = Carbon::parse($request->started_at);
        $endDateTime = Carbon::parse($request->ended_at);
        $durationHours = max(
            round($startDateTime->diffInMinutes($endDateTime) / 60, 2),
            0.25,
        );

        $task = Task::create([
            'project_id' => $request->project_id,
            'user_id' => $request->user_id,
            'started_at' => $startDateTime,
            'ended_at' => $endDateTime,
            'duration_hours' => $durationHours,
            'notes' => $request->notes,
        ]);

        Project::whereKey($request->project_id)->update([
            'last_used_at' => now(),
        ]);

        $task->load(['project:id,name', 'user:id,name']);

        return response()->json([
            'message' => 'Tarea creada correctamente.',
            'task' => $task,
        ]);
    }

    public function users()
    {
        $users = User::orderBy('name')->get(['id', 'name']);

        return response()->json($users);
    }
}
