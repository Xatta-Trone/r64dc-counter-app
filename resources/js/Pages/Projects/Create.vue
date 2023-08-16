<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import { reactive, ref, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';



defineProps({
    'times': Array
});

const form = useForm({
    title: null,
    start_time: "",
    end_time: "",
    items: [],
    interval: 5,
    day: null,
    intersection: "",
    approach_name: "",
    weather_condition: "",
});

const itemName = ref("")

function handleNewItem() {
    if (itemName.value == "") {
        return;
    }

    const Id = 'item' + (new Date()).getTime()

    const item = {
        title: itemName.value,
        left: 0,
        right: 0,
        through: 0,
        key: null,
        id: Id
    }
    form.items.push(item);
    itemName.value = "";

    nextTick(() => {
        // console.log(this.$refs)
        // this.$refs.Id.focus();
        currentIdx.value = Id;
        if (document.getElementById(Id) != null) {
            document.getElementById(Id)?.focus();
        }


    });

}


function handleFocus(Id) {
    if (document.getElementById(Id) != null) {
        document.getElementById(Id)?.focus();
    }
    currentIdx.value = Id
}


// handle key inputs
const currentIdx = ref("");
const handleKeys = ref([]);

function handleKeyInput(e) {
    console.log(e, currentIdx)
    const key = e.key

    if (key == undefined || key == null) {
        return alert("Could not add this key");
    }

    if (key == " ") {
        return alert("Space key is preserved for video play/pause. Please choose other key.");
    }

    if (currentIdx.value == "") {
        return;

    }


    if (handleKeys.value.indexOf(key) != -1) {
        alert(`${key} is already under use. Please choose a different one.`);
        return;
    } else {
        for (let index = 0; index < form.items.length; index++) {
            const item = form.items[index];
            if (item.id == currentIdx.value && item.key != key) {
                form.items[index].key = key
                handleKeys.value.push(key)
                break;
            }
        }

    }
}

// handle delete
function handleDelete(index) {
    const item = form.items[index]
    console.log(item)

    if (currentIdx.value == item.id) {
        currentIdx.value = ""
    }

    handleKeys.value = handleKeys.value.filter(i => i != item.key)

    form.items = form.items.filter((formItem) => formItem.id != item.id)
}

// handle submit
function handleSubmit() {

    if (form.items.length == 0) {
        alert('Please add some items first.')
        return;
    }

    if (form.items.some((item) => item.key == null)) {
        alert('Some key-map missing. Please check again.')
        return;
    }

    form.post('/projects')

}

// In case of a range picker, you'll receive [Date, Date]
const format = (date) => {
    if (date == null || date == undefined) {
        return
    }
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();

    return `${day}-${month}-${year}`;
};

</script>
<template>
    <AdminLayout>

        <Head title="Create Project" />
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                Create project
            </h2>
            <div>
                <Link :href="route('projects.index')"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Back
                </Link>
            </div>
        </div>

        <div class="bg-white shadow-sm px-4 py-3 rounded-sm">
            <form @submit.prevent="handleSubmit">
                <div class="mb-6">
                    <label for="title" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">Project
                        Title</label>
                    <input type="text" id="title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                        v-model="form.title" required>
                    <div v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.title
                    }}
                    </div>
                </div>

                <div class="flex">
                    <div class="flex-auto">
                        <label for="startTime" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Start time
                        </label>
                        <select id="startTime" v-model="form.start_time" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 ">
                            <option value="">Choose Start time</option>
                            <option v-for="time in times" :key="'start' + time" :value="time">{{ time }}</option>
                        </select>
                        <div v-if="form.errors.start_time" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                            form.errors.start_time }}
                        </div>
                    </div>
                    <div class="flex-auto ml-3">
                        <label for="endTime" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            End time
                        </label>
                        <select id="endTime" v-model="form.end_time" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 ">
                            <option value="">Choose End time</option>
                            <option v-for="time in times" :key="'end' + time" :value="time">{{ time }}</option>
                        </select>
                        <div v-if="form.errors.end_time" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                            form.errors.end_time }}
                        </div>
                    </div>

                    <div class="flex-auto ml-3">
                        <label for="interval" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Interval (minutes)
                        </label>
                        <select id="interval" v-model="form.interval" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 ">
                            <option value="">Choose Interval</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">50</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                        </select>
                        <div v-if="form.errors.interval" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                            form.errors.interval }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex">
                    <div class="flex-auto">
                        <label class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Day
                        </label>
                        <VueDatePicker class="w-full" v-model="form.day" :enable-time-picker="false" :format="format">
                        </VueDatePicker>
                    </div>
                    <div class="flex-auto ml-3">
                        <label for="intersection" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Intersection
                        </label>
                        <input type="text" id="intersection"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                            v-model="form.intersection" required>
                        <div v-if="form.errors.intersection" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                            form.errors.intersection
                        }}
                        </div>
                    </div>

                    <div class="flex-auto ml-3">
                        <label for="approach" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Approach Name
                        </label>
                        <input type="text" id="approach"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                            v-model="form.approach_name" required>
                        <div v-if="form.errors.approach_name" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                            form.errors.approach_name
                        }}
                        </div>
                    </div>
                    <div class="flex-auto ml-3">
                        <label for="weather" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Weather Condition
                        </label>
                        <select id="weather" v-model="form.weather_condition" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 ">
                            <option value="">Choose weather condition</option>
                            <option value="Sunny">Sunny</option>
                            <option value="Fair">Fair</option>
                            <option value="Cloudy">Cloudy</option>
                            <option value="Rainy">Rainy</option>
                        </select>
                        <div v-if="form.errors.interval" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                            form.errors.interval }}
                        </div>
                    </div>

                </div>





                <!-- items -->
                <div class="mt-6" v-show="form.items.length > 0">
                    <label class="block text-md font-medium text-gray-900 dark:text-white">
                        Items
                    </label>
                    <div class="grid grid-cols-4 gap-4">
                        <div v-for="(item, i) in form.items" :key="item.id" @click="handleFocus(item.id)" class=" p-2 font-medium text-gray-900 bg-white border border-gray-200 rounded-lg
                                  focus:bg-blue-300">
                            Name: {{ item.title }}
                            <span class="block mb-1"></span>

                            Key: <input
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-1"
                                :ref="item.id" :id="item.id" v-model="form.items[i].key" readonly
                                v-on:keypress.prevent="handleKeyInput" />
                            <span class="block mb-1"></span>
                            <button type="button" @click.prevent="handleDelete(i)"
                                class="float-right focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-1">Delete</button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex">
                    <div class="flex-auto">
                        <label for="Item" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Add New Item for counting and press <kbd
                                class="px-2 py-1.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">Enter
                            </kbd>
                            to add</label>
                        <input type="text" id="Item"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                            placeholder="Car/CNG" v-on:keydown.enter.prevent="handleNewItem" v-model="itemName">
                    </div>

                </div>

                <button type="submit"
                    class="mt-5 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create
                    project</button>
            </form>

        </div>


    </AdminLayout>
</template>
<style>
.dp__action_select {
    background-color: #000000 !important;
    color: white !important;
}
</style>
