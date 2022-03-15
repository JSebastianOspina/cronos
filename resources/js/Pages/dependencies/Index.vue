<template>
    <Head title="Todas las dependencias"/>

    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-5">
                            <h1 class="text-2xl font-bold">Gestionar dependencias</h1>

                            <button
                                v-if="isAdmin"
                                @click="openCreateUserModal"
                                class="p-2 text-center bg-principal text-white mx-1 rounded">Crear nueva
                            </button>
                        </div>

                        <table class="items-center bg-transparent border-collapse mx-auto ">
                            <thead>
                            <tr>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Nombre dependencia
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
                                    {{ dependency.name }} ({{ dependency.monitoring_type }})
                                </td>

                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{ dependency.users.length }}
                                </td>
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-center ">
                                    <Link :href="route('dependencies.users.index', {dependency:dependency.id})"
                                          class="p-2 text-center bg-green-600 text-white mx-1 rounded">Gestionar
                                        usuarios
                                    </Link>

                                    <Link :href="route('records.filter', {dependency:dependency.id})"
                                          class="p-2 text-center bg-principal text-white mx-1 rounded">Gestionar eventos
                                    </Link>

                                    <button
                                        @click="showConfirmDeleteModal(dependency.id)"
                                        v-if="isAdmin"
                                        class="p-2 text-center bg-red-600 text-white mx-1 rounded">Borrar
                                        dependencia
                                    </button>

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
import Swal from 'sweetalert2';

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
        dependencies: Array,
        isAdmin: Boolean
    },

    methods: {

        openCreateUserModal: async function () {

            const dependencyName = await this.askForDependencyNameWithModal();
            if (dependencyName === undefined) {
                return;
            }

            const monitoringType = await this.askForMonitoringTypeWithModal();
            if (monitoringType === undefined) {
                return;
            }
            const monitorsFunctions = await this.askForMonitorsFunctionsWithModal();
            if (monitorsFunctions === undefined) {
                return;
            }

            //Conseguir URL
            let url = route('dependencies.store');

            //Información a enviar
            let data = {
                name: dependencyName,
                monitoringType,
                monitorsFunctions
            }

            try {
                let response = await axios.post(url, data);
                let modal = await Swal.fire('Creación de dependencia', ' Dependencia creada exitosamente', 'success')
                location.reload();

            } catch (e) {
                //Disparar ventana con información del error
                Swal.fire('Ha ocurrido un error', e.response.data.message, 'error')
            }


        },
        askForDependencyNameWithModal: async function () {
            const {value} = await Swal.fire({
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Confirmar nombre',
                title: 'Crear una nueva dependencia',
                input: 'text',
                inputLabel: 'Nombre de la dependencia',
                inputPlaceholder: 'Ayudas educativas'
            });

            return value;
        },
        askForMonitoringTypeWithModal: async function () {
            const {value} = await Swal.fire({
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Confirmar tipo de monitoría',
                title: '¿Que tipo de monitorías se prestan?',
                input: 'select',
                inputOptions: {
                    'academica': 'Académicas',
                    'administrativa': 'Administrativas',
                },
                inputLabel: 'Tipo de monitorías',
                inputPlaceholder: 'Selecciona el tipo de monitorías',

            });

            return value;
        },
        askForMonitorsFunctionsWithModal: async function () {
            const {value} = await Swal.fire({
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Guardar y crear dependencia',
                title: 'Funciones de los monitores',
                input: 'text',
                inputLabel: '¿Que funciones desempeñan los monitores? (separadas por comas)',
                inputPlaceholder: 'Organización de libros, prestamos, mantenimiento'
            });

            return value;
        },

        deleteDependency: async function (dependencyId) {
            let url = route('dependencies.destroy', {
                'dependency': dependencyId
            });
            try {
                let request = await axios.delete(url);
                await Swal.fire('Proceso exitoso', request.data, "success");
                location.reload();

            } catch (e) {
                //Disparar ventana con información del error
                Swal.fire('Ha ocurrido un error', e.response.data, 'error')
            }
        },

        showConfirmDeleteModal: async function (dependencyId) {
            const modal = await Swal.fire({
                title: 'Cuidado, esta acción es irreversible',
                text: 'Todos los usuarios y horarios relacionadas a la dependencia serán eliminados',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Entiendo el riesgo, borrar dependencia',
                allowOutsideClick: false,
            })

            if (modal.isConfirmed) {
                this.deleteDependency(dependencyId);
            }

        }

    }
}
</script>
