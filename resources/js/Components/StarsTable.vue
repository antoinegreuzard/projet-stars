<template>
    <div>
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
                <td><img :src="star.image" alt="Image de star" style="width: 50px; height: auto;"></td>
                <td>{{ star.description }}</td>
                <td>
                    <!-- Boutons ou liens pour modifier/supprimer -->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            stars: []
        }
    },
    mounted() {
        this.fetchStars();
    },
    methods: {
        async fetchStars() {
            try {
                const response = await fetch('/api/stars', {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer YOUR_TOKEN'
                    }
                });
                if (!response.ok) throw new Error('Network response was not ok');
                this.stars = await response.json();
            } catch (error) {
                console.error("There was an error fetching the stars: ", error);
            }
        }
    }
}
</script>