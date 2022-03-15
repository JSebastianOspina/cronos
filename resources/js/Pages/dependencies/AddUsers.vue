<template>
    <Head title="Todas las dependencias"/>

    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-end w-full">
                            <Link :href="route('dependencies.index')"
                                  class="p-2 text-center bg-principal text-white mx-1 rounded"> Volver a las
                                dependencias
                            </Link>
                        </div>
                        <h1 class="text-3xl text-center font-semibold text-principal mb-3">Usuarios de la dependencia
                            {{ dependency.name }}</h1>

                        <table class="items-center bg-transparent border-collapse mx-auto ">
                            <thead>
                            <tr>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Nombre
                                </th>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Email
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
                                    {{ user.pivot.role === 0 ? 'Monitor' : 'Supervisor' }}
                                </td>

                                <td class="px-6 align-middle whitespace-nowrap p-4 text-center ">

                                    <Link
                                        class="px-2 py-2.5 text-center bg-principal text-white mx-1 rounded"
                                        :href="route('users.schedules.show',{
                                              user: user.id,
                                              dependency:dependency.id
                                          })">Gestionar horario
                                    </Link>

                                    <button class="p-2 text-center bg-yellow-400 text-white mx-1 rounded"
                                            @click="generateReport(dependency.id, user.id)">Descargar reporte
                                    </button>

                                    <button class="p-2 text-center bg-red-600 text-white mx-1 rounded"
                                            @click="showConfirmDeleteModal(dependency.id, user.id)">Eliminar
                                    </button>

                                </td>

                            </tr>

                            </tbody>

                        </table>

                        <div class="grid grid-cols-1 mt-8">

                            <!-- Añadir usuarios a la dependnecia-->
                            <div>
                                <h2 class="text-xl font-bold text-principal mb-3">Añadir un nuevo
                                    usuario
                                </h2>


                                <label class="text-gray-600 text-lg" for="user">Selecciona un usuario para añadirlo
                                    a la
                                    dependencia</label>
                                <select id="user" v-model="userId"
                                        class="block w-full rounded-lg text-gray-600 mb-2">
                                    <option disabled>Seleccione un usuario</option>
                                    <option v-for="user in allUsers" :value="user.id">{{ user.name }}</option>
                                </select>

                                <label class="text-gray-600 text-lg" for="role">Selecciona el rol del usuario</label>
                                <select id="role" v-model="roleId"
                                        class="block w-full rounded-lg text-gray-600 mb-2">
                                    <option value="0">Monitor</option>
                                    <option value="1">Supervisor</option>
                                </select>

                                <button
                                    :disabled="isLoading"
                                    class="bg-principal text-white font-bold p-3 rounded-md mt-3 text-center mx-auto inline-block w-full uppercase"
                                    @click="addUserToDependency"
                                >Añadir usuario a la dependencia
                                </button>

                            </div>

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
import {useForm} from '@inertiajs/inertia-vue3'
import {Link} from '@inertiajs/inertia-vue3'
import Swal from "sweetalert2";

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Link
    },
    props: {
        dependency: Object,
        users: Array,
        allUsers: Array,
    },

    methods: {

        generateReport: async function (dependencyId, userId) {
            let {value: startDate, isStartDateDismissed} = await Swal.fire({
                title: 'Por favor, selecciona la fecha de inicio',
                html: '<input id="startDate" type="date" class="swal2-input">',
                confirmButtonText: 'Seleccionar fecha',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                allowOutsideClick: false,
                allowEnterKey: true,
                preConfirm: () => {
                    return document.getElementById('startDate').value;
                }
            });
            //User canceled, stop execution
            if (isStartDateDismissed) {
                return;
            }

            let {value: endDate, isEndDateDismissed} = await Swal.fire({
                title: 'Por favor, selecciona la fecha de finalización',
                html: '<input id="endDate" type="date" class="swal2-input">',
                confirmButtonText: 'Seleccionar fecha',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                allowOutsideClick: false,
                allowEnterKey: true,
                preConfirm: () => {
                    return document.getElementById('endDate').value
                }
            });

            //User canceled, stop execution

            if (isEndDateDismissed) {
                return;
            }

            window.open(route('downloadUserDependencyRecords', {
                dependencyId,
                userId
            }) + `?startDate=${startDate}&endDate=${endDate}`);


        },
        addUserToDependency: async function () {

            this.isLoading = true;
            let url = route('dependencies.users.store', {
                dependency: this.dependency.id
            });

            let data = {
                userId: this.userId,
                roleId: this.roleId,
            }
            try {
                let request = await axios.post(url, data);
                await Swal.fire('Proceso exitoso', request.data, "success");
                this.isLoading = false;
                location.reload();
            } catch (e) {
                //Disparar ventana con información del error
                this.isLoading = false;
                Swal.fire('Ha ocurrido un error', e.response.data, 'error')
            }
        },
        showConfirmDeleteModal: async function (dependencyId, userId) {
            const modal = await Swal.fire({
                title: 'Cuidado, esta acción es irreversible',
                text: 'El usuario será eliminado y todos los registros asociados también',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Entiendo el riesgo, eliminar usuario',
                allowOutsideClick: false,
            })

            if (modal.isConfirmed) {
                this.deleteUserFromDependency(dependencyId, userId);
            }

        },

        deleteUserFromDependency: async function (dependencyId, userId) {
            let url = route('dependencies.users.destroy', {
                'dependency': dependencyId,
                'user': userId
            });
            try {
                let request = await axios.delete(url);
                await Swal.fire('Proceso exitoso', request.data, "success");
                location.reload();

            } catch (e) {
                //Disparar ventana con información del error
                Swal.fire('Ha ocurrido un error', e.response.data, 'error')
            }
        }
    },

    data() {
        return {
            userId: 0,
            roleId: 0,
            isLoading: false,
        }
    }


}
</script>
