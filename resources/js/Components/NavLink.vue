<template>

    <Link v-if="!href" :class="classes" :as="as" :type="type">
        <slot/>
    </Link>

    <Link v-else :href="href" :class="classes" :as="as" :type="type">
        <slot/>
    </Link>
</template>

<script>
import {Link} from '@inertiajs/inertia-vue3';

export default {
    components: {
        Link,
    },
    props: ['href', 'active', 'as', 'type'],
    computed: {
        classes() {
            return this.active
                ? 'inline-flex items-center px-1 pt-1  text-sm font-medium leading-5 text-white focus:outline-none transition  duration-150 ease-in-out'
                : 'inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-400 hover:text-white hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 transition duration-150 ease-in-out'
        }
    }
}
</script>
