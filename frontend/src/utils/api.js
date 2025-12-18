import axios from 'axios'

// Backend apiPrefix is '' in bootstrap/app.php, so no /api suffix needed
const getBaseURL = () => {
  let url = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  // Remove trailing slash if present
  return url.replace(/\/+$/, '')
}

const api = axios.create({
  baseURL: getBaseURL(),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: true
})

// Add token to requests
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('app_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// Handle errors
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Unauthorized - clear auth and redirect to login
      localStorage.removeItem('app_token')
      localStorage.removeItem('app_user')
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api
