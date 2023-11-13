<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import { reactive, ref, nextTick, onMounted, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from 'axios';
import Required from '../../Shared/Required.vue'



let props = defineProps({
    'times': Array,
    'project': Object,
    'parents': Array,
    'vehicles': Array,
});


onMounted(() => {
    let params = new URLSearchParams(window.location.search)

    const parent_project_id = params.get('parent_project_id')

    if (parent_project_id != null) {
        form.parent_project_id = parent_project_id
        // update the value
        updateTitle(parent_project_id)
        // handleProjectChange(parent_project_id);
    }

    if (props.project) {
        form.title = form.title ? form.title : props.project.title;
        form.intersection = props.project.intersection;
        form.project_intersection_id = props.project.project_intersection_id;
        form.approach_name = props.project.approach_name;
        form.weather_condition = props.project.weather_condition
        form.day = new Date(props.project.day)
        form.start_time = props.project.first_project_data.start_time
        form.end_time = props.project.last_project_data.end_time
        form.parent_project_id = props.project.parent_project_id ? props.project.parent_project_id : null
        form.items = props.project.first_project_data.data.map(element => { return { ...element, left: 0, through: 0, right: 0 } })
        updateKeyMap();

        if (props.project.parent_project_id) {
            handleProjectChange(props.project.parent_project_id)
        }
    }

});

const availableVehicles = computed({
    // getter
    get() {
        const current = form.items.map(el => el.title)
        return props.vehicles.filter(el => !current.includes(el))
    },
});

const updateTitle = () => {
    form.intersection = "";
    form.project_intersection_id = "";
    let item = props.parents?.find(item => item.id == form.parent_project_id);
    if (item) {
        form.title = item.title;
        handleProjectChange(item.id);
    }



}

const form = useForm({
    title: null,
    start_time: "",
    end_time: "",
    items: [],
    interval: 5,
    day: null,
    intersection: "",
    project_intersection_id: "",
    approach_name: "",
    weather_condition: "",
    parent_project_id: "",
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

    if (key == " " || key == "+" || key == "-") {
        return alert("Space, + and - key is preserved for video play/pause/playback speed. Please choose other key.");
    }

    if (currentIdx.value == "") {
        return;

    }

    updateKeyMap();

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

const updateKeyMap = () => {
    handleKeys.value = [];
    for (let index = 0; index < form.items.length; index++) {
        const item = form.items[index];
        handleKeys.value.push(item.key)
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

let intersections = ref([]);
const handleProjectChange = (id) => {
    axios.get(`/api/intersections?parent_id=${id}`)
        .then(res => {
            // console.log(res.data)
            intersections.value = res.data;
        })
        .catch(err => {
            console.log(err)
            alert('error occurred');
            intersections.value = []
        });

};

const handleIntersectionChange = () => {
    let intersection = intersections?.value.find(item => item.id == form.project_intersection_id);
    if (intersection) {
        form.intersection = intersection.intersection_name
    } else {
        form.intersection = ""
    }

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
                    <label for="parent_project_id" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                        Project Folder
                        <Required />
                    </label>
                    <select id="parent_project_id" v-model="form.parent_project_id" required @change="updateTitle"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 ">
                        <option value="">Select Project Folder</option>
                        <option v-for="parent in parents" :key="'parent' + parent.id" :value="parent.id">{{ parent.title }}
                        </option>
                    </select>
                    <div v-if="form.errors.parent_project_id" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                        form.errors.parent_project_id }}
                    </div>
                </div>

                <div class="mb-6">
                    <label for="title" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">Project
                        Title
                        <Required />
                    </label>
                    <input type="text" id="title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                        v-model="form.title" required :readonly="$page.props.auth.user.is_admin == false">
                    <div v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.title
                    }}
                    </div>
                </div>

                <div class="flex">
                    <div class="flex-auto">
                        <label for="startTime" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Start time
                            <Required />
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
                            <Required />
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
                            <Required />
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
                            <Required />
                        </label>
                        <VueDatePicker class="w-full" v-model="form.day" :enable-time-picker="false" :format="format">
                        </VueDatePicker>
                    </div>
                    <div class="flex-auto ml-3">
                        <label for="intersection" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Intersection
                            <Required />
                        </label>
                        <!-- <input type="text" id="intersection"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                            v-model="form.intersection" required> -->
                        <select id="intersection" v-model="form.project_intersection_id" required
                            @change="handleIntersectionChange"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 ">
                            <option value="">Select Intersection</option>
                            <option v-for="intersection in intersections" :key="'intersection' + intersection.id"
                                :value="intersection.id">{{ intersection.intersection_name }}</option>
                        </select>
                        <div v-if="form.errors.project_intersection_id" class="mt-2 text-sm text-red-600 dark:text-red-500">
                            {{
                                form.errors.intersection
                            }}
                        </div>
                    </div>

                    <div class="flex-auto ml-3">
                        <label for="approach" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Approach Name
                            <Required />
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
                            <Required />
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
                        <label for="vehicles" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">
                            Vehicle Classification {{ itemName }}
                        </label>
                        <select id="vehicles" v-model="itemName"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 ">
                            <option value="" disabled>Choose vehicle classification</option>
                            <option v-for="vehicle in availableVehicles" :value="vehicle" :key="vehicle">{{ vehicle }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-auto">
                        <label for="vehicles" class="block mb-2 text-md font-medium text-gray-900 dark:text-white"></label>
                        <button @click.prevent="handleNewItem" class="mt-6 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg w-full sm:w-auto px-5 py-2 ml-3 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Add Vehicle {{ itemName ? itemName : "" }}</button>
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
}</style>
