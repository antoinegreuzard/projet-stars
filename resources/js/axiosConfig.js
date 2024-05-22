import axios from 'axios'

axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Function to fetch the CSRF token
export const fetchCsrfToken = async () => {
    try {
        await axios.get('/sanctum/csrf-cookie')
    } catch (error) {
        console.error('Error fetching CSRF token:', error)
    }
}

export default axios
