<script setup>
import AdminLTELayout from "@/Layouts/AdminLTELayout.vue";
import TaskCalendarBoard from "@/Components/TaskCalendarBoard.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Inicio" />

    <AdminLTELayout>
        <template #header>Inicio</template>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bienvenido</h3>
                    </div>
                    <div class="card-body">
                        <p>
                            ¡Has iniciado sesión exitosamente en el sistema
                            SIMJ!
                        </p>
                        <p>
                            Utiliza el menú lateral para navegar entre las
                            diferentes secciones.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <TaskCalendarBoard />
            </div>
        </div>
    </AdminLTELayout>
</template>

<style scoped>
.small-box {
    border-radius: 4px;
    margin-bottom: 20px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow:
        0 1px 3px rgba(0, 0, 0, 0.12),
        0 1px 2px rgba(0, 0, 0, 0.24);
    border-radius: 2px;
    background: #fff;
}

.small-box > .inner {
    background: inherit;
    padding: 10px 15px 10px 15px;
}

.small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box p {
    font-size: 15px;
    margin: 0;
    color: rgba(0, 0, 0, 0.5);
}

.small-box .icon {
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0, 0, 0, 0.1);
}

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff !important;
}

.bg-success {
    background-color: #28a745 !important;
    color: #fff !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: #fff !important;
}
</style>
