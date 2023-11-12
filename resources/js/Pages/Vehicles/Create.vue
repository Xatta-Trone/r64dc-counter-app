<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue"
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'


let props = defineProps({

});

const form = useForm({
    name: "",
});

// handle submit
function handleSubmit() {
    form.post(route('vehicle-classifiers.store'));
}

</script>
<template>
    <AdminLayout>

        <Head title="Create Vehicle Classifier" />
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                Create Vehicle Classifier
            </h2>

            <div>
                <Link :href="route('vehicle-classifiers.index')"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 ">
                Cancel
                </Link>
            </div>
        </div>

        <!-- table -->

        <div class="bg-white shadow-sm px-4 py-3 rounded-sm">
            <form @submit.prevent="handleSubmit">
                <div class="mb-3">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                    <input type="text" id="name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-2"
                        v-model="form.name" placeholder="Bus/Truck" required>
                    <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.name }}
                    </div>
                </div>


                <div class="mt-1 flex gap-4">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg w-full sm:w-auto px-5 py-2.5 text-center ">Create
                        Item</button>
                    <div>
                        <Link :href="route('vehicle-classifiers.index')"
                            class="flex items-center gap-2  bg-white text-blue-800 border-blue-800 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center ">
                        Cancel
                        </Link>
                    </div>

                </div>


            </form>

        </div>

    </AdminLayout>
</template>
