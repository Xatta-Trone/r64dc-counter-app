<script setup>
import FlashMessage from '@/Shared/FlashMessage.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from "axios"
import { Head } from '@inertiajs/vue3';

let props = defineProps({
    project: {
        type: Object,
    },
});

const plyrr = ref(null)
var plyr = null
var overlay = null

onMounted(() => {
    document.addEventListener("keydown", handleKeyPress);

    plyr = document.querySelector('.plyr');
    overlay = document.querySelector(".test")
    plyrr.value.player.volume = 0.0
    plyr.append(overlay)

    plyr.addEventListener("mouseenter", handleMouseMovement);
    plyr.addEventListener("mouseleave", handleMouseMovement);
    plyr.addEventListener("mousemove", handleMouseMovement);
    plyrr.value.player.on('ended', handleVideoEnd);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeyPress);
    if (plyr) {
        plyr.removeEventListener("mouseenter", handleMouseMovement);
        plyr.removeEventListener("mouseleave", handleMouseMovement);
        plyr.removeEventListener("mousemove", handleMouseMovement);
    }
    if (plyrr.value != null)  {
        plyrr.value.player.off('ended', handleVideoEnd);
    }
});


var overlayEvent = null;

const handleMouseMovement = (e) => {
    // console.log(plyrr.value.player.fullscreen.active);
    // console.log('Mouse movement', e)
    if (overlayEvent != null) {
        clearTimeout(overlayEvent);
    }

    if (plyrr.value.player.fullscreen.active == false) {
        return;
    }

    overlay.style.display = "block";

    overlayEvent = setTimeout(() => {
        console.log('timeout executed');
        overlay.style.display = "none";
    }, 4000);
};

const handleVideoEnd = (e) => {
    console.log('video ended', e, currentVideoIdx.value, videos.value.length)

    if (currentVideoIdx.value == null || currentVideoIdx.value == (videos.value.length - 1)) {
        return alert('End of videos');
    }

    const str = `
        Play next video: ${videos.value[currentVideoIdx.value+1].name} and
        Move to next slot.
    `;

    if(confirm(str)) {
        selectNextSlot();
        playNextVideo();

    }
};


const handleKeyPress = (e) => {
    console.log(e)
    let key = e.key

    if (key != null || key != undefined || key != " ") {
        if (key == "+") {
            return changePlaybackSpeed(true);
        }
        if (key == "-") {
            return changePlaybackSpeed(false);
        }


        if (keyMap.value[key] != undefined) {
            let index = keyMap.value[key]
            console.log(index)
            // do the increment
            console.log(countData.value.data[index][currentSide.value], index)
            countData.value.data[index][currentSide.value] = countData.value.data[index][currentSide.value] + 1
        }

    }

}


let currentSlotId = ref("");
const handleChange = (e) => {
    if (e.target.value == "") {
        clearData();
    } else {
        getCountData(e.target.value);
    }
};

const selectNextSlot = () => {
    // current index
    const currentSlot = currentSlotId.value;
    console.log(currentSlot);
    if (currentSlot == "") return;
    const currentSlotIdx = props.project.project_data.findIndex((el) => el.id == currentSlot);
    console.log(currentSlotIdx);
    if (currentSlotIdx < props.project.project_data.length - 1) {
        currentSlotId.value = props.project.project_data[currentSlotIdx + 1].id;
        // update the data
        getCountData(currentSlotId.value);
    }
};

