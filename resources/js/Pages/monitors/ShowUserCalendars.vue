<template>
    <Head title="Hacer Check In - Out"/>

    <BreezeAuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                Mis Horarios
                            </h2>

                        </div>

                        <div
                            v-for="calendar in calendars"
                            class="flex flex-col w-5/6 md:w-1/3 mx-auto border border-gray-200 rounded-lg overflow-hidden mb-5">
                            <div class="bg-principal p-2 text-center">
                                <span class="text-white ">Horario de {{ calendar.dependency.name }}</span>
                            </div>


                            <a
                                :href="calendar.url"
                                target="_blank"
                                class="p-2 text-center bg-secundario  mx-3 md:mx-auto md:w-1/2 rounded mb-4 my-5 shadow">
                                Ver calendario
                            </a>
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
        return {
            calendars: [],
        }
    },

    props: {
        monitorId: Number
    },

    mounted() {
        this.getMonitorCalendars(this.monitorId);
    },


    methods: {

        getMonitorCalendars: async function (monitorId) {
            let url = route('api.monitors.getUserCalendars', {
                monitor: monitorId
            });
            let request = await axios.get(url);
            this.calendars = request.data;
            console.log(request.data)
        },


    }
}
</script>
