<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'


defineProps({
    project: {
        type: Object,
    },
});


</script>
<template>
    <AdminLayout>
        <Head :title="project.title" />
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                {{ project.title }}
            </h2>
            <div>
                <Link :href="route('projects.index')"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Back
                </Link>
            </div>
        </div>
        <div class="bg-white shadow-sm px-4 py-3 rounded-sm">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Time Slot
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Counted by
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="time in project.project_data" :key="time.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ time.start_time }}-{{ time.end_time }}
                        </td>
                        <td class="px-6 py-4">
                            {{ time.user?.name }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AdminLayout>
</template>
