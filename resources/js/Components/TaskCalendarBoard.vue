<script setup>
import {
    nextTick,
    onBeforeUnmount,
    onMounted,
    reactive,
    ref,
    watch,
} from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin, { Draggable } from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";

const DEFAULT_DURATION_MINUTES = 60;
const PROJECT_COLORS = [
    "#0d6efd",
    "#6610f2",
    "#198754",
    "#d63384",
    "#fd7e14",
    "#0dcaf0",
    "#6c757d",
];

const page = usePage();
const currentUser = page.props.auth.user;

const projects = ref([]);
const users = ref([]);
const tasks = ref([]);
const selectedUser = ref(currentUser?.id ?? null);
const loading = ref(false);
const showModal = ref(false);
const modalMode = ref("create");
const calendarElement = ref(null);
const externalElement = ref(null);

const errors = reactive({});
const alert = reactive({
    type: "",
    message: "",
});

const showReportModal = ref(false);
const reportForm = reactive({
    from: "",
    to: "",
    project_id: "",
    user_id: "",
});

const showProjectForm = ref(false);
const newProjectName = ref("");
const projectCreationLoading = ref(false);
const projectCreationErrors = reactive({
    name: [],
});

const CALENDAR_VIEWS = {
    week: "timeGridWeek",
    day: "timeGridDay",
    manage: "listWeek",
};

const activeCalendarView = ref("week");

let calendar = null;
let draggable = null;

const getProjectColor = (projectId) => {
    if (!projectId) {
        return PROJECT_COLORS[0];
    }

    const index = Math.abs(Number(projectId)) % PROJECT_COLORS.length;
    return PROJECT_COLORS[index];
};

