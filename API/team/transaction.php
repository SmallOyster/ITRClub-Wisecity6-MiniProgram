<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序API-交易
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-04-29
 * @version 2019-05-10
 */

if(isset($_GET['sid'])) session_id($_GET['sid']);
session_start();
require_once '../publicFunc.php';

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
	
	$sql='SELECT a.id,a.type,a.goods_name,a.update_time,a.status,IFNULL((SELECT b.name FROM team b WHERE a.from_team_id=b.id),"劳动者") AS initiator,IFNULL((SELECT b.name FROM team b WHERE a.to_team_id=b.id),"劳动者") AS receiver FROM order_log a WHERE (a.from_team_id=? OR a.to_team_id=?) ';
	
	$orderBy=json_decode($orderBy,true);
	foreach($orderBy as $key=>$value){
		if($key=='m') $sql.="AND a.money_type='{$value}' ";
		elseif($key=='sec' && $value=='t') $sql.='ORDER BY a.type '.$sc;
		elseif($key=='sec' && $value=='g') $sql.='ORDER BY a.goods_name '.$sc;
		elseif($key=='sec' && $value=='c') $sql.='ORDER BY a.create_time '.$sc;
		elseif($key=='sec' && $value=='s') $sql.='ORDER BY a.status '.$sc;
	}
	
	if(strpos($sql,'ORDER BY')==false) $sql.='ORDER BY a.update_time DESC';

	$query=PDOQuery($dbcon,$sql,[$teamId,$teamId],[PDO::PARAM_INT,PDO::PARAM_INT]);
	
	returnAjaxData(200,'success',['list'=>$query[0],'sessionId'=>session_id()]);
}


function getDetail($dbcon,$teamId=0,$orderId='')
{	
	$query=PDOQuery($dbcon,'SELECT a.type,a.goods_name AS goodsName,a.num,a.money,a.remark,a.extra_param AS extraParam,a.status,a.create_time AS createTime,a.update_time AS updateTime,(SELECT real_name FROM user d WHERE d.id=a.update_user_id) AS updateUserName,IFNULL((SELECT b.name FROM team b WHERE a.from_team_id=b.id),"劳动者") AS initiator,IFNULL((SELECT b.name FROM team b WHERE a.to_team_id=b.id),"劳动者") AS receiver,IFNULL((SELECT c.bank_name FROM `group` c WHERE c.bank_id=a.money_type AND c.bank_id!=0),"黄金") AS moneyType FROM order_log a WHERE a.id=? AND (a.from_team_id=? OR a.to_team_id=?)',[$orderId,$teamId,$teamId],[PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT]);
	
	if(isset($query[0][0])) returnAjaxData(200,'success',['info'=>$query[0][0]]);
	else returnAjaxData(404,'Order not found');
}
