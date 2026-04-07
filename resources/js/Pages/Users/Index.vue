<script setup>
import AdminLTELayout from "@/Layouts/AdminLTELayout.vue";
import { Head } from "@inertiajs/vue3";
import { reactive, ref, onMounted } from "vue";
import axios from "axios";

const users = ref([]);
const meta = reactive({ current_page: 1, last_page: 1, total: 0 });
const filter = ref("");
const loading = ref(false);
const showModal = ref(false);
const editing = ref(false);
const form = reactive({
    id: null,
    name: "",
    email: "",
    password: "",
    is_admin: false,
});
const errors = reactive({});
const alert = reactive({ type: "", message: "" });

const resetForm = () => {
    form.id = null;
    form.name = "";
    form.email = "";
    form.password = "";
    form.is_admin = false;
    Object.keys(errors).forEach((key) => delete errors[key]);
    editing.value = false;
};

const openModal = (user = null) => {
    resetForm();

    if (user) {
        form.id = user.id;
        form.name = user.name;
        form.email = user.email;
        form.password = "";
        form.is_admin = user.is_admin;
        editing.value = true;
    }

    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const loadUsers = async (page = 1) => {
    loading.value = true;

    try {
        const response = await axios.get(route("users.list"), {
            params: { page, filter: filter.value },
        });

        users.value = response.data.data;
        meta.current_page = response.data.current_page;
        meta.last_page = response.data.last_page;
        meta.total = response.data.total;
    } catch (error) {
        alert.type = "danger";
        alert.message = "No se pudieron cargar los usuarios.";
    } finally {
        loading.value = false;
    }
};

const saveUser = async () => {
    loading.value = true;
    Object.keys(errors).forEach((key) => delete errors[key]);
    alert.type = "";
    alert.message = "";

    try {
        const payload = {
            name: form.name,
            email: form.email,
            password: form.password,
            is_admin: form.is_admin,
        };

        if (editing.value) {
            await axios.put(route("users.update", { user: form.id }), payload);
            alert.type = "success";
            alert.message = "Usuario actualizado correctamente.";
        } else {
            await axios.post(route("users.store"), payload);
            alert.type = "success";
            alert.message = "Usuario creado correctamente.";
        }

        closeModal();
        await loadUsers(meta.current_page);
    } catch (error) {
        if (error.response?.status === 422) {
            const responseErrors = error.response.data.errors || {};
            Object.assign(errors, responseErrors);
        } else {
            alert.type = "danger";
            alert.message = "Error al guardar el usuario.";
        }
    } finally {
        loading.value = false;
    }
};

const deleteUser = async (user) => {
    if (!confirm(`¿Eliminar al usuario ${user.name}?`)) {
        return;
    }

    loading.value = true;
    alert.type = "";
    alert.message = "";

    try {
        await axios.delete(route("users.destroy", { user: user.id }));
        alert.type = "success";
        alert.message = "Usuario eliminado correctamente.";
        await loadUsers(meta.current_page);
    } catch (error) {
        alert.type = "danger";
        alert.message = "No se pudo eliminar el usuario.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadUsers();
});
</script>

<template>
    <Head title="Usuarios" />

    <AdminLTELayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-0">Gestión de Usuarios</h1>
                    <p class="text-muted mb-0">
                        Administra los usuarios de la aplicación y define quién
                        es administrador.
                    </p>
                </div>
                <button
                    class="btn btn-primary"
                    type="button"
                    @click="openModal()"
                >
                    <i class="fas fa-plus me-2"></i>Nuevo usuario
                </button>
            </div>
        </template>

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

                <div class="row mb-3">
                    <div class="col-md-6">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Buscar por nombre o email"
                            v-model="filter"
                            @keyup.enter="loadUsers(1)"
                        />
                    </div>
                    <div class="col-md-2 d-grid">
                        <button
                            class="btn btn-outline-secondary"
                            type="button"
                            @click="loadUsers(1)"
                        >
                            Buscar
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Correo electrónico</th>
                                <th>Administrador</th>
                                <th>Creado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.length === 0">
                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>
                                    <span
                                        class="badge"
                                        :class="
                                            user.is_admin
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{ user.is_admin ? "Sí" : "No" }}
                                    </span>
                                </td>
                                <td>
                                    {{
                                        new Date(
                                            user.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-sm btn-light me-2"
                                        type="button"
                                        @click="openModal(user)"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        type="button"
                                        @click="deleteUser(user)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav v-if="meta.last_page > 1" aria-label="Paginación">
                    <ul class="pagination justify-content-end mb-0">
                        <li
                            class="page-item"
                            :class="{ disabled: meta.current_page === 1 }"
                        >
                            <button
                                class="page-link"
                                type="button"
                                @click="loadUsers(meta.current_page - 1)"
                                :disabled="meta.current_page === 1"
                            >
                                Anterior
                            </button>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">
                                Página {{ meta.current_page }} de
                                {{ meta.last_page }}
                            </span>
                        </li>
                        <li
                            class="page-item"
                            :class="{
                                disabled: meta.current_page === meta.last_page,
                            }"
                        >
                            <button
                                class="page-link"
                                type="button"
                                @click="loadUsers(meta.current_page + 1)"
                                :disabled="meta.current_page === meta.last_page"
                            >
                                Siguiente
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div
            class="modal fade show"
            tabindex="-1"
            role="dialog"
            v-if="showModal"
            style="display: block; background: rgba(0, 0, 0, 0.4)"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ editing ? "Editar usuario" : "Crear usuario" }}
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
                            <label class="form-label">Nombre</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="form-control"
                            />
                            <div
                                v-if="errors.name"
                                class="text-danger small mt-1"
                            >
                                {{ errors.name[0] }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="form-control"
                            />
                            <div
                                v-if="errors.email"
                                class="text-danger small mt-1"
                            >
                                {{ errors.email[0] }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="form-control"
                                :placeholder="
                                    editing
                                        ? 'Dejar en blanco para no cambiar'
                                        : ''
                                "
                            />
                            <div
                                v-if="errors.password"
                                class="text-danger small mt-1"
                            >
                                {{ errors.password[0] }}
                            </div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="isAdmin"
                                v-model="form.is_admin"
                            />
                            <label class="form-check-label" for="isAdmin"
                                >Administrador</label
                            >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="closeModal"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            @click="saveUser"
                            :disabled="loading"
                        >
                            {{ editing ? "Actualizar" : "Crear" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLTELayout>
</template>