const escapeHtml = (value) => {
    return String(value || "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
};

const enrichProject = (project) => ({
    ...project,
    color: getProjectColor(project.id),
    creatorName: project.creator?.name ?? "Sin creador",
    formattedLastUsed: project.last_used_at
        ? new Date(project.last_used_at).toLocaleDateString("es-ES", {
              day: "2-digit",
              month: "2-digit",
              year: "numeric",
          })
        : "Sin uso",
    tasksCount: project.tasks_count ?? 0,
});

const enrichTaskEvent = (task) => {
    const color = getProjectColor(task.project?.id);
    return {
        ...task,
        backgroundColor: color,
        borderColor: color,
        textColor: "#fff",
        rawStart: task.start,
        rawEnd: task.end,
    };
};

const padNumber = (value) => String(value).padStart(2, "0");

const formatLocalDateTimeValue = (date) => {
    if (!date) {
        return "";
    }

    return `${date.getFullYear()}-${padNumber(date.getMonth() + 1)}-${padNumber(
        date.getDate(),
    )}T${padNumber(date.getHours())}:${padNumber(date.getMinutes())}`;
};

const addMinutesToDateTimeValue = (
    dateTimeValue,
    minutes = DEFAULT_DURATION_MINUTES,
) => {
    if (!dateTimeValue) {
        return "";
    }

    const date = new Date(dateTimeValue);
    date.setMinutes(date.getMinutes() + minutes);
    return formatLocalDateTimeValue(date);
};

const parseDateTimeValue = (value) => {
    if (!value) {
        return null;
    }

    return new Date(value);
};

const form = reactive({
    project_id: null,
    user_id: currentUser?.id ?? null,
    start_datetime: formatLocalDateTimeValue(new Date()),
    end_datetime: addMinutesToDateTimeValue(
        formatLocalDateTimeValue(new Date()),
        DEFAULT_DURATION_MINUTES,
    ),
    notes: "",
});

const ensureEndAfterStart = () => {
    const start = parseDateTimeValue(form.start_datetime);
    const end = parseDateTimeValue(form.end_datetime);

    if (!start || !end) {
        return;
    }

    if (end <= start) {
        form.end_datetime = addMinutesToDateTimeValue(
            form.start_datetime,
            DEFAULT_DURATION_MINUTES,
        );
    }
};

const clearErrors = () => {
    Object.keys(errors).forEach((key) => delete errors[key]);
};

const clearAlert = () => {
    alert.type = "";
    alert.message = "";
};

const resetForm = () => {
    form.project_id = null;
    form.user_id = selectedUser.value ?? currentUser?.id ?? null;
    const nowValue = formatLocalDateTimeValue(new Date());
    form.start_datetime = nowValue;
    form.end_datetime = addMinutesToDateTimeValue(
        nowValue,
        DEFAULT_DURATION_MINUTES,
    );
    form.notes = "";
    clearErrors();
};

const openModalForSlot = ({ startDateTime, projectId = null }) => {
    resetForm();
    modalMode.value = "create";
    form.project_id = projectId;
    form.start_datetime =
        startDateTime || formatLocalDateTimeValue(new Date());
    form.end_datetime = addMinutesToDateTimeValue(
        form.start_datetime,
        DEFAULT_DURATION_MINUTES,
    );
    showModal.value = true;
};

const isoToLocalDatetime = (value) => {
    if (!value) {
        return "";
    }

    return formatLocalDateTimeValue(new Date(value));
};

const openEventModal = (event) => {
    if (!event) {
        return;
    }

    const rawStart =
        event.extendedProps?.rawStart ?? event.start?.toISOString() ?? "";
    const rawEnd = event.extendedProps?.rawEnd ?? event.end?.toISOString() ?? "";

    modalMode.value = "view";
    form.project_id =
        event.extendedProps?.project?.id ?? event.project?.id ?? null;
    form.user_id =
        event.extendedProps?.user?.id ?? event.user?.id ?? selectedUser.value;
    form.start_datetime = isoToLocalDatetime(rawStart);
    form.end_datetime = isoToLocalDatetime(rawEnd);
    form.notes = event.extendedProps?.notes ?? "";
    clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
    modalMode.value = "create";
};

const openReportModal = () => {
    reportForm.from = "";
    reportForm.to = "";
    reportForm.project_id = "";
    reportForm.user_id = selectedUser.value ?? "";
    showReportModal.value = true;
};

const closeReportModal = () => {
    showReportModal.value = false;
};

const toggleProjectForm = () => {
    showProjectForm.value = !showProjectForm.value;
    if (!showProjectForm.value) {
        newProjectName.value = "";
        projectCreationErrors.name = [];
    }
};

const createProject = async () => {
    if (!newProjectName.value.trim()) {
        projectCreationErrors.name = ["El nombre es requerido."];
        return;
    }

    projectCreationLoading.value = true;
    projectCreationErrors.name = [];

    try {
        await axios.post(route("projects.store"), {
            name: newProjectName.value.trim(),
        });

        alert.type = "success";
        alert.message = "Proyecto creado correctamente.";
        await loadProjects();
        newProjectName.value = "";
        showProjectForm.value = false;
    } catch (error) {
        if (error.response?.status === 422) {
            projectCreationErrors.name =
                error.response.data.errors?.name ?? ["Nombre inválido"];
        } else {
            alert.type = "danger";
            alert.message = "No se pudo crear el proyecto.";
        }
    } finally {
        projectCreationLoading.value = false;
    }
};

const generateReport = () => {
    const params = new URLSearchParams();

    if (reportForm.from) {
        params.append("from", reportForm.from);
    }

    if (reportForm.to) {
        params.append("to", reportForm.to);
    }

    if (reportForm.project_id) {
        params.append("project_id", reportForm.project_id);
    }

    if (reportForm.user_id) {
        params.append("user_id", reportForm.user_id);
    }

    const url = `${route("reports.tasks.pdf")}${params.toString() ? `?${params}` : ""}`;
    window.open(url, "_blank");
    closeReportModal();
};

const buildEventContentHtml = (eventInfo) => {
    const title = escapeHtml(eventInfo.event.title);
    const duration = eventInfo.event.extendedProps?.duration_hours;
    const notes = escapeHtml(eventInfo.event.extendedProps?.notes);
    const durationText = duration
        ? `<div class="fc-event-duration">${duration} h</div>`
        : "";
    const notesText = notes ? `<div class="fc-event-notes">${notes}</div>` : "";

    return {
        html: `<div class="fc-event-content"><div class="fc-event-title">${title}</div>${durationText}${notesText}</div>`,
    };
};

const refreshEvents = () => {
    if (!calendar) {
        return;
    }

    calendar.removeAllEvents();
    tasks.value.forEach((task) => {
        calendar.addEvent(task);
    });
};

const initCalendar = () => {
    if (!calendarElement.value) {
        return;
    }

    if (calendar) {
        calendar.destroy();
    }

    calendar = new Calendar(calendarElement.value, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        locale: esLocale,
        initialView: CALENDAR_VIEWS[activeCalendarView.value],
        headerToolbar: false,
        views: {
            listWeek: {
                buttonText: "Gestión",
            },
        },
        droppable: true,
        editable: false,
        selectable: true,
        allDaySlot: false,
        eventReceive: (info) => {
            const startDateTime = formatLocalDateTimeValue(
                info.event.start ?? new Date(),
            );
            openModalForSlot({
                projectId: Number(info.draggedEl.dataset.projectId),
                startDateTime,
            });
            info.event.remove();
        },
        eventClick: (info) => {
            info.jsEvent?.preventDefault();
            openEventModal(info.event);
        },
        eventContent: buildEventContentHtml,
    });

    calendar.render();
    refreshEvents();
};

