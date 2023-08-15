<template>
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-4xl font-bold text-black">Projects</h2>

            <div>
                <Link :href="route('projects.create')"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15 7H9V1C9 0.4 8.6 0 8 0C7.4 0 7 0.4 7 1V7H1C0.4 7 0 7.4 0 8C0 8.6 0.4 9 1 9H7V15C7 15.6 7.4 16 8 16C8.6 16 9 15.6 9 15V9H15C15.6 9 16 8.6 16 8C16 7.4 15.6 7 15 7Z"
                        fill=""></path>
                </svg>
                Create Project
                </Link>
            </div>
        </div>

        <FlashMessageVue />

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="pb-4 bg-white dark:bg-gray-900 ml-3 mt-3">
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="table-search" v-model="search"
                        class="block p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search for items">
                </div>
            </div>
            <table class="w-full text-md text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                            Id
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Project
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Time slots (count)
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="project in projects.data" :key="project.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="w-4 p-4">
                            {{ project.id }}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ project.title }}
                        </td>
                        <td class="px-6 py-4">
                            {{ project.project_data_count }}
                        </td>
                        <td class="px-6 py-4">
                            <Link :href="`/projects-slots/${project.id}`"
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2">View Time Slots</Link>
                            <Link :href='route("projects.count",{id: project.id})'
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-2">Counting Page</Link>
                            <a href="#" @click.prevent="deleteHandler(project.id)"
                                class="mx-1 font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>

                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        v-show="projects.data.length == 0">
                        <td colspan="4" class="text-center py-1">
                            No data found
                        </td>
                    </tr>


                </tbody>
            </table>
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




    </div>
</template>

<script  setup>
import { Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from '@inertiajs/vue3';
import FlashMessageVue from "@/Shared/FlashMessage.vue";

defineProps({ projects: { type: Object } });

// handle search
let search = ref("")

watch(search, (val)=> {
    console.log(val)
    router.get('/', { search: val }, { preserveState: true, replace: true });

});

const deleteHandler = (id) => {
    router.delete(route('projects.delete', { id: id }), {
        onBefore: () => confirm('Are you sure you want to delete this project?'),
    });
}

</script>
