<?php
	if($_POST && $_POST['action'] == 'save_setting')
	{
		update_option('WPALIYUN_BACK_H', $_POST['WPALIYUN_BACK_H']);
		update_option('WPALIYUN_BACK_M', $_POST['WPALIYUN_BACK_M']);
		update_option('WPALIYUN_BACK_ACT', $_POST['WPALIYUN_BACK_ACT']);
		update_option('WPALIYUN_AUTO_DEL_LOG', $_POST['WPALIYUN_AUTO_DEL_LOG']);
		update_option('WPALIYUN_AUTO_DEL_ZIP', $_POST['WPALIYUN_AUTO_DEL_ZIP']);
		update_option('WPALIYUN_BAK_DATA', $_POST['WPALIYUN_BAK_DATA']);
		update_option('WPALIYUN_BAK_THEME', $_POST['WPALIYUN_BAK_THEME']);
		update_option('WPALIYUN_BAK_UPLOADS', $_POST['WPALIYUN_BAK_UPLOADS']);
		update_option('WPALIYUN_BACK_SAVE_COUNT', $_POST['WPALIYUN_BACK_SAVE_COUNT']);
		
		wp_aliyun_log('备份配置保存成功！');
		if($_POST['WPALIYUN_BACK_ACT'])wp_aliyun_log('启动定时备份，下一次备份时间：'.wp_aliyun_get_next_cron_time());
	}
	
	if(get_option('WPALIYUN_BACK_ACT'))
	{
		//启动定时备份
		$nowtime = time();
		$nexttime = mktime(get_option('WPALIYUN_BACK_H'),get_option('WPALIYUN_BACK_M'),0,date('m'),date('d'),date('Y'));
		if($nowtime > $nexttime) { $nexttime = $nexttime + 24*60*60; }
		
		if( !wp_next_scheduled( 'wp_aliyun_data_cron_hook' ) ) { wp_schedule_event($nexttime, 'daily', 'wp_aliyun_data_cron_hook' ); }
		add_action( 'wp_aliyun_data_cron_hook', 'wp_aliyun_cron_data_exec' );
		function wp_aliyun_cron_data_exec() { wp_aliyun_backup_data2oss(); }
		
		$nexttime = $nexttime + 5*60;
		if( !wp_next_scheduled( 'wp_aliyun_themes_cron_hook' ) ) { wp_schedule_event($nexttime, 'daily', 'wp_aliyun_themes_cron_hook' ); }
		add_action( 'wp_aliyun_themes_cron_hook', 'wp_aliyun_cron_themes_exec' );
		function wp_aliyun_cron_themes_exec() { wp_aliyun_backup_themes2oss(); }
		
		$nexttime = $nexttime + 5*60;
		if( !wp_next_scheduled( 'wp_aliyun_uploads_cron_hook' ) ) { wp_schedule_event($nexttime, 'daily', 'wp_aliyun_uploads_cron_hook' ); }
		add_action( 'wp_aliyun_uploads_cron_hook', 'wp_aliyun_cron_uploads_exec' );
		function wp_aliyun_cron_uploads_exec() { wp_aliyun_backup_uploads2oss(); }
		
		$nexttime = $nexttime + 5*60;
		if( !wp_next_scheduled( 'wp_aliyun_auto_del_cron_hook' ) ) { wp_schedule_event($nexttime, 'daily', 'wp_aliyun_auto_del_cron_hook' ); }
		add_action( 'wp_aliyun_auto_del_cron_hook', 'wp_aliyun_cron_auto_del_exec' );
		function wp_aliyun_cron_auto_del_exec() { wp_aliyun_auto_del(); }
	}
?>