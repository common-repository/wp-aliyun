<?php
	function wp_aliyun_create_oss_dir($oss_sdk_service,$prefix,$tmp)
	{
		$name = $prefix.'-'.$tmp;
		$response = $oss_sdk_service->is_object_exist(WP_ALIYUN_OSS_BASE,$name.'/');
		
		if($response->status != '200')
		{
			wp_aliyun_log('创建数据库备份目录:'.$name);
			$oss_sdk_service->create_object_dir(WP_ALIYUN_OSS_BASE,$name);
			$response = $oss_sdk_service->is_object_exist(WP_ALIYUN_OSS_BASE,$name.'/');
			if($response->status != '200'){ return '['.$response->status .']'. $name.'目录创建失败！'; }
		}
		
		return 'ok';
	}
	
	function wp_aliyun_check_oss($id,$key,$prefix)
	{
		if($prefix==''||$id==''||$key==''){ return '信息不全！'; }
		
		$oss_sdk_service = new ALIOSS($id,$key);
		$oss_sdk_service->set_debug_mode(FALSE);
		
		$response = $oss_sdk_service->get_bucket_acl(WP_ALIYUN_OSS_BASE,array(ALIOSS::OSS_CONTENT_TYPE => 'text/xml'));
		
		if($response->status != '200') { return WP_ALIYUN_OSS_BASE.'目录验证失败！'; }
		
		$tmp = wp_aliyun_create_oss_dir($oss_sdk_service,$prefix,WP_ALIYUN_OSS_DATA);
		if($tmp != 'ok') { return $tmp; }
		
		$tmp = wp_aliyun_create_oss_dir($oss_sdk_service,$prefix,WP_ALIYUN_OSS_THEMES);
		if($tmp != 'ok') { return $tmp; }
		
		$tmp = wp_aliyun_create_oss_dir($oss_sdk_service,$prefix,WP_ALIYUN_OSS_UPLOADS);
		if($tmp != 'ok') { return $tmp; }
		
		return 'ok';
	}
	
	try
	{
		if($_POST && $_POST['action'] == 'check_oss')
		{
			$OSS_ACCESS_ID = $_POST['OSS_ACCESS_ID'];
			$OSS_ACCESS_KEY = $_POST['OSS_ACCESS_KEY'];
			$OSS_PREFIX = $_POST['OSS_PREFIX'];
			update_option('OSS_ACCESS_ID',$OSS_ACCESS_ID);
			update_option('OSS_ACCESS_KEY',$OSS_ACCESS_KEY);
			update_option('OSS_PREFIX',$OSS_PREFIX);
			
			//开始验证
			wp_aliyun_log('OSS_ACCESS_ID：'.$OSS_ACCESS_ID);
			wp_aliyun_log('OSS_ACCESS_KEY：'.$OSS_ACCESS_KEY);
			wp_aliyun_log('OSS_PREFIX：'.$OSS_PREFIX);
			
			$tip = wp_aliyun_check_oss($OSS_ACCESS_ID,$OSS_ACCESS_KEY,$OSS_PREFIX);
			if($tip != 'ok'){ wp_aliyun_log($tip,true); }
		}
	}catch (Exception $ex){
		wp_aliyun_log($ex->getMessage(),true);
		die();
	}
	
?>