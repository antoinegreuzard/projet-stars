<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <button @click="showCreateForm = true"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition ease-in-out duration-150">
            Add a star
        </button>

        <div v-if="showCreateForm" class="mt-4 p-4 bg-gray-50 rounded-lg shadow-inner">
            <TextInput
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                v-model="starForm.name"
                placeholder="Name"
            />
            <TextInput
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                v-model="starForm.first_name"
                placeholder="Firstname"
            />
            <input type="file" @change="handleFileUpload"
                   class="input w-full mb-3 px-4 py-2 border rounded shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <textarea v-model="starForm.description" placeholder="Description"
                      class="textarea w-full mb-3 px-4 py-2 border rounded shadow-sm"></textarea>
            <div class="flex justify-end space-x-2">
                <button @click="handleSubmitCreate"
                        class="btn bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create
                </button>
                <button @click="showCreateForm = false"
                        class="btn bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel
                </button>
            </div>
        </div>

        <div v-if="showEditForm" class="mt-4 p-4 bg-gray-50 rounded-lg shadow-inner">
            <TextInput
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                v-model="editingStar.name"
                placeholder="Name"
            />
            <TextInput
                class="input w-full mb-3 px-4 py-2 border rounded shadow-sm"
                v-model="editingStar.first_name"
                placeholder="Firstname"
            />

            <div v-if="editingStar.image" class="mb-3">
                <img :src="editingStar.image" alt="Aperçu de l'image" class="w-32 h-auto rounded">
            </div>

            <input type="file" @change="handleFileUpload"
                   class="input w-full mb-3 px-4 py-2 border rounded shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

            <textarea v-model="editingStar.description" placeholder="Description"
                      class="textarea w-full mb-3 px-4 py-2 border rounded shadow-sm"></textarea>

            <div class="flex justify-end space-x-2">
                <button @click="handleSubmitEdit"
                        class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Modifier
                </button>
                <button @click="cancelEdit"
                        class="btn bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Annuler
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
                <tr v-for="star in stars" :key="star.id" class="border-b odd:bg-white even:bg-gray-50">
                    <td class="px-4 py-3">{{ star.name }}</td>
                    <td class="px-4 py-3">{{ star.first_name }}</td>
                    <td class="px-4 py-3"><img :src="star.image" alt="Star Image" class="w-12 h-auto rounded-full"></td>
                    <td class="px-4 py-3">{{ star.description }}</td>
                    <td class="px-4 py-3">
                        <button @click="prepareEditStar(star.id)"
                                class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">Edit
                        </button>
                        <button @click="deleteStar(star.id)"
                                class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded">Delete
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import TextInput from "@/Components/TextInput.vue";

export default {
    components: {TextInput},
    data() {
        return {
            stars: [],
            showCreateForm: false,
            showEditForm: false,
            starForm: {
                name: '',
                first_name: '',
                image: '',
                description: ''
            },
            editingStar: null,
        };
    },
    mounted() {
        this.fetchStars();
    },
    methods: {
        async fetchStars() {
            try {
                const response = await axios.get('/api/stars', {
                    withCredentials: true,
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                this.stars = response.data;
            } catch (error) {
                console.error("There was an error fetching the stars: ", error);
            }
        },
        async createStar(formData) {
            try {
                const response = await axios.post('/api/stars', formData, {
                    withCredentials: true,
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                });
                this.stars.push(response.data);
                this.resetForm();
                this.showCreateForm = false;
            } catch (error) {
                console.error("There was an error creating the star: ", error.response.data);
            }
        },
        async updateStar() {
            try {
                const response = await axios.put(`/api/stars/${this.editingStar.id}`, this.editingStar, {
                    withCredentials: true,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                });
                const index = this.stars.findIndex(star => star.id === this.editingStar.id);
                if (index !== -1) {
                    this.stars.splice(index, 1, response.data);
                }
                this.cancelEdit();
            } catch (error) {
                console.error("There was an error updating the star: ", error);
            }
        },
        async deleteStar(id) {
            try {
                await axios.delete(`/api/stars/${id}`, {
                    withCredentials: true,
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                this.stars = this.stars.filter(star => star.id !== id);
            } catch (error) {
                console.error("There was an error deleting the star: ", error);
            }
        },
        prepareEditStar(id) {
            this.editingStar = {...this.stars.find(star => star.id === id)};
            this.showEditForm = true;
        },
        handleSubmitCreate() {
            const formData = new FormData();
            formData.append('name', this.starForm.name);
            formData.append('first_name', this.starForm.first_name);
            if (this.starForm.image instanceof File) {
                formData.append('image', this.starForm.image);
            }
            formData.append('description', this.starForm.description);

            this.createStar(formData);
        },
        handleSubmitEdit() {
            this.updateStar();
        },
        cancelEdit() {
            this.showEditForm = false;
            this.editingStar = null;
        },
        resetForm() {
            this.starForm = {name: '', first_name: '', image: '', description: ''};
        },
        handleFileUpload(event) {
            if (this.showEditForm) {
                this.editingStar.newImage = event.target.files[0];
            } else {
                this.starForm.image = event.target.files[0];
            }
        }

    },
};
</script>