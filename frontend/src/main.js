import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './routes'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import '@vueform/slider/themes/default.css'// Thanh trượt chọn giá lọc

// 1. CHỈ IMPORT CÁC ICON FREE MÀ BẠN CẦN
import { 
    faHome, faTags, faEnvelope, faMapMarkerAlt, faFilter, faTrash,
    faChevronDown, faTimes, faChevronUp, faChair, faBed, faBlender, faUtensils, faFan, 
    faLightbulb, faTree, faToilet, faCouch, faDungeon, faKitchenSet,
    faThLarge, faList, faMobileAlt, faTabletAlt, faLaptop, faDesktop, faCamera, faTv, 
    faStopwatch, faPlug, faMicrochip, faCar, faMotorcycle, faTruck, faBolt,
    faBicycle, faTractor, faCog, faDog, faCat, faFish, faBone, faDove,
    faSnowflake, faSoap, faShirt, faClock, faShoePrints, faShoppingBag, faSprayCan, faGem,
    faGuitar, faBook, faFutbol, faImage, faGamepad, faCompactDisc,
    faPrint, faScrewdriverWrench, faDrumstickBite, faBacon, faEgg, faMugHot, 
    faBirthdayCake, faCandyCane, faCocktail, faEllipsisH, faCheck,
    faBars, faSearch, faHeart, faComment, faBell, faComments, faStore, faCalendarAlt, faSort,
    faLocationCrosshairs, faBuilding, faChevronRight, faArrowLeft, faHeadset
} from '@fortawesome/free-solid-svg-icons'


// 2. THÊM VÀO THƯ VIỆN
library.add(
    faHome, faTags, faEnvelope, faCheck,
    faMapMarkerAlt, faFilter, faTrash,  faCalendarAlt, faSort,
    faChevronDown, faTimes, faChevronUp,
    faChair, faBed, faBlender, faUtensils, faFan, 
    faLightbulb, faTree, faToilet, faCouch,
    faThLarge, faList, faDungeon, faKitchenSet,
    faMobileAlt, faTabletAlt, faLaptop, faDesktop, faCamera, faTv, 
    faStopwatch, faPlug, faMicrochip,
    faCar, faMotorcycle, faTruck, faBolt, faBicycle, faTractor, faCog,
    faDog, faCat, faFish, faBone, faDove, faComments, faStore,
    faSnowflake, faSoap, faShirt, faClock, faShoePrints, faShoppingBag, faSprayCan, faGem,
    faGuitar, faBook, faFutbol, faImage, faGamepad, faCompactDisc,
    faPrint, faScrewdriverWrench,faDrumstickBite, faBacon, faEgg, faMugHot, 
    faBirthdayCake, faCandyCane, faCocktail, faEllipsisH,
    faBars, faSearch, faHeart, faComment, faBell, faLocationCrosshairs, faBuilding, faChevronRight, faArrowLeft, faHeadset
)

// (brand icons were temporarily added here; removed to revert logo changes)


const app = createApp(App)

//Đăng ký component Icon
app.component('font-awesome-icon', FontAwesomeIcon)

app.use(router)
app.mount('#app')