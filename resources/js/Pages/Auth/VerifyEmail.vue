<script setup>
import { computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent",
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificar correo electrónico" />

        <div class="mb-4 text-sm text-muted">
            Gracias por registrarte. Antes de comenzar, por favor verifica tu
            correo electrónico haciendo clic en el enlace que te enviamos. Si no
            lo recibiste, te enviaremos otro.
        </div>

        <div
            class="mb-4 text-sm font-medium text-success"
            v-if="verificationLinkSent"
        >
            Se ha enviado un nuevo enlace de verificación a tu correo
            electrónico.
        </div>

        <form @submit.prevent="submit">
            <div class="d-flex justify-content-between align-items-center">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reenviar correo de verificación
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-decoration-none"
                >
                    Cerrar sesión
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
