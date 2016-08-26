<?php

namespace captcha;
use atomar\core\Logger;

/**
 * Implements hook_uninstall()
 */
function uninstall() {
  // destroy tables and variables
  $sql = <<<SQL
SET foreign_key_checks = 0;
DROP TABLE IF EXISTS `captcha`;
SET foreign_key_checks = 1;
SQL;

  // perform installation
  \R::begin();
  try {
    \R::exec($sql);
    \R::commit();
    return true;
  } catch (\Exception $e) {
    \R::rollback();
    Logger::log_error('Failed to un-install Captcha', $e->getMessage());
    return false;
  }
}

/**
 * Implements hook_update_version()
 */
function update_1() {
  // prepare sql
  $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `captcha` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `token` (`token`(191)),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;

  // perform installation
  \R::begin();
  try {
    \R::exec($sql);
    \R::commit();
    return true;
  } catch (\Exception $e) {
    \R::rollback();
    Logger::log_error('Installation of Captcha failed', $e->getMessage());
    return false;
  }
}