import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Admin from '../pages/Admin.vue'
import Login from '../pages/Login.vue'
import Profile from '../pages/Profile.vue'
import EditProfile from '../pages/EditProfile.vue'
import Favorites from '../pages/Favorites.vue'
import Forgot from '../pages/Forgot.vue'
import Register from '../pages/Register.vue'
import ManageListings from '../pages/ManageListings.vue';

import ProductCatalog from '../pages/ProductCatalog.vue'
import OrderManagement from '../pages/OrderManagement.vue'
import ProductDetail from '../pages/ProductDetail.vue';
import Chat from '../pages/Chat.vue';
import SalesOrderManagement from '../pages/SalesOrderManagement.vue';
import CreatePost from '../pages/CreatePost.vue';
import EditPost from '../pages/EditPost.vue';
import SellerProfile from '../pages/SellerProfile.vue';

const routes = [
    {
        path: '/',
        redirect: '/home'
    },
    {
        path: '/home',
        name: 'Home',
        component: Home
    },
    {
        path: '/admin',
        name: 'Admin',
        component: Admin,
        meta: { requiresAdmin: true }
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/profile',
        name: 'Profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        path: '/profile/edit',
        name: 'EditProfile',
        component: EditProfile,
        meta: { requiresAuth: true }
    },
    {
        path: '/favorites',
        name: 'Favorites',
        component: Favorites,
        meta: { requiresAuth: true }
    },
    {
        path: '/forgot',
        name: 'Forgot',
        component: Forgot
    },
    {
        path: '/register',
        name: 'Register',
        component: Register
    },
    {
        path: '/products',
        name: 'ProductCatalog',
        component: ProductCatalog
    },
    {
        path: '/orders',
        name: 'OrderManagement',
        component: OrderManagement,
        meta: { requiresAuth: true }
    },
    {
        path: '/product/:id', // ":id" là một tham số động
        name: 'ProductDetail', // Đặt tên cho route
        component: ProductDetail // Component sẽ hiển thị
    },
    {
        path: '/chat',
        name: 'Chat',
        component: Chat,
        meta: { requiresAuth: true }
    },
    {
        path: '/manage-posts',
        name: 'ManageListings',
        component: ManageListings,
        meta: { requiresAuth: true }
    },
    {
        path: '/post',
        name: 'CreatePost',
        component: CreatePost
    },

    { // <--- THÊM DÒNG NÀY
        path: '/edit-post/:id',
        name: 'EditPost', 
        component: EditPost }, 

    { 
        path: '/purchase-orders', 
        component: OrderManagement 
    },
    {
        path: '/sales-orders',
        component: SalesOrderManagement
    },
    {
        path: '/seller/:id',
        name: 'SellerProfile',
        component: SellerProfile
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Global guard: protect routes
import { useAuth } from '../utils/useAuth'

router.beforeEach((to, from, next) => {
    const { isLoggedIn, user } = useAuth()

    // Check admin routes
    if (to.meta && to.meta.requiresAdmin) {
        if (isLoggedIn.value && user.value && user.value.role === 'admin') {
            next()
        } else {
            next({ path: '/login' })
        }
    }
    // Check auth routes
    else if (to.meta && to.meta.requiresAuth) {
        if (isLoggedIn.value) {
            next()
        } else {
            next({ path: '/login' })
        }
    }
    else {
        next()
    }
})

export default router

