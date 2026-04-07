# Manual Breve de Uso

## 1. Acceso

1. Iniciar sesión con usuario válido.
2. Ir a `Inicio` o `Control Proyectos`.

## 2. Gestión de proyectos

1. En el bloque `Control de proyectos` se muestran los proyectos arrastrables.
2. Si el usuario es administrador, puede crear proyecto con el botón `+`.
3. Cada tarjeta muestra:
   - nombre del proyecto,
   - creador,
   - número de tareas,
   - último uso.

## 3. Crear tarea

1. Arrastrar un proyecto al calendario.
2. Se abre el formulario con:
   - Inicio tarea (fecha/hora),
   - Texto informativo,
   - Fin tarea (fecha/hora).
3. Guardar tarea.
4. El calendario y el listado de proyectos se recargan automáticamente por AJAX.

## 4. Consultar tarea existente

1. Hacer clic en el evento del calendario.
2. Se abre modal de detalles en modo lectura.

## 5. Cambiar usuario del calendario

1. Usar selector `Usuario del calendario`.
2. Se recargan tareas del usuario seleccionado por AJAX.

## 6. Informe PDF

1. Pulsar botón PDF en `Control de proyectos`.
2. Elegir filtros:
   - Fecha desde/hasta,
   - Proyecto,
   - Usuario.
3. Generar informe.
4. Se descarga PDF agrupado por proyecto con total de minutos.

## 7. Gestión de usuarios (solo admin)

1. Entrar a `Usuarios` desde menú lateral.
2. Crear, editar o eliminar usuarios.
3. Usuarios no administradores reciben acceso denegado (403).

