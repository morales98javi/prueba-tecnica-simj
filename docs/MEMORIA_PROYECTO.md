# Memoria del Proyecto

## 1. Objetivo

Desarrollar un panel de gestión para:

- administrar proyectos,
- asignar tareas por usuario en calendario,
- y emitir un informe PDF de tareas realizadas.

La solución se ha orientado a reproducir el flujo y la disposición visual solicitados en los anexos de la prueba.

## 2. Alcance funcional implementado

- Tablero único de operación (proyectos + calendario + reporte PDF).
- Proyectos arrastrables al calendario.
- Alta de tarea desde arrastre con formulario de inicio, fin y texto informativo.
- Carga de tareas por usuario mediante AJAX al cambiar el selector o entrar en la vista.
- Colores por proyecto en tarjetas y eventos del calendario.
- Vista de calendario en Semana, Día y Gestión (lista).
- Informe PDF agrupado por proyecto, columnas fijas y total de minutos por proyecto.
- Gestión de usuarios restringida a administradores.

## 3. Decisiones técnicas

- Laravel 13 + Eloquent ORM para la capa de dominio y persistencia.
- Vue 3 + Inertia para interfaz dinámica y navegación sin recarga completa.
- FullCalendar para interacción drag & drop y visualización temporal.
- DomPDF para exportación de informes en formato portable.
- Control de permisos admin desacoplado mediante middleware dedicado.

## 4. Criterios de calidad aplicados

- Relaciones explícitas en modelos (`belongsTo`, `hasMany`).
- Endpoints JSON claros para recargas AJAX.
- Validaciones backend en creación/actualización.
- Estructura modular en frontend (layout + componente de tablero + páginas).
- Documentación funcional y técnica separada por secciones.

## 5. Entorno de desarrollo

- Sistema: Windows 11.
- PHP: 8.4.x.
- Framework: Laravel 13.x.
- Node.js + npm para compilación frontend (Vite).
- Base de datos: MySQL/MariaDB.
- Servidor local de pruebas: entorno tipo `*.test` (compatible con Herd/Valet/Nginx local).

## 6. Resultado

Se entrega una aplicación operativa y documentada, alineada con los criterios de evaluación:

- uso de ORM y relaciones,
- UX/UI con tablero unificado,
- uso de Vue en vistas,
- estructura de código mantenible,
- y memoria con manual de uso y entorno técnico.

## 7. Adecuación explícita a la consigna

- Entrega prevista mediante repositorio público de GitHub.
- Framework Laravel con versión de PHP superior a 8.3.
- Interfaz basada en Bootstrap 5.
- Base de diseño tipo AdminLTE (AdminLTE 3 o similar).
- Sistema de plantillas orientado a Vue (Inertia + Vue 3), priorizado sobre Blade en la capa de vistas funcionales.
