<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const navCollapsed = ref(false);
</script>

<template>
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-dark bg-dark">
            <div class="container-fluid">
                <Link
                    class="navbar-brand text-white"
                    :href="route('dashboard')"
                >
                    <i class="fas fa-tachometer-alt me-2"></i>SIMJ Panel
                </Link>

                <button
                    class="navbar-toggler"
                    type="button"
                    @click="navCollapsed = !navCollapsed"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div
                    class="collapse navbar-collapse"
                    :class="{ show: navCollapsed }"
                >
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle text-white"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                            >
                                <i class="fas fa-user-circle me-1"></i>
                                {{ $page.props.auth.user.name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <Link
                                        class="dropdown-item"
                                        :href="route('profile.edit')"
                                    >
                                        Perfil
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        class="dropdown-item"
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Cerrar sesión
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="d-flex">
            <aside
                class="main-sidebar sidebar-dark-primary bg-primary vh-100 p-3"
                style="width: 250px"
            >
                <div class="sidebar-brand mb-4 text-white">
                    <Link
                        :href="route('dashboard')"
                        class="text-decoration-none text-white"
                    >
                        <span class="fs-5 fw-bold">
                            <i class="fas fa-tools me-2"></i>SIMJ
                        </span>
                    </Link>
                </div>

                <nav class="nav flex-column">
                    <Link
                        class="nav-link text-white"
                        :href="route('dashboard')"
                    >
                        <i class="fas fa-home me-2"></i>Dashboard
                    </Link>
                    <Link
                        class="nav-link text-white"
                        :href="route('users.index')"
                    >
                        <i class="fas fa-users me-2"></i>Usuarios
                    </Link>
                    <Link
                        class="nav-link text-white"
                        :href="route('projects.index')"
                    >
                        <i class="fas fa-folder-open me-2"></i>Proyectos
                    </Link>
                    <Link
                        class="nav-link text-white"
                        :href="route('tasks.index')"
                    >
                        <i class="fas fa-calendar-check me-2"></i>Tareas
                    </Link>
                    <Link
                        class="nav-link text-white"
                        :href="route('reports.tasks')"
                    >
                        <i class="fas fa-file-pdf me-2"></i>Informes
                    </Link>
                </nav>
            </aside>

            <div class="content-wrapper flex-fill p-4">
                <section class="content-header mb-3">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <slot name="header" />
                            </div>
                        </div>
                    </div>
                </section>

                <section class="content">
                    <div class="container-fluid">
                        <slot />
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
