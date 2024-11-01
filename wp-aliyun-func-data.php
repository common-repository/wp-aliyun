<?php
/**
 * 上传文件到阿里云上
 *
 * @since 1.0.0
 * 参数 $bucket 	单元名称
 * 参数 $file_path 	上传的文件路径
 * 参数 $savename 	保存名
 * @return void
 */

/**
 * 备份数据库为sql并且压缩
 *
 * @since 1.0.0
 */
function wp_aliyun_create_data_zip(){
	try
	{
		global $wpdb;
		$database = WP_ALIYUN_DATABASE_NAME;
		
		$tabelstobackup=array();
		$sql = "SHOW TABLES FROM `".$database."` like '". $wpdb->prefix ."%'";
		$result=mysql_query($sql); //get table status
		
		wp_aliyun_log('获取备份数据库所有表。');
		while ($data = mysql_fetch_row($result)) {
			$tabelstobackup[]=$data[0];
		}
		
		wp_aliyun_log('获取表创建sql语句。');
		if (count($tabelstobackup)>0) {
			$result=mysql_query("SHOW TABLE STATUS FROM `".$database."` like '". $wpdb->prefix ."%'"); //get table status
			if (!$result)
			{
				wp_aliyun_log('没有找到任何数据表。',true);
				return '';
			}
			
			while ($data = mysql_fetch_assoc($result)) {
				$status[$data['Name']]=$data;
			}
		}
		
		$filecratetime = date("YmdHis");
		$file_prefix = $database .'_data_'. $filecratetime;
		$savepath = WP_ALIYUN_ZIP_PATH . $file_prefix . '.sql';
		if ($file = fopen($savepath, 'wb')) {
			foreach($tabelstobackup as $table) {
				if(strpos($table,$wpdb->prefix) == 0)
				{
					wp_aliyun_log('导出数据库表 "'.$table.'"...');
					wp_aliyun_db_dump_table($table,$status[$table],$file);
				}
			}
		}
		fclose($file);
		wp_aliyun_log('数据库导出完成！');
		
		$zipfilename = WP_ALIYUN_ZIP_PATH . $file_prefix . '.zip';
		$filelist = array();
		$filelist[0]=$savepath;
		
		wp_aliyun_log('正在压缩(PclZip)备份文件...');
		
		$zip = new PclZip($zipfilename); 
		$v_list = $zip->create($filelist);
		
		wp_aliyun_log('添加大小为 '.sprintf('%.2f',filesize($zipfilename) / 1024) . '  KB 的数据库压缩文件 "'.$file_prefix.'.zip" 到备份文件夹');
		
		wp_aliyun_log('删除sql文件...');
		wp_aliyun_del_file($savepath);

		wp_aliyun_log('数据库备份压缩完成！');
		return $file_prefix . '.zip';
		
	}catch (Exception $ex){
		wp_aliyun_log($ex->getMessage(),true);
		die();
	}
}

function wp_aliyun_db_dump_table($table,$status,$file) 
{
	try
	{
		// 创建表
		fwrite($file, "--\n\n");
		fwrite($file, "-- 创建表 $table\n");
		fwrite($file, "--\n\n");
		fwrite($file, "DROP TABLE IF EXISTS `" . $table .  "`;\n");
		//获取表结构
		$result=mysql_query("SHOW CREATE TABLE `".$table."`");
		$tablestruc=mysql_fetch_assoc($result);
		fwrite($file, $tablestruc['Create Table'].";\n");

		//创建数据
		$result=mysql_query("SELECT * FROM `".$table."`");
	    //获取字段信息
	    $fieldsarray = array();
	    $fieldinfo   = array();
	    $fields      = mysql_num_fields( $result );
	    for ( $i = 0; $i < $fields; $i ++ ) {
	        $fieldsarray[$i]             = mysql_field_name( $result, $i );
	        $fieldinfo[$fieldsarray[$i]] = mysql_fetch_field( $result, $i );
	    }
		fwrite($file, "--\n");
		fwrite($file, "-- 为 $table 创建数据\n");
		fwrite($file, "--\n\n");
		while ($data = mysql_fetch_assoc($result)) 
		{
			$keys = array();
			$values = array();
	        foreach ( $data as $key => $value ) 
	        {
	            if ( is_null( $value ) || ! isset($value) ) // Make Value NULL to string NULL
	                $value = "NULL";
	            elseif ( $fieldinfo[$key]->numeric == 1 && $fieldinfo[$key]->type != 'timestamp' && $fieldinfo[$key]->blob != 1 ) //is value numeric no esc
	                $value = empty($value) ? 0 : $value;
	            else
	                $value = "'" . mysql_real_escape_string( $value ) . "'";
	            $values[] = $value;
	        }
			fwrite($file, "REPLACE INTO `".$table."` VALUES ( ".implode(", ",$values)." );\n");
		}
	}catch (Exception $ex){
		wp_aliyun_log($ex->getMessage(),true);
		die();
	}
	
}

function wp_aliyun_backup_data2oss()
{
	if(!wp_aliyun_check_oss(get_option('OSS_ACCESS_ID'),get_option('OSS_ACCESS_KEY'),get_option('OSS_PREFIX'))){ wp_aliyun_log('阿里云验证错误！',true); return; }
	$zipfilename = wp_aliyun_create_data_zip();
	$file_path = WP_ALIYUN_ZIP_PATH . $zipfilename;
	wp_aliyun_back2aliyun(WP_ALIYUN_OSS_BASE,$file_path,get_option('OSS_PREFIX').'-'.WP_ALIYUN_OSS_DATA.'/'.$zipfilename);
	
	if(get_option('WPALIYUN_AUTO_DEL_ZIP'))
	{
		wp_aliyun_log('自动删除zip文件...');
		wp_aliyun_del_file($file_path);
	}
}

?>