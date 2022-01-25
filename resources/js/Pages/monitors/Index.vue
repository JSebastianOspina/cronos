<template>
    <Head title="Gestionar monitores"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestionar monitores
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <table class="items-center bg-transparent border-collapse mx-auto ">
                    <thead>
                    <tr>
                        <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Nombre
                        </th>

                        <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Email
                        </th>
                        <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                            Acciones
                        </th>

                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="monitor in monitors">
                        <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                            {{ monitor.name }}
                        </td>

                        <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                            {{ monitor.email }}
                        </td>
                        <td class="px-6 align-middle whitespace-nowrap p-4 text-center ">
                            <Link :href="route('dependencies.users.index', {dependency:monitor.id})"
                                  class="p-2 text-center bg-green-600 text-white mx-1 rounded">Gestionar
                                usuarios
                            </Link>


                            <button
                                @click="deleteDependency(monitor.id)"
                                class="p-2 text-center bg-red-600 text-white mx-1 rounded">Borrar
                                dependencia
                            </button>

                        </td>

                    </tr>

                    </tbody>

                </table>

            </div>
        </div>
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

    props: {
        monitors: Array
    },

    methods: {}
}
</script>
