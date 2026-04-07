# SIMJ Panel - Control de Proyectos y Tareas

Aplicación web desarrollada con Laravel + Vue para gestionar proyectos, asignar tareas mediante calendario arrastrable y generar informes PDF de tareas realizadas.

## Tecnologías

- Backend: Laravel 13, PHP 8.4, Eloquent ORM.
- Frontend: Vue 3 + Inertia.js + Axios.
- UI base: Bootstrap 5 + AdminLTE (estilo AdminLTE 3 o similar).
- Calendario: FullCalendar (semana, día y gestión/listado).
- Informes: `barryvdh/laravel-dompdf`.
- Base de datos: MySQL/MariaDB.

## Funcionalidades principales

- Panel unificado para control de proyectos y tareas.
- Creación de tareas desde arrastre de proyecto al calendario.
- Selección de usuario de calendario con recarga AJAX de tareas.
- Colores por proyecto en listado y eventos del calendario.
- Gestión de usuarios solo para administradores.
- Generación de informe PDF con filtros (fecha, proyecto, usuario), agrupado por proyecto y total de minutos.

## Instalación rápida

1. Clonar el repositorio.
2. Instalar dependencias:
    - `composer install`
    - `npm install`
3. Configurar entorno:
    - Copiar `.env.example` a `.env`
    - Ajustar conexión a base de datos.
    - Ejecutar `php artisan key:generate`
4. Migrar base de datos:
    - `php artisan migrate`
5. Compilar assets:
    - Desarrollo: `npm run dev`
    - Producción: `npm run build`
6. Levantar servidor:
    - `php artisan serve` o servidor local (Herd/Valet/Nginx/Apache).

## Estructura funcional

- Tablero principal: [`resources/js/Pages/Dashboard.vue`](/c:/Users/jmorales/laravel-prueba/resources/js/Pages/Dashboard.vue)
- Calendario y formularios de tarea: [`resources/js/Components/TaskCalendarBoard.vue`](/c:/Users/jmorales/laravel-prueba/resources/js/Components/TaskCalendarBoard.vue)
- Layout global (cabecera, menú lateral, contenido, footer): [`resources/js/Layouts/AdminLTELayout.vue`](/c:/Users/jmorales/laravel-prueba/resources/js/Layouts/AdminLTELayout.vue)
- Controlador de tareas: [`app/Http/Controllers/TaskController.php`](/c:/Users/jmorales/laravel-prueba/app/Http/Controllers/TaskController.php)
- Controlador de proyectos: [`app/Http/Controllers/ProjectController.php`](/c:/Users/jmorales/laravel-prueba/app/Http/Controllers/ProjectController.php)
- Reportes PDF: [`app/Http/Controllers/ReportController.php`](/c:/Users/jmorales/laravel-prueba/app/Http/Controllers/ReportController.php)
- Vista PDF: [`resources/views/reports/tasks-pdf.blade.php`](/c:/Users/jmorales/laravel-prueba/resources/views/reports/tasks-pdf.blade.php)

## Documentación de entrega

- Memoria del proyecto: [`docs/MEMORIA_PROYECTO.md`](/c:/Users/jmorales/laravel-prueba/docs/MEMORIA_PROYECTO.md)
- Manual breve de uso: [`docs/MANUAL_USO.md`](/c:/Users/jmorales/laravel-prueba/docs/MANUAL_USO.md)
- Arquitectura y relaciones ORM: [`docs/ARQUITECTURA_Y_RELACIONES.md`](/c:/Users/jmorales/laravel-prueba/docs/ARQUITECTURA_Y_RELACIONES.md)
