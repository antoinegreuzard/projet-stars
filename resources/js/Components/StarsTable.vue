<template>
    <div>
        <button @click="showCreateForm = true">Ajouter une star</button>

        <div v-if="showCreateForm">
            <input v-model="starForm.nom" placeholder="Nom">
            <input v-model="starForm.prenom" placeholder="Prénom">
            <input v-model="starForm.image" placeholder="URL de l'image">
            <textarea v-model="starForm.description" placeholder="Description"></textarea>
            <button @click="handleSubmitCreate">Créer</button>
            <button @click="showCreateForm = false">Annuler</button>
        </div>

        <div v-if="showEditForm">
            <input v-model="editingStar.nom" placeholder="Nom">
            <input v-model="editingStar.prenom" placeholder="Prénom">
            <input v-model="editingStar.image" placeholder="URL de l'image">
            <textarea v-model="editingStar.description" placeholder="Description"></textarea>
            <button @click="handleSubmitEdit">Modifier</button>
            <button @click="cancelEdit">Annuler</button>
        </div>

        <table>
            <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Image</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="star in stars" :key="star.id">
                <td>{{ star.nom }}</td>
                <td>{{ star.prenom }}</td>
                <td>
                    <img :src="star.image" alt="Star Image" style="width: 50px; height: auto;">
                </td>
                <td>{{ star.description }}</td>
                <td>
                    <button @click="prepareEditStar(star.id)">Modifier</button>
                    <button @click="deleteStar(star.id)">Supprimer</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    data() {
        return {
            stars: [],
            showCreateForm: false,
            showEditForm: false,
            starForm: {
                nom: '',
                prenom: '',
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
        async createStar() {
            try {
                const response = await axios.post('/api/stars', this.starForm, {
                    withCredentials: true,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                });
                this.stars.push(response.data);
                this.resetForm();
                this.showCreateForm = false;
            } catch (error) {
                console.error("There was an error creating the star: ", error);
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
            this.createStar();
        },
        handleSubmitEdit() {
            this.updateStar();
        },
        cancelEdit() {
            this.showEditForm = false;
            this.editingStar = null;
        },
        resetForm() {
            this.starForm = {nom: '', prenom: '', image: '', description: ''};
        }
    },
};
</script>

<style>
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
}
</style>