-- =============================================
-- Schema generated from Laravel migrations (--pretend --force)
-- Generated at: 2025-12-11 10:18:23
-- Dùng cho GCP Cloud SQL / MySQL
-- =============================================

SET FOREIGN_KEY_CHECKS=0;


-- -----------------------------------------
-- Table: cache
DROP TABLE IF EXISTS cache;
create table `cache` (`key` varchar(255) not null, `value` mediumtext not null, `expiration` int not null, primary key (`key`)) default character set utf8mb4 collate utf8mb4_unicode_ci;

-- -----------------------------------------
-- Table: cache_locks
DROP TABLE IF EXISTS cache_locks;
create table `cache_locks` (`key` varchar(255) not null, `owner` varchar(255) not null, `expiration` int not null, primary key (`key`)) default character set utf8mb4 collate utf8mb4_unicode_ci;

-- -----------------------------------------
-- Table: jobs
DROP TABLE IF EXISTS jobs;
create table `jobs` (`id` bigint unsigned not null auto_increment primary key, `queue` varchar(255) not null, `payload` longtext not null, `attempts` tinyint unsigned not null, `reserved_at` int unsigned null, `available_at` int unsigned not null, `created_at` int unsigned not null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `jobs` add index `jobs_queue_index`(`queue`);

-- -----------------------------------------
-- Table: job_batches
DROP TABLE IF EXISTS job_batches;
create table `job_batches` (`id` varchar(255) not null, `name` varchar(255) not null, `total_jobs` int not null, `pending_jobs` int not null, `failed_jobs` int not null, `failed_job_ids` longtext not null, `options` mediumtext null, `cancelled_at` int null, `created_at` int not null, `finished_at` int null, primary key (`id`)) default character set utf8mb4 collate utf8mb4_unicode_ci;

-- -----------------------------------------
-- Table: failed_jobs
DROP TABLE IF EXISTS failed_jobs;
create table `failed_jobs` (`id` bigint unsigned not null auto_increment primary key, `uuid` varchar(255) not null, `connection` text not null, `queue` text not null, `payload` longtext not null, `exception` longtext not null, `failed_at` timestamp not null default CURRENT_TIMESTAMP) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `failed_jobs` add unique `failed_jobs_uuid_unique`(`uuid`);

-- -----------------------------------------
-- Table: users
DROP TABLE IF EXISTS users;
create table `users` (`id` bigint unsigned not null auto_increment primary key, `username` varchar(50) not null, `email` varchar(100) not null, `password` varchar(255) not null, `full_name` varchar(100) not null, `phone_number` varchar(30) null, `address` text null, `gender` enum('male', 'female', 'other') null, `avatar` varchar(255) null, `bio` text null, `website` varchar(255) null, `facebook` varchar(255) null, `instagram` varchar(255) null, `twitter` varchar(255) null, `status` enum('active', 'inactive', 'banned', 'suspended') not null default 'active', `role` enum('customer', 'seller', 'admin') not null default 'customer', `remember_token` varchar(100) null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `users` add unique `users_username_unique`(`username`);
alter table `users` add unique `users_email_unique`(`email`);
alter table `users` add unique `users_phone_number_unique`(`phone_number`);

-- -----------------------------------------
-- Table: sessions
DROP TABLE IF EXISTS sessions;
create table `sessions` (`id` varchar(255) not null, `user_id` bigint unsigned null, `ip_address` varchar(45) null, `user_agent` text null, `payload` longtext not null, `last_activity` int not null, primary key (`id`)) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `sessions` add index `sessions_user_id_index`(`user_id`);
alter table `sessions` add index `sessions_last_activity_index`(`last_activity`);

-- -----------------------------------------
-- Table: categories
DROP TABLE IF EXISTS categories;
create table `categories` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(100) not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `categories` add unique `categories_name_unique`(`name`);

-- -----------------------------------------
-- Table: wallets
DROP TABLE IF EXISTS wallets;
create table `wallets` (`id` bigint unsigned not null auto_increment primary key, `balance` decimal(15, 2) not null default '0', `status` enum('active', 'inactive', 'locked') not null default 'active', `user_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `wallets` add constraint `wallets_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `wallets` add unique `wallets_user_id_unique`(`user_id`);

-- -----------------------------------------
-- Table: bank_accounts
DROP TABLE IF EXISTS bank_accounts;
create table `bank_accounts` (`id` bigint unsigned not null auto_increment primary key, `account_holder_name` varchar(100) not null, `account_number` varchar(50) not null, `bank_name` varchar(100) not null, `branch` varchar(100) null, `status` enum('active', 'inactive') not null default 'active', `user_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `bank_accounts` add constraint `bank_accounts_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `bank_accounts` add unique `bank_accounts_account_number_unique`(`account_number`);

-- -----------------------------------------
-- Table: shipping_addresses
DROP TABLE IF EXISTS shipping_addresses;
create table `shipping_addresses` (`id` bigint unsigned not null auto_increment primary key, `receiver_name` varchar(100) not null, `phone_number` varchar(15) not null, `street_address` varchar(255) not null, `ward` varchar(100) not null, `district` varchar(100) not null, `province` varchar(100) not null, `is_default` tinyint(1) not null default '0', `status` enum('active', 'inactive') not null default 'active', `user_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `shipping_addresses` add constraint `shipping_addresses_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: promotions
DROP TABLE IF EXISTS promotions;
create table `promotions` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(200) not null, `description` text null, `type` enum('fixed', 'percentage') not null, `discount_amount` decimal(15, 2) not null, `conditions` text null, `start_date` datetime not null, `end_date` datetime not null, `status` enum('active', 'inactive', 'expired') not null default 'active', `max_usage_limit` int null, `usage_count` int not null default '0', `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;

-- -----------------------------------------
-- Table: products
DROP TABLE IF EXISTS products;
create table `products` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `description` text null, `status` enum('active', 'inactive', 'out_of_stock') not null default 'active', `seller_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `products` add constraint `products_seller_id_foreign` foreign key (`seller_id`) references `users` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: product_variants
DROP TABLE IF EXISTS product_variants;
create table `product_variants` (`id` bigint unsigned not null auto_increment primary key, `color` varchar(50) null, `size` varchar(20) null, `quantity` int not null default '0', `status` enum('active', 'inactive', 'out_of_stock') not null default 'active', `price` decimal(15, 2) not null, `product_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `product_variants` add constraint `product_variants_product_id_foreign` foreign key (`product_id`) references `products` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: product_images
DROP TABLE IF EXISTS product_images;
create table `product_images` (`id` bigint unsigned not null auto_increment primary key, `image_url` varchar(500) not null, `variant_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `product_images` add constraint `product_images_variant_id_foreign` foreign key (`variant_id`) references `product_variants` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: services
DROP TABLE IF EXISTS services;
create table `services` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(100) not null, `price` decimal(15, 2) not null, `description` text null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;

-- -----------------------------------------
-- Table: product_posts
DROP TABLE IF EXISTS product_posts;
create table `product_posts` (`id` bigint unsigned not null auto_increment primary key, `title` varchar(255) not null, `description` text null, `posted_date` datetime not null default CURRENT_TIMESTAMP, `status` enum('draft', 'published', 'hidden', 'rejected') not null default 'draft', `product_id` bigint unsigned not null, `admin_id` bigint unsigned null, `seller_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, `deleted_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `product_posts` add constraint `product_posts_product_id_foreign` foreign key (`product_id`) references `products` (`id`) on delete cascade;
alter table `product_posts` add constraint `product_posts_admin_id_foreign` foreign key (`admin_id`) references `users` (`id`) on delete set null;
alter table `product_posts` add constraint `product_posts_seller_id_foreign` foreign key (`seller_id`) references `users` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: orders
DROP TABLE IF EXISTS orders;
create table `orders` (`id` bigint unsigned not null auto_increment primary key, `order_date` datetime not null default CURRENT_TIMESTAMP, `delivery_date` datetime null, `shipping_fee` decimal(15, 2) not null default '0', `status` enum('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') not null default 'pending', `notes` text null, `payment_method` enum('cash', 'bank_transfer', 'wallet', 'credit_card') not null, `tracking_code` varchar(100) null, `user_id` bigint unsigned not null, `address_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `orders` add constraint `orders_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete restrict;
alter table `orders` add constraint `orders_address_id_foreign` foreign key (`address_id`) references `shipping_addresses` (`id`) on delete restrict;
alter table `orders` add unique `orders_tracking_code_unique`(`tracking_code`);

-- -----------------------------------------
-- Table: transactions
DROP TABLE IF EXISTS transactions;
create table `transactions` (`id` bigint unsigned not null auto_increment primary key, `order_id` bigint unsigned not null, `amount` decimal(15, 2) not null, `payment_method` enum('cash', 'bank_transfer', 'wallet', 'credit_card') not null, `status` enum('pending', 'completed', 'failed', 'refunded') not null default 'pending', `transaction_code` varchar(100) null, `payment_gateway` varchar(50) null, `transaction_date` datetime not null default CURRENT_TIMESTAMP, `response_data` text null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `transactions` add constraint `transactions_order_id_foreign` foreign key (`order_id`) references `orders` (`id`) on delete restrict;
alter table `transactions` add unique `transactions_transaction_code_unique`(`transaction_code`);

-- -----------------------------------------
-- Table: refunds
DROP TABLE IF EXISTS refunds;
create table `refunds` (`id` bigint unsigned not null auto_increment primary key, `refund_amount` decimal(15, 2) not null, `reason` text null, `status` enum('pending', 'approved', 'rejected', 'completed') not null default 'pending', `request_date` datetime not null default CURRENT_TIMESTAMP, `approval_date` datetime null, `notes` text null, `order_id` bigint unsigned not null, `admin_id` bigint unsigned null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `refunds` add constraint `refunds_order_id_foreign` foreign key (`order_id`) references `orders` (`id`) on delete restrict;
alter table `refunds` add constraint `refunds_admin_id_foreign` foreign key (`admin_id`) references `users` (`id`) on delete set null;

-- -----------------------------------------
-- Table: refund_details
DROP TABLE IF EXISTS refund_details;
create table `refund_details` (`id` bigint unsigned not null auto_increment primary key, `refund_id` bigint unsigned not null, `variant_id` bigint unsigned not null, `quantity` int not null, `refund_amount` decimal(15, 2) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `refund_details` add constraint `refund_details_refund_id_foreign` foreign key (`refund_id`) references `refunds` (`id`) on delete cascade;
alter table `refund_details` add constraint `refund_details_variant_id_foreign` foreign key (`variant_id`) references `product_variants` (`id`) on delete restrict;

-- -----------------------------------------
-- Table: applied_promotions
DROP TABLE IF EXISTS applied_promotions;
create table `applied_promotions` (`promotion_id` bigint unsigned not null, `order_id` bigint unsigned not null, `discounted_amount` decimal(15, 2) not null, `created_at` timestamp null, primary key (`promotion_id`, `order_id`)) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `applied_promotions` add constraint `applied_promotions_promotion_id_foreign` foreign key (`promotion_id`) references `promotions` (`id`) on delete cascade;
alter table `applied_promotions` add constraint `applied_promotions_order_id_foreign` foreign key (`order_id`) references `orders` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: order_details
DROP TABLE IF EXISTS order_details;
create table `order_details` (`order_id` bigint unsigned not null, `variant_id` bigint unsigned not null, `quantity` int not null, `unit_price` decimal(15, 2) not null, `created_at` timestamp null, primary key (`order_id`, `variant_id`)) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `order_details` add constraint `order_details_order_id_foreign` foreign key (`order_id`) references `orders` (`id`) on delete cascade;
alter table `order_details` add constraint `order_details_variant_id_foreign` foreign key (`variant_id`) references `product_variants` (`id`) on delete restrict;

-- -----------------------------------------
-- Table: cart_items
DROP TABLE IF EXISTS cart_items;
create table `cart_items` (`user_id` bigint unsigned not null, `variant_id` bigint unsigned not null, `quantity` int not null, `created_at` timestamp null, `updated_at` timestamp null, primary key (`user_id`, `variant_id`)) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `cart_items` add constraint `cart_items_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `cart_items` add constraint `cart_items_variant_id_foreign` foreign key (`variant_id`) references `product_variants` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: reviews
DROP TABLE IF EXISTS reviews;
create table `reviews` (`id` bigint unsigned not null auto_increment primary key, `product_id` bigint unsigned not null, `user_id` bigint unsigned not null, `rating` int unsigned not null, `content` text null, `review_date` datetime not null default CURRENT_TIMESTAMP, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `reviews` add constraint `reviews_product_id_foreign` foreign key (`product_id`) references `products` (`id`) on delete cascade;
alter table `reviews` add constraint `reviews_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `reviews` add unique `reviews_product_id_user_id_unique`(`product_id`, `user_id`);

-- -----------------------------------------
-- Table: messages
DROP TABLE IF EXISTS messages;
create table `messages` (`id` bigint unsigned not null auto_increment primary key, `receiver_id` bigint unsigned not null, `sender_id` bigint unsigned not null, `sent_date` datetime not null default CURRENT_TIMESTAMP, `content` text not null, `status` enum('sent', 'read', 'deleted') not null default 'sent', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `messages` add constraint `messages_receiver_id_foreign` foreign key (`receiver_id`) references `users` (`id`) on delete cascade;
alter table `messages` add constraint `messages_sender_id_foreign` foreign key (`sender_id`) references `users` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: applied_services
DROP TABLE IF EXISTS applied_services;
create table `applied_services` (`post_id` bigint unsigned not null, `service_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null, primary key (`post_id`, `service_id`)) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `applied_services` add constraint `applied_services_post_id_foreign` foreign key (`post_id`) references `product_posts` (`id`) on delete cascade;
alter table `applied_services` add constraint `applied_services_service_id_foreign` foreign key (`service_id`) references `services` (`id`) on delete restrict;

-- -----------------------------------------
-- Table: service_payments
DROP TABLE IF EXISTS service_payments;
create table `service_payments` (`id` bigint unsigned not null auto_increment primary key, `seller_id` bigint unsigned not null, `service_id` bigint unsigned not null, `status` enum('pending', 'completed', 'failed') not null default 'pending', `purchase_date` datetime not null default CURRENT_TIMESTAMP, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `service_payments` add constraint `service_payments_seller_id_foreign` foreign key (`seller_id`) references `users` (`id`) on delete cascade;
alter table `service_payments` add constraint `service_payments_service_id_foreign` foreign key (`service_id`) references `services` (`id`) on delete restrict;

-- -----------------------------------------
-- Table: product_categories
DROP TABLE IF EXISTS product_categories;
create table `product_categories` (`product_id` bigint unsigned not null, `category_id` bigint unsigned not null, `created_at` timestamp null, primary key (`product_id`, `category_id`)) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `product_categories` add constraint `product_categories_product_id_foreign` foreign key (`product_id`) references `products` (`id`) on delete cascade;
alter table `product_categories` add constraint `product_categories_category_id_foreign` foreign key (`category_id`) references `categories` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: inventory_histories
DROP TABLE IF EXISTS inventory_histories;
create table `inventory_histories` (`id` bigint unsigned not null auto_increment primary key, `variant_id` bigint unsigned not null, `change_type` enum('import', 'export', 'adjust', 'return', 'damaged') not null, `quantity_change` int not null, `old_quantity` int not null, `new_quantity` int not null, `reason` varchar(255) null, `created_by` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `inventory_histories` add constraint `inventory_histories_variant_id_foreign` foreign key (`variant_id`) references `product_variants` (`id`) on delete cascade;
alter table `inventory_histories` add constraint `inventory_histories_created_by_foreign` foreign key (`created_by`) references `users` (`id`) on delete restrict;

-- -----------------------------------------
-- Table: seller_reviews
DROP TABLE IF EXISTS seller_reviews;
create table `seller_reviews` (`id` bigint unsigned not null auto_increment primary key, `seller_id` bigint unsigned not null, `user_id` bigint unsigned not null, `order_id` bigint unsigned not null, `rating` int not null, `content` text null, `response_time` int null, `shipping_quality` int null, `review_date` datetime not null default CURRENT_TIMESTAMP, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `seller_reviews` add constraint `seller_reviews_seller_id_foreign` foreign key (`seller_id`) references `users` (`id`) on delete cascade;
alter table `seller_reviews` add constraint `seller_reviews_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `seller_reviews` add constraint `seller_reviews_order_id_foreign` foreign key (`order_id`) references `orders` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: notifications
DROP TABLE IF EXISTS notifications;
create table `notifications` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `type` enum('order', 'promotion', 'system', 'review', 'message') not null, `title` varchar(255) not null, `content` text not null, `read_at` timestamp null, `link` varchar(500) null, `expired_date` datetime null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `notifications` add constraint `notifications_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;

-- -----------------------------------------
-- Table: personal_access_tokens
DROP TABLE IF EXISTS personal_access_tokens;
create table `personal_access_tokens` (`id` bigint unsigned not null auto_increment primary key, `tokenable_type` varchar(255) not null, `tokenable_id` bigint unsigned not null, `name` text not null, `token` varchar(64) not null, `abilities` text null, `last_used_at` timestamp null, `expires_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `personal_access_tokens` add index `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type`, `tokenable_id`);
alter table `personal_access_tokens` add unique `personal_access_tokens_token_unique`(`token`);
alter table `personal_access_tokens` add index `personal_access_tokens_expires_at_index`(`expires_at`);

-- -----------------------------------------
-- Table: favorites
DROP TABLE IF EXISTS favorites;
create table `favorites` (`id` bigint unsigned not null auto_increment primary key, `user_id` bigint unsigned not null, `product_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate utf8mb4_unicode_ci;
alter table `favorites` add constraint `favorites_user_id_foreign` foreign key (`user_id`) references `users` (`id`) on delete cascade;
alter table `favorites` add constraint `favorites_product_id_foreign` foreign key (`product_id`) references `products` (`id`) on delete cascade;
alter table `favorites` add unique `favorites_user_id_product_id_unique`(`user_id`, `product_id`);

SET FOREIGN_KEY_CHECKS=1;

