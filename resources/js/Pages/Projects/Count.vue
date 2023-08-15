<script setup>
import FlashMessage from '@/Shared/FlashMessage.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from "axios"
import { Head } from '@inertiajs/vue3';


defineProps({
    project: {
        type: Object,
    },
});

onMounted(() => {
    document.addEventListener("keydown", handleKeyPress);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeyPress);
});

const handleKeyPress = (e) => {
    console.log(e)
    let key = e.key

    if (key != null || key != undefined || key != " ") {
        // if (key == " ") {
        //     return toggleVideo();
        // }

        if (keyMap.value[key] != undefined) {
            let index = keyMap.value[key]
            console.log(index)
            // do the increment
            console.log(countData.value.data[index][currentSide.value], index)
            countData.value.data[index][currentSide.value] = countData.value.data[index][currentSide.value] + 1
        }

    }

}

const toggleVideo = () => {
    var videoNode = document.querySelector('video')
    console.log(videoNode.paused)
    if (videoNode.paused) {
        videoNode.play();
    } else {
        videoNode.pause();
    }

};

let currentSlotId = ref("");
const handleChange = (e) => {
    if (e.target.value == "") {
        clearData();
    } else {
        getCountData(e.target.value);
    }


};

const clearData = () => {
    keyMap.value = {}
    countData.value = null

};

// count data
let loading = ref(false);
let countData = ref(null);
const sides = ['left', 'through', 'right'];
let currentSide = ref('left');
let keyMap = ref({});
const plyr = ref(null);


const getCountData = (id) => {
    loading.value = true
    clearData();
    console.log(id);
    axios.get(route("projects.countData", { id }))
        .then(res => {
            console.log(res.data)
            countData.value = res.data.data

            let newKeyMap = {}
            for (const [i, v] of res.data.data.data.entries()) {
                console.log(i, v)
                newKeyMap[v.key] = i
            }

            keyMap.value = newKeyMap
        })
        .catch(err => {
            console.log(err)
            countData.value = null
        })
        .finally(() => { loading.value = false })

};

let videoSource = ref(null)


const getFileInputValue = (event) => {
    console.log(event)
    //get the file input value
    const file = event.target.files[0];
    var type = file.type
    var videoNode = document.querySelector('video')
    var canPlay = videoNode.canPlayType(type)
    if (canPlay === '') {
        alert("can not play this video");
        return;
    }

    var fileURL = URL.createObjectURL(file)
    videoSource.value = fileURL
    videoNode.playbackRate = 1
    videoNode.play();

};

// playback speed

const handlePlaybackChange = (event) => {
    var videoNode = document.querySelector('video')
    videoNode.playbackRate = parseFloat(event.target.value)
    videoNode.play();
};



</script>
<template>
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 overflow-x-hidden">

        <Head title="Count" />
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-4xl font-bold text-black">{{ project.title }}</h2>
            <div>
                <button class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15 7H9V1C9 0.4 8.6 0 8 0C7.4 0 7 0.4 7 1V7H1C0.4 7 0 7.4 0 8C0 8.6 0.4 9 1 9H7V15C7 15.6 7.4 16 8 16C8.6 16 9 15.6 9 15V9H15C15.6 9 16 8.6 16 8C16 7.4 15.6 7 15 7Z"
                        fill=""></path>
                </svg>
                Save Data
                </button>
            </div>
        </div>
        <div class="flex border rounded-lg px-2 py-4 my-4 shadow-lg">
            <div class="w-1/2">
                <label class="block mb-2 text-md font-medium text-gray-900 dark:text-white" for="file_input">Select Video
                    file to play (.mp4)</label>
                <input
                    class="block py-1.5 px-1 w-full text-lg text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    id="file_input" type="file" @change="getFileInputValue" accept="video/mp4,video/x-m4v">
                <br>

                <div v-if="videoSource">
                    <label for="playBackSpeed" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Video
                        playback speed</label>
                    <select id="playBackSpeed" @change="handlePlaybackChange"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="1" selected>1x</option>
                        <option value="1.5">1.5x</option>
                        <option value="2">2x</option>
                        <option value="2.5">2.5x</option>
                        <option value="3">3x</option>
                        <option value="3.5">3.5x</option>
                        <option value="4">4x</option>
                        <option value="4.5">4.5x</option>
                        <option value="5">5x</option>
                        <option value="10">10x</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label for="countries" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">Select a
                        time slot to work on</label>
                    <select id="countries" v-model="currentSlotId" @change="handleChange"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-md rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-1.5">
                        <option value="">Choose a time slot</option>
                        <option :value="slot.id" v-for="slot in project.project_data" :key="slot.id">{{ slot.start_time
                        }}-{{ slot.end_time }} <span v-if="slot.user">Currently being counted by {{
    slot.user?.name }}</span>
                        </option>
                    </select>
                    <FlashMessage />
                </div>

                <div class="mt-4" v-if="countData">
                    <h2 class="grow-0 text-2xl font-bold text-black">Time Slot: {{ countData.start_time }}-{{
                        countData.end_time }}
                    </h2>
                    <h2 class="grow-0 text-2xl font-bold text-black my-2"> Select current side</h2>
                    <div class="flex w-full">
                        <div v-for="side in sides" :key="side"
                            class="flex w-full items-center pl-4 border border-gray-200 rounded ml-3 px-2">
                            <input v-model="currentSide" type="radio" name="side" :id="`side-${side}`" :value="side"
                                class=" text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                            <label :for="`side-${side}`"
                                class="w-full py-2 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ side
                                }}</label>
                        </div>
                    </div>

                </div>

            </div>
            <div class="w-1/2 px-2">
                <video controls autoplay muted :src="videoSource"></video>
            </div>

        </div>

        <div class="border rounded-lg px-2 py-4 my-4 shadow-lg">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                v-if="countData == null && loading">
                <h2 class="text-4xl font-bold text-black">Loading data.....</h2>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                v-if="countData == null && loading == false">
                <h2 class="text-4xl font-bold text-black">Please choose a time slot</h2>
            </div>

            <div v-if="countData">
                <div class="grid gap-3 grid-cols-5" v-if="countData != null">
                    <div class="inline-flex rounded-md " role="group" v-for="(item, i) in countData.data" :key="item.id">
                        <span
                            class="px-4 py-2 grow text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                            {{ item.title }}
                        </span>
                        <span
                            class="px-4 py-2 text-md font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">

                            <kbd
                                class="px-2 py-1.5 text-md font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">{{
                                    item.key }}</kbd>
                        </span>
                        <span
                            class="px-4 py-2 text-md font-medium text-gray-900 bg-white border border-gray-200 rounded-r-md hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                            {{ countData.data[i][currentSide] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
