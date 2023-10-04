<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import { reactive, ref, nextTick, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
    title: null,
    '_method': 'PATCH',
    intersections: []
});

const props = defineProps({
    project: {
        type: Object,
    },
});

onMounted(() => {
    form.title = props.project.title;
    form.intersections = props.project.intersections;
});


// handle submit
function handleSubmit() {
    form.put(route('parent-projects.update', { parent_project: props.project }))

}

const addIntersection = () => {
    form.intersections.push({ "intersection_name": "" });
};

// handle delete
function handleDelete(index) {
    form.intersections.splice(index, 1)
}


</script>
<template>
    <AdminLayout>

        <Head title="Update Project Folder" />
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                Update Project Folder
            </h2>
            <div>
                <Link :href="route('parent-projects.index')"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Back
                </Link>
            </div>
        </div>

        <div class="bg-white shadow-sm px-4 py-3 rounded-sm">
            <form @submit.prevent="handleSubmit">
                <div class="mb-6">
                    <label for="title" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">Project
                        Name <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Required</span></label>
                    <input type="text" id="title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                        v-model="form.title" required>
                    <div v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.title
                    }}
                    </div>
                </div>

                <div class="mb-6">
                    <label for="intersection_name"
                        class="block mb-2 text-md font-medium text-gray-900 dark:text-white">Intersections<span
                            class="ml-2 bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Required</span></label>

                    <div v-for="(_, i) in form.intersections" :key="'intersection' + i" class="mb-1">
                        <div class="flex">
                            <input type="text" v-model="form.intersections[i].intersection_name" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 grow mr-2">
                            <button type="button" @click.prevent="handleDelete(i)"
                                class="float-right focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-1">Delete</button>
                        </div>
                        <div v-if="form.errors.title" class="mt-2 text-sm text-red-600 dark:text-red-500">{{
                            form.errors.intersections[i] }}
                        </div>
                    </div>


                    <button @click.prevent="addIntersection"
                        class="text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-300 font-small rounded-lg w-full sm:w-auto px-5 py-2 text-center mt-2">Add
                        Intersection</button>

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
}
</style>
