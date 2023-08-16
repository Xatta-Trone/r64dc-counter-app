<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue"
import { Head } from '@inertiajs/vue3';
import PlusIcon from "@/Shared/Icons/PlusIcon.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import { useForm } from '@inertiajs/vue3'


let props = defineProps({
    'user': Object
});


onMounted(() => {
    // console.log(props.user);
    form.name = props.user.name;
    form.email = props.user.email;
    form.is_admin = props.user.is_admin;
    form.is_active = props.user.is_active;
});



const form = useForm({
    name: "",
    email: "",
    is_admin: 0,
    is_active: 1,
});

// handle submit
function handleSubmit() {
    form.put(route('users.update', { user: props.user }));
}

</script>
<template>
    <AdminLayout>

        <Head title="Create User" />
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                Update User
            </h2>

            <div>
                <Link :href="route('users.index')"
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
                        v-model="form.name" placeholder="Monzurul Islam" required>
                    <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.name }}
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-mail</label>
                    <input type="email" id="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-2"
                        v-model="form.email" placeholder="monzurul.ce.buet@gmail.com" required>
                    <div v-if="form.errors.email" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ form.errors.email
                    }}
                    </div>
                </div>

                <div class="mb-3">
                    <label for="admin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Is Admin
                        ?</label>
                    <select id="admin" v-model="form.is_admin"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="admin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account Active
                        ?</label>
                    <select id="admin" v-model="form.is_active"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div class="mt-1 flex gap-4">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg w-full sm:w-auto px-5 py-2.5 text-center ">Update
                        User</button>
                    <div>
                        <Link :href="route('users.index')"
                            class="flex items-center gap-2  bg-white text-blue-800 border-blue-800 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center ">
                        Cancel
                        </Link>
                    </div>

                </div>


            </form>

        </div>

    </AdminLayout>
</template>
