<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序API-钱库
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-04-30
 * @version 2019-05-04
 */

require_once '../publicFunc.php';

$mod=inputGet('mod',0,1);
$groupId=inputGet('groupId',0,1);

switch($mod){
	case 'list':
		getList($dbcon,$groupId);
		break;
	default:
		returnAjaxData(5001,'Invaild mod');
}


function getList($dbcon,$groupId=0)
{
	$query=PDOQuery($dbcon,'SELECT money_type AS moneyType,num FROM treasury WHERE org_type=2 AND org_id=?',[$groupId],[PDO::PARAM_INT]);
	
	returnAjaxData(200,'success',['list'=>$query[0]]);
}
