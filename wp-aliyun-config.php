<?php

date_default_timezone_set('Asia/Shanghai');
/* is_debug */
if (!defined('WP_ALIYUN_IS_DEBUG')) define('WP_ALIYUN_IS_DEBUG', true);

global $wpdb;
if (!defined('WP_ALIYUN_DATABASE_NAME')) define('WP_ALIYUN_DATABASE_NAME', $wpdb->get_var($wpdb->prepare("select database()","")));
if (!defined('WP_ALIYUN_OSS_BASE')) define('WP_ALIYUN_OSS_BASE', 'wpaliyun');
if (!defined('WP_ALIYUN_OSS_DATA')) define('WP_ALIYUN_OSS_DATA', 'data');
if (!defined('WP_ALIYUN_OSS_THEMES')) define('WP_ALIYUN_OSS_THEMES', 'themes');
if (!defined('WP_ALIYUN_OSS_UPLOADS')) define('WP_ALIYUN_OSS_UPLOADS', 'uploads');
if (!defined('WP_ALIYUN_THEME_NAME')) define('WP_ALIYUN_THEME_NAME', get_option('template'));

if (!defined('WP_ALIYUN_PAGE_URL'))
	define('WP_ALIYUN_PAGE_URL', get_option('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/');

if (!defined('WP_ALIYUN_PATH'))
	define('WP_ALIYUN_PATH', ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/');

if (!defined('WP_ALIYUN_THEME_PATH'))
	define('WP_ALIYUN_THEME_PATH', ABSPATH.'wp-content/themes/'.WP_ALIYUN_THEME_NAME.'/');

if (!defined('WP_ALIYUN_UPLOADS_PATH'))
	define('WP_ALIYUN_UPLOADS_PATH', ABSPATH.'wp-content/uploads/');

if (!defined('WP_ALIYUN_ZIP_URL'))
	define('WP_ALIYUN_ZIP_URL', WP_ALIYUN_PAGE_URL.'zip/');

if (!defined('WP_ALIYUN_ZIP_PATH'))
	define('WP_ALIYUN_ZIP_PATH', WP_ALIYUN_PATH.'zip/');


if (!defined('WP_ALIYUN_ZIP_URL'))
	define('WP_ALIYUN_ZIP_URL', WP_ALIYUN_PAGE_URL.'zip/');

if (!defined('WP_ALIYUN_ZIP_PATH'))
	define('WP_ALIYUN_ZIP_PATH', WP_ALIYUN_PATH.'zip/');

if (!defined('WP_ALIYUN_LOG_PATH'))
	define('WP_ALIYUN_LOG_PATH', WP_ALIYUN_PATH.'log/');

if (!defined('WP_ALIYUN_LOG'))
	define('WP_ALIYUN_LOG', WP_ALIYUN_LOG_PATH.'log_'.date("Y-m-d_H-i-s") .'.html');
?>