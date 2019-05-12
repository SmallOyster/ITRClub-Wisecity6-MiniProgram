<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序API-庄
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-08
 * @version 2019-05-12
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
	
	$sql='SELECT a.id,a.create_time,a.update_time,a.status,a.type,a.num,a.money_type AS moneyTypeId,IF(a.money_type=0,"黄金",(SELECT b.bank_name FROM `group` b WHERE a.money_type=b.bank_id)) AS moneyTypeName FROM bank_log a WHERE a.team_id=? ';
	
	$orderBy=json_decode($orderBy,true);
	foreach($orderBy as $key=>$value){
		if($key=='m'){
			if(strlen($value)==1) $sql.="AND a.money_type='{$value}' ";
			elseif(strlen($value)==2) $sql.="AND a.money_type=".substr($value,1,1)." AND a.bank_id=".substr($value,0,1).' ';
		}
		elseif($key=='sec' && $value=='c') $sql.='ORDER BY a.create_time '.$sc;
		elseif($key=='sec' && $value=='t') $sql.='ORDER BY a.type '.$sc;
		elseif($key=='sec' && $value=='s') $sql.='ORDER BY a.status '.$sc;
	}
	
	if(strpos($sql,'ORDER BY')==false) $sql.='ORDER BY a.update_time DESC';

	$query=PDOQuery($dbcon,$sql,[$teamId],[PDO::PARAM_INT]);
	
	returnAjaxData(200,'success',['list'=>$query[0],'sessionId'=>session_id()]);
}


function getDetail($dbcon,$teamId=0,$orderId='')
{	
	$query=PDOQuery($dbcon,'SELECT a.id,a.create_time AS createTime,a.update_time AS updateTime,a.status,a.type,a.num,a.remark,a.extra_param AS extraParam,a.money_type AS moneyTypeId,(SELECT real_name FROM user d WHERE d.id=a.update_user_id) AS updateUserName,IF(a.money_type=0,"黄金",(SELECT b.bank_name FROM `group` b WHERE a.money_type=b.bank_id)) AS moneyTypeName,(SELECT bank_name FROM `group` b WHERE a.bank_id=b.id) AS bankName FROM bank_log a WHERE a.team_id=? AND a.id=?',[$teamId,$orderId],[PDO::PARAM_INT,PDO::PARAM_STR]);
	
	if(isset($query[0][0])){
		$query[0][0]['extraParam']=json_decode($query[0][0]['extraParam'],true);
		returnAjaxData(200,'success',['info'=>$query[0][0]]);
	}
	else returnAjaxData(404,'Order not found');
}
