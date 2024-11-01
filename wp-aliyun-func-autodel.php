<?php
/**
 * 自动删除历史备份
 *
 */
function wp_aliyun_auto_del_func($prefix,$savecount)
{
	if($savecount <= 0){ return; }
	
	if(!wp_aliyun_check_oss(get_option('OSS_ACCESS_ID'),get_option('OSS_ACCESS_KEY'),get_option('OSS_PREFIX'))){ wp_aliyun_log('阿里云验证错误！',true); return; }
	
	$obj = new ALIOSS(get_option('OSS_ACCESS_ID'),get_option('OSS_ACCESS_KEY'));
	$obj->set_debug_mode(FALSE);
	
	$bucket = WP_ALIYUN_OSS_BASE;
	$options = array(
		'delimiter' => '',
		'marker' => '',
		'prefix' => $prefix.'/',
		'max-keys' => 200
	);
	
	$response = $obj->list_object($bucket,$options);
	
	$p = xml_parser_create();
	xml_parse_into_struct($p, $response->body, $vals, $index);
	xml_parser_free($p);

	$objects = array();
	
	for($i=0;$i<count($vals);$i++)
	{
		if($vals[$i]['tag']=='KEY')
		{
			$f = $vals[$i]['value'];
			array_push($objects,$f);
		}
	}
	
	$delobjects = array();
	if(count($objects) > $savecount)
	{
		for($i=0;$i<count($objects) - $savecount;$i++)
		{
			array_push($delobjects,$objects[$i]);
		}
		$options = array('quiet' => false);
		$response = $obj->delete_objects($bucket,$delobjects,$options);
	}
}
function wp_aliyun_auto_del($savecount)
{
	try
	{
		if(!$savecount)
		{
			$savecount = get_option('WPALIYUN_BACK_SAVE_COUNT');
		}
		wp_aliyun_auto_del_func(get_option('OSS_PREFIX') . '-data',$savecount);
		wp_aliyun_auto_del_func(get_option('OSS_PREFIX') . '-themes',$savecount);
		wp_aliyun_auto_del_func(get_option('OSS_PREFIX') . '-uploads',$savecount);
		
	}catch (Exception $ex)
	{
		wp_aliyun_log($ex->getMessage(),true);
		die();
	}
}
?>