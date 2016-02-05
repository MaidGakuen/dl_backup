<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

$today = date('Y-m-d');
$lastdo = option::get('dl_backup_lastdo');  
$d1=strtotime($today);
$d2=strtotime($lastdo);
$c=(int)round(($d1-$d2)/3600/24);
$email = option::get('dl_backup_email');
$day = (int)option::get('dl_backup_day');
if($c >= $day && !empty($day) && !empty($email)){
option::set('dl_backup_lastdo',date('Y-m-d'));
global $m;
$e = $m->query('SHOW TABLES');
$aaa = 'Tables_in_'.DB_NAME;
$dump  = '#Warning: Do not change the comments!!!'  . "\n";
$dump .= '#Tieba-Cloud-Sign Database Backup' . "\n";
$dump .= '#Version:' . SYSTEM_VER . "\n";
$dump .= '#Date:' . date('Y-m-d H:m:s') . "\n";
$dump .= '############## Start ##############' . "\n";
while ($v = $m->fetch_array($e)) {
	$list  = $v;
	foreach ($list as $table) {
		$dump .= dataBak($table);
	}
}
$dump .= "\n" . '############## End ##############';
$title = SYSTEM_NAME . " " . date('Y-m-d') . " 数据库备份";
$x = misc::mail($email,$title,"备份文件已附上，请查看附件",array('backup-'.date('Ymd').'.sql' => $dump));
if($x != true){
	option::set('dl_backup_log',date('Y-m-d H:m:s').'  数据库备份邮件发送失败！');
	} else {
		option::set('dl_backup_log',date('Y-m-d H:m:s').'  数据库备份邮件发送成功！');
		}	
} else {
    if ($c < $day && !empty($day) && !empty($email)) {
        option::set('dl_backup_log',date('Y-m-d H:m:s') . '  设置正确！上次备份日期：' . $lastdo);
    } else {
        option::set('dl_backup_log',date('Y-m-d H:m:s') . '  设置不正确，无法进行备份并且发送邮件！');
    }
}
?>
