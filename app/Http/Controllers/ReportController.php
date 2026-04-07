<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Reports/Tasks', [
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = Task::with(['project', 'user']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('started_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('ended_at', '<=', $request->to);
        }

        $tasks = $query->orderBy('project_id')->orderBy('started_at')->get();

        $projects = $tasks
            ->groupBy(fn (Task $task) => $task->project->name)
            ->map(function ($projectTasks, $projectName) {
                return [
                    'project_name' => $projectName,
                    'total_minutes' => $projectTasks->sum(
                        fn (Task $task) => $task->duration_hours * 60
                    ),
                    'tasks' => $projectTasks->map(function (Task $task) {
                        return [
                            'id' => $task->id,
                            'start' => $task->started_at->format('d/m/Y H:i'),
                            'end' => $task->ended_at->format('d/m/Y H:i'),
                            'minutes' => (int) round($task->duration_hours * 60),
                            'user' => $task->user->name,
                            'description' => $task->notes ?: 'Sin descripcion',
                        ];
                    }),
                ];
            });

        $appliedFilters = [
            'Proyecto' => $request->filled('project_id')
                ? Project::find($request->project_id)?->name
                : 'Todos los proyectos',
            'Usuario' => $request->filled('user_id')
                ? User::find($request->user_id)?->name
                : 'Todos los usuarios',
            'Desde' => $request->filled('from')
                ? Carbon::parse($request->from)->format('d/m/Y')
                : 'Sin fecha de inicio',
            'Hasta' => $request->filled('to')
                ? Carbon::parse($request->to)->format('d/m/Y')
                : 'Sin fecha final',
        ];

        $pdf = Pdf::loadView('reports.tasks-pdf', [
            'projects' => $projects,
            'appliedFilters' => $appliedFilters,
        ]);

        return $pdf->download('informe-tareas-simj.pdf');
    }
}
