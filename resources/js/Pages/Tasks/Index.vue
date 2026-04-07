<script setup>
import AdminLTELayout from "@/Layouts/AdminLTELayout.vue";
import TaskCalendarBoard from "@/Components/TaskCalendarBoard.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Tareas" />

    <AdminLTELayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-0">Control de Tareas</h1>
                    <p class="text-muted mb-0">
                        Arrastra un proyecto al calendario o pulsa en un tramo horario para crear una tarea.
                    </p>
                </div>
            </div>
        </template>

        <TaskCalendarBoard />
    </AdminLTELayout>
</template>
