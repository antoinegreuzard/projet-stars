<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <button
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition ease-in-out duration-150"
            @click="showCreateForm = true"
        >
            Add a star
        </button>

        <div
            v-if="showCreateForm"
            class="mt-4 p-4 bg-gray-50 rounded-lg shadow-inner"
        >
            <TextInput
                v-model="starForm.name"
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                placeholder="Name"
            />
            <TextInput
                v-model="starForm.first_name"
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                placeholder="Firstname"
            />
            <input
                type="file"
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                @change="handleFileUpload"
            />
            <textarea
                v-model="starForm.description"
                placeholder="Description"
                class="textarea w-full mb-3 px-4 py-2 border rounded shadow-sm"
            ></textarea>
            <div class="flex justify-end space-x-2">
                <button
                    class="btn bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                    @click="handleSubmitCreate"
                >
                    Create
                </button>
                <button
                    class="btn bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                    @click="showCreateForm = false"
                >
                    Cancel
                </button>
            </div>
        </div>

        <div
            v-if="showEditForm"
            class="mt-4 p-4 bg-gray-50 rounded-lg shadow-inner"
        >
            <TextInput
                v-model="editingStar.name"
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                placeholder="Name"
            />
            <TextInput
                v-model="editingStar.first_name"
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                placeholder="Firstname"
            />

            <div v-if="editingStar.image" class="mb-3">
                <img
                    :src="editingStar.image"
                    alt="Aperçu de l'image"
                    class="w-32 h-auto rounded"
                />
            </div>

            <input
                type="file"
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                @change="handleFileUpload"
            />

            <textarea
                v-model="editingStar.description"
                placeholder="Description"
                class="textarea w-full mb-3 px-4 py-2 border rounded shadow-sm"
            ></textarea>

            <div class="flex justify-end space-x-2">
                <button
                    class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    @click="handleSubmitEdit"
                >
                    Save
                </button>
                <button
                    class="btn bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                    @click="cancelEdit"
                >
                    Cancel
                </button>
            </div>
        </div>
        <div class="overflow-x-auto mt-6">
            <table class="w-full text-left rounded-lg overflow-hidden">
                <thead class="bg-gray-200 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">first_name</th>
                        <th class="px-4 py-3">Image</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="star in stars"
                        :key="star.id"
                        class="border-b odd:bg-white even:bg-gray-50"
                    >
                        <td class="px-4 py-3">{{ star.name }}</td>
                        <td class="px-4 py-3">{{ star.first_name }}</td>
                        <td class="px-4 py-3">
                            <img
                                :src="star.image"
                                alt="Star Image"
                                class="w-12 h-auto rounded-full"
                            />
                        </td>
                        <td class="px-4 py-3">{{ star.description }}</td>
                        <td class="px-4 py-3">
                            <button
                                class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded"
                                @click="prepareEditStar(star.id)"
                            >
                                Edit
                            </button>
                            <button
                                class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded"
                                @click="
                                    star.id !== undefined && deleteStar(star.id)
                                "
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import TextInput from '@/Components/TextInput.vue'
import type { Ref } from 'vue'

// Définir les interfaces pour le typage
interface Star {
    id?: number
    name: string
    first_name: string
    image: string
    description: string
    newImage?: File
}

// Réactives references
const stars: Ref<Star[]> = ref([])
const showCreateForm = ref(false)
const showEditForm = ref(false)
const starForm: Ref<Star> = ref({
    name: '',
    first_name: '',
    image: '',
    description: ''
})
const editingStar: Ref<Star> = ref({
    id: undefined,
    name: '',
    first_name: '',
    image: '',
    description: ''
})

const resetForm = () => {
    starForm.value = {
        name: '',
        first_name: '',
        image: '',
        description: ''
    }
}

const cancelEdit = () => {
    showEditForm.value = false
    editingStar.value = {
        id: undefined,
        name: '',
        first_name: '',
        image: '',
        description: ''
    }
}

// Méthodes
const fetchStars = async () => {
    try {
        const response = await axios.get('/api/stars', {
            withCredentials: true,
            headers: { Accept: 'application/json' }
        })
        stars.value = response.data
    } catch (error) {
        console.error('There was an error fetching the stars: ', error)
    }
}

const createStar = async (formData: FormData) => {
    try {
        const response = await axios.post('/api/stars', formData, {
            withCredentials: true,
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        stars.value.push(response.data)
        resetForm()
        showCreateForm.value = false
    } catch (error: unknown) {
        if (axios.isAxiosError(error)) {
            console.error('There was an error: ', error.response?.data)
        } else {
            console.error('Error', error)
        }
    }
}

const updateStar = async () => {
    if (!editingStar.value) return
    try {
        const formData = new FormData()
        formData.append('name', editingStar.value.name)
        formData.append('first_name', editingStar.value.first_name)
        if (editingStar.value.newImage) {
            formData.append('image', editingStar.value.newImage)
        }
        formData.append('description', editingStar.value.description)

        const response = await axios.post(
            `/api/stars/${editingStar.value.id}`,
            formData,
            {
                withCredentials: true,
                headers: { 'Content-Type': 'multipart/form-data' }
            }
        )
        const index = stars.value.findIndex(
            star => star.id === editingStar.value.id
        )
        if (index !== -1) {
            stars.value.splice(index, 1, response.data)
        }
        cancelEdit()
    } catch (error) {
        console.error('There was an error updating the star: ', error)
    }
}

const deleteStar = async (id: number) => {
    try {
        await axios.delete(`/api/stars/${id}`, {
            withCredentials: true,
            headers: { Accept: 'application/json' }
        })
        stars.value = stars.value.filter(star => star.id !== id)
    } catch (error) {
        console.error('There was an error deleting the star: ', error)
    }
}

const prepareEditStar = (id: number | undefined) => {
    if (typeof id !== 'undefined') {
        const star = stars.value.find(starElement => starElement.id === id)
        if (star) {
            editingStar.value = { ...star }
            showEditForm.value = true
        }
    }
}

const handleSubmitCreate = () => {
    const formData = new FormData()
    formData.append('name', starForm.value.name)
    formData.append('first_name', starForm.value.first_name)
    if (starForm.value.image) {
        formData.append('image', starForm.value.image)
    }
    formData.append('description', starForm.value.description)

    createStar(formData)
}

const handleSubmitEdit = () => {
    updateStar()
}

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        const file = target.files[0]
        const fileUrl = URL.createObjectURL(file)
        if (showEditForm.value) {
            editingStar.value.image = fileUrl
        } else {
            starForm.value.image = fileUrl
        }
    }
}

onMounted(fetchStars)
</script>