const setCalendarView = (viewKey) => {
    activeCalendarView.value = viewKey;
    if (calendar) {
        calendar.changeView(CALENDAR_VIEWS[viewKey]);
    }
};

const goPrev = () => {
    calendar?.prev();
};

const goNext = () => {
    calendar?.next();
};

const goToday = () => {
    calendar?.today();
};

const initExternalDrag = async () => {
    await nextTick();

    if (!externalElement.value) {
        return;
    }

    if (draggable) {
        draggable.destroy();
    }

    draggable = new Draggable(externalElement.value, {
        itemSelector: ".external-event",
        eventData(eventEl) {
            return {
                title: eventEl.dataset.title,
                backgroundColor: eventEl.dataset.color,
                borderColor: eventEl.dataset.color,
            };
        },
    });
};

const loadProjects = async () => {
    const response = await axios.get(route("projects.options"));
    projects.value = response.data.map(enrichProject);
    await initExternalDrag();
};

const loadUsers = async () => {
    const response = await axios.get(route("tasks.users"));
    users.value = response.data;

    if (!users.value.some((user) => user.id === selectedUser.value)) {
        selectedUser.value = currentUser?.id ?? users.value[0]?.id ?? null;
    }
};

const loadTasks = async (userId = selectedUser.value) => {
    if (!userId) {
        tasks.value = [];
        refreshEvents();
        return;
    }

    loading.value = true;

    try {
        const response = await axios.get(route("tasks.list"), {
            params: { user_id: userId },
        });

        tasks.value = response.data.map(enrichTaskEvent);
        refreshEvents();
    } catch (error) {
        alert.type = "danger";
        alert.message = "No se pudieron cargar las tareas.";
    } finally {
        loading.value = false;
    }
};

const saveTask = async () => {
    loading.value = true;
    clearErrors();
    clearAlert();
    ensureEndAfterStart();

    try {
        await axios.post(route("tasks.store"), {
            project_id: form.project_id,
            user_id: form.user_id,
            started_at: form.start_datetime,
            ended_at: form.end_datetime,
            notes: form.notes,
        });

        alert.type = "success";
        alert.message = "Tarea guardada correctamente.";

        closeModal();
        await loadProjects();

        const savedUserId = Number(form.user_id);
        if (selectedUser.value !== savedUserId) {
            selectedUser.value = savedUserId;
        } else {
            await loadTasks(savedUserId);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            Object.assign(errors, error.response.data.errors || {});
        } else {
            alert.type = "danger";
            alert.message = "Error al guardar la tarea.";
        }
    } finally {
        loading.value = false;
    }
};

watch(selectedUser, async (newUserId) => {
    form.user_id = newUserId;
    clearAlert();
    await loadTasks(newUserId);
});

watch(
    () => form.start_datetime,
    (newValue) => {
        if (modalMode.value !== "create" || !newValue) {
            return;
        }

        form.end_datetime = addMinutesToDateTimeValue(
            newValue,
            DEFAULT_DURATION_MINUTES,
        );
    },
);

