<template>
    <Head title="Gestionar agendas del usuario"/>

    <BreezeAuthenticatedLayout>

        <div class="py-12 px-6">
            <div class=" mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-5">
                            <div class="flex justify-between mb-5 items-center">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                                    Administrar eventos de la dependencia {{ dependency.name }}
                                </h2>

                                <button class="bg-principal p-2 text-white rounded"
                                        @click="setDatesForTodayAndTomorrow()">
                                    Ver registros de hoy
                                </button>
                            </div>
                            <div class="flex justify-between items-center mb-3">
                                <div>

                                    <p class="font-semibold text-lg text-gray-800 leading-tight mb-2" v-if="!canSearch"
                                    >
                                        Por favor, seleccione un rango de fechas
                                    </p>
                                    <p class="font-semibold text-lg text-gray-800 leading-tight mb-2" v-if="canSearch"
                                    >
                                        Viendo eventos del {{ start_date }} al {{ end_date }}
                                    </p>
                                </div>
                                <div>

                                    <label for="start_date" class="mx-2">Fecha de inicio</label>
                                    <input v-model="start_date" type="date" id="start_date"
                                           class="px-3 py-2 rounded  border-indigo-400"
                                           @input="changeFilterDates">

                                    <label for="end_date" class="mx-2">Fecha de fin</label>
                                    <input v-model="end_date" type="date" id="end_date"
                                           class="px-3 py-2 rounded  border-indigo-400"
                                           @input="changeFilterDates">
                                </div>

                            </div>
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

                        </div>
                        <div class="overflow-auto" id="tableContainer">
                            <table class="items-center bg-transparent border-collapse mx-auto ">
                                <thead>
                                <tr>
                                    <th class="px-3 align-middle border border-solid py-3 uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">
                                        Fecha
                                    </th>

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
                                        {{ record.date }}
                                    </td>
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
                                        <button
                                            @click="openObservationsModal(record.id)"
                                            class="p-2 text-center bg-red-600 text-white mx-1 rounded bg-principal">Ver
                                            observaciones
                                        </button>

                                        <button
                                            @click="makeObservation(record.id)"
                                            class="p-2 text-center bg-red-600  mx-1 rounded bg-secundario">Hacer
                                            observación
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
import Swal from "sweetalert2";

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },

    data: () => {
        return {
            records: [],
            start_date: '',
            end_date: '',
            canSearch: false,
        }
    },

    mounted() {

        //Get values from local storage if they exist
        if (localStorage.start_date) {
            this.start_date = localStorage.start_date;
        }
        if (localStorage.end_date) {
            this.end_date = localStorage.end_date;
        }
        if (this.start_date !== '' || this.end_date !== '') {
            this.changeFilterDates();
        }
    },
    watch: {
        start_date(newDate) {
            localStorage.start_date = newDate;
        },
        end_date(newDate) {
            localStorage.end_date = newDate;
        }

    },

    props: {
        dependency: Object
    },

    methods: {
        async openObservationsModal(recordId) {

            //find the specified record
            let record = this.records.find((record) => {
                return record.id === recordId;
            });
            if (record.length === 0) {
                Toastify({
                    text: 'Ha ocurrido un error al intentar obtener las observaciones del usuario'
                }).showToast();
                return;
            }

            let observations = JSON.parse(record.observation);
            //Doesn't have records
            if (observations === null) {
                Toastify({
                    text: 'No hay observaciones para este registro'
                }).showToast();
                return;
            }

            //Create html table
            let tableRows = '';
            observations.forEach(observation => {
                tableRows += `<tr><td class="p-3">${observation.date}</td><td class="p-3">${observation.message}</td><td class="p-3">${observation.supervisor}</td><td class="p-3">${observation.status}</td></tr>`;
            })
            let table =
                '<table class="border-collapse w-full"><tr><th>Fecha</th><th>Mensaje</th><th>Supervisor</th><th>Estado</th></tr>' +
                '<tbody>' + tableRows +
                '</tbody></table>';
            await Swal.fire({
                title: 'Observaciones',
                html: table,
                width: '80rem'
            });

        },
        async makeObservation(recordId) {
            const {value: observationMessage, isDismissed} = await Swal.fire({
                title: 'Por favor, añade una observación',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                allowOutsideClick: false,
                input: 'text',
                inputLabel: 'Mensaje descriptivo que explique la eventualidad',
                inputPlaceholder: 'Ejemplo: El monitor llegó media hora tarde.',
                confirmButtonText: 'Guardar observación',
                inputValidator(inputValue) {
                    if (!inputValue) {
                        return 'Debes introducir una observación';

                    }
                }
            })
            //The user cancel the operation
            if (isDismissed) {
                Toastify({
                    text: 'Haz cancelado el proceso. No se realizó la observación'
                }).showToast();
                return;
            }
            //Retrieve url
            let url = route('records.observations.store', {record: recordId});
            try {
                //Make request, sending the user observation in the body
                let request = await axios.post(url, {
                    observation: observationMessage
                });
                Toastify({
                    text: request.data.msg,
                }).showToast();

            } catch (error) {
                //Show error message
                Toastify({
                    text: error.response.data.error
                }).showToast();
            }
            //Update the data again
            this.changeFilterDates();
        },

        setDatesForTodayAndTomorrow() {
            let today = new Date();
            let tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);

            this.start_date = today.toISOString().substr(0, 10);
            this.end_date = tomorrow.toISOString().substr(0, 10);
            this.changeFilterDates();
        },

        async changeFilterDates() {
            //Check if the user provided both fields
            if (this.start_date === '' || this.end_date === '') {
                this.canSearch = false;
                return;
            }
            //Show the range on screen
            this.canSearch = true;
            try {
                let url = route('api.records.filter', {'dependency': this.dependency.id});
                let request = await axios.get(url, {
                    params: {
                        startDate: this.start_date,
                        endDate: this.end_date,
                    }
                });
                //Assign the records to the variable
                this.records = request.data.records;
                //Format the data
                this.formatMonitorHours();

                //Show confirm message
                Toastify({
                    text: 'Los eventos han sido actualizados',
                    duration: 1500
                }).showToast();

            } catch (error) {
                //Show error message
                Toastify({
                    text: error.response.data.error
                }).showToast();
                this.records = [];
            }

        },

        async cancelHours(recordId) {

            const {value: observationMessage, isConfirmed, isDismissed} = await Swal.fire({
                title: 'Por favor, añade una observación',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                allowOutsideClick: false,
                input: 'text',
                inputLabel: 'Mensaje descriptivo de la razón de cancelación de la monitoria',
                inputPlaceholder: 'Ejemplo: El monitor no pudo asistir por X motivo',
                confirmButtonText: 'Guardar observación y cancelar monitoría',
                inputValidator(inputValue) {
                    if (!inputValue) {
                        return 'Debes introducir una observación';

                    }
                }
            })
            //The user cancel the operation
            if (isDismissed) {
                Toastify({
                    text: 'Haz cancelado el proceso de cancelación'
                }).showToast();
                return;
            }


            let url = route('records.cancelMonitorHours', {record: recordId});
            let request = await axios.post(url, {
                observation: observationMessage
            });
            Toastify({
                text: request.data.msg,
            }).showToast();
            //Update the data again
            this.changeFilterDates();
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
                //First, lets get the date for each record
                record.date = this.getDateFromDateTime(record.start_planned_date);
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
        getDateFromDateTime(dateTime) {
            return dateTime.substr(0, 10);
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
