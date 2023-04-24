<script setup>
    import {Head, Link} from '@inertiajs/vue3';
    import axios from 'axios';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import {useForm} from '@inertiajs/vue3';
    import {ref} from 'vue';
    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css'
    import {reactive} from 'vue'
    import {router} from '@inertiajs/vue3'

    defineProps({
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
        laravelVersion: {
            type: String,
            required: true,
        },
        phpVersion: {
            type: String,
            required: true,
        },
    });


    const availableSlots = ref([]);
    const selectedDate = ref('');
    const selectedTimeSlot = ref('');

    const form = useForm({
        name: null,
        email: null,
        phone: null,
        vehicle_make: null,
        vehicle_model: null,
        date: null,
        start_at: null
    });

    const date = ref(new Date());
    // In case of a range picker, you'll receive [Date, Date]
    const format = (date) =>
    {
        const day = date.getDate();
        const month = date.getMonth() + 1;
        const year = date.getFullYear();

        return `Selected date is ${day}/${month}/${year}`;
    }


    const handleDate = (modelData) =>
    {
        date.value = modelData;
        // do something else with the data
        console.log('date', date);

        console.log('window.location.origin', window.location.origin);

        const day = modelData.getDate();
        const month = modelData.getMonth() + 1;
        const year = modelData.getFullYear();

        console.log('date ********************', `${day}/${month}/${year}`)
        selectedDate.value = `${day}-${month}-${year}`;
        form.date = `${day}-${month}-${year}`;

        // fetch time slots using controller route
        axios
            .get(window.location.origin + '/get-available-slots/' + `${day}-${month}-${year}`)
            .then(response =>
            {
                console.log('response ****', response);
                availableSlots.value = response.data;

            })

    };

    const selectTimeSlot = (value) =>
    {
        console.log('timeSlot ****', value);
        if (value)
        {
            selectedTimeSlot.value = value;
            form.start_at = value;
        }

    }


</script>

<template>
    <Head title="Welcome"/>

    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
    >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
            >Dashboard
            </Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Log in
                </Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Register
                </Link
                >
            </template>
        </div>

        <div class="max-w-7xl mx-auto p-6 lg:p-8">


            <div class="flex justify-center">
                <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                    Hayden's Garage</h1>
            </div>

            <h3 class="mb-4 mt-6 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-2xl lg:text-2xl dark:text-white">
                Select a date to start your garage appointment</h3>

            <VueDatePicker :model-value="date" @update:model-value="handleDate" v-model="date"
                           :enable-time-picker="false" :min-date="new Date()" auto-apply
                           :format="format" :disabled-week-days="[6, 0]"></VueDatePicker>


            <div class="mr-4 mt-6 mb-6">
                <SecondaryButton class="mr-2 mt-2"
                                 :class="{ [`!bg-gray-500`]: selectedTimeSlot && selectedTimeSlot === item }"
                                 :key="item" v-on:click="selectTimeSlot(item)" v-for="item in availableSlots">

                    {{ item }}

                </SecondaryButton>
            </div>

            <div v-if="$page.props.flash.error"
                 class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                 role="alert">
                <strong class="font-bold">Holy smokes!</strong>
                <span class="block sm:inline"> {{ $page.props.flash.error }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 20 20"><title>Close</title><path
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
              </span>
            </div>

            <div v-if="$page.props.flash.success" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                 role="alert">
                <span class="font-medium">Success!</span> {{ $page.props.flash.success }}
            </div>

            <form v-if="selectedDate && selectedTimeSlot && !$page.props.flash.success" class="w-full max-w-sm"
                  @submit.prevent="form.post('/create-appointment')">
                <div class="md:flex md:items-center mb-6">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4"
                               for="inline-full-name">
                            Full Name
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <input v-model="form.name"
                               class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                               id="inline-full-name" type="text">
                    </div>
                    <div v-if="form.errors.name">{{ form.errors.name }}</div>
                </div>

                <div class="md:flex md:items-center mb-6">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4"
                               for="inline-email">
                            Email
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <input v-model="form.email"
                               class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                               id="inline-email" type="text">
                    </div>
                    <div v-if="form.errors.email">{{ form.errors.email }}</div>
                </div>

                <div class="md:flex md:items-center mb-6">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4"
                               for="inline-phone">
                            Phone
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <input v-model="form.phone"
                               class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                               id="inline-phone" type="text">
                    </div>
                    <div v-if="form.errors.phone">{{ form.errors.phone }}</div>
                </div>

                <div class="md:flex md:items-center mb-6">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4"
                               for="inline-vehicleMake">
                            Vehicle Make
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <input v-model="form.vehicle_make"
                               class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                               id="inline-vehicleMake" type="text">
                    </div>
                    <div v-if="form.errors.vehicle_make">{{ form.errors.vehicle_make }}</div>
                </div>

                <div class="md:flex md:items-center mb-6">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4"
                               for="inline-vehicleModel">
                            Vehicle Model
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <input v-model="form.vehicle_model"
                               class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                               id="inline-vehicleModel" type="text">
                    </div>
                    <div v-if="form.errors.vehicle_model">{{ form.errors.vehicle_model }}</div>
                </div>

                <input v-model="form.date" id="inline-selectedDate" type="hidden">
                <input v-model="form.start_at" id="inline-selectedTimeSlot" type="hidden">

                <div class="md:flex md:items-center">
                    <div class="md:w-1/3"></div>
                    <div class="md:w-2/3">
                        <button
                            class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                            type="submit" :disabled="form.processing">Submit
                        </button>
                    </div>
                </div>
            </form>


            <div class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-left">
                    <div class="flex items-center gap-4">
                        <a
                            href="https://github.com/sponsors/taylorotwell"
                            class="group inline-flex items-center hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                class="-mt-px mr-1 w-5 h-5 stroke-gray-400 dark:stroke-gray-600 group-hover:stroke-gray-600 dark:group-hover:stroke-gray-400"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"
                                />
                            </svg>
                            Sponsor
                        </a>
                    </div>
                </div>

                <div class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .bg-dots-darker {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
    }

    @media (prefers-color-scheme: dark) {
        .dark\:bg-dots-lighter {
            background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
        }
    }
</style>
