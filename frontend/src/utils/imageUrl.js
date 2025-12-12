/**
 * Helper để xử lý URL hình ảnh từ backend
 * Chuyển đổi các URL relative thành absolute URL
 */

// Sử dụng environment variable hoặc fallback về localhost
const BACKEND_URL = import.meta.env.VITE_API_URL?.replace('/api', '') || 'http://localhost:8000';
const DEFAULT_PLACEHOLDER = 'https://via.placeholder.com/200/eeeeee/cccccc?text=No+Image';

/**
 * Chuyển đổi URL hình ảnh từ backend thành URL đầy đủ
 * @param {string} url - URL hình ảnh từ API
 * @param {string} placeholder - URL placeholder nếu không có hình
 * @returns {string} URL đầy đủ
 */
export const getImageUrl = (url, placeholder = DEFAULT_PLACEHOLDER) => {
  if (!url) return placeholder;
  
  // Nếu đã là URL đầy đủ (https:// hoặc http:// không phải localhost)
  if (url.startsWith('https://')) {
    return url;
  }
  
  if (url.startsWith('http://') && !url.includes('localhost')) {
    return url;
  }
  
  // Fix URL localhost thiếu port 8000 (development)
  if (url.startsWith('http://localhost/')) {
    return url.replace('http://localhost/', BACKEND_URL + '/');
  }
  
  // Nếu là relative URL bắt đầu với /storage (local development)
  if (url.startsWith('/storage')) {
    return BACKEND_URL + url;
  }
  
  return url;
};

export default getImageUrl;
