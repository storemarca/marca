-- إنشاء جدول affiliates
CREATE TABLE IF NOT EXISTS `affiliates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','suspended') NOT NULL DEFAULT 'pending',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 10.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `lifetime_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rejection_reason` text DEFAULT NULL,
  `payment_details` json DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `social_media` json DEFAULT NULL,
  `marketing_methods` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliates_user_id_foreign` (`user_id`),
  CONSTRAINT `affiliates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول affiliate_links
CREATE TABLE IF NOT EXISTS `affiliate_links` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `affiliate_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `target_type` varchar(255) NOT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_url` varchar(255) DEFAULT NULL,
  `clicks` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `conversions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_links_slug_unique` (`slug`),
  KEY `affiliate_links_affiliate_id_foreign` (`affiliate_id`),
  CONSTRAINT `affiliate_links_affiliate_id_foreign` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول affiliate_link_stats
CREATE TABLE IF NOT EXISTS `affiliate_link_stats` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `affiliate_link_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `is_conversion` tinyint(1) NOT NULL DEFAULT 0,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commission_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_link_stats_affiliate_link_id_foreign` (`affiliate_link_id`),
  KEY `affiliate_link_stats_order_id_foreign` (`order_id`),
  CONSTRAINT `affiliate_link_stats_affiliate_link_id_foreign` FOREIGN KEY (`affiliate_link_id`) REFERENCES `affiliate_links` (`id`) ON DELETE CASCADE,
  CONSTRAINT `affiliate_link_stats_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول referrals
CREATE TABLE IF NOT EXISTS `referrals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `affiliate_id` bigint(20) UNSIGNED NOT NULL,
  `referred_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `referral_code` varchar(255) NOT NULL,
  `status` enum('pending','converted','expired') NOT NULL DEFAULT 'pending',
  `source` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `referrals_affiliate_id_foreign` (`affiliate_id`),
  KEY `referrals_referred_user_id_foreign` (`referred_user_id`),
  CONSTRAINT `referrals_affiliate_id_foreign` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `referrals_referred_user_id_foreign` FOREIGN KEY (`referred_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول commission_transactions
CREATE TABLE IF NOT EXISTS `commission_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `affiliate_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `referral_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commission_transactions_affiliate_id_foreign` (`affiliate_id`),
  KEY `commission_transactions_order_id_foreign` (`order_id`),
  KEY `commission_transactions_referral_id_foreign` (`referral_id`),
  CONSTRAINT `commission_transactions_affiliate_id_foreign` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commission_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `commission_transactions_referral_id_foreign` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول withdrawal_requests
CREATE TABLE IF NOT EXISTS `withdrawal_requests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `affiliate_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','paid') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_details` json DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `transaction_reference` varchar(255) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdrawal_requests_affiliate_id_foreign` (`affiliate_id`),
  CONSTRAINT `withdrawal_requests_affiliate_id_foreign` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 