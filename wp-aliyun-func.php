<?php
require_once((dirname(__FILE__)) . '/wp-aliyun-func-data.php');
require_once((dirname(__FILE__)) . '/wp-aliyun-func-themes.php');
require_once((dirname(__FILE__)) . '/wp-aliyun-func-uploads.php');
require_once((dirname(__FILE__)) . '/wp-aliyun-func-autodel.php');
/**
 * 日志
 *
 * @since 1.0.0
 */
function wp_aliyun_log($info,$error = false)
{
	try
	{
		if(is_array($info))
		{
			foreach($info as $i)
			wp_aliyun_log($i,$error);
		}
		else
		{
			$logfile = WP_ALIYUN_LOG;
			
			$file = fopen($logfile,'a+');
			
			if (is_file($logfile))
			{
				$s = '<span class="timestamp">'.date('Y/m/d H:i:s').'</span>';
				if($error){$s .= '<span class="error">[错误：]'.$info.'</span>';}
				else {$s .= '<span>[执行：]'.$info.'</span>';}
				
				$s .= '<br>';
				fwrite($file,$s);
				fclose($file);
			}
		}
	}catch (Exception $ex){
			echo $ex->getMessage();
			die();
	}
}

/**
 * 上传文件到阿里云上
 *
 * @since 1.0.0
 * 参数 $bucket 	单元名称
 * 参数 $file_path 	上传的文件路径
 * 参数 $savename 	保存名
 * @return void
 */
function wp_aliyun_back2aliyun($bucket,$file_path,$savename)
{
	try
	{
		$oss_sdk_service = new ALIOSS(get_option('OSS_ACCESS_ID'),get_option('OSS_ACCESS_KEY'));
		$oss_sdk_service->set_debug_mode(TRUE);
		
		wp_aliyun_log('开始上传备份到阿里云...');
		
		$response = $oss_sdk_service->upload_file_by_file($bucket,$savename,$file_path);
		wp_aliyun_log('阿里云：['.$response->status.']'.$response->body);
		
	}catch (Exception $ex){
		wp_aliyun_log($ex->getMessage(),true);
		die();
	}
}

function wp_aliyun_run()
{
	if(!wp_aliyun_check_oss(get_option('OSS_ACCESS_ID'),get_option('OSS_ACCESS_KEY'),get_option('OSS_PREFIX'))){ wp_aliyun_log('阿里云验证错误！',true); return; }
	
	$i = 1;
	wp_aliyun_log('任务开始！');
	$begintime = date('Y-m-d H:i:s');
	
	/*  开始执行任务  */
	//1.备份数据库
	if(!get_option('WPALIYUN_BAK_DATA')) { wp_aliyun_log('没有设置导出数据库...'); }
	else
	{
		wp_aliyun_log(($i++).'.正在导出数据库...');
		wp_aliyun_backup_data2oss();
	}
	
	//2.备份主题
	if(!get_option('WPALIYUN_BAK_THEME')) { wp_aliyun_log('没有设置导出主题...'); }
	else
	{
		wp_aliyun_log(($i++).'.正在导出主题...');
		wp_aliyun_backup_themes2oss();
	}
	
	//3.备份上传文件
	if(!get_option('WPALIYUN_BAK_UPLOADS')) { wp_aliyun_log('没有设置导出上传文件...'); }
	else
	{
		wp_aliyun_log(($i++).'.正在导出上传文件...');
		wp_aliyun_backup_uploads2oss();
	}
	
	$savecount = get_option('WPALIYUN_BACK_SAVE_COUNT');
	if($savecount <= 0 || !$savecount){wp_aliyun_log('未设置自动清除过期备份。');}
	else
	{
		wp_aliyun_log('自动清除过期备份。当前配置备份上限数量为：['.$savecount.']');
		wp_aliyun_auto_del($savecount);
	}
	
	/*  执行任务结束  */
	$end = date('Y-m-d H:i:s');
	$times=strtotime($end)-strtotime($begintime);
	if($times == 0)$times=1;
	wp_aliyun_log('任务完成于 '.$times.' 秒内！');
}

//删除文件
function wp_aliyun_del_file($dir,$deldir=true) 
{
	if(!is_dir($dir))
	{
		//如果是文件，直接删除
	   	unlink($dir);
	}else{
		$str = scandir($dir);
		foreach($str as $file){
		   if($file!="." && $file!=".."){
		    $path = $dir."/".$file;
		      if(!is_dir($path)) {
		                unlink($path);
		            } else {
		                deldir($path);
		            }
		   }
		}
		if($deldir)
		{
			if(rmdir($dir)) { return true; } 
			else { return false; }
		}else{ return true; }
	}
}

function wp_aliyun_get_path_files($path){
	$files = array();
    foreach (glob($path.DIRECTORY_SEPARATOR.'*') as $f)
    {
        $filestmp = is_dir($f) ? wp_aliyun_get_path_files($f) : $f;
        if($filestmp == ''){ break; }
        
        if(is_array($filestmp)) { $files = array_merge_recursive($files,$filestmp); }
		else { array_push($files,str_replace("//","/",str_replace("\\","/",$filestmp))); }
    }
    return $files;
}

function wp_aliyun_get_next_cron_time()
{
	$nowtime = time();
	$nexttime = mktime(get_option('WPALIYUN_BACK_H'),get_option('WPALIYUN_BACK_M'),0,date('m'),date('d'),date('Y'));
	if($nowtime > $nexttime) { $n = date('Y年m月d日 H:i', $nexttime + 24*60*60); }
	else { $n = date('Y年m月d日 H:i', $nexttime); }
	return $n;
}

//自动删除日志
if(get_option('WPALIYUN_AUTO_DEL_LOG'))
{
	//自动删除日志，必须在 require_once((dirname(__FILE__)) . '/wp-aliyun-func.php') 下面执行
	wp_aliyun_del_file(WP_ALIYUN_LOG_PATH,false);
}
?>