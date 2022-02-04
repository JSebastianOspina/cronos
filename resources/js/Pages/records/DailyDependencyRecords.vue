<template>
    <Head title="Gestionar agendas del usuario"/>

    <BreezeAuthenticatedLayout>

        <div class="py-12 px-6">
            <div class=" mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-5">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                                Administrar eventos del {{ today }}
                            </h2>

                            <p>
                                Para aprobar las horas de un monitor (registrar estas horas como válidas) de
                                clic al botón
                                "Aprobar".
                            </p>

                            <p class="mb-4">
                                Para cancelar la asistencia de un monitor (no contar estas horas) de clic al botón
                                "Cancelar".
                            </p>

                            <div class="flex flex-col">
                                <div class="flex items-center">
                                    <div class="bg-green-200 h-5 w-5 rounded-full mr-2"></div>
                                    <p>
                                        Esta monitoría se encuentra aprobada. Tanto el monitor como supervisor han
                                        registrado
                                        las horas que le corresponden.
                                    </p>
                                </div>
                                <div class="flex items-center">
                                    <div class="bg-red-200 h-5 w-5 rounded-full mr-2"></div>
                                    <p>
                                        Esta monitoría ha sido cancelada por el supervisor.
                                    </p>
                                </div>
                                <div class="flex items-center">
                                    <div
                                        class="bg-green-white border-2 border-gray-400 h-5 w-5 rounded-full mr-2"></div>
                                    <p>
                                        La monitoría está en proceso. El supervisor no ha registrado las dos horas
                                        correspondientes.
                                    </p>
                                </div>
                            </div>
                            <p class="mt-4">
                                TIP: Para desplazarse horizontalmente en la tabla mantenga oprimido shift mientras hace
                                scroll con la rueda del ratón.
                            </p>
                        </div>
                        <div class="overflow-auto" id="tableContainer">
                            <table class="items-center bg-transparent border-collapse mx-auto ">
                                <thead>
                                <tr>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Monitor
                                    </th>

                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Hora de inicio
                                    </th>

                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Hora de salida
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Check in
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Check out
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Hora de inicio (SUPERVISOR)
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Hora de salida (SUPERVISOR)
                                    </th>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Acciones
                                    </th>

                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="record in records"
                                    :class="
                                {
                                    'bg-red-50' :  record.status === 'canceled',
                                    'bg-green-50' : record.status === 'approved',

                                }                           ">
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-center ">
                                        {{ record.monitor.name }}
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">

                                        <input type="time" :value="record.start_planned_date"
                                               readonly
                                               class="px-3 py-2 rounded  border-0 focus:border-0 focus:shadow-none focus:ring-0 text-gray-600">

                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left">

                                        <input type="time" :value="record.end_planned_date"
                                               readonly
                                               class="px-3 py-2 rounded  border-0 focus:border-0 focus:shadow-none focus:ring-0 text-gray-600">
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">

                                        <input type="time" :value="record.start_monitor_date"
                                               readonly
                                               class="px-3 py-2 rounded  border-0 focus:border-0 focus:shadow-none focus:ring-0 text-gray-600">
                                    </td>
                                    <td class="px-3 align-middle whitespace-nowrap p-4 text-left ">

                                        <input type="time" :value="record.end_monitor_date"
                                               readonly
                                               class="px-3 py-2 rounded  border-0 focus:border-0 focus:shadow-none focus:ring-0 text-gray-600">
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
                                            @click="approveMonitorHours(record.id,record.start_monitor_date,record.end_monitor_date)"
                                            class="p-2 text-center bg-green-600 text-white mx-1 rounded">Aprobar
                                        </button>

                                        <button
                                            @click="cancelHours(record.id)"
                                            class="p-2 text-center bg-red-600 text-white mx-1 rounded">Cancelar
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
import Toastify from 'toastify-js'
import "toastify-js/src/toastify.css"

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },

    data: () => {
        return {}
    },

    mounted() {
        this.formatMonitorHours();
    },
    props: {
        today: String,
        records: Array,
    },

    methods: {

        async cancelHours(recordId) {
            let url = route('records.cancelMonitorHours', {record: recordId});
            let request = await axios.post(url);
            Toastify({
                text: request.data.msg,
            }).showToast();
            setTimeout(() => {
                location.reload();
            }, 3000);
        },

        approveMonitorHours(recordId, startHour, endHour) {
            if (startHour == null || endHour == null) {
                Toastify({
                    text: 'No puedes aprobar las horas de un monitor si este no las ha registrado totalmente',
                }).showToast();
                return;
            }

            this.records.forEach((record) => {
                if (record.id === recordId) {
                    record.start_approved_date = startHour;
                    record.end_approved_date = endHour;
                }
            });

            //now make api request
            this.updateSupervisorHour(recordId, 'start', startHour);
            this.updateSupervisorHour(recordId, 'end', endHour);

        },
        formatMonitorHours() {
            this.records.forEach((record) => {
                if (record.start_planned_date !== null) {
                    record.start_planned_date = this.extractHourFromDateTime(record.start_planned_date);
                }
                if (record.end_planned_date !== null) {
                    record.end_planned_date = this.extractHourFromDateTime(record.end_planned_date);
                }
                if (record.start_monitor_date !== null) {
                    record.start_monitor_date = this.extractHourFromDateTime(record.start_monitor_date);
                }
                if (record.end_monitor_date !== null) {
                    record.end_monitor_date = this.extractHourFromDateTime(record.end_monitor_date);
                }
                if (record.start_approved_date !== null) {
                    record.start_approved_date = this.extractHourFromDateTime(record.start_approved_date);
                }
                if (record.end_approved_date !== null) {
                    record.end_approved_date = this.extractHourFromDateTime(record.end_approved_date);
                }

            });
        },
        extractHourFromDateTime(dateTime) {
            return dateTime.substr(11, 5);
        },


        async updateSupervisorHour(recordId, typeOfHour, value) {
            let url = route('records.updateSupervisorHour', {record: recordId});
            let request = await axios.patch(url, {
                type: typeOfHour,
                hour: value
            });
            Toastify({
                text: request.data.msg,
            }).showToast();

        }
        ,


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


