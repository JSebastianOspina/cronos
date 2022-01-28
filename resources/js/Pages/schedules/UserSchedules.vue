<template>
    <Head title="Gestionar agendas del usuario"/>

    <BreezeAuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Agendas del usuario {{ user.name }}
                            </h2>
                            <button
                                @click="openCreateEventModal"
                                class="p-2 text-center bg-principal text-white mx-1 rounded">Crear nuevo evento
                            </button>
                        </div>

                        <table class="items-center bg-transparent border-collapse mx-auto ">
                            <thead>
                            <tr>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Fecha de inicio
                                </th>

                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Hora de entrada
                                </th>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Hora de salida
                                </th>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    Tipo de evento
                                </th>
                                <th class="px-6 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                    Acciones
                                </th>

                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="schedule in schedules">
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{ schedule.date }}
                                </td>

                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{ schedule.start_hour }}
                                </td>
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{ schedule.end_hour }}
                                </td>
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-left ">
                                    {{
                                        schedule.type === 'unique' ? 'Unico' : 'Periodico (todos los ' + getWeekDay(schedule.day_of_week) + ')'
                                    }}
                                </td>
                                <td class="px-6 align-middle whitespace-nowrap p-4 text-center ">

                                    <button
                                        @click="deleteEvent(schedule.id)"
                                        class="p-2 text-center bg-red-600 text-white mx-1 rounded">Borrar evento
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
import Swal from 'sweetalert2';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },

    data: () => {
        return {}
    },
    props: {
        user: Object,
        schedules: Array,
    },

    methods: {
        getWeekDay: function (weekDayNumber) {
            let days = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
            return days[weekDayNumber - 1];
        },


        openCreateEventModal: async function () {
            const {value: formValues} = await Swal.fire({
                title: 'Nuevo evento',
                html:
                    '<div class="flex flex-col space-y-3">' +
                    '<div class="flex justify-between items-center "><label for="date">Fecha de inicio</label>' +
                    '<input id="date" class="px-3 py-2 rounded  border-indigo-400" type="date" min="2022-01-25"></div>' +
                    '<div class="flex justify-between items-center "><label for="start_hour">Hora de entrada</label>' +
                    '<input id="start_hour" class="px-3 py-2 rounded  border-indigo-400" type="time"></div>' +
                    '<div class="flex justify-between items-center "><label for="end_hour">Hora de salida</label>' +
                    '<input id="end_hour" class="px-3 py-2 rounded  border-indigo-400" type="time"></div>' +
                    '<div class="flex justify-between items-center "><label for="type">Selecciona el tipo de evento</label>' +
                    '<select  id="type" class="pl-3 pr-8 py-2 rounded border-indigo-400">' +
                    '<option value="unique">Unico</option>' +
                    '<option value="periodic">Periodico</option>' +
                    '</select>' +
                    '</div>',

                focusConfirm: false,
                confirmButtonText: 'Crear evento',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                allowOutsideClick: false,
                preConfirm: () => {
                    return {
                        date: document.getElementById('date').value,
                        start_hour: document.getElementById('start_hour').value,
                        end_hour: document.getElementById('end_hour').value,
                        type: document.getElementById('type').value,
                    }

                }
            })

            if (formValues) {
                let url = route('users.schedules.store', {user: this.user.id});

                try {
                    let request = await axios.post(url, formValues);
                    await Swal.fire('Proceso exitoso', request.data, "success");
                    location.reload();

                } catch (e) {
                    //Disparar ventana con información del error
                    Swal.fire('Ha ocurrido un error', e.response.data, 'error')
                }


            }
        },

        deleteEvent: async function (eventId) {
            let url = route('schedules.destroy', {
                'schedule': eventId
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
    }
}
</script>
