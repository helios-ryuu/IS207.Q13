// Dữ liệu địa chỉ Việt Nam (rút gọn các quận/huyện và phường/xã phổ biến)
// Structure: provinces -> districts -> wards

export const provinces = [
    { code: 'HN', name: 'Hà Nội' },
    { code: 'HCM', name: 'TP. Hồ Chí Minh' },
    { code: 'DN', name: 'Đà Nẵng' },
    { code: 'HP', name: 'Hải Phòng' },
    { code: 'CT', name: 'Cần Thơ' },
    { code: 'AGG', name: 'An Giang' },
    { code: 'BRV', name: 'Bà Rịa - Vũng Tàu' },
    { code: 'BGG', name: 'Bắc Giang' },
    { code: 'BKN', name: 'Bắc Kạn' },
    { code: 'BLU', name: 'Bạc Liêu' },
    { code: 'BNH', name: 'Bắc Ninh' },
    { code: 'BTR', name: 'Bến Tre' },
    { code: 'BDH', name: 'Bình Định' },
    { code: 'BDG', name: 'Bình Dương' },
    { code: 'BPC', name: 'Bình Phước' },
    { code: 'BTN', name: 'Bình Thuận' },
    { code: 'CMU', name: 'Cà Mau' },
    { code: 'CBG', name: 'Cao Bằng' },
    { code: 'DKN', name: 'Đắk Nông' },
    { code: 'DLK', name: 'Đắk Lắk' },
    { code: 'DBN', name: 'Điện Biên' },
    { code: 'DNI', name: 'Đồng Nai' },
    { code: 'DTP', name: 'Đồng Tháp' },
    { code: 'GLI', name: 'Gia Lai' },
    { code: 'HGG', name: 'Hà Giang' },
    { code: 'HNM', name: 'Hà Nam' },
    { code: 'HTA', name: 'Hà Tĩnh' },
    { code: 'HDG', name: 'Hải Dương' },
    { code: 'HBH', name: 'Hòa Bình' },
    { code: 'HGG2', name: 'Hậu Giang' },
    { code: 'HYN', name: 'Hưng Yên' },
    { code: 'KHA', name: 'Khánh Hòa' },
    { code: 'KGG', name: 'Kiên Giang' },
    { code: 'KTM', name: 'Kon Tum' },
    { code: 'LCI', name: 'Lai Châu' },
    { code: 'LDG', name: 'Lâm Đồng' },
    { code: 'LSN', name: 'Lạng Sơn' },
    { code: 'LCA', name: 'Lào Cai' },
    { code: 'LAN', name: 'Long An' },
    { code: 'NDH', name: 'Nam Định' },
    { code: 'NAN', name: 'Nghệ An' },
    { code: 'NBH', name: 'Ninh Bình' },
    { code: 'NTN', name: 'Ninh Thuận' },
    { code: 'PTH', name: 'Phú Thọ' },
    { code: 'PYN', name: 'Phú Yên' },
    { code: 'QBH', name: 'Quảng Bình' },
    { code: 'QNM', name: 'Quảng Nam' },
    { code: 'QNI', name: 'Quảng Ngãi' },
    { code: 'QNH', name: 'Quảng Ninh' },
    { code: 'QTI', name: 'Quảng Trị' },
    { code: 'STG', name: 'Sóc Trăng' },
    { code: 'SLA', name: 'Sơn La' },
    { code: 'TNH', name: 'Tây Ninh' },
    { code: 'TBH', name: 'Thái Bình' },
    { code: 'TNN', name: 'Thái Nguyên' },
    { code: 'THH', name: 'Thanh Hóa' },
    { code: 'TTH', name: 'Thừa Thiên Huế' },
    { code: 'TGG', name: 'Tiền Giang' },
    { code: 'TVH', name: 'Trà Vinh' },
    { code: 'TQG', name: 'Tuyên Quang' },
    { code: 'VLG', name: 'Vĩnh Long' },
    { code: 'VPC', name: 'Vĩnh Phúc' },
    { code: 'YBI', name: 'Yên Bái' }
];

