<template>
    <Head title="Dashboard"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        Buscar usuario
                    </div>

                    <div class="flex justify-center flex-col w-1/2 mx-auto space-y-3">

                        <label for="email">Correo electronico</label>
                        <input type="text" id="email" v-model="userEmail">
                        <button @click="searchUser" class="px-2 py-1 bg-blue-600 text-white uppercase rounded-xl block">
                            buscar
                        </button>
                    </div>


                    <div v-if="userExists">
                        Usuario encontrado
                        <p>
                            El usuario es:
                        </p>
                        <p>
                            Nombre : {{ user.name }}
                        </p>
                        <p>correo: {{ user.email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head} from '@inertiajs/inertia-vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        Head,
    },

    data: () => {
        return {
            contador: 0,
            mostrarInput: true,
            userEmail: '',
            userExists: false,
            user: null
        }
    },


    methods: {
        add() {
            this.contador++;
        },

        async searchUser() {
            let email = this.userEmail;

            let request = await fetch('/buscarUsuario/' + email);

            let response = await request.json();

            this.userExists = true;
            this.user = response;

        }
    }
}
</script>
