<template>
    <Head title="Error"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                No tienes los permisos suficientes para acceder a esta página
            </h2>
            <p>
                Si crees que se trata de un error, comunicate con un administrador.
            </p>
        </template>

    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head} from '@inertiajs/inertia-vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },

    data: () => {
        return {}
    },

    methods: {}
}
</script>