const selectPreviousSlot = () => {
    // current index
    const currentSlot = currentSlotId.value;
    console.log(currentSlot);
    const currentSlotIdx = props.project.project_data.findIndex((el) => el.id == currentSlot);
    console.log(currentSlotIdx);
    if (currentSlotIdx != 0 && (props.project.project_data.length) > 0 && currentSlotIdx <= (props.project.project_data.length - 1)) {
        currentSlotId.value = props.project.project_data[currentSlotIdx - 1].id;
        // update the data
        getCountData(currentSlotId.value);
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


const getCountData = (id) => {
    // save the current data
    updateCountData();

    // proceed to the next data
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

// update countData
const handleUpdateCount = (index, increment = false) => {
    if (increment) {
        countData.value.data[index][currentSide.value] = countData.value.data[index][currentSide.value] + 1;
    } else {
        countData.value.data[index][currentSide.value] = countData.value.data[index][currentSide.value] == 0 ? 0 : countData.value.data[index][currentSide.value] - 1;
    }

};

let videos = ref([]);
let currentVideoIdx = ref(null);


const getFileInputValue = (event) => {
    videos.value = [];
    var videoNode = document.querySelector('video')

    console.log(event.target.files)

    //get the file input value
    for (let index = 0; index < event.target.files.length; index++) {
        const file = event.target.files[index];
        if (videoNode.canPlayType(file.type) != '') {
            videos.value.push({
                name: file.name,
                src: URL.createObjectURL(file),
                type: file.type
            });
        }

    }

    if (videos.value.length > 0) {
        currentVideoIdx.value = 0;
        playVideo(0);
    }


};

const playNextVideo = () => {
    // current index
    let idx = currentVideoIdx.value;
    if (idx < videos.value.length - 1) {
        currentVideoIdx.value = currentVideoIdx.value + 1;
        playVideo(currentVideoIdx.value);
    }
}

const playPreviousVideo = () => {
    // current index
    let idx = currentVideoIdx.value;
    if (idx != 0 && (videos.value.length) > 0 && idx <= (videos.value.length - 1)) {
        currentVideoIdx.value = currentVideoIdx.value - 1;
        playVideo(currentVideoIdx.value);
    }
}

const playVideo = (index) => {
    let videoFile = videos.value[index];
    var videoNode = document.querySelector('video');
    console.log(videoNode);
    var canPlay = videoNode.canPlayType(videoFile.type)
    if (canPlay === '') {
        alert("can not play this video");
        return;
    }

    currentVideoIdx.value = index;
    videoNode.pause();
    videoNode.defaultPlaybackRate = parseInt(playbackSpeed.value);
    videoNode.playbackRate = parseFloat(playbackSpeed.value)
    videoNode.play();

}

// playback speed
let playbackSpeed = ref(1)
let playbackSpeeds = ref([1, 2, 4, 8, 16, 32]);
const handlePlaybackChange = () => {
    var videoNode = document.querySelector('video')
    const paused = videoNode.paused
    videoNode.pause();
    videoNode.defaultPlaybackRate = parseInt(playbackSpeed.value);
    videoNode.playbackRate = parseFloat(playbackSpeed.value)
    if (paused == false) {
        videoNode.play();
    }
};

const changePlaybackSpeed = (increase = true) => {
    // find index
    const idx = playbackSpeeds.value.indexOf(playbackSpeed.value);
    const len = playbackSpeeds.value.length;

    if (increase && idx < (len - 1)) {
        playbackSpeed.value = playbackSpeeds.value[idx + 1];
    }

    if (!increase && idx > 0 && idx <= (len - 1)) {
        playbackSpeed.value = playbackSpeeds.value[idx - 1];
    }

    handlePlaybackChange();

};


// update the data
let updating = ref(false);
const updateCountData = () => {
    console.log('updating....');
    if (countData.value == null || updating.value == true) {
        return
    }
    updating.value = true
    axios.post(route("projects.updateCountData", { id: countData.value.id }), { data: countData.value.data })
        .then(res => {
            console.log(res.data)
        })
        .catch(err => {
            console.log(err)
            alert(err.response.data.msg)

        })
        .finally(() => { updating.value = false })

};

// update the data
let resetting = ref(false);
const resetData = () => {
    console.log('updating....');

    if (confirm("Are you sure, you want to reset data ?") == false) {
        return;
    }

    if (countData.value == null || resetting.value == true) {
        return
    }
    resetting.value = true
    let changedData = countData.value.data.map(element => {
        let e = { ...element };
        e[currentSide.value] = 0
        return e;
    });
    countData.value.data = changedData;
    console.log(changedData);
    resetting.value = false;

    return alert("Now click Update data to save.");

};

// interval
let interval = ref()
onMounted(() => {
    interval = setInterval(() => {
        updateCountData();
    }, 1000 * 30);
});
onUnmounted(() => {
    clearInterval(interval);
});



</script>
<template>
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 overflow-x-hidden">

        <Head title="Count" />
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-4xl font-bold text-black">{{ project.title }}</h2>
            <div class="flex flex-row">
                <a :href='route("projects.export", { id: project.id })' target="_blank"
                    class="flex items-center gap-2 text-white bg-zinc-700 hover:bg-zinc-800 focus:outline-none focus:ring-4 focus:ring-zinc-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-zinc-600 dark:hover:bg-zinc-700 dark:focus:ring-zinc-800 disabled:cursor-not-allowed">
                    Export data
                </a>
                <button @click.prevent="resetData" :disabled="resetting || countData == null"
                    class="flex items-center gap-2 text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 disabled:cursor-not-allowed">
                    {{ resetting ? "Resetting...." : "Reset Data" }}
                </button>
                <button @click.prevent="updateCountData" :disabled="updating || countData == null"
                    class="flex items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:cursor-not-allowed">
                    {{ updating ? "Updating...." : "Update Data" }}
                </button>
            </div>
        </div>
        <div class="flex border rounded-lg px-2 py-4 pb-8 my-4 shadow-lg">
            <div class="w-1/2">
                <label class="block mb-2 text-md font-medium text-gray-900 dark:text-white" for="file_input">Select Video
                    file to play (.mp4)</label>
                <input
                    class="block py-1.5 px-1 w-full text-lg text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    id="file_input" type="file" @change="getFileInputValue" accept="video/mp4,video/x-m4v" multiple>
                <br>

                <div class="inline-flex rounded-md shadow-sm float-right" role="group">
                    <button type="button" @click.prevent="playPreviousVideo"
                        :disabled="currentVideoIdx == null || currentVideoIdx == 0"
                        class="px-4 py-2 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-l-lg hover:bg-blue-500 hover:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                        Previous
                    </button>

                    <button type="button" @click.prevent="playNextVideo"
                        :disabled="currentVideoIdx == null || currentVideoIdx == (videos.length - 1)"
                        class="px-4 py-2 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-r-md hover:bg-blue-500 hover:text-white  dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
                <br>

                <div v-if="currentVideoIdx != null">
                    <label for="playBackSpeed" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">Video
                        playback speed</label>
                    <select id="playBackSpeed" @change="handlePlaybackChange" v-model="playbackSpeed"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option :value="speed" v-for="speed in playbackSpeeds" :key="`speed-${speed}`">{{ `${speed}x` }}
                        </option>
                    </select>
                </div>

                <div class="mt-4">
                    <label for="countries" class="block mb-2 text-md font-medium text-gray-900 dark:text-white">Select a
                        time slot to work on</label>
                    <select id="countries" v-model="currentSlotId" @change="handleChange"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-md rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-1.5">
                        <option value="">Choose a time slot</option>
                        <option :value="slot.id" v-for="slot in project.project_data" :key="slot.id">{{ slot.start_time
                        }}-{{ slot.end_time }}
                        </option>
                    </select>
                    <FlashMessage />
                    <div class="inline-flex rounded-md shadow-sm float-right mt-2" role="group">
                        <button type="button" @click.prevent="selectPreviousSlot"
                            class="px-4 py-2 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-l-lg hover:bg-blue-500 hover:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                            Previous
                        </button>

                        <button type="button" @click.prevent="selectNextSlot"
                            class="px-4 py-2 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-r-md hover:bg-blue-500 hover:text-white  dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
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
            <div class="w-1/2 px-2 relative">
                <vue-plyr ref="plyrr">
                    <video controls muted :src="videos[currentVideoIdx]?.src" onended="() => {handleVideoEnd}"
                        class="w-full">
                    </video>
                </vue-plyr>

                <div class="test absolute top-0 left-0 bg-white px-3 py-2 transition-all w-3/4"
                    style="z-index: 2; display: none;">
                    <div class="flex flex-row">
                        <div class="grow">
                            Current video: <span class="text-red-600">{{ videos[currentVideoIdx]?.name }}</span>
                        </div>
                        <div class="clear-both rounded-md shadow-sm float-right text-sm" role="group">
                            <button type="button" @click.prevent="playPreviousVideo"
                                :disabled="currentVideoIdx == null || currentVideoIdx == 0"
                                class="px-3 py-1 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-l-lg hover:bg-blue-500 hover:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                                Previous video
                            </button>

                            <button type="button" @click.prevent="playNextVideo"
                                :disabled="currentVideoIdx == null || currentVideoIdx == (videos.length - 1)"
                                class="px-3 py-1 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-r-md hover:bg-blue-500 hover:text-white  dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                                Next video
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-row gap-x-2">
                        <div class="flex-1" v-if="currentVideoIdx != null">
                            <label for="playBackSpeed"
                                class="block mb-1 text-md font-medium text-gray-900 dark:text-white">Video
                                playback speed</label>
                            <select id="playBackSpeed" @change="handlePlaybackChange" v-model="playbackSpeed"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option :value="speed" v-for="speed in playbackSpeeds" :key="`speed-${speed}`">{{
                                    `${speed}x` }}
                                </option>
                            </select>
                        </div>
                        <!-- time-slot -->
                        <div class="flex-1">
                            <label for="countries"
                                class="block mb-1 text-md font-medium text-gray-900 dark:text-white">Select a
                                time slot to work on</label>
                            <select id="countries" v-model="currentSlotId" @change="handleChange"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-2 py-1">
                                <option value="">Choose a time slot</option>
                                <option :value="slot.id" v-for="slot in project.project_data" :key="slot.id">{{
                                    slot.start_time
                                }}-{{ slot.end_time }}
                                </option>
                            </select>
                            <FlashMessage />
                            <div class="inline-flex rounded-md shadow-sm float-right mt-1 text-xs" role="group">
                                <button type="button" @click.prevent="selectPreviousSlot"
                                    class="px-3 py-1 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-l-lg hover:bg-blue-500 hover:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                                    Previous
                                </button>

                                <button type="button" @click.prevent="selectNextSlot"
                                    class="px-3 py-1 text-xs font-medium text-blue-500 bg-transparent border border-blue-500 rounded-r-md hover:bg-blue-500 hover:text-white  dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-blue-700 dark:focus:bg-blue-700 disabled:cursor-not-allowed">
                                    Next
                                </button>
                            </div>
                        </div>

                        <!-- count data -->

                        <div class="flex-1" v-if="countData">
                            <h2 class="grow-0 text-md font-bold text-black">Time Slot: {{ countData.start_time }}-{{
                                countData.end_time }}
                            </h2>
                        </div>

                        <div class="flex-1" v-else>
                            <h2 class="grow-0 text-md font-bold text-red-600">
                                Select time slot
                            </h2>
                        </div>

                        <!-- current side -->

                        <div class="flex-1">
                            <h2 class="grow-0 text-md font-bold text-black"> Select current side</h2>
                            <div class="flex w-full" v-if="countData">
                                <div v-for="side in sides" :key="side"
                                    class="flex w-full items-center pl-4 border border-gray-200 rounded ml-3 px-2">
                                    <input v-model="currentSide" type="radio" name="side" :id="`side-${side}`" :value="side"
                                        class=" text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                                    <label :for="`side-${side}`"
                                        class="w-full py-2 ml-2 text-md font-medium text-gray-900 dark:text-gray-300">{{
                                            side
                                        }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="mt-1">Current video: <span class="ml-2 text-blue-500">{{ videos[currentVideoIdx]?.name }}</span>
                </div>
            </div>

        </div>

        <div class="border rounded-lg px-2 py-4 my-4 shadow-lg">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                v-if="countData == null && loading">
                <h2 class="text-4xl font-bold text-black">Loading data.
                    ....</h2>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                v-if="countData == null && loading == false">
                <h2 class="text-4xl font-bold text-black">Please choose a time slot</h2>
            </div>

            <div v-if="countData">
                <div class="grid gap-3 grid-cols-5" v-if="countData != null">
                    <div v-for="(item, i) in countData.data" :key="item.id" class="flex flex-col">
                        <div class="inline-flex rounded-md " role="group">
                            <span
                                class="px-4 py-2 grow text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-tl-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                {{ item.title }}
                            </span>
                            <span
                                class="px-4 py-2 text-md font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">

                                <kbd
                                    class="px-2 py-1.5 text-md font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">{{
                                        item.key }}</kbd>
                            </span>
                            <span
                                class="px-4 py-2 text-md font-medium text-gray-900 bg-white border border-gray-200 rounded-tr-md hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                {{ countData.data[i][currentSide] }}
                            </span>
                        </div>
                        <div class="inline-flex rounded-md " role="group">
                            <span @click.prevent="handleUpdateCount(i, true)" class="px-4 py-2 grow text-lg font-medium text-center text-gray-900 bg-white border border-gray-200 rounded-bl-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white cursor-pointer
">
                                +
                            </span>

                            <span @click.prevent="handleUpdateCount(i, false)"
                                class="px-4 py-2 grow text-lg text-center font-medium text-gray-900 bg-white border border-gray-200 rounded-br-md hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white cursor-pointer">
                                -
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
.test {
    transition: all 5s ease;
    -webkit-transition: all 5s ease;
    -moz-transition: all 5s ease;
    -o-transition: all 5s ease;
}
</style>
