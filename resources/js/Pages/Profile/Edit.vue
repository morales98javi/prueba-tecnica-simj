<script setup>
import AdminLTELayout from "@/Layouts/AdminLTELayout.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AdminLTELayout>
        <template #header>Perfil</template>

        <div class="profile-page">
            <section class="profile-panel">
                <div class="panel-header">
                    <h2>Información del perfil</h2>
                    <p>Actualiza tu nombre y correo electrónico.</p>
                </div>
                <div class="panel-body">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="w-100"
                    />
                </div>
            </section>

            <section class="profile-panel">
                <div class="panel-header">
                    <h2>Actualizar contraseña</h2>
                    <p>
                        Asegúrate de usar una contraseña segura y única para tu
                        cuenta.
                    </p>
                </div>
                <div class="panel-body">
                    <UpdatePasswordForm class="w-100" />
                </div>
            </section>
        </div>
    </AdminLTELayout>
</template>

<style scoped>
.profile-page {
    display: flex;
    flex-direction: column;
    gap: 1.4rem;
}

.profile-panel {
    background: #f8fbff;
    border-radius: 1.1rem;
    padding: 1.5rem;
    box-shadow: 0 15px 25px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.05);
}

.panel-header h2 {
    font-size: 1.35rem;
    margin-bottom: 0.15rem;
}

.panel-header p {
    margin: 0;
    color: #536072;
    font-size: 0.95rem;
}

.panel-body {
    margin-top: 1.2rem;
    background: #fff;
    border-radius: 0.8rem;
    padding: 1.4rem;
    border: 1px solid rgba(15, 23, 42, 0.05);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
}
</style>
