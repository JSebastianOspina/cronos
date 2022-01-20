<template>
    <Head title="Todas las dependencias"/>

    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-5">
                            <h1 class="text-2xl font-bold">Gestionar dependencias</h1>
                            <Link :href="route('dependencies.create')"
                                  class="p-2 text-center bg-principal text-white mx-1 rounded">Crear nueva dependencia
                            </Link>
                        </div>

                        <table class="items-center bg-transparent border-collapse mx-auto ">
                            <thead>
                            <tr>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Nombre dependencia
                                </th>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Identificador
                                </th>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Número de usuarios
                                </th>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                    Acciones
                                </th>

                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="dependency in dependencies">
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{ dependency.name }}
                                </td>
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{ dependency.identifier }}
                                </td>
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{ dependency.users.length }}
                                </td>
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-center ">
                                    <Link :href="'/admin/dependencies/'+dependency.id+'/users'"
                                          class="p-2 text-center bg-green-600 text-white mx-1 rounded">Gestionar
                                        usuarios
                                    </Link>

                                    <form class="inline"
                                          @submit.prevent="form.delete('/admin/dependencies/'+dependency.id)">
                                        <button class="p-2 text-center bg-red-600 text-white mx-1 rounded">Borrar
                                            dependencia
                                        </button>
                                    </form>

                                </td>

                            </tr>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head} from '@inertiajs/inertia-vue3';
import {useForm} from '@inertiajs/inertia-vue3'
import {Link} from '@inertiajs/inertia-vue3'
export default {
    setup() {
        const form = useForm({});
        return {form};
    },
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Link
    },
    props: {
        dependencies: Array
    },
}
</script>
