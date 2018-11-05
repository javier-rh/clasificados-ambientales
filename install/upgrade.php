<?php
ignore_user_abort(1);

if ($config['version'] < "7.3") {

    // Check if SSL enabled
    $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off"
        ? "https://" : "http://";

// Define APPURL
    $site_url = $protocol
        . $_SERVER["SERVER_NAME"]
        . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
        . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");


    $site_url = $site_url."/";
    $site_url = str_replace ("admin/", "", $site_url);

    update_option($config,"site_url",$site_url);

}

if ($config['version'] < "7.2") {

    /***
     * Version 7.2 Upgrade
     ***/
    $filename = get_option($config,"quickad_secret_file");
    $filename = $filename.".php";
    unlink($filename);
    delete_option($config,"quickad_secret_file");
    delete_option($config,"purchase_key");

    update_option($config, "validation_time ", '0');
    update_option($config,'cron_time',time());
    update_option($config,'cron_exec_time','86400');
    update_option($config,'cron_validation_time',time());
    update_option($config, "cron_validation_exec_time ", '86400');
    update_option($config, "contact_validation ", '0');
    update_option($config, "delete_expired ", '0');
    update_option($config, "email_sub_signup_details ", '{SITE_TITLE} - {LANG_THANKSIGNUP}');
    update_option($config, "email_message_signup_details", 'Dear Valued Thanks for creating an account {SITE_TITLE} ,\n\nYour username: {USERNAME}\nYour password: {PASSWORD}\n\n\nHave further questions? You can start chat with live support team.\nSincerely,\n\n{SITE_TITLE} Team!\n{SITE_URL}');

    echo "ALTER latitude longitude in countries Table...  \t\t";
    $q = "ALTER TABLE `" . $config['db']['pre'] . "countries`
    ADD `latitude` VARCHAR(100) NULL DEFAULT NULL AFTER `code`,
    ADD `longitude` VARCHAR(100) NULL DEFAULT NULL AFTER `latitude`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";


}

