<template>
    <div class="flex h-screen overflow-hidden bg-gray-50">
        <!-- sidebar -->
        <aside
            class="absolute left-0 top-0 z-9999 flex h-screen w-64 flex-col overflow-y-hidden bg-slate-800 duration-300 ease-linear lg:static lg:translate-x-0 -translate-x-full">
            <!-- logo -->
            <div class="flex items-center justify-between gap-2 px-6 py-5 lg:py-6">
                <Link href="/" class="text-white text-xl font-bold inline-block">
                    <img :src="img" alt="Logo" class="h-8 inline-block mr-2" /> Route 64DC Ltd.
                </Link>
            </div>
            <!-- links -->
            <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
                <nav class="mt-1 py-4 px-4 lg:mt-2 lg:px-6">
                    <div>
                        <h3 class="mb-4 ml-4 text-sm font-medium text-white">MENU</h3>
                        <ul class="mb-6 flex flex-col gap-1.5">
                            <li v-if="$page.props.auth.user.is_admin">
                                    <Link :href="route('parent-projects.index')"
                                        class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-white duration-300 ease-in-out hover:bg-slate-600 dark:hover:bg-meta-4">
                                    <DashboardIcon />
                                    Project Folders
                                    </Link>
                                </li>
                            <li>
                                <Link :href="route('projects.index')"
                                    class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-white duration-300 ease-in-out hover:bg-slate-600 dark:hover:bg-meta-4">
                                <DashboardIcon />
                                Project Items
                                </Link>
                            </li>
                            <li v-if="$page.props.auth.user.is_admin">
                                <Link :href="route('users.index')"
                                    class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-white duration-300 ease-in-out hover:bg-slate-600 dark:hover:bg-meta-4">
                                <DashboardIcon />
                                Users
                                </Link>
                            </li>
                            <li v-if="$page.props.auth.user.is_admin">
                                    <Link :href="route('vehicle-classifiers.index')"
                                        class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-white duration-300 ease-in-out hover:bg-slate-600 dark:hover:bg-meta-4">
                                    <DashboardIcon />
                                    Vehicle Classifier
                                    </Link>
                                </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </aside>
        <!-- main content -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <header class="sticky top-0 z-999 flex w-full bg-white shadow-sm dark:bg-slate-800 dark:drop-shadow-none">
                <div class="flex flex-grow items-center justify-between py-4 px-4 shadow-sm md:px-6 2xl:px-11">
                    <div></div>
                    <div class="flex items-center gap-3 2xsm:gap-7">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        {{ $page.props.auth.user.name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>
            <!-- main -->
            <main>
                <div class="mx-auto max-w-screen-2xl p-4 md:p-4 2xl:p-6 ">
                    <FlashMessage />
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import DashboardIcon from '../Shared/Icons/DashboardIcon.vue'
import FlashMessage from '../Shared/FlashMessage.vue'
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from "@inertiajs/vue3";
import img from "@/asset/img/logo512.png"

</script>
