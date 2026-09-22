-- Patch kompatibilitas schema untuk database lama ZYA CBT.
-- Aman dijalankan berulang kali pada MariaDB/MySQL.
ALTER TABLE `cbt_user`
    ADD COLUMN IF NOT EXISTS `user_login` int(11) NOT NULL DEFAULT 0 COMMENT '0=Tidak Login, 1=Login' AFTER `user_id`;

ALTER TABLE `cbt_user`
    ADD COLUMN IF NOT EXISTS `user_login_date` date DEFAULT NULL AFTER `user_login`;
