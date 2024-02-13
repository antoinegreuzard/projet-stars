<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <Head title="Home"/>
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4 w-full">
                <h1 class="text-4xl font-bold text-center w-full">Profile Browser</h1>
                <div class="absolute top-0 right-0 p-6">
                    <Link v-if="!$page.props.auth.user && canLogin" :href="route('login')"
                          class="text-blue-500 hover:underline mr-4">Log in
                    </Link>
                    <Link v-if="!$page.props.auth.user && canRegister" :href="route('register')"
                          class="text-blue-500 hover:underline">Register
                    </Link>
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-blue-500 hover:underline">
                        Dashboard
                    </Link>
                </div>
            </div>
            <div class="flex mt-8">
                <div class="w-40 border-r-2 border-gray-200">
                    <ul>
                        <li v-for="(star, index) in stars" :key="star.id"
                            :class="{'bg-green-500 text-white': index === activeStarIndex}"
                            @click="activeStarIndex = index" class="cursor-pointer p-2 hover:bg-green-300">
                            {{ star.name }}
                        </li>
                    </ul>
                </div>
                <div class="flex-1 p-4">
                    <div v-if="selectedStar">
                        <h2 class="text-xl font-bold">{{ selectedStar.name }} {{ selectedStar.first_name }}</h2>
                        <img :src="selectedStar.image" alt="Star Image" class="w-48 h-auto mt-4 rounded">
                        <p class="mt-4">{{ selectedStar.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue';
import {Head, Link} from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const stars = ref([]);
const activeStarIndex = ref(0);

onMounted(async () => {
    try {
        const response = await axios.get('/api/stars');
        stars.value = response.data;
        if (stars.value.length > 0) {
            activeStarIndex.value = 0;
        }
    } catch (error) {
        console.error('Error fetching stars:', error);
    }
});

const selectedStar = computed(() => stars.value[activeStarIndex.value]);
</script>
