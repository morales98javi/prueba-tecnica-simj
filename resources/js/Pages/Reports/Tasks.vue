<script setup>
import AdminLTELayout from "@/Layouts/AdminLTELayout.vue";
import { Head } from "@inertiajs/vue3";
import { reactive } from "vue";

const props = defineProps({
    projects: Array,
    users: Array,
});

const filters = reactive({
    project_id: "",
    user_id: "",
    from: "",
    to: "",
});

const buildQuery = () => {
    const params = new URLSearchParams();

    if (filters.project_id) params.append("project_id", filters.project_id);
    if (filters.user_id) params.append("user_id", filters.user_id);
    if (filters.from) params.append("from", filters.from);
    if (filters.to) params.append("to", filters.to);

    return params.toString();
};

const downloadPdf = () => {
    const query = buildQuery();
    const url = route("reports.tasks.pdf") + (query ? `?${query}` : "");
    window.open(url, "_blank");
};
</script>

<template>
    <Head title="Informes" />

    <AdminLTELayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-0">Informe de Tareas</h1>
                    <p class="text-muted mb-0">
                        Descarga un PDF con las tareas agrupadas por proyecto.
                    </p>
                </div>
            </div>
        </template>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-3">
                        <label class="form-label">Proyecto</label>
                        <select
                            class="form-select"
                            v-model="filters.project_id"
                        >
                            <option value="">Todos</option>
                            <option
                                v-for="project in props.projects"
                                :key="project.id"
                                :value="project.id"
                            >
                                {{ project.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Usuario</label>
                        <select class="form-select" v-model="filters.user_id">
                            <option value="">Todos</option>
                            <option
                                v-for="user in props.users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="filters.from"
                        />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="filters.to"
                        />
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button
                            class="btn btn-primary w-100"
                            type="button"
                            @click="downloadPdf"
                        >
                            <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLTELayout>
</template>
