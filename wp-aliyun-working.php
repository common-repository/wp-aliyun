<?
function wp_aliyun_backwpup_read_logfile($logfile) {
	if (is_file($logfile))
		$logfiledata=file($logfile);
	else
		return array();
	$lines=array();
	foreach ($logfiledata as $line){
		$lines[]=$line;
	}
	return $lines;
}

if (isset($_POST['logfile']))
	$logfile=realpath($_POST['logfile']);
if (substr($logfile,-5)!='.html')
	die();

if (isset($_POST['logpos'])) $logpos=$_POST['logpos'];
else $logpos = 0;

$log='';
if (is_file($logfile)) 
{
	$logfilarray=wp_aliyun_backwpup_read_logfile($logfile);
	for ($i=$logpos;$i<count($logfilarray);$i++)
	{
			$log.=$logfilarray[$i];
			$tlogpos = $i;
	}
	echo json_encode(array('LOG'=>$log,'logpos'=>$tlogpos+1));
}
die();
?>