if ($config['version'] < "7.1") {

    /***
     * Version 7.1 Upgrade
     ***/

    echo "DROP payment TABLE IF EXISTS...  \t\t";
    $q = "DROP TABLE IF EXISTS `" . $config['db']['pre'] . "payments`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `payments`...  \t\t";
    $q = "CREATE TABLE IF NOT EXISTS `" . $config['db']['pre'] . "payments`  (
  `payment_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_install` enum('0','1') NOT NULL DEFAULT '0',
  `payment_title` varchar(255) NOT NULL DEFAULT '',
  `payment_folder` varchar(30) NOT NULL DEFAULT '',
  `payment_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

      echo "Insert All Payment Method in Payment Table...  \t\t";
    $q = "INSERT INTO `" . $config['db']['pre'] . "payments` (`payment_id`, `payment_install`, `payment_title`, `payment_folder`, `payment_desc`) VALUES
(1, '1', 'Paypal', 'paypal', 'You will be redirected to paypal to complete payment.'),
(2, '1', 'Credit & Debit Card', 'stripe', 'Make an instant deposit using online payment service Stripe. Pay with your debit or credit card.'),
(3, '1', 'Bank Deposit (Offline Payment)', 'wire_transfer', NULL),
(4, '1', '2Checkout', '2checkout', NULL),
(5, '1', 'Paystack', 'paystack', 'You will be redirected to Paystack to complete payment.')";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";
}

if ($config['version'] < "7.0") {

    /*
    **
    Version 7.0 Upgrade
    ***/



    update_option($config, "email_sub_post_notification", '{SITE_TITLE} - {LANG_ADNOTICE}');
    update_option($config, "email_message_post_notification", 'This message has been sent automatically by the {SITE_TITLE} system.\nIf you need to contact us, go to {LINK_CONTACT}\n-----------------------------------------\nThe following project was recently added to {SITE_TITLE} and fits under your expertise:\n\n{ADTITLE}\n{ADLINK}');


    echo "ALTER Status in product Table...  \t\t";
    $q = "ALTER TABLE `" . $config['db']['pre'] . "product` CHANGE `status` `status` ENUM('pending','active','rejected','expire') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending'";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "ALTER Newsletter in product Table...  \t\t";
    $q = "ALTER TABLE `" . $config['db']['pre'] . "product`
    ADD `expire_date` INT(12) NOT NULL DEFAULT '0' AFTER `created_at`,
    ADD `emailed` ENUM('0', '1') NOT NULL DEFAULT '0' AFTER `admin_seen`,
    ADD `hide` ENUM('0', '1') NOT NULL DEFAULT '0' AFTER `emailed`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "ALTER User Table...  \t\t";
    $q = "ALTER TABLE `" . $config['db']['pre'] . "user`
    ADD `group_id` INT(11) NOT NULL DEFAULT '1' AFTER `online`,
    ADD `notify` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `group_id`,
    ADD `notify_cat` VARCHAR(255) NULL DEFAULT NULL AFTER `notify`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";



    echo "DROP emailq TABLE IF EXISTS...  \t\t";
    $q = "DROP TABLE IF EXISTS `" . $config['db']['pre'] . "emailq`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `emailq`...  \t\t";
    $q = "CREATE TABLE IF NOT EXISTS `" . $config['db']['pre'] . "emailq` (
    `q_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `toname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`q_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";



    echo "DROP logs TABLE IF EXISTS...  \t\t";
    $q = "DROP TABLE IF EXISTS `" . $config['db']['pre'] . "logs`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `logs`...  \t\t";
    $q = "CREATE TABLE IF NOT EXISTS `" . $config['db']['pre'] . "logs` (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_date` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `log_summary` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `log_details` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";



    echo "DROP Notification TABLE IF EXISTS...  \t\t";
    $q = "DROP TABLE IF EXISTS `" . $config['db']['pre'] . "notification`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `Notification`...  \t\t";
    $q = "CREATE TABLE IF NOT EXISTS `" . $config['db']['pre'] . "notification`  (
  `user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `cat_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `user_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "DROP subscriptions TABLE IF EXISTS...  \t\t";
    $q = "DROP TABLE IF EXISTS `" . $config['db']['pre'] . "subscriptions`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `subscriptions`...  \t\t";
    $q = "CREATE TABLE IF NOT EXISTS `" . $config['db']['pre'] . "subscriptions`  (
  `sub_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_term` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'MONTHLY',
  `sub_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `sub_image` text COLLATE utf8_unicode_ci,
  `group_id` smallint(10) DEFAULT NULL,
  `recommended` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `discount_badge` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`sub_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";


    echo "DROP upgrades TABLE IF EXISTS...  \t\t";
    $q = "DROP TABLE IF EXISTS `" . $config['db']['pre'] . "upgrades`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `upgrades`...  \t\t";
    $q = "CREATE TABLE IF NOT EXISTS `" . $config['db']['pre'] . "upgrades`  (
  `upgrade_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `upgrade_lasttime` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `upgrade_expires` int(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`upgrade_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "DROP usergroups TABLE IF EXISTS...  \t\t";
    $q = "DROP TABLE IF EXISTS `" . $config['db']['pre'] . "usergroups`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `usergroups`...  \t\t";
    $q = "CREATE TABLE IF NOT EXISTS `" . $config['db']['pre'] . "usergroups`  (
  `group_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_removable` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `group_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ad_duration` smallint(10) DEFAULT NULL,
  `urgent_project_fee` double(8,2) NOT NULL DEFAULT '0.00',
  `featured_project_fee` double(8,2) NOT NULL DEFAULT '0.00',
  `highlight_project_fee` double(8,2) NOT NULL DEFAULT '0.00',
  `top_search_result` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `show_on_home` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `show_in_home_search` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "TRUNCATE usergroups Table...  \t\t";
    $q = "TRUNCATE TABLE `" . $config['db']['pre'] . "usergroups`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";

    echo "CREATE TABLE `usergroups`...  \t\t";
    $q = "INSERT INTO `" . $config['db']['pre'] . "usergroups` (`group_id`, `group_removable`, `group_name`, `ad_duration`, `urgent_project_fee`, `featured_project_fee`, `highlight_project_fee`, `top_search_result`, `show_on_home`, `show_in_home_search`) VALUES
    (1, 0, 'Registerd users (Free)', 1, 20.00, 20.00, 20.00, 'no', 'no', 'no')";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "success<br>";
}

?>