export const districts = {
    'HN': [
        { code: 'HN_BD', name: 'Quận Ba Đình' },
        { code: 'HN_HK', name: 'Quận Hoàn Kiếm' },
        { code: 'HN_TH', name: 'Quận Tây Hồ' },
        { code: 'HN_LC', name: 'Quận Long Biên' },
        { code: 'HN_CG', name: 'Quận Cầu Giấy' },
        { code: 'HN_DD', name: 'Quận Đống Đa' },
        { code: 'HN_HM', name: 'Quận Hai Bà Trưng' },
        { code: 'HN_HXN', name: 'Quận Hoàng Mai' },
        { code: 'HN_TL', name: 'Quận Thanh Xuân' },
        { code: 'HN_SD', name: 'Huyện Sóc Sơn' },
        { code: 'HN_DL', name: 'Huyện Đông Anh' },
        { code: 'HN_GL', name: 'Huyện Gia Lâm' },
        { code: 'HN_NM', name: 'Quận Nam Từ Liêm' },
        { code: 'HN_TL2', name: 'Quận Bắc Từ Liêm' },
        { code: 'HN_MT', name: 'Huyện Mê Linh' },
        { code: 'HN_HD', name: 'Quận Hà Đông' },
        { code: 'HN_ST', name: 'Thị xã Sơn Tây' },
        { code: 'HN_BA', name: 'Huyện Ba Vì' },
        { code: 'HN_PT', name: 'Huyện Phúc Thọ' },
        { code: 'HN_DH', name: 'Huyện Đan Phượng' },
        { code: 'HN_HL', name: 'Huyện Hoài Đức' },
        { code: 'HN_QO', name: 'Huyện Quốc Oai' },
        { code: 'HN_TH2', name: 'Huyện Thạch Thất' },
        { code: 'HN_CT', name: 'Huyện Chương Mỹ' },
        { code: 'HN_TO', name: 'Huyện Thanh Oai' },
        { code: 'HN_TT', name: 'Huyện Thường Tín' },
        { code: 'HN_PX', name: 'Huyện Phú Xuyên' },
        { code: 'HN_UH', name: 'Huyện Ứng Hòa' },
        { code: 'HN_ML', name: 'Huyện Mỹ Đức' },
        { code: 'HN_TT2', name: 'Huyện Thanh Trì' }
    ],
    'HCM': [
        { code: 'HCM_Q1', name: 'Quận 1' },
        { code: 'HCM_Q3', name: 'Quận 3' },
        { code: 'HCM_Q4', name: 'Quận 4' },
        { code: 'HCM_Q5', name: 'Quận 5' },
        { code: 'HCM_Q6', name: 'Quận 6' },
        { code: 'HCM_Q7', name: 'Quận 7' },
        { code: 'HCM_Q8', name: 'Quận 8' },
        { code: 'HCM_Q10', name: 'Quận 10' },
        { code: 'HCM_Q11', name: 'Quận 11' },
        { code: 'HCM_Q12', name: 'Quận 12' },
        { code: 'HCM_BT', name: 'Quận Bình Thạnh' },
        { code: 'HCM_TB', name: 'Quận Tân Bình' },
        { code: 'HCM_TP', name: 'Quận Tân Phú' },
        { code: 'HCM_PN', name: 'Quận Phú Nhuận' },
        { code: 'HCM_TD', name: 'TP. Thủ Đức' },
        { code: 'HCM_GV', name: 'Quận Gò Vấp' },
        { code: 'HCM_BC', name: 'Huyện Bình Chánh' },
        { code: 'HCM_CG', name: 'Huyện Củ Chi' },
        { code: 'HCM_HM', name: 'Huyện Hóc Môn' },
        { code: 'HCM_NBE', name: 'Huyện Nhà Bè' },
        { code: 'HCM_CG2', name: 'Huyện Cần Giờ' }
    ],
    'DN': [
        { code: 'DN_HC', name: 'Quận Hải Châu' },
        { code: 'DN_TK', name: 'Quận Thanh Khê' },
        { code: 'DN_ST', name: 'Quận Sơn Trà' },
        { code: 'DN_NK', name: 'Quận Ngũ Hành Sơn' },
        { code: 'DN_LC', name: 'Quận Liên Chiểu' },
        { code: 'DN_CM', name: 'Quận Cẩm Lệ' },
        { code: 'DN_HV', name: 'Huyện Hòa Vang' },
        { code: 'DN_HS', name: 'Huyện Hoàng Sa' }
    ],
    'HP': [
        { code: 'HP_HB', name: 'Quận Hồng Bàng' },
        { code: 'HP_NK', name: 'Quận Ngô Quyền' },
        { code: 'HP_LC', name: 'Quận Lê Chân' },
        { code: 'HP_KA', name: 'Quận Kiến An' },
        { code: 'HP_HC', name: 'Quận Hải An' },
        { code: 'HP_DB', name: 'Quận Đồ Sơn' },
        { code: 'HP_DP', name: 'Quận Dương Kinh' },
        { code: 'HP_TL', name: 'Huyện Thủy Nguyên' },
        { code: 'HP_AL', name: 'Huyện An Lão' },
        { code: 'HP_KT', name: 'Huyện Kiến Thụy' },
        { code: 'HP_TH', name: 'Huyện Tiên Lãng' },
        { code: 'HP_VB', name: 'Huyện Vĩnh Bảo' },
        { code: 'HP_CB', name: 'Huyện Cát Hải' },
        { code: 'HP_BL', name: 'Huyện Bạch Long Vĩ' },
        { code: 'HP_AL2', name: 'Huyện An Dương' }
    ],
    'CT': [
        { code: 'CT_NK', name: 'Quận Ninh Kiều' },
        { code: 'CT_OT', name: 'Quận Ô Môn' },
        { code: 'CT_BT', name: 'Quận Bình Thủy' },
        { code: 'CT_CR', name: 'Quận Cái Răng' },
        { code: 'CT_TN', name: 'Quận Thốt Nốt' },
        { code: 'CT_VT', name: 'Huyện Vĩnh Thạnh' },
        { code: 'CT_CL', name: 'Huyện Cờ Đỏ' },
        { code: 'CT_PD', name: 'Huyện Phong Điền' },
        { code: 'CT_TL', name: 'Huyện Thới Lai' }
    ],
    // Các tỉnh còn lại
    'AGG': [
        { code: 'AGG_LX', name: 'TP. Long Xuyên' },
        { code: 'AGG_CDoc', name: 'TP. Châu Đốc' },
        { code: 'AGG_AP', name: 'Huyện An Phú' },
        { code: 'AGG_TT', name: 'Huyện Tịnh Biên' },
        { code: 'AGG_TX', name: 'Huyện Tri Tôn' },
        { code: 'AGG_CTP', name: 'Huyện Châu Phú' },
        { code: 'AGG_CTH', name: 'Huyện Châu Thành' }
    ],
    'BRV': [
        { code: 'BRV_VT', name: 'TP. Vũng Tàu' },
        { code: 'BRV_BR', name: 'TP. Bà Rịa' },
        { code: 'BRV_XM', name: 'Huyện Xuyên Mộc' },
        { code: 'BRV_LĐ', name: 'Huyện Long Điền' },
        { code: 'BRV_ĐĐ', name: 'Huyện Đất Đỏ' },
        { code: 'BRV_CT', name: 'Huyện Châu Đức' }
    ],
    'BGG': [
        { code: 'BGG_BG', name: 'TP. Bắc Giang' },
        { code: 'BGG_YT', name: 'Huyện Yên Thế' },
        { code: 'BGG_TC', name: 'Huyện Tân Yên' },
        { code: 'BGG_LC', name: 'Huyện Lạng Giang' },
        { code: 'BGG_LS', name: 'Huyện Lục Nam' },
        { code: 'BGG_HH', name: 'Huyện Hiệp Hòa' }
    ],
    'BKN': [
        { code: 'BKN_BK', name: 'TP. Bắc Kạn' },
        { code: 'BKN_PT', name: 'Huyện Pác Nặm' },
        { code: 'BKN_BT', name: 'Huyện Ba Bể' },
        { code: 'BKN_NR', name: 'Huyện Ngân Sơn' },
        { code: 'BKN_CH', name: 'Huyện Chợ Đồn' }
    ],
    'BLU': [
        { code: 'BLU_BL', name: 'TP. Bạc Liêu' },
        { code: 'BLU_HX', name: 'Huyện Hồng Dân' },
        { code: 'BLU_PC', name: 'Huyện Phước Long' },
        { code: 'BLU_VC', name: 'Huyện Vĩnh Lợi' },
        { code: 'BLU_GT', name: 'TX. Giá Rai' }
    ],
    'BNH': [
        { code: 'BNH_BN', name: 'TP. Bắc Ninh' },
        { code: 'BNH_TN', name: 'TX. Từ Sơn' },
        { code: 'BNH_YP', name: 'Huyện Yên Phong' },
        { code: 'BNH_QV', name: 'Huyện Quế Võ' },
        { code: 'BNH_TE', name: 'Huyện Tiên Du' },
        { code: 'BNH_TB', name: 'Huyện Thuận Thành' }
    ],
    'BTR': [
        { code: 'BTR_BT', name: 'TP. Bến Tre' },
        { code: 'BTR_CTH', name: 'Huyện Châu Thành' },
        { code: 'BTR_CT', name: 'Huyện Chợ Lách' },
        { code: 'BTR_MC', name: 'Huyện Mỏ Cày Nam' },
        { code: 'BTR_GT', name: 'Huyện Giồng Trôm' },
        { code: 'BTR_BĐ', name: 'Huyện Bình Đại' }
    ],
    'BDH': [
        { code: 'BDH_QN', name: 'TP. Quy Nhơn' },
        { code: 'BDH_AS', name: 'TX. An Nhơn' },
        { code: 'BDH_HS', name: 'TX. Hoài Nhơn' },
        { code: 'BDH_TH', name: 'Huyện Tuy Phước' },
        { code: 'BDH_PK', name: 'Huyện Phù Cát' }
    ],
    'BDG': [
        { code: 'BDG_TD', name: 'TP. Thủ Dầu Một' },
        { code: 'BDG_TA', name: 'TP. Thuận An' },
        { code: 'BDG_DA', name: 'TP. Dĩ An' },
        { code: 'BDG_BA', name: 'TX. Bến Cát' },
        { code: 'BDG_TU', name: 'TX. Tân Uyên' },
        { code: 'BDG_BC', name: 'Huyện Bàu Bàng' },
        { code: 'BDG_DC', name: 'Huyện Dầu Tiếng' },
        { code: 'BDG_PA', name: 'Huyện Phú Giáo' }
    ],
    'BPC': [
        { code: 'BPC_DP', name: 'TX. Đồng Xoài' },
        { code: 'BPC_PC', name: 'TX. Phước Long' },
        { code: 'BPC_BL', name: 'TX. Bình Long' },
        { code: 'BPC_BĐ', name: 'Huyện Bù Đăng' },
        { code: 'BPC_CJ', name: 'Huyện Chơn Thành' }
    ],
    'BTN': [
        { code: 'BTN_PT', name: 'TP. Phan Thiết' },
        { code: 'BTN_LG', name: 'TX. La Gi' },
        { code: 'BTN_TL', name: 'Huyện Tuy Phong' },
        { code: 'BTN_BC', name: 'Huyện Bắc Bình' },
        { code: 'BTN_HT', name: 'Huyện Hàm Thuận Bắc' }
    ],
    'CMU': [
        { code: 'CMU_CM', name: 'TP. Cà Mau' },
        { code: 'CMU_UD', name: 'Huyện U Minh' },
        { code: 'CMU_TB', name: 'Huyện Thới Bình' },
        { code: 'CMU_TC', name: 'Huyện Trần Văn Thời' },
        { code: 'CMU_CD', name: 'Huyện Cái Nước' },
        { code: 'CMU_ĐD', name: 'Huyện Đầm Dơi' },
        { code: 'CMU_NP', name: 'Huyện Năm Căn' },
        { code: 'CMU_PH', name: 'Huyện Phú Tân' },
        { code: 'CMU_NC', name: 'Huyện Ngọc Hiển' }
    ],
    'CBG': [
        { code: 'CBG_CB', name: 'TP. Cao Bằng' },
        { code: 'CBG_BL', name: 'Huyện Bảo Lâm' },
        { code: 'CBG_TL', name: 'Huyện Thạch An' },
        { code: 'CBG_QU', name: 'Huyện Quảng Hòa' },
        { code: 'CBG_HC', name: 'Huyện Hòa An' }
    ],
    'DKN': [
        { code: 'DKN_GN', name: 'TP. Gia Nghĩa' },
        { code: 'DKN_ĐR', name: 'Huyện Đắk R\'lấp' },
        { code: 'DKN_ĐG', name: 'Huyện Đắk Glong' },
        { code: 'DKN_CS', name: 'Huyện Cư Jút' },
        { code: 'DKN_KN', name: 'Huyện Krông Nô' }
    ],
    'DLK': [
        { code: 'DLK_BMT', name: 'TP. Buôn Ma Thuột' },
        { code: 'DLK_BH', name: 'TX. Buôn Hồ' },
        { code: 'DLK_EA', name: 'Huyện Ea H\'leo' },
        { code: 'DLK_ES', name: 'Huyện Ea Súp' },
        { code: 'DLK_KB', name: 'Huyện Krông Buk' }
    ],
    'DBN': [
        { code: 'DBN_ĐB', name: 'TP. Điện Biên Phủ' },
        { code: 'DBN_ML', name: 'TX. Mường Lay' },
        { code: 'DBN_MC', name: 'Huyện Mường Chà' },
        { code: 'DBN_TA', name: 'Huyện Tủa Chùa' },
        { code: 'DBN_ĐĐ', name: 'Huyện Điện Biên Đông' }
    ],
    'DNI': [
        { code: 'DNI_BH', name: 'TP. Biên Hòa' },
        { code: 'DNI_LK', name: 'TP. Long Khánh' },
        { code: 'DNI_TN', name: 'Huyện Tân Phú' },
        { code: 'DNI_VĐ', name: 'Huyện Vĩnh Cửu' },
        { code: 'DNI_ĐN', name: 'Huyện Định Quán' },
        { code: 'DNI_TR', name: 'Huyện Trảng Bom' },
        { code: 'DNI_TH', name: 'Huyện Thống Nhất' },
        { code: 'DNI_CM', name: 'Huyện Cẩm Mỹ' },
        { code: 'DNI_LT', name: 'Huyện Long Thành' },
        { code: 'DNI_XL', name: 'Huyện Xuân Lộc' },
        { code: 'DNI_NH', name: 'Huyện Nhơn Trạch' }
    ],
    'DTP': [
        { code: 'DTP_CL', name: 'TP. Cao Lãnh' },
        { code: 'DTP_SM', name: 'TP. Sa Đéc' },
        { code: 'DTP_HN', name: 'TX. Hồng Ngự' },
        { code: 'DTP_TH', name: 'Huyện Tân Hồng' },
        { code: 'DTP_HP', name: 'Huyện Hồng Ngự' }
    ],
    'GLI': [
        { code: 'GLI_PK', name: 'TP. Pleiku' },
        { code: 'GLI_AK', name: 'TX. An Khê' },
        { code: 'GLI_AC', name: 'TX. Ayun Pa' },
        { code: 'GLI_KB', name: 'Huyện KBang' },
        { code: 'GLI_ĐC', name: 'Huyện Đăk Đoa' }
    ],
    'HGG': [
        { code: 'HGG_HG', name: 'TP. Hà Giang' },
        { code: 'HGG_ĐV', name: 'Huyện Đồng Văn' },
        { code: 'HGG_MV', name: 'Huyện Mèo Vạc' },
        { code: 'HGG_YM', name: 'Huyện Yên Minh' },
        { code: 'HGG_QS', name: 'Huyện Quản Bạ' }
    ],
    'HNM': [
        { code: 'HNM_PL', name: 'TP. Phủ Lý' },
        { code: 'HNM_DT', name: 'TX. Duy Tiên' },
        { code: 'HNM_KV', name: 'Huyện Kim Bảng' },
        { code: 'HNM_TV', name: 'Huyện Thanh Liêm' },
        { code: 'HNM_BL', name: 'Huyện Bình Lục' }
    ],
    'HTA': [
        { code: 'HTA_HT', name: 'TP. Hà Tĩnh' },
        { code: 'HTA_HL', name: 'TX. Hồng Lĩnh' },
        { code: 'HTA_KA', name: 'TX. Kỳ Anh' },
        { code: 'HTA_HS', name: 'Huyện Hương Sơn' },
        { code: 'HTA_ĐC', name: 'Huyện Đức Thọ' }
    ],
    'HDG': [
        { code: 'HDG_HD', name: 'TP. Hải Dương' },
        { code: 'HDG_CL', name: 'TX. Chí Linh' },
        { code: 'HDG_KT', name: 'TX. Kinh Môn' },
        { code: 'HDG_NS', name: 'Huyện Nam Sách' },
        { code: 'HDG_TH', name: 'Huyện Thanh Hà' }
    ],
    'HBH': [
        { code: 'HBH_HB', name: 'TP. Hòa Bình' },
        { code: 'HBH_ĐB', name: 'Huyện Đà Bắc' },
        { code: 'HBH_KS', name: 'Huyện Kỳ Sơn' },
        { code: 'HBH_LC', name: 'Huyện Lương Sơn' },
        { code: 'HBH_KC', name: 'Huyện Kim Bôi' }
    ],
    'HGG2': [
        { code: 'HGG2_VT', name: 'TP. Vị Thanh' },
        { code: 'HGG2_NC', name: 'TX. Ngã Bảy' },
        { code: 'HGG2_LM', name: 'TX. Long Mỹ' },
        { code: 'HGG2_CT', name: 'Huyện Châu Thành A' },
        { code: 'HGG2_PT', name: 'Huyện Phụng Hiệp' }
    ],
    'HYN': [
        { code: 'HYN_HY', name: 'TP. Hưng Yên' },
        { code: 'HYN_MH', name: 'Huyện Mỹ Hào' },
        { code: 'HYN_VL', name: 'Huyện Văn Lâm' },
        { code: 'HYN_VG', name: 'Huyện Văn Giang' },
        { code: 'HYN_YM', name: 'Huyện Yên Mỹ' }
    ],
    'KHA': [
        { code: 'KHA_NT', name: 'TP. Nha Trang' },
        { code: 'KHA_CR', name: 'TP. Cam Ranh' },
        { code: 'KHA_NP', name: 'TX. Ninh Hòa' },
        { code: 'KHA_VN', name: 'Huyện Vạn Ninh' },
        { code: 'KHA_KS', name: 'Huyện Khánh Sơn' }
    ],
    'KGG': [
        { code: 'KGG_RG', name: 'TP. Rạch Giá' },
        { code: 'KGG_HT', name: 'TP. Hà Tiên' },
        { code: 'KGG_PQ', name: 'TP. Phú Quốc' },
        { code: 'KGG_KL', name: 'Huyện Kiên Lương' },
        { code: 'KGG_HH', name: 'Huyện Hòn Đất' }
    ],
    'KTM': [
        { code: 'KTM_KT', name: 'TP. Kon Tum' },
        { code: 'KTM_ĐG', name: 'Huyện Đắk Glei' },
        { code: 'KTM_NG', name: 'Huyện Ngọc Hồi' },
        { code: 'KTM_ĐT', name: 'Huyện Đắk Tô' },
        { code: 'KTM_KH', name: 'Huyện Kon Plông' }
    ],
    'LCI': [
        { code: 'LCI_LC', name: 'TP. Lai Châu' },
        { code: 'LCI_MT', name: 'Huyện Mường Tè' },
        { code: 'LCI_SH', name: 'Huyện Sìn Hồ' },
        { code: 'LCI_PH', name: 'Huyện Phong Thổ' },
        { code: 'LCI_TD', name: 'Huyện Tam Đường' }
    ],
    'LDG': [
        { code: 'LDG_DL', name: 'TP. Đà Lạt' },
        { code: 'LDG_BL', name: 'TP. Bảo Lộc' },
        { code: 'LDG_ĐD', name: 'Huyện Đam Rông' },
        { code: 'LDG_LC', name: 'Huyện Lạc Dương' },
        { code: 'LDG_LS', name: 'Huyện Lâm Hà' },
        { code: 'LDG_ĐH', name: 'Huyện Đơn Dương' }
    ],
    'LSN': [
        { code: 'LSN_LS', name: 'TP. Lạng Sơn' },
        { code: 'LSN_CC', name: 'Huyện Cao Lộc' },
        { code: 'LSN_TL', name: 'Huyện Tràng Định' },
        { code: 'LSN_VC', name: 'Huyện Văn Lãng' },
        { code: 'LSN_BQ', name: 'Huyện Bắc Sơn' }
    ],
    'LCA': [
        { code: 'LCA_LC', name: 'TP. Lào Cai' },
        { code: 'LCA_SP', name: 'TX. Sa Pa' },
        { code: 'LCA_BC', name: 'Huyện Bát Xát' },
        { code: 'LCA_MC', name: 'Huyện Mường Khương' },
        { code: 'LCA_SC', name: 'Huyện Si Ma Cai' }
    ],
    'LAN': [
        { code: 'LAN_TĂ', name: 'TP. Tân An' },
        { code: 'LAN_KG', name: 'TX. Kiến Tường' },
        { code: 'LAN_TT', name: 'Huyện Tân Trụ' },
        { code: 'LAN_CT', name: 'Huyện Châu Thành' },
        { code: 'LAN_BT', name: 'Huyện Bến Lức' }
    ],
    'NDH': [
        { code: 'NDH_NĐ', name: 'TP. Nam Định' },
        { code: 'NDH_MK', name: 'Huyện Mỹ Lộc' },
        { code: 'NDH_VB', name: 'Huyện Vụ Bản' },
        { code: 'NDH_YK', name: 'Huyện Ý Yên' },
        { code: 'NDH_NĐĐ', name: 'Huyện Nam Trực' }
    ],
    'NAN': [
        { code: 'NAN_VP', name: 'TP. Vinh' },
        { code: 'NAN_CV', name: 'TX. Cửa Lò' },
        { code: 'NAN_TH', name: 'TX. Thái Hòa' },
        { code: 'NAN_HC', name: 'TX. Hoàng Mai' },
        { code: 'NAN_QP', name: 'Huyện Quỳ Hợp' }
    ],
    'NBH': [
        { code: 'NBH_NB', name: 'TP. Ninh Bình' },
        { code: 'NBH_TM', name: 'TP. Tam Điệp' },
        { code: 'NBH_NM', name: 'Huyện Nho Quan' },
        { code: 'NBH_GV', name: 'Huyện Gia Viễn' },
        { code: 'NBH_HP', name: 'Huyện Hoa Lư' }
    ],
    'NTN': [
        { code: 'NTN_PC', name: 'TP. Phan Rang-Tháp Chàm' },
        { code: 'NTN_BP', name: 'Huyện Bác Ái' },
        { code: 'NTN_NH', name: 'Huyện Ninh Hải' },
        { code: 'NTN_NP', name: 'Huyện Ninh Phước' },
        { code: 'NTN_NS', name: 'Huyện Ninh Sơn' }
    ],
    'PTH': [
        { code: 'PTH_VY', name: 'TP. Việt Trì' },
        { code: 'PTH_PV', name: 'TX. Phú Thọ' },
        { code: 'PTH_ĐH', name: 'Huyện Đoan Hùng' },
        { code: 'PTH_HS', name: 'Huyện Hạ Hòa' },
        { code: 'PTH_TN', name: 'Huyện Thanh Ba' }
    ],
    'PYN': [
        { code: 'PYN_TH', name: 'TP. Tuy Hòa' },
        { code: 'PYN_SH', name: 'TX. Sông Cầu' },
        { code: 'PYN_ĐX', name: 'Huyện Đồng Xuân' },
        { code: 'PYN_TN', name: 'Huyện Tuy An' },
        { code: 'PYN_SA', name: 'Huyện Sơn Hòa' }
    ],
    'QBH': [
        { code: 'QBH_ĐH', name: 'TP. Đồng Hới' },
        { code: 'QBH_MH', name: 'Huyện Minh Hóa' },
        { code: 'QBH_TH', name: 'Huyện Tuyên Hóa' },
        { code: 'QBH_QT', name: 'Huyện Quảng Trạch' },
        { code: 'QBH_BT', name: 'TX. Ba Đồn' }
    ],
    'QNM': [
        { code: 'QNM_TA', name: 'TP. Tam Kỳ' },
        { code: 'QNM_HA', name: 'TP. Hội An' },
        { code: 'QNM_ĐB', name: 'TX. Điện Bàn' },
        { code: 'QNM_TG', name: 'Huyện Thăng Bình' },
        { code: 'QNM_QS', name: 'Huyện Quế Sơn' }
    ],
    'QNI': [
        { code: 'QNI_QN', name: 'TP. Quảng Ngãi' },
        { code: 'QNI_ĐP', name: 'TX. Đức Phổ' },
        { code: 'QNI_BC', name: 'Huyện Bình Sơn' },
        { code: 'QNI_SN', name: 'Huyện Sơn Tịnh' },
        { code: 'QNI_TB', name: 'Huyện Tư Nghĩa' }
    ],
    'QNH': [
        { code: 'QNH_HL', name: 'TP. Hạ Long' },
        { code: 'QNH_MC', name: 'TP. Móng Cái' },
        { code: 'QNH_UB', name: 'TP. Uông Bí' },
        { code: 'QNH_CM', name: 'TP. Cẩm Phả' },
        { code: 'QNH_ĐT', name: 'TX. Đông Triều' },
        { code: 'QNH_QY', name: 'TX. Quảng Yên' }
    ],
    'QTI': [
        { code: 'QTI_ĐH', name: 'TP. Đông Hà' },
        { code: 'QTI_QT', name: 'TX. Quảng Trị' },
        { code: 'QTI_VL', name: 'Huyện Vĩnh Linh' },
        { code: 'QTI_HL', name: 'Huyện Hướng Hóa' },
        { code: 'QTI_GL', name: 'Huyện Gio Linh' }
    ],
    'STG': [
        { code: 'STG_ST', name: 'TP. Sóc Trăng' },
        { code: 'STG_CT', name: 'Huyện Châu Thành' },
        { code: 'STG_KS', name: 'Huyện Kế Sách' },
        { code: 'STG_MT', name: 'Huyện Mỹ Tú' },
        { code: 'STG_VT', name: 'TX. Vĩnh Châu' }
    ],
    'SLA': [
        { code: 'SLA_SL', name: 'TP. Sơn La' },
        { code: 'SLA_QN', name: 'Huyện Quỳnh Nhai' },
        { code: 'SLA_TB', name: 'Huyện Thuận Châu' },
        { code: 'SLA_MC', name: 'Huyện Mường La' },
        { code: 'SLA_BC', name: 'Huyện Bắc Yên' }
    ],
    'TNH': [
        { code: 'TNH_TN', name: 'TP. Tây Ninh' },
        { code: 'TNH_TB', name: 'Huyện Tân Biên' },
        { code: 'TNH_TC', name: 'Huyện Tân Châu' },
        { code: 'TNH_DB', name: 'Huyện Dương Minh Châu' },
        { code: 'TNH_CT', name: 'Huyện Châu Thành' },
        { code: 'TNH_HT', name: 'TX. Hòa Thành' }
    ],
    'TBH': [
        { code: 'TBH_TB', name: 'TP. Thái Bình' },
        { code: 'TBH_QP', name: 'Huyện Quỳnh Phụ' },
        { code: 'TBH_HH', name: 'Huyện Hưng Hà' },
        { code: 'TBH_ĐH', name: 'Huyện Đông Hưng' },
        { code: 'TBH_TT', name: 'Huyện Thái Thụy' }
    ],
    'TNN': [
        { code: 'TNN_TN', name: 'TP. Thái Nguyên' },
        { code: 'TNN_SC', name: 'TP. Sông Công' },
        { code: 'TNN_PT', name: 'TX. Phổ Yên' },
        { code: 'TNN_ĐH', name: 'Huyện Định Hóa' },
        { code: 'TNN_VN', name: 'Huyện Võ Nhai' },
        { code: 'TNN_PL', name: 'Huyện Phú Lương' }
    ],
    'THH': [
        { code: 'THH_TH', name: 'TP. Thanh Hóa' },
        { code: 'THH_BĐ', name: 'TX. Bỉm Sơn' },
        { code: 'THH_SH', name: 'TP. Sầm Sơn' },
        { code: 'THH_NT', name: 'TX. Nghi Sơn' },
        { code: 'THH_MC', name: 'Huyện Mường Lát' }
    ],
    'TTH': [
        { code: 'TTH_HU', name: 'TP. Huế' },
        { code: 'TTH_HS', name: 'TX. Hương Thủy' },
        { code: 'TTH_HT', name: 'TX. Hương Trà' },
        { code: 'TTH_PV', name: 'Huyện Phú Vang' },
        { code: 'TTH_PL', name: 'Huyện Phú Lộc' }
    ],
    'TGG': [
        { code: 'TGG_MT', name: 'TP. Mỹ Tho' },
        { code: 'TGG_GC', name: 'TX. Gò Công' },
        { code: 'TGG_CT', name: 'TX. Cai Lậy' },
        { code: 'TGG_TP', name: 'Huyện Tân Phước' },
        { code: 'TGG_CT2', name: 'Huyện Châu Thành' }
    ],
    'TVH': [
        { code: 'TVH_TV', name: 'TP. Trà Vinh' },
        { code: 'TVH_DL', name: 'TX. Duyên Hải' },
        { code: 'TVH_CC', name: 'Huyện Càng Long' },
        { code: 'TVH_CH', name: 'Huyện Cầu Kè' },
        { code: 'TVH_TL', name: 'Huyện Tiểu Cần' }
    ],
    'TQG': [
        { code: 'TQG_TQ', name: 'TP. Tuyên Quang' },
        { code: 'TQG_LB', name: 'Huyện Lâm Bình' },
        { code: 'TQG_NH', name: 'Huyện Na Hang' },
        { code: 'TQG_CH', name: 'Huyện Chiêm Hóa' },
        { code: 'TQG_HY', name: 'Huyện Hàm Yên' }
    ],
    'VLG': [
        { code: 'VLG_VL', name: 'TP. Vĩnh Long' },
        { code: 'VLG_LL', name: 'Huyện Long Hồ' },
        { code: 'VLG_MH', name: 'Huyện Mang Thít' },
        { code: 'VLG_VH', name: 'Huyện Vũng Liêm' },
        { code: 'VLG_TC', name: 'Huyện Tam Bình' },
        { code: 'VLG_BM', name: 'TX. Bình Minh' }
    ],
    'VPC': [
        { code: 'VPC_VY', name: 'TP. Vĩnh Yên' },
        { code: 'VPC_PT', name: 'TP. Phúc Yên' },
        { code: 'VPC_LP', name: 'Huyện Lập Thạch' },
        { code: 'VPC_TA', name: 'Huyện Tam Dương' },
        { code: 'VPC_TĐ', name: 'Huyện Tam Đảo' }
    ],
    'YBI': [
        { code: 'YBI_YB', name: 'TP. Yên Bái' },
        { code: 'YBI_NM', name: 'TX. Nghĩa Lộ' },
        { code: 'YBI_LY', name: 'Huyện Lục Yên' },
        { code: 'YBI_VY', name: 'Huyện Văn Yên' },
        { code: 'YBI_MC', name: 'Huyện Mù Căng Chải' }
    ]
};

