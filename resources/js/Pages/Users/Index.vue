<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue"
import { Head } from '@inertiajs/vue3';
import PlusIcon from "@/Shared/Icons/PlusIcon.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import debounce from 'lodash/debounce'

let props = defineProps({
    users: {
        type: Object
    },
    filters: {
        type: Object,
    }
});


// delete handler
const deleteHandler = (id) => {
    router.delete(route('users.destroy', { id: id, force: false }), {
        onBefore: () => confirm('Are you sure you want to delete this user?'),
    });
};

const forceDeleteHandler = (id) => {
    router.delete(route('users.destroy', { id: id, force: true }), {
        onBefore: () => confirm('Are you sure you want to permanently delete this user?'),
    });
};

const restoreHandler = (id) => {
    router.delete(route('users.restore', { id: id, force: true }), {
        onBefore: () => confirm('Are you sure you want to restore this user?'),
    });
};


const clearFilters = () => {
    search.value = "";
    active.value = "";
    is_admin.value = ""
};

// search
// handle search
let search = ref(props.filters?.search ?? "")
let active = ref(props.filters?.active ?? "")
let is_admin = ref(props.filters?.is_admin ?? "")

watch(search, debounce(function (val) {
     handleFilter();
}, 300));

watch(active, debounce(function (val) {
    search.value = ""
    handleFilter();
}, 300));

watch(is_admin, debounce(function (val) {
    search.value = ""
    handleFilter();
}, 300));

const handleFilter = () => {
    router.get(route('users.index'), { is_admin: is_admin.value, active: active.value, search: search.value }, { preserveState: true, replace: true });
}



</script>
<template>
    <AdminLayout>

        <Head title="Users" />
        <!-- title -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-slate-600 dark:text-white">
                Users
            </h2>

            <div>
                <Link :href="route('users.create')"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <PlusIcon />
                Create User
                </Link>
            </div>
        </div>

        <!-- table -->

        <div class="bg-white shadow-sm px-2 py-3">
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
                    <label for="active" class="">Active Users</label>
                    <select id="active" v-model="active"
                        class="mt-1 block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected>All</option>
                        <option value="1">Active</option>
                        <option value="2">Deleted</option>
                    </select>
                </div>
                <div class="ml-5 grow">
                    <label for="is_admin" class="">Admin Filter</label>
                    <select id="is_admin" v-model="is_admin"
                        class="mt-1 block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected>All</option>
                        <option value="1">Only Admins</option>
                        <option value="0">Non-admins</option>
                    </select>
                </div>
                <div class="ml-5">
                    <label for="clear" class="block">Clear filters</label>
                    <span @click="clearFilters"
                        class="inline-block bg-gray-800 w-full text-center px-2 py-1.5 mt-1 cursor-pointer rounded-md text-white">X</span>
                </div>
            </div>
            <!-- table -->
            <table class="w-full text-md text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                            Id
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            E-mail
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Is-admin
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Active
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Joined
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Deleted
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in  users.data " :key="user.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="w-4 p-4 py-3">
                            {{ user.id }}
                        </td>
                        <td scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ user.name }}
                        </td>
                        <td class="px-6 py-3">
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-3">
                            <span v-if="user.is_admin"
                                class="bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Yes</span>
                            <span v-else
                                class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">No</span>
                        </td>
                        <td class="px-6 py-3">
                            <span v-if="user.is_active"
                                class="bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Yes</span>
                            <span v-else
                                class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">No</span>
                        </td>
                        <td class="px-6 py-3">
                            {{ new Intl.DateTimeFormat('en-GB').format(new Date(user.created_at)) }}{{ }}
                        </td>
                        <td class="px-6 py-3">
                            <span v-if="user.deleted_at"
                                class="bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Yes</span>
                            <span v-else
                                class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">No</span>
                        </td>
                        <td class="px-6 py-3">

                            <Link :href="route('users.edit', { id: user.id })"
                                v-if="$page.props.auth.user.is_admin && $page.props.auth.user.id != user.id"
                                class="px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-blue-300 mr-1">
                            Edit
                            </Link>
                            <a v-if="$page.props.auth.user.is_admin && $page.props.auth.user.id != user.id && user.deleted_at == null"
                                href="#" @click.prevent="deleteHandler(user.id)"
                                class="px-3 py-2 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:outline-none focus:ring-red-300 mr-1">Delete</a>
                            <a v-if="$page.props.auth.user.is_admin && $page.props.auth.user.id != user.id && user.deleted_at != null"
                                href="#" @click.prevent="forceDeleteHandler(user.id)"
                                class="px-3 py-2 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:outline-none focus:ring-red-300 mr-1">Delete
                                Permanently</a>
                            <a v-if="$page.props.auth.user.is_admin && $page.props.auth.user.id != user.id && user.deleted_at != null"
                                href="#" @click.prevent="restoreHandler(user.id)"
                                class="px-3 py-2 text-sm font-medium text-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-gray-300 mr-1">
                                Restore User
                            </a>
                        </td>
                    </tr>

                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        v-show="users.data.length == 0">
                        <td colspan="8" class="text-center py-1">
                            No data found
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="-space-x-px text-base mt-3 ml-3">
                {{ users.total }} record(s)
            </div>
            <ul class="inline-flex -space-x-px text-base mt-3 mb-3 ml-3">
                <li v-for="(link, i) in users.links" :key="'link' + i">
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
