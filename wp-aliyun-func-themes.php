<?php

function wp_aliyun_create_themes_zip(){
	try
	{
		$filecratetime = date("YmdHis");
		$file_prefix = 'theme_'.$theme_name. $filecratetime;
		$zipfilename = WP_ALIYUN_ZIP_PATH . $file_prefix . '.zip';
		
		wp_aliyun_log('当前主题目录['.WP_ALIYUN_THEME_PATH.']');
		wp_aliyun_log('正在导出当前主题['.$theme_name.']...');
		
		$filelist = wp_aliyun_get_path_files(WP_ALIYUN_THEME_PATH);
		
		$zip = new PclZip($zipfilename); 
		$v_list = $zip->create($filelist);
		
		if(file_exists($zipfilename)){
			wp_aliyun_log('添加大小为 '.sprintf('%.2f',filesize($zipfilename) / 1024) . '  KB 的数据库压缩文件 "'.$file_prefix.'.zip" 到备份文件夹');}
		else{
			wp_aliyun_log($file_prefix.'.zip" 创建失败！',true);
			return;
		}
		wp_aliyun_log('主题['.$theme_name.']备份压缩完成！');
		
		return $file_prefix . '.zip';
		
	}catch (Exception $ex){
		wp_aliyun_log($ex->getMessage(),true);
		die();
	}
}

function wp_aliyun_backup_themes2oss()
{
	if(!wp_aliyun_check_oss(get_option('OSS_ACCESS_ID'),get_option('OSS_ACCESS_KEY'),get_option('OSS_PREFIX'))){ wp_aliyun_log('阿里云验证错误！',true); return; }
	
	$zipfilename = wp_aliyun_create_themes_zip();
	$file_path = WP_ALIYUN_ZIP_PATH . $zipfilename;
	wp_aliyun_back2aliyun(WP_ALIYUN_OSS_BASE,$file_path,get_option('OSS_PREFIX').'-'.WP_ALIYUN_OSS_THEMES.'/'.$zipfilename);
	
	if(get_option('WPALIYUN_AUTO_DEL_ZIP'))
	{
		wp_aliyun_log('自动删除zip文件...');
		wp_aliyun_del_file($file_path);
	}
}

?>