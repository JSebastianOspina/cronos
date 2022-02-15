<template>
    <Head title="Hacer Check In - Out"/>

    <BreezeAuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Mis monitorias
                            </h2>

                        </div>

                        <div
                            v-for="record in records"
                            class="flex flex-col w-5/6 md:w-1/3 mx-auto border border-gray-200 rounded-lg overflow-hidden mb-5">
                            <div class="bg-principal p-2 text-center">
                                <span class="text-white ">Monitoría de {{ record.name }}</span>
                            </div>
                            <div class="px-3 py-2">
                                <p> Fecha: {{ record.date }}</p>
                                <p> Entrada: {{ record.start_hour }}</p>
                                <p> Salida: {{ record.end_hour }}</p>
                            </div>

                            <button
                                @click="makeCheckInOrCheckout(record.id)"
                                class="p-2 text-center  text-white mx-3 md:mx-auto md:w-1/2 rounded mb-4 mt-2 shadow"
                                :class="getButtonColor(record.status)">
                                {{ getButtonText(record.status) }}
                            </button>
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
import dayjs from 'dayjs';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },

    data: () => {
        return {
            records: [],
        }
    },

    mounted() {
        this.getActiveRecords();
    },


    methods: {

        getActiveRecords: async function () {
            let url = route('check.getActiveRecords');
            let request = await axios.get(url);
            this.records = request.data;
            this.formatDates();
            console.log(this.records);
        },

        formatDates() {
            this.records.forEach((record) => {
                let fullStartDate =  dayjs(record.start_planned_date);
                let fullEndDate =  dayjs(record.end_planned_date);
                record.start_hour = fullStartDate.hour() + ':' + fullStartDate.minute();
                record.end_hour = fullEndDate.hour() + ':' + fullEndDate.minute();
                record.date = this.getWeekDay(fullStartDate.day()) + ' ' + fullStartDate.date() + ' de ' + this.getMonthName(fullStartDate.month());
            })
        },

        getButtonText: function (status) {
            if (status === 'created') {
                return 'Hacer Check IN';
            }
            if (status === 'in_process') {
                return 'Hacer Check OUT';
            }
        },

        getButtonColor: function (status) {
            if (status === 'created') {
                return 'bg-green-600';
            }
            if (status === 'in_process') {
                return 'bg-red-600';
            }
        },

        getWeekDay: function (weekDayNumber) {
            let days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
            return days[weekDayNumber];
        },
        getMonthName: function (monthNumber) {
            let months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            return months[monthNumber];
        },


        makeCheckInOrCheckout: async function (recordId) {
            let url = route('check.makeCheckInOrCheckout', {
                'recordId': recordId
            });
            try {
                let request = await axios.post(url);
                await Swal.fire('Proceso exitoso', request.data.msg, "success");
                location.reload();

            } catch (e) {
                //Disparar ventana con información del error
                Swal.fire('Ha ocurrido un error', e.response.data, 'error')
            }
        }
    }
}
</script>
