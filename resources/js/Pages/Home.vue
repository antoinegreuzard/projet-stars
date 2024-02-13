<script setup>
import {ref, onMounted, computed} from 'vue';
import axios from 'axios';
import {Head, Link} from '@inertiajs/vue3';

const stars = ref([]);
const activeStarIndex = ref(0);

onMounted(async () => {
    try {
        const response = await axios.get('/api/stars');
        stars.value = response.data;
        if (stars.value.length > 0) activeStarIndex.value = 0;
    } catch (error) {
        console.error('Error fetching stars:', error);
    }
});

const selectedStar = computed(() => stars.value[activeStarIndex.value]);
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <Head title="Home"/>
        <nav class="bg-white shadow mb-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <Link :href="route('home')" class="text-lg font-semibold text-gray-800">Profile Browser</Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')" class="text-gray-800 hover:text-gray-600">Dashboard</Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-gray-800 hover:text-gray-600">Login</Link>
                            <Link :href="route('register')" class="text-gray-800 hover:text-gray-600">Register</Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="sm:flex p-6 sm:p-8">
                        <div class="sm:w-64 flex-shrink-0">
                            <ul class="space-y-1">
                                <li v-for="(star, index) in stars" :key="star.id" @click="activeStarIndex = index"
                                    :class="{'bg-gray-500 text-white': index === activeStarIndex}"
                                    class="cursor-pointer p-2 hover:bg-gray-300 rounded-md">
                                    {{ star.name }}
                                </li>
                            </ul>
                        </div>
                        <div class="flex-1 p-4">
                            <div v-if="selectedStar" class="space-y-4">
                                <h2 class="text-xl font-bold">{{ selectedStar.name }} {{ selectedStar.first_name }}</h2>
                                <img :src="selectedStar.image" alt="Star Image"
                                     class="w-full max-w-xs rounded-lg shadow-md">
                                <p>{{ selectedStar.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>