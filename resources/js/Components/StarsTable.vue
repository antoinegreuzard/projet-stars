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
                @change="handleFileUpload($event, false)"
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
            v-if="showEditForm && editingStar"
            ref="editFormContainer"
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
                    alt="Star Image"
                    class="w-32 h-auto rounded"
                />
            </div>

            <input
                type="file"
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                @change="handleFileUpload($event, true)"
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
                        <th class="px-4 py-3">First Name</th>
                        <th class="px-4 py-3">Image</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody v-if="stars">
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
                        <td v-if="star.id" class="px-4 py-3">
                            <button
                                class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded"
                                @click="prepareEditStar(star.id)"
                            >
                                Edit
                            </button>
                            <button
                                class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded"
                                @click="deleteStar(star.id)"
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
import { ref, onMounted, nextTick } from 'vue'
import axios from '@/axiosConfig'
import TextInput from '@/Components/TextInput.vue'
import type { Ref } from 'vue'
import { Star } from '@/types'

// State variables
const stars: Ref<Star[]> = ref([])
const showCreateForm = ref(false)
const showEditForm = ref(false)
const starForm: Ref<Star> = ref({
    name: '',
    first_name: '',
    image: '',
    description: ''
})
const editingStar: Ref<Star | null> = ref(null)
const editFormContainer = ref<HTMLElement | null>(null)

// Fetch stars from API
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

// Handle file upload for both create and edit forms
const handleFileUpload = (event: Event, isEdit: boolean) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        const file = target.files[0]
        if (isEdit) {
            if (editingStar.value) editingStar.value.newImage = file
        } else {
            starForm.value.newImage = file
        }
    }
}

// Handle form submission for both create and edit
const handleFormSubmit = async (isEdit: boolean) => {
    const formData = new FormData()
    const starData = isEdit ? editingStar.value : starForm.value

    if (!starData) return

    formData.append('name', starData.name)
    formData.append('first_name', starData.first_name)
    formData.append('description', starData.description)

    if (starData.newImage) {
        formData.append('image', starData.newImage, starData.newImage.name)
    }

    if (isEdit && starData.id) {
        formData.append('_method', 'PUT')
    }

    try {
        const axiosConfig = {
            method: 'post',
            url: `/api/stars${isEdit && starData.id ? `/${starData.id}` : ''}`,
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' },
            withCredentials: true
        }

        const axiosResponse = await axios(axiosConfig)

        if (isEdit && editingStar.value) {
            const index = stars.value.findIndex(
                star => star.id === editingStar.value?.id
            )
            if (index !== -1) {
                stars.value.splice(index, 1, axiosResponse.data)
            }
            cancelEdit()
        } else {
            stars.value.push(axiosResponse.data)
            resetForm()
            showCreateForm.value = false
        }
    } catch (error) {
        console.error('There was an error processing the star: ', error)
    }
}

// Handle create form submission
const handleSubmitCreate = () => handleFormSubmit(false)

// Handle edit form submission
const handleSubmitEdit = () => handleFormSubmit(true)

// Reset form data
const resetForm = () => {
    starForm.value = {
        name: '',
        first_name: '',
        image: '',
        description: ''
    }
}

// Cancel edit and reset edit form
const cancelEdit = () => {
    showEditForm.value = false
    editingStar.value = null
}

// Prepare edit form with star data
const prepareEditStar = (id: number) => {
    const star = stars.value.find(starEl => starEl.id === id)
    if (star) {
        editingStar.value = { ...star, newImage: undefined }
        showEditForm.value = true
        nextTick(() => {
            if (editFormContainer.value) {
                editFormContainer.value.scrollIntoView({ behavior: 'smooth' })
            }
        })
    }
}

// Delete star by ID
const deleteStar = async (id: number) => {
    try {
        await axios.post(`/api/stars/${id}`, { _method: 'delete' })
        stars.value = stars.value.filter(star => star.id !== id)
    } catch (error) {
        console.error('There was an error deleting the star: ', error)
    }
}

// Fetch stars on component mount
onMounted(fetchStars)
</script>
