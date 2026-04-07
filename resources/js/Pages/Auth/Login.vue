<script setup>
import { ref } from "vue";
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});
const showPassword = ref(false);

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión" />

        <div v-if="status" class="alert alert-success text-sm">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <InputLabel for="email" value="Correo electrónico" />
                <div class="input-group">
                    <TextInput
                        id="email"
                        type="email"
                        class="form-control"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Correo electrónico"
                    />
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <div class="mb-3">
                <InputLabel for="password" value="Contraseña" />
                <div class="input-group">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="form-control"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Contraseña"
                    />
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        @click="showPassword = !showPassword"
                        :aria-label="
                            showPassword
                                ? 'Ocultar contraseña'
                                : 'Mostrar contraseña'
                        "
                    >
                        <i
                            :class="
                                showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'
                            "
                        ></i>
                    </button>
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <div class="row mb-3">
                <div class="col-8">
                    <div class="form-check">
                        <Checkbox
                            id="remember"
                            name="remember"
                            v-model:checked="form.remember"
                            class="form-check-input"
                        />
                        <label class="form-check-label ms-2" for="remember">
                            Recuérdame
                        </label>
                    </div>
                </div>
                <div class="col-4">
                    <PrimaryButton
                        class="w-100"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Iniciar sesión
                    </PrimaryButton>
                </div>
            </div>

            <p class="mb-0 text-center">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-decoration-none"
                >
                    ¿Olvidaste tu contraseña?
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