export const wards = {
    // Hà Nội - Ba Đình
    'HN_BD': [
        { name: 'Phường Phúc Xá' },
        { name: 'Phường Trúc Bạch' },
        { name: 'Phường Vĩnh Phúc' },
        { name: 'Phường Cống Vị' },
        { name: 'Phường Liễu Giai' },
        { name: 'Phường Nguyễn Trung Trực' },
        { name: 'Phường Quán Thánh' },
        { name: 'Phường Ngọc Hà' },
        { name: 'Phường Điện Biên' },
        { name: 'Phường Đội Cấn' },
        { name: 'Phường Ngọc Khánh' },
        { name: 'Phường Kim Mã' },
        { name: 'Phường Giảng Võ' },
        { name: 'Phường Thành Công' }
    ],
    // Hà Nội - Hoàn Kiếm
    'HN_HK': [
        { name: 'Phường Phúc Tân' },
        { name: 'Phường Đồng Xuân' },
        { name: 'Phường Hàng Mã' },
        { name: 'Phường Hàng Buồm' },
        { name: 'Phường Hàng Đào' },
        { name: 'Phường Hàng Bồ' },
        { name: 'Phường Cửa Đông' },
        { name: 'Phường Lý Thái Tổ' },
        { name: 'Phường Hàng Bạc' },
        { name: 'Phường Hàng Gai' },
        { name: 'Phường Chương Dương' },
        { name: 'Phường Hàng Trống' },
        { name: 'Phường Cửa Nam' },
        { name: 'Phường Hàng Bông' },
        { name: 'Phường Tràng Tiền' },
        { name: 'Phường Trần Hưng Đạo' },
        { name: 'Phường Phan Chu Trinh' },
        { name: 'Phường Hàng Bài' }
    ],
    // Hà Nội - Cầu Giấy
    'HN_CG': [
        { name: 'Phường Nghĩa Đô' },
        { name: 'Phường Nghĩa Tân' },
        { name: 'Phường Mai Dịch' },
        { name: 'Phường Dịch Vọng' },
        { name: 'Phường Dịch Vọng Hậu' },
        { name: 'Phường Quan Hoa' },
        { name: 'Phường Yên Hòa' },
        { name: 'Phường Trung Hòa' }
    ],
    // HCM - Quận 1
    'HCM_Q1': [
        { name: 'Phường Tân Định' },
        { name: 'Phường Đa Kao' },
        { name: 'Phường Bến Nghé' },
        { name: 'Phường Bến Thành' },
        { name: 'Phường Nguyễn Thái Bình' },
        { name: 'Phường Phạm Ngũ Lão' },
        { name: 'Phường Cầu Ông Lãnh' },
        { name: 'Phường Cô Giang' },
        { name: 'Phường Nguyễn Cư Trinh' },
        { name: 'Phường Cầu Kho' }
    ],
    // HCM - Quận 3
    'HCM_Q3': [
        { name: 'Phường 1' },
        { name: 'Phường 2' },
        { name: 'Phường 3' },
        { name: 'Phường 4' },
        { name: 'Phường 5' },
        { name: 'Phường 6' },
        { name: 'Phường 7' },
        { name: 'Phường 8' },
        { name: 'Phường 9' },
        { name: 'Phường 10' },
        { name: 'Phường 11' },
        { name: 'Phường 12' },
        { name: 'Phường 13' },
        { name: 'Phường 14' },
        { name: 'Phường Võ Thị Sáu' }
    ],
    // HCM - Quận 7
    'HCM_Q7': [
        { name: 'Phường Tân Thuận Đông' },
        { name: 'Phường Tân Thuận Tây' },
        { name: 'Phường Tân Kiểng' },
        { name: 'Phường Tân Hưng' },
        { name: 'Phường Bình Thuận' },
        { name: 'Phường Tân Quy' },
        { name: 'Phường Phú Thuận' },
        { name: 'Phường Tân Phú' },
        { name: 'Phường Tân Phong' },
        { name: 'Phường Phú Mỹ' }
    ],
    // HCM - Bình Thạnh
    'HCM_BT': [
        { name: 'Phường 1' },
        { name: 'Phường 2' },
        { name: 'Phường 3' },
        { name: 'Phường 5' },
        { name: 'Phường 6' },
        { name: 'Phường 7' },
        { name: 'Phường 11' },
        { name: 'Phường 12' },
        { name: 'Phường 13' },
        { name: 'Phường 14' },
        { name: 'Phường 15' },
        { name: 'Phường 17' },
        { name: 'Phường 19' },
        { name: 'Phường 21' },
        { name: 'Phường 22' },
        { name: 'Phường 24' },
        { name: 'Phường 25' },
        { name: 'Phường 26' },
        { name: 'Phường 27' },
        { name: 'Phường 28' }
    ],
    // HCM - Thủ Đức
    'HCM_TD': [
        { name: 'Phường Linh Xuân' },
        { name: 'Phường Bình Chiểu' },
        { name: 'Phường Linh Trung' },
        { name: 'Phường Tam Bình' },
        { name: 'Phường Tam Phú' },
        { name: 'Phường Hiệp Bình Phước' },
        { name: 'Phường Hiệp Bình Chánh' },
        { name: 'Phường Linh Chiểu' },
        { name: 'Phường Linh Tây' },
        { name: 'Phường Linh Đông' },
        { name: 'Phường Bình Thọ' },
        { name: 'Phường Trường Thọ' },
        { name: 'Phường Long Bình' },
        { name: 'Phường Long Thạnh Mỹ' },
        { name: 'Phường Tân Phú' },
        { name: 'Phường Hiệp Phú' },
        { name: 'Phường Tăng Nhơn Phú A' },
        { name: 'Phường Tăng Nhơn Phú B' },
        { name: 'Phường Phước Long A' },
        { name: 'Phường Phước Long B' },
        { name: 'Phường Trường Thạnh' },
        { name: 'Phường Long Phước' },
        { name: 'Phường Long Trường' },
        { name: 'Phường Phước Bình' },
        { name: 'Phường Phú Hữu' },
        { name: 'Phường Thảo Điền' },
        { name: 'Phường An Phú' },
        { name: 'Phường An Khánh' },
        { name: 'Phường Bình Trưng Đông' },
        { name: 'Phường Bình Trưng Tây' },
        { name: 'Phường Cát Lái' },
        { name: 'Phường Thạnh Mỹ Lợi' },
        { name: 'Phường An Lợi Đông' },
        { name: 'Phường Thủ Thiêm' }
    ],
    // HCM - Gò Vấp
    'HCM_GV': [
        { name: 'Phường 1' },
        { name: 'Phường 3' },
        { name: 'Phường 4' },
        { name: 'Phường 5' },
        { name: 'Phường 6' },
        { name: 'Phường 7' },
        { name: 'Phường 8' },
        { name: 'Phường 9' },
        { name: 'Phường 10' },
        { name: 'Phường 11' },
        { name: 'Phường 12' },
        { name: 'Phường 13' },
        { name: 'Phường 14' },
        { name: 'Phường 15' },
        { name: 'Phường 16' },
        { name: 'Phường 17' }
    ],
    // HCM - Tân Bình
    'HCM_TB': [
        { name: 'Phường 1' },
        { name: 'Phường 2' },
        { name: 'Phường 3' },
        { name: 'Phường 4' },
        { name: 'Phường 5' },
        { name: 'Phường 6' },
        { name: 'Phường 7' },
        { name: 'Phường 8' },
        { name: 'Phường 9' },
        { name: 'Phường 10' },
        { name: 'Phường 11' },
        { name: 'Phường 12' },
        { name: 'Phường 13' },
        { name: 'Phường 14' },
        { name: 'Phường 15' }
    ],
    // Đà Nẵng - Hải Châu
    'DN_HC': [
        { name: 'Phường Thanh Bình' },
        { name: 'Phường Thuận Phước' },
        { name: 'Phường Thạch Thang' },
        { name: 'Phường Hải Châu I' },
        { name: 'Phường Hải Châu II' },
        { name: 'Phường Phước Ninh' },
        { name: 'Phường Hoà Thuận Tây' },
        { name: 'Phường Hoà Thuận Đông' },
        { name: 'Phường Nam Dương' },
        { name: 'Phường Bình Hiên' },
        { name: 'Phường Bình Thuận' },
        { name: 'Phường Hoà Cường Bắc' },
        { name: 'Phường Hoà Cường Nam' }
    ],
    // Cần Thơ - Ninh Kiều
    'CT_NK': [
        { name: 'Phường Cái Khế' },
        { name: 'Phường An Hoà' },
        { name: 'Phường Thới Bình' },
        { name: 'Phường An Nghiệp' },
        { name: 'Phường An Cư' },
        { name: 'Phường Tân An' },
        { name: 'Phường An Phú' },
        { name: 'Phường Xuân Khánh' },
        { name: 'Phường Hưng Lợi' },
        { name: 'Phường An Khánh' },
        { name: 'Phường An Bình' }
    ],
    // Cà Mau - TP. Cà Mau
    'CMU_CM': [
        { name: 'Phường 1' },
        { name: 'Phường 2' },
        { name: 'Phường 4' },
        { name: 'Phường 5' },
        { name: 'Phường 6' },
        { name: 'Phường 7' },
        { name: 'Phường 8' },
        { name: 'Phường 9' },
        { name: 'Phường Tân Thành' },
        { name: 'Phường Tân Xuyên' }
    ],
    // Bình Dương - TP. Thủ Dầu Một
    'BDG_TD': [
        { name: 'Phường Hiệp Thành' },
        { name: 'Phường Phú Lợi' },
        { name: 'Phường Phú Cường' },
        { name: 'Phường Phú Hòa' },
        { name: 'Phường Phú Thọ' },
        { name: 'Phường Chánh Nghĩa' },
        { name: 'Phường Định Hòa' },
        { name: 'Phường Hòa Phú' },
        { name: 'Phường Phú Tân' },
        { name: 'Phường Tân An' },
        { name: 'Phường Hiệp An' },
        { name: 'Phường Tương Bình Hiệp' }
    ],
    // Đồng Nai - TP. Biên Hòa
    'DNI_BH': [
        { name: 'Phường Trảng Dài' },
        { name: 'Phường Tân Phong' },
        { name: 'Phường Tân Biên' },
        { name: 'Phường Hố Nai' },
        { name: 'Phường Tân Hòa' },
        { name: 'Phường Tân Hiệp' },
        { name: 'Phường Bửu Long' },
        { name: 'Phường Tân Tiến' },
        { name: 'Phường Tam Hiệp' },
        { name: 'Phường Long Bình' },
        { name: 'Phường Quang Vinh' },
        { name: 'Phường An Bình' },
        { name: 'Phường Thanh Bình' }
    ],
    // Lâm Đồng - TP. Đà Lạt
    'LDG_DL': [
        { name: 'Phường 1' },
        { name: 'Phường 2' },
        { name: 'Phường 3' },
        { name: 'Phường 4' },
        { name: 'Phường 5' },
        { name: 'Phường 6' },
        { name: 'Phường 7' },
        { name: 'Phường 8' },
        { name: 'Phường 9' },
        { name: 'Phường 10' },
        { name: 'Phường 11' },
        { name: 'Phường 12' }
    ],
    // Khánh Hòa - TP. Nha Trang
    'KHA_NT': [
        { name: 'Phường Vĩnh Hòa' },
        { name: 'Phường Vĩnh Hải' },
        { name: 'Phường Vĩnh Phước' },
        { name: 'Phường Ngọc Hiệp' },
        { name: 'Phường Vĩnh Thọ' },
        { name: 'Phường Xương Huân' },
        { name: 'Phường Vạn Thắng' },
        { name: 'Phường Vạn Thạnh' },
        { name: 'Phường Phương Sài' },
        { name: 'Phường Phương Sơn' },
        { name: 'Phường Phước Hải' },
        { name: 'Phường Phước Tân' },
        { name: 'Phường Lộc Thọ' },
        { name: 'Phường Phước Tiến' },
        { name: 'Phường Tân Lập' },
        { name: 'Phường Phước Hòa' }
    ],
    // An Giang - TP. Long Xuyên
    'AGG_LX': [
        { name: 'Phường Mỹ Bình' },
        { name: 'Phường Mỹ Long' },
        { name: 'Phường Mỹ Xuyên' },
        { name: 'Phường Bình Khánh' },
        { name: 'Phường Mỹ Phước' },
        { name: 'Phường Mỹ Quý' },
        { name: 'Phường Mỹ Thới' },
        { name: 'Phường Mỹ Thạnh' },
        { name: 'Phường Bình Đức' },
        { name: 'Phường Đông Xuyên' }
    ],
    // Kiên Giang - TP. Rạch Giá
    'KGG_RG': [
        { name: 'Phường Vĩnh Thanh Vân' },
        { name: 'Phường Vĩnh Thanh' },
        { name: 'Phường Vĩnh Quang' },
        { name: 'Phường Vĩnh Hiệp' },
        { name: 'Phường Vĩnh Bảo' },
        { name: 'Phường Vĩnh Lạc' },
        { name: 'Phường An Hòa' },
        { name: 'Phường An Bình' },
        { name: 'Phường Rạch Sỏi' }
    ],
    // Quảng Ninh - TP. Hạ Long
    'QNH_HL': [
        { name: 'Phường Hà Khánh' },
        { name: 'Phường Hà Phong' },
        { name: 'Phường Hà Khẩu' },
        { name: 'Phường Cao Xanh' },
        { name: 'Phường Giếng Đáy' },
        { name: 'Phường Hà Tu' },
        { name: 'Phường Hà Trung' },
        { name: 'Phường Hà Lầm' },
        { name: 'Phường Bãi Cháy' },
        { name: 'Phường Cao Thắng' },
        { name: 'Phường Hùng Thắng' },
        { name: 'Phường Yết Kiêu' },
        { name: 'Phường Trần Hưng Đạo' },
        { name: 'Phường Hồng Hải' },
        { name: 'Phường Hồng Gai' },
        { name: 'Phường Bạch Đằng' }
    ],
    // Thừa Thiên Huế - TP. Huế
    'TTH_HU': [
        { name: 'Phường Phú Thuận' },
        { name: 'Phường Phú Bình' },
        { name: 'Phường Tây Lộc' },
        { name: 'Phường Thuận Lộc' },
        { name: 'Phường Phú Hiệp' },
        { name: 'Phường Phú Hậu' },
        { name: 'Phường Thuận Hòa' },
        { name: 'Phường Thuận Thành' },
        { name: 'Phường Phú Hòa' },
        { name: 'Phường Phú Cát' },
        { name: 'Phường Kim Long' },
        { name: 'Phường Vĩ Dạ' },
        { name: 'Phường Phường Đúc' },
        { name: 'Phường Vĩnh Ninh' },
        { name: 'Phường Phú Hội' },
        { name: 'Phường Phú Nhuận' }
    ],
    // Nghệ An - TP. Vinh
    'NAN_VP': [
        { name: 'Phường Đội Cung' },
        { name: 'Phường Lê Lợi' },
        { name: 'Phường Quán Bàu' },
        { name: 'Phường Hưng Bình' },
        { name: 'Phường Hưng Phúc' },
        { name: 'Phường Hưng Dũng' },
        { name: 'Phường Cửa Nam' },
        { name: 'Phường Quang Trung' },
        { name: 'Phường Đông Vĩnh' },
        { name: 'Phường Hà Huy Tập' },
        { name: 'Phường Lê Mao' },
        { name: 'Phường Trường Thi' },
        { name: 'Phường Bến Thủy' },
        { name: 'Phường Hồng Sơn' },
        { name: 'Phường Trung Đô' }
    ],
    // Thanh Hóa - TP. Thanh Hóa
    'THH_TH': [
        { name: 'Phường Lam Sơn' },
        { name: 'Phường Ngọc Trạo' },
        { name: 'Phường Đông Vệ' },
        { name: 'Phường Đông Sơn' },
        { name: 'Phường Tân Sơn' },
        { name: 'Phường Trường Thi' },
        { name: 'Phường Điện Biên' },
        { name: 'Phường Phú Sơn' },
        { name: 'Phường An Hoạch' },
        { name: 'Phường Quảng Thắng' },
        { name: 'Phường Đông Cương' },
        { name: 'Phường Đông Hương' }
    ],
    // Bà Rịa - Vũng Tàu - TP. Vũng Tàu
    'BRV_VT': [
        { name: 'Phường 1' },
        { name: 'Phường 2' },
        { name: 'Phường 3' },
        { name: 'Phường 4' },
        { name: 'Phường 5' },
        { name: 'Phường 7' },
        { name: 'Phường 8' },
        { name: 'Phường 9' },
        { name: 'Phường 10' },
        { name: 'Phường 11' },
        { name: 'Phường 12' },
        { name: 'Phường Thắng Nhì' },
        { name: 'Phường Thắng Nhất' },
        { name: 'Phường Nguyễn An Ninh' },
        { name: 'Phường Rạch Dừa' }
    ],
    // Bắc Ninh - TP. Bắc Ninh
    'BNH_BN': [
        { name: 'Phường Vũ Ninh' },
        { name: 'Phường Đáp Cầu' },
        { name: 'Phường Thị Cầu' },
        { name: 'Phường Kinh Bắc' },
        { name: 'Phường Vệ An' },
        { name: 'Phường Tiên Du' },
        { name: 'Phường Đại Phúc' },
        { name: 'Phường Ninh Xá' },
        { name: 'Phường Suối Hoa' },
        { name: 'Phường Võ Cường' }
    ],
    // Tiền Giang - TP. Mỹ Tho
    'TGG_MT': [
        { name: 'Phường 1' },
        { name: 'Phường 2' },
        { name: 'Phường 3' },
        { name: 'Phường 4' },
        { name: 'Phường 5' },
        { name: 'Phường 6' },
        { name: 'Phường 7' },
        { name: 'Phường 8' },
        { name: 'Phường 9' },
        { name: 'Phường 10' },
        { name: 'Phường Tân Long' }
    ]
};

// Hàm lấy danh sách quận/huyện theo tỉnh
export const getDistrictsByProvince = (provinceCode) => {
    return districts[provinceCode] || [];
};

// Hàm lấy danh sách phường/xã theo quận/huyện
export const getWardsByDistrict = (districtCode) => {
    return wards[districtCode] || [];
};
