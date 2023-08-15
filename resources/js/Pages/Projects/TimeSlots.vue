<script setup>

defineProps({
    project: {
        type: Object,
    },
});


</script>
<template>
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-4xl font-bold text-black">{{ project.title }}</h2>
        </div>
        <!-- time slots -->

        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3" >
                        Time Slot
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Counted by
                    </th>
                </tr>
            </thead>
            <tbody >
                <tr v-for="time in project.project_data" :key="time.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ time.start_time }}-{{ time.end_time }}
                    </td>
                    <td class="px-6 py-4">
                        {{ time.user?.name }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- <ul
            class="w-full text-md font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <li v-for="time in project.project_data" :key="time.id"
                class="w-full px-4 py-2 border-b border-gray-200 rounded-t-lg dark:border-gray-600">{{ time.start_time }}-{{
                    time.end_time }}</li>
            <li class="w-full px-4 py-2 border-b border-gray-200 dark:border-gray-600">Settings</li>
            <li class="w-full px-4 py-2 border-b border-gray-200 dark:border-gray-600">Messages</li>
            <li class="w-full px-4 py-2 rounded-b-lg">Download</li>
        </ul> -->

    </div>
</template>
