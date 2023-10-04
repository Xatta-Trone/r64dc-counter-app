<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import { reactive, ref, nextTick, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Required from '../../Shared/Required.vue'
import axios from 'axios';



let props = defineProps({
    'project': Object,
    'parents': Array,
});


onMounted(() => {
    if (props.project) {
        form.title = form.title ? form.title : props.project.title;
        form.intersection = props.project.intersection;
        form.project_intersection_id = props.project.project_intersection_id;
        form.approach_name = props.project.approach_name;
        form.weather_condition = props.project.weather_condition
        form.day = new Date(props.project.day)
        form.parent_project_id = props.project.parent_project_id ? props.project.parent_project_id : null

        if (props.project.parent_project_id) {
            handleProjectChange(props.project.parent_project_id)
        }
    }

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
    day: null,
    intersection: "",
    approach_name: "",
    weather_condition: "",
    parent_project_id: "",
    project_intersection_id: "",
});

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


// handle submit
function handleSubmit() {

    form.put(route('projects.update', { id: props.project.id }))

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

        <Head title="Update Project" />
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                Update project
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
                        v-model="form.title" required>
                    <div v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.title
                    }}
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
                            Approach Name <Required />
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
                            Weather Condition <Required />
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

                <button type="submit"
                    class="mt-5 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update
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
