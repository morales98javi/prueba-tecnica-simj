# Arquitectura y Relaciones

## 1. Arquitectura general

- Backend Laravel (API web + render Inertia).
- Frontend Vue con componentes reutilizables.
- Comunicación AJAX con Axios para listar/crear datos sin recarga completa.
- Módulo de calendario con FullCalendar.
- Módulo de reportes PDF con DomPDF.

## 2. Modelos principales

## `User`

- Campos relevantes: `name`, `email`, `password`, `is_admin`.
- Relaciones:
  - `tasks()` -> `hasMany(Task::class)`
  - `createdProjects()` -> `hasMany(Project::class, 'created_by')`

## `Project`

- Campos relevantes: `name`, `created_by`, `last_used_at`.
- Relaciones:
  - `creator()` -> `belongsTo(User::class, 'created_by')`
  - `tasks()` -> `hasMany(Task::class)`

## `Task`

- Campos relevantes: `project_id`, `user_id`, `started_at`, `ended_at`, `duration_hours`, `notes`.
- Relaciones:
  - `project()` -> `belongsTo(Project::class)`
  - `user()` -> `belongsTo(User::class)`

## 3. Relaciones de base de datos

- `projects.created_by` -> FK a `users.id` (cascade on delete).
- `tasks.project_id` -> FK a `projects.id` (cascade on delete).
- `tasks.user_id` -> FK a `users.id` (cascade on delete).

## 4. Seguridad y permisos

- Rutas de usuarios protegidas por middleware `admin`:
  - [`app/Http/Middleware/EnsureAdmin.php`](/c:/Users/jmorales/laravel-prueba/app/Http/Middleware/EnsureAdmin.php)
  - Alias registrado en [`bootstrap/app.php`](/c:/Users/jmorales/laravel-prueba/bootstrap/app.php)
  - Aplicado en [`routes/web.php`](/c:/Users/jmorales/laravel-prueba/routes/web.php)

## 5. Flujo de datos clave

1. Front pide proyectos/usuarios/tareas vía AJAX.
2. Usuario arrastra proyecto al calendario.
3. Front abre modal y envía `POST /tasks`.
4. Backend valida, guarda tarea y actualiza `projects.last_used_at`.
5. Front recarga tareas y proyectos para reflejar datos actualizados.

## 6. Reporte PDF

- Fuente de datos: tareas filtradas por proyecto/usuario/fechas.
- Estructura:
  - cabecera con filtros aplicados,
  - agrupación por proyecto,
  - tabla con columnas fijas:
    - `ID`, `INICIO`, `FIN`, `MIN`, `USUARIO`, `TAREA REALIZADA`,
  - total de minutos por proyecto.

