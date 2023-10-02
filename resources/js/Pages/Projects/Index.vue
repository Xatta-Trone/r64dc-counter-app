
<script  setup>
import { Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from '@inertiajs/vue3';
import PlusIcon from "@/Shared/Icons/PlusIcon.vue";
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import debounce from 'lodash/debounce'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';


let props = defineProps({
    projects: { type: Object },
    filters: { type: Object },
    users: { type: Array },
});

// handle search
let search = ref(props.filters?.search ?? "")
let user_id = ref(props.filters?.user_id ?? "")
let is_deleted = ref(props.filters?.is_deleted ?? "")
let date = ref(props.filters?.date ?? null)

const clearFilters = () => {
    search.value = "";
    user_id.value = "";
    is_deleted.value = "";
    date.value = null;
};

watch(search, debounce(() => {
    handleSearch();
},));

watch(is_deleted, debounce(() => {
    handleSearch();
},));
watch(date, debounce(() => {
    handleSearch();
},));
watch(user_id, debounce(() => {
    handleSearch();
},));

const handleSearch = () => {
    return router.get(route('projects.index'), { search: search.value, is_deleted: is_deleted.value, date: format(date.value), user_id: user_id.value }, { preserveState: true, replace: true });
}


const deleteHandler = (id, force = false) => {
    router.delete(route('projects.delete', { id: id, force: force }), {
        onBefore: () => confirm('Are you sure you want to delete this project?'),
    });
};

const forceDeleteHandler = (id, force = false) => {
    router.delete(route('projects.delete', { id: id, force: force }), {
        onBefore: () => confirm('Are you sure you want to permanently delete this project?'),
    });
};



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
    <Head title="Projects" />
    <AdminLayout>
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                Projects
            </h2>

            <div>
                <Link :href="route('projects.create')"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <PlusIcon />
                Create Project
                </Link>
            </div>
        </div>
        <!-- table -->
        <div class="bg-white shadow-sm px-2 py-3">
            <!-- search bar  -->
            <!-- search bar  -->
            <div class="pb-4 bg-white dark:bg-gray-900 ml-3 mt-3 flex">
                <div class="grow">
                    <label for="table-search" class="">Search</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" id="table-search" v-model="search"
                            class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 w-full"
                            placeholder="Search for items">
                    </div>
                </div>
                <div class="ml-5 grow">
                    <label for="user_id" class="">Filter Surveyor</label>
                    <select id="user_id" v-model="user_id"
                        class="mt-1 block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected>Select Surveyor</option>
                        <option :value="user.id" v-for="user in users" :key="user.id + '-user'">{{ user.name }}</option>
                    </select>
                </div>
                <div class="ml-5 grow">
                    <label for="date" class="">Filter Date</label>
                    <VueDatePicker class="w-full mt-1" v-model="date" :enable-time-picker="false" :format="format" />

                </div>
                <div class="ml-5 grow">
                    <label for="is_deleted" class="">Show deleted</label>
                    <select id="is_deleted" v-model="is_deleted"
                        class="mt-1 block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="ml-5">
                    <label for="clear" class="block">Clear filters</label>
                    <span @click="clearFilters"
                        class="inline-block bg-gray-800 w-full text-center px-2 py-1.5 mt-1 cursor-pointer rounded-md text-white">X</span>
                </div>
            </div>
            <!-- table -->
            <table class="w-full text-md text-left text-gray-500 dark:text-gray-400 overflow-x-auto">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Project
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Slots
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Surveyor
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Day
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Intersection
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Approach
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Weather
                        </th>
                        <th scope="col" class="px-6 py-3" v-show="$page.props.auth.user.is_admin">
                            Deleted
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(project, idx) in projects.data" :key="project.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="w-4 p-4">
                            {{ idx + 1 }}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ project.title }}
                        </td>
                        <td class="px-6 py-4">
                            {{ project.project_data_count }}
                        </td>
                        <td class="px-6 py-4">
                            {{ project.user ? project.user.name : "" }}
                        </td>
                        <td class="px-6 py-4">
                            {{ project.day }}
                        </td>
                        <td class="px-6 py-4">
                            {{ project.intersection }}
                        </td>
                        <td class="px-6 py-4">
                            {{ project.approach_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ project.weather_condition }}
                        </td>
                        <td class="px-6 py-3" v-show="$page.props.auth.user.is_admin">
                            <span v-if="project.deleted_at != null"
                                class="bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Yes</span>
                            <span v-else
                                class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">No</span>
                        </td>
                        <td class="px-6 py-4">
                            <Link :href="`/projects-slots/${project.id}`"
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-1">Time
                            Slots
                            </Link>
                            <Link :href='route("projects.count", { id: project.id })'
                                class="font-medium text-green-600 dark:text-green-500 hover:underline mr-1">Counting page
                            </Link>
                            <Link :href='route("projects.duplicate", { id: project.id })'
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-1">Duplicate
                            </Link>
                            <a :href='route("projects.export", { id: project.id })' target="_blank"
                                class="font-medium text-zinc-600 dark:text-zinc-500 hover:underline mr-1">Export
                            </a>
                            <a href="#" @click.prevent="deleteHandler(project.id)" v-if="project.deleted_at == null"
                                class="mr-1 font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
                            <a href="#" @click.prevent="forceDeleteHandler(project.id, true)"
                                v-if="project.deleted_at != undefined && project.deleted_at != null"
                                class="mr-1 font-medium text-red-600 dark:text-red-500 hover:underline">Force Delete</a>
                        </td>
                    </tr>

                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        v-show="projects.data.length == 0">
                        <td colspan="20" class="text-center py-1">
                            No data found
                        </td>
                    </tr>


                </tbody>
            </table>

            <div class="-space-x-px text-base mt-3 ml-3">
                {{ projects.total }} record(s)
            </div>
            <!-- pagination -->
            <ul class="inline-flex -space-x-px text-base mt-3 mb-3 ml-3">
                <li v-for="(link, i) in projects.links" :key="'link' + i">
                    <Link :href="link.url" v-if="link.url"
                        class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300" :class="{
                            'text-blue-600 bg-blue-100 hover:bg-blue-200 hover:text-blue-700':
                                link.active,
                            'text-gray-500 bg-white hover:bg-gray-100 hover:text-gray-700':
                                link.active == false,
                        }">
                    <span v-html="link.label"></span>
                    </Link>
                    <span v-else
                        class="flex items-center justify-cen ter px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                        <span v-html="link.label"></span>
                    </span>
                </li>
            </ul>

        </div>


    </AdminLayout>
</template>