watch(
    () => form.end_datetime,
    () => {
        if (modalMode.value !== "create") {
            return;
        }

        ensureEndAfterStart();
    },
);

onMounted(async () => {
    try {
        await loadUsers();
        await loadProjects();
        initCalendar();
        await loadTasks(selectedUser.value);
    } catch (error) {
        alert.type = "danger";
        alert.message = "No se pudo inicializar el calendario de tareas.";
    }
});

onBeforeUnmount(() => {
    if (calendar) {
        calendar.destroy();
    }

    if (draggable) {
        draggable.destroy();
    }
});

const findProjectName = (projectId) =>
    projects.value.find((project) => project.id === projectId)?.name ??
    "Proyecto seleccionado";

const findUserName = (userId) =>
    users.value.find((user) => user.id === userId)?.name ??
    currentUser?.name ??
    "Usuario asignado";
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div
                        v-if="alert.message"
                        class="alert"
                        :class="`alert-${alert.type}`"
                        role="alert"
                    >
                        {{ alert.message }}
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label"
                                >Usuario del calendario</label
                            >
                            <select
                                class="form-select"
                                v-model.number="selectedUser"
                            >
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 mb-4">
                                <div class="card project-column h-100">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="mb-0">Control de proyectos</h5>
                                            <small class="text-muted">Arrastra para asignar</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button
                                                v-if="currentUser?.is_admin"
                                                type="button"
                                                class="btn btn-primary btn-sm"
                                                @click="toggleProjectForm"
                                            >
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-sm"
                                                @click="openReportModal"
                                            >
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        v-if="showProjectForm && currentUser?.is_admin"
                                        class="project-form-card border-bottom"
                                    >
                                        <div class="d-flex gap-2 align-items-end">
                                            <div class="flex-fill">
                                                <label class="form-label mb-1">
                                                    Nombre del proyecto
                                                </label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="newProjectName"
                                                    :disabled="projectCreationLoading"
                                                />
                                                <div
                                                    v-if="projectCreationErrors.name.length"
                                                    class="text-danger small mt-1"
                                                >
                                                    {{ projectCreationErrors.name[0] }}
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-success"
                                                :disabled="projectCreationLoading"
                                                @click="createProject"
                                            >
                                                {{ projectCreationLoading ? "Guardando..." : "Crear" }}
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                @click="toggleProjectForm"
                                            >
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        id="external-projects"
                                        ref="externalElement"
                                        class="card-body project-list"
                                    >
                                    <div
                                        v-if="projects.length === 0"
                                        class="text-muted"
                                    >
                                        No hay proyectos disponibles.
                                    </div>
                                    <div
                                        v-for="project in projects"
                                        :key="project.id"
                                        class="project-card external-event text-white"
                                        :data-title="project.name"
                                        :data-project-id="project.id"
                                        :data-color="project.color"
                                        :style="{
                                            backgroundColor: project.color,
                                            borderColor: project.color,
                                        }"
                                    >
                                        <div class="project-card-header">
                                            <h6 class="mb-1">
                                                {{ project.name }}
                                            </h6>
                                            <span
                                                class="badge bg-white text-dark"
                                            >
                                                {{ project.tasksCount }} tareas
                                            </span>
                                        </div>
                                        <p class="mb-1 small">
                                            Creado por {{ project.creatorName }}
                                        </p>
                                        <p class="mb-0 small">
                                            Último uso:
                                            {{ project.formattedLastUsed }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="calendar-toolbar p-3">
                                        <div class="calendar-nav d-flex align-items-center gap-2">
                                            <button
                                                type="button"
                                                class="btn btn-outline-dark btn-sm"
                                                @click="goPrev"
                                            >
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-dark btn-sm"
                                                @click="goNext"
                                            >
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                @click="goToday"
                                            >
                                                Hoy
                                            </button>
                                        </div>
                                        <div class="calendar-view-switch btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                :class="{ active: activeCalendarView === 'week' }"
                                                @click="setCalendarView('week')"
                                            >
                                                Semana
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                :class="{ active: activeCalendarView === 'day' }"
                                                @click="setCalendarView('day')"
                                            >
                                                Día
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                :class="{ active: activeCalendarView === 'manage' }"
                                                @click="setCalendarView('manage')"
                                            >
                                                Gestión
                                            </button>
                                        </div>
                                    </div>
                                    <div class="position-relative">
                                        <div
                                            v-if="loading"
                                            class="position-absolute top-0 end-0 m-3 badge bg-light text-dark"
                                            style="z-index: 10"
                                        >
                                            Cargando...
                                        </div>
                                        <div
                                            ref="calendarElement"
                                            id="calendar"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="showModal"
        class="modal fade show"
        tabindex="-1"
        style="display: block; background: rgba(0, 0, 0, 0.4)"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{
                            modalMode === "create"
                                ? "Nueva tarea"
                                : "Detalles de la tarea"
                        }}
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        aria-label="Close"
                        @click="closeModal"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Inicio tarea</label>
                        <input
                            v-model="form.start_datetime"
                            type="datetime-local"
                            class="form-control"
                            :disabled="modalMode !== 'create'"
                        />
                        <div
                            v-if="errors.start_time || errors.date"
                            class="text-danger small mt-1"
                        >
                            {{ errors.date?.[0] || errors.start_time?.[0] }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Texto informativo</label>
                        <textarea
                            v-model="form.notes"
                            class="form-control"
                            rows="3"
                            :disabled="modalMode !== 'create'"
                        ></textarea>
                        <div v-if="errors.notes" class="text-danger small mt-1">
                            {{ errors.notes[0] }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fin tarea</label>
                        <input
                            v-model="form.end_datetime"
                            type="datetime-local"
                            class="form-control"
                            :disabled="modalMode !== 'create'"
                        />
                        <div
                            v-if="errors.end_time"
                            class="text-danger small mt-1"
                        >
                            {{ errors.end_time[0] }}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        v-if="modalMode === 'create'"
                        @click="closeModal"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        :disabled="loading"
                        v-if="modalMode === 'create'"
                        @click="saveTask"
                    >
                        Guardar tarea
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        v-else
                        @click="closeModal"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="showReportModal"
        class="modal fade show"
        tabindex="-1"
        style="display: block; background: rgba(0, 0, 0, 0.4)"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Opciones del informe</h5>
                    <button
                        type="button"
                        class="btn-close"
                        aria-label="Close"
                        @click="closeReportModal"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha Desde</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="reportForm.from"
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Hasta</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="reportForm.to"
                            />
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Proyecto</label>
                            <select
                                class="form-select"
                                v-model="reportForm.project_id"
                            >
                                <option value="">Todos los proyectos</option>
                                <option
                                    v-for="project in projects"
                                    :key="project.id"
                                    :value="project.id"
                                >
                                    {{ project.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuario</label>
                            <select
                                class="form-select"
                                v-model="reportForm.user_id"
                            >
                                <option value="">Todos los usuarios</option>
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="closeReportModal"
                    >
                        Cerrar
                    </button>
                    <button
                        type="button"
                        class="btn btn-success"
                        @click="generateReport"
                    >
                        Generar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fc-event-content {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    cursor: pointer;
}

.fc-event-title,
.fc-event-notes {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
}

.fc-event-notes {
    font-size: 0.7rem;
    opacity: 0.9;
    margin-top: 0.1rem;
    color: #f8f9fb;
}

.fc-event-duration {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.85);
}

.modal .form-control[disabled] {
    background-color: #f8f9fa;
}

.control-header {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.control-header h5 {
    font-weight: 600;
}

.project-column {
    height: 100%;
}

.project-column .card-body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-height: 0;
}

.project-list {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
    overflow-y: auto;
}

.project-card {
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow:
        0 4px 8px rgba(0, 0, 0, 0.08),
        0 2px 4px rgba(0, 0, 0, 0.06);
    cursor: grab;
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.project-card:active {
    transform: scale(0.98);
}

.project-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

 .calendar-toolbar {
    border-bottom: 1px solid #e9ecef;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.calendar-nav button,
.calendar-view-switch button {
    min-width: 50px;
}

.calendar-view-switch .btn.active {
    color: #fff !important;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.project-form-card {
    background: #f8f9fa;
    padding: 1rem;
}
</style>
