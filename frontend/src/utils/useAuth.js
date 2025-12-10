import { ref, computed } from 'vue'
import api from './api'

const tokenKey = 'app_token'
const userKey = 'app_user'

const token = ref(localStorage.getItem(tokenKey) || '')
const user = ref(JSON.parse(localStorage.getItem(userKey) || 'null'))

function login(newToken, userInfo = null){
  token.value = newToken
  user.value = userInfo
  localStorage.setItem(tokenKey, newToken)
  if(userInfo) localStorage.setItem(userKey, JSON.stringify(userInfo))
}

function updateUser(userInfo) {
  user.value = userInfo
  localStorage.setItem(userKey, JSON.stringify(userInfo))
}

async function logout(){
  try {
    await api.post('/auth/logout')
  } catch (err) {
    console.error('Logout error:', err)
  } finally {
    token.value = ''
    user.value = null
    localStorage.removeItem(tokenKey)
    localStorage.removeItem(userKey)
  }
}

const isLoggedIn = computed(()=> !!token.value)

export function useAuth(){
  return {
    token,
    user,
    isLoggedIn,
    login,
    updateUser,
    logout
  }
}

export default useAuth
