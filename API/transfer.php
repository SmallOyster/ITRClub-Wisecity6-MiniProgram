<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序API-转账
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-05
 * @version 2019-05-21
 */
 
if(isset($_GET['sid'])) session_id($_GET['sid']);
session_start();
require_once 'publicFunc.php';

$mod=inputGet('mod',0,1);
$teamId=inputGet('teamId',0,1);

switch($mod){
	case 'list':
		getList($dbcon,$teamId,inputGet('orderBy',1,1));
		break;
	case 'detail':
		getDetail($dbcon,$teamId,inputGet('orderId',0,1));
		break;
	default:
		returnAjaxData(5001,'Invaild mod');
}


function getList($dbcon,$teamId=0,$orderBy='')
{
	$sc=isset($_SESSION['itrwc_wxmp_order'])&&$_SESSION['itrwc_wxmp_order']=='ASC'?'DESC':'ASC';
	$_SESSION['itrwc_wxmp_order']=$sc;
	
	$sql='SELECT a.id,a.create_time,a.update_time,a.status,a.num,a.money_type AS moneyTypeId,IF(a.money_type=0,"白银",(SELECT c.bank_name FROM `group` c WHERE a.money_type=c.bank_id)) AS moneyTypeName,(SELECT b.name FROM team b WHERE a.from_team_id=b.id) AS initiator,(SELECT b.name FROM team b WHERE a.to_team_id=b.id) AS receiver FROM transfer_log a WHERE (a.from_team_id=? OR a.to_team_id=?) ';
	
	$orderBy=json_decode($orderBy,true);
	foreach($orderBy as $key=>$value){
		if($key=='m') $sql.="AND a.money_type='{$value}' ";
		elseif($key=='sec' && $value=='c') $sql.='ORDER BY a.create_time '.$sc;
		elseif($key=='sec' && $value=='n') $sql.='ORDER BY a.num '.$sc;
		elseif($key=='sec' && $value=='m') $sql.='ORDER BY a.money_type '.$sc;
		elseif($key=='sec' && $value=='t') $sql.='ORDER BY a.from_team_id '.$sc;
		elseif($key=='sec' && $value=='s') $sql.='ORDER BY a.status '.$sc;
	}
	
	if(strpos($sql,'ORDER BY')==false) $sql.='ORDER BY a.update_time DESC';

	$query=PDOQuery($dbcon,$sql,[$teamId,$teamId],[PDO::PARAM_INT,PDO::PARAM_INT]);
	
	returnAjaxData(200,'success',['list'=>$query[0],'sessionId'=>session_id()]);
}


function getDetail($dbcon,$teamId=0,$orderId='')
{	
	$query=PDOQuery($dbcon,'SELECT a.create_time AS createTime,a.update_time AS updateTime,a.status,a.num,a.remark,a.money_type AS moneyTypeId,(SELECT real_name FROM user d WHERE d.id=a.update_user_id) AS updateUserName,IF(a.money_type=0,"白银",(SELECT c.bank_name FROM `group` c WHERE a.money_type=c.bank_id)) AS moneyTypeName,(SELECT b.name FROM team b WHERE a.from_team_id=b.id) AS initiator,(SELECT b.name FROM team b WHERE a.to_team_id=b.id) AS receiver FROM transfer_log a WHERE a.id=? AND (a.from_team_id=? OR a.to_team_id=?)',[$orderId,$teamId,$teamId],[PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT]);
	
	if(isset($query[0][0])) returnAjaxData(200,'success',['info'=>$query[0][0]]);
	else returnAjaxData(404,'Order not found');
}
