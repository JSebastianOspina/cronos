<template>
    <Head title="Gestionar agendas del usuario"/>

    <BreezeAuthenticatedLayout>

        <div class="py-12 px-6">
            <div class=" mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Administrar registros del {{ today }}
                            </h2>

                        </div>
                        <div class="overflow-auto" id="tableContainer">
                            <table class="items-center bg-transparent border-collapse mx-auto ">
                                <thead>
                                <tr>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Monitor
                                    </th>

                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Hora de inicio
                                    </th>

                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Hora de salida
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Check in
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Check out
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Hora de inicio (SUPERVISOR)
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                        Hora de salida (SUPERVISOR)
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Acciones
                                    </th>

                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="record in records">
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ record.monitor.name }}
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ record.start_planned_date }}
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ record.end_planned_date }}
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ record.start_monitor_date }}
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">
                                        {{ record.end_monitor_date }}
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">
                                        <div class="flex justify-center">
                                            <input type="time" v-model="record.start_approved_date"
                                                   @input="updateSupervisorHour(record.id,'start',record.start_approved_date)"
                                                   class="px-3 py-2 rounded  border-indigo-400">
                                        </div>

                                    </td>

                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">
                                        <div class="flex justify-center">
                                            <input type="time" v-model="record.end_approved_date"
                                                   @input="updateSupervisorHour(record.id,'end',record.end_approved_date)"
                                                   class="px-3 py-2 rounded  border-indigo-400">
                                        </div>

                                    </td>

                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-center ">

                                        <button
                                            @click="deleteEvent(record.id)"
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
        today: String,
        records: Array,
    },

    methods: {

        async updateSupervisorHour(recordId, typeOfHour, value) {
            console.log(recordId, typeOfHour, value);
            let url = route('records.updateSupervisorHour', {record: recordId});
            let request = await axios.patch(url, {
                type: typeOfHour,
                hour: value
            });
            console.log(request.data);

        },
        getWeekDay: function (weekDayNumber) {
            let days = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
            return days[weekDayNumber - 1];
        },


    }
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


