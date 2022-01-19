<template>
    <Head title="Gestionar usuarios "/>

    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-5">
                            <h1 class="text-2xl font-bold">Gestionar usuarios </h1>
                        </div>

                        <div class="overflow-auto" id="tableContainer">
                            <table class="items-center bg-transparent border-collapse mx-auto ">
                                <thead>
                                <tr>
                                    <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Nombre
                                    </th>
                                    <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Correo electrónico
                                    </th>

                                    <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Rol
                                    </th>
                                    <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Acciones
                                    </th>

                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="user in users">
                                    <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ user.email }}
                                    </td>

                                    <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ getUserRole(user.role) }}
                                    </td>

                                    <td class="px-6 align-middle whitespace-nowrap p-4 text-center ">

                                        <button class="p-2 text-center bg-green-600 text-white mx-1 rounded"
                                                @click="changeRole(1, user.id)">Hacer
                                            monitor
                                        </button>

                                        <button class="p-2 text-center bg-yellow-400 text-white mx-1 rounded"
                                                @click="changeRole(2, user.id)">Hacer
                                            supervisor
                                        </button>
                                        <button class="p-2 text-center bg-blue-600 text-white mx-1 rounded"
                                                @click="changeRole(3, user.id)">Hacer
                                            administrador
                                        </button>

                                        <button class="p-2 text-center bg-red-600 text-white mx-1 rounded"
                                                @click="changeRole(0, user.id)">Remover
                                            privilegios
                                        </button>

                                    </td>

                                </tr>

                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head} from '@inertiajs/inertia-vue3';
import {Link} from '@inertiajs/inertia-vue3'
import Swal from 'sweetalert2';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Link
    },
    methods: {
        async changeRole(role, user_id) {
            let request = await axios.post(route('users.roles.update', {role, user_id}));
            if (request.status === 201) {
                window.location.reload();
            } else {
                Swal.fire({
                    title: 'Ups',
                    text: 'Ha ocurrido un error, el recurso no ha podido ser actualizado',
                    icon: "error",
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    window.location.reload();
                });
            }


        },
        getUserRole(role) {
            if (role === 0) {
                return 'Usuario sin privilegios';
            }
            if (role === 1) {
                return 'Monitor';
            }
            if (role === 2) {
                return 'Supervisor';
            }
            if (role === 3) {
                return 'Administrador';
            }
        }
    },
    props: {
        users: Array
    },
}
</script>

<style>
#tableContainer::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
#tableContainer {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
