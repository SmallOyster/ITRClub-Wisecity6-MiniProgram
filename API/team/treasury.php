<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序API-钱库
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-04-29
 * @version 2019-05-04
 */

require_once '../publicFunc.php';

$mod=inputGet('mod',0,1);

switch($mod){
	case 'list':
		getList($dbcon,inputGet('teamId',0,1));
		break;
	case 'ticketName':
		getTicketName($dbcon);
		break;
	default:
		returnAjaxData(5001,'Invaild mod');
}


function getList($dbcon,$teamId=0)
{
	$query1=PDOQuery($dbcon,'SELECT money_type AS moneyType,num FROM treasury WHERE org_type=1 AND org_id=? AND money_type IN (0,4,5)',[$teamId],[PDO::PARAM_INT]);
	$list1=$query1[0];
	
	$query2=PDOQuery($dbcon,'SELECT a.money_type AS moneyType,a.num,b.name AS bank_name,(SELECT c.bank_name FROM `group` c WHERE SUBSTR(a.money_type,2,1)=c.bank_id) AS currency FROM treasury a,`group` b WHERE a.org_type=1 AND a.org_id=? AND SUBSTR(a.money_type,1,1)=b.bank_id AND a.money_type NOT IN (0,4,5) ORDER BY a.money_type',[$teamId],[PDO::PARAM_INT]);
	$list2=$query2[0];
	
	$rtn=[];$total1=count($list1);$total2=count($list2);
	for($i=0;$i<$total1;$i++){
		$rtn[$i]=$list1[$i];
	}
	for($j=0;$j<$total2;$j++){
		$rtn[$i+$j]=$list2[$j];
	}
	
	returnAjaxData(200,'success',['list'=>$rtn]);
}


function getTicketName($dbcon){
	$query=PDOQuery($dbcon,'SELECT bank_name AS ticketName FROM `group` WHERE bank_id IN (4,5) ORDER BY bank_id');
	
	if($query[1]==2) returnAjaxData(200,'success',[0=>'黄金',4=>$query[0][0]['ticketName'],5=>$query[0][1]['ticketName']]);
	else returnAjaxData(404,'Ticket not found');
}
