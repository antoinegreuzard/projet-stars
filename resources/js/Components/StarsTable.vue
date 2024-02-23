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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import TextInput from '@/Components/TextInput.vue'
import type { Ref } from 'vue'

interface Star {
    id?: number
    name: string
    first_name: string
    image: string
    description: string
    newImage?: File
}

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
    editingStar.value = null
}

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

const handleFormSubmit = async (isEdit: boolean) => {
    const formData = new FormData()
    const starData = isEdit ? editingStar.value : starForm.value

    if (!starData) return

    console.log(isEdit)
    console.log(starData.name)

    formData.append('name', starData.name)
    formData.append('first_name', starData.first_name)
    formData.append('description', starData.description)

    console.log(formData.get('name'))

    if (starData.newImage) {
        formData.append('image', starData.newImage, starData.newImage.name)
    }

    if (isEdit) {
        formData.append('_method', 'PUT')
    }

    try {
        const axiosConfig = {
            method: 'post',
            url: `/api/stars/${isEdit && starData.id ? starData.id : ''}`,
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' },
            withCredentials: true
        }
        console.log(axiosConfig)

        const axiosResponse = await axios(axiosConfig)
        console.log(axiosResponse)

        if (isEdit) {
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

const handleSubmitCreate = () => handleFormSubmit(false)
const handleSubmitEdit = () => handleFormSubmit(true)

const deleteStar = async (id: number) => {
    if (id === undefined) return

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

const prepareEditStar = (id: number) => {
    if (id === undefined) return

    const star = stars.value.find(starEl => starEl.id === id)
    if (star) {
        editingStar.value = { ...star, newImage: undefined }
        showEditForm.value = true
    }
}

const handleFileUpload = (event: Event, isEdit: boolean) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        const file = target.files[0]
        if (isEdit) {
            editingStar.value!.newImage = file
        } else {
            starForm.value.newImage = file
        }
    }
}

onMounted(fetchStars)
</script>
