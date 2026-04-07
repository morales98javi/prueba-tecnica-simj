<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <div class="invalid-feedback d-block">
            {{ message }}
        </div>
    </div>
</template>
