<?php
/*
Plugin Name: WP-ALIYUN
Plugin URI: http://wordpress.org/extend/plugins/wp-aliyun/
Description: aliyun WordPress 插件
Version: 2.3.1
Author: <a href="http://hcsem.com/">黄聪</a>
*/

require_once((dirname(__FILE__)) . '/lib/oss/sdk.class.php');
require_once((dirname(__FILE__)) . '/lib/pclzip.lib.php');

require_once((dirname(__FILE__)) . '/wp-aliyun-config.php');
require_once((dirname(__FILE__)) . '/wp-aliyun-func.php');
require_once((dirname(__FILE__)) . '/wp-aliyun-form.php');
require_once((dirname(__FILE__)) . '/wp-aliyun-checkoss.php');
require_once((dirname(__FILE__)) . '/wp-aliyun-savesetting.php');

//创建菜单
add_action('admin_menu', 'wpaliyun_admin_menu');

if($_POST['action'] == 'now')
{
	wp_aliyun_run();
}

?>