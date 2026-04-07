<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <Link href="/" class="text-decoration-none">
                    <b>SIMJ</b> Panel
                </Link>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <p class="h4 mb-0">Acceso al sistema</p>
                </div>
                <div class="card-body login-card-body">
                    <p class="login-box-msg">
                        Inicie sesión para acceder al panel
                    </p>
                    <slot />
                </div>
            </div>

            <p class="mt-3 text-center text-muted">
                <small>Sistema de gestión de proyectos y tareas</small>
            </p>
        </div>
    </div>
</template>
