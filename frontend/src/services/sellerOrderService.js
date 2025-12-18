import api from '../utils/api'

/**
 * Seller Order Service - API calls for Sales Order Management
 */

// Lấy danh sách đơn bán của seller
export const getSellerOrders = (status = null) => {
    const params = status && status !== 'all' ? { status } : {}
    return api.get('/seller/orders', { params })
}

// Chấp nhận đơn hàng (pending -> processing)
export const acceptOrder = (orderId) => {
    return api.put(`/seller/orders/${orderId}/accept`)
}

// Giao cho vận chuyển (processing -> shipping)
export const shipOrder = (orderId) => {
    return api.put(`/seller/orders/${orderId}/ship`)
}

// Seller hủy đơn
export const cancelOrder = (orderId) => {
    return api.put(`/seller/orders/${orderId}/cancel`)
}

// Xác nhận hoàn hàng (return -> refunded)
export const confirmReturn = (orderId) => {
    return api.put(`/seller/orders/${orderId}/confirm-return`)
}

export default {
    getSellerOrders,
    acceptOrder,
    shipOrder,
    cancelOrder,
    confirmReturn
}
