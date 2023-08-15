<template>
    <div>
        <div v-if="$page.props.flash.success" x-cloak x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 5000)"
            class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 flex justify-between items-center"
            role="alert">
            <span>{{ $page.props.flash.success }}</span> <span x-on:click="show = false"
                class="mr-2 bg-blue-300 px-2 py-1 cursor-pointer">X</span>
        </div>
        <div v-if="$page.props.flash.message" x-data="{ showMsg: true }" x-show="showMsg"
            x-init="setTimeout(() => showMsg = false, 5000)"
            class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400  flex justify-between items-center"
            role="alert">
            <span>{{ $page.props.flash.message }}</span> <span x-on:click="showMsg = false"
                class="mr-2 bg-blue-300 px-2 py-1 cursor-pointer">X</span>
        </div>
        <div v-if="$page.props.flash.error" x-data="{ showErr: true }" x-show="showErr"
            x-init="setTimeout(() => showErr = false, 5000)"
            class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400  flex justify-between items-center"
            role="alert">
            <span>{{ $page.props.flash.error }}</span> <span x-on:click="showMsg = false"
                class="mr-2 bg-red-300 px-2 py-1 cursor-pointer">X</span>
        </div>

    </div>
</template>

<script setup>
</script>
