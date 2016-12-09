<?php

namespace captcha;
use atomar\core\HookReceiver;

class Hooks extends HookReceiver
{
    // There are a number of hooks available. here is an example.
    function hookRoute($extension)
    {
        return array(
           // '/api/(?P<api>[a-zA-Z\_-]+)/?(\?.*)?' => 'captcha\controller\Api',
           // '/(\?.*)?' => 'captcha\controller\Index'
       );
    }

    function hookInstall()
    {
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
            throw $e;
        }
    }

    function hookUninstall()
    {
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
            throw $e;
        }
    }

    function hookPermission()
    {
        return array(
            'administer_captcha',
            'access_captcha'
        );
    }

    function hookLibraries()
    {
        return array('CaptchaAPI.php');
    }

    function hookTwig($twig)
    {
        $form = new \Twig_SimpleFunction('captcha_form', function() {
            CaptchaAPI::twig_insert_form();
        });
        $twig->addFunction($form);
    }
}