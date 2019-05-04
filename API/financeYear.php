<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序API-财年
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-04-28
 * @version 2019-05-04
 */

require_once 'publicFunc.php';

$mod=inputGet('mod',0,1);

switch($mod){
	case 'list':
		getList($dbcon);
		break;
	case 'detail':
		getDetail($dbcon,inputGet('num',0,1));
		break;
	case 'now':
		getNow($dbcon);
		break;
	default:
		returnAjaxData(5001,'Invaild mod');
}


function getList($dbcon)
{
	$query=PDOQuery($dbcon,'SELECT num,start_time,end_time,status FROM finance_year');
	
	returnAjaxData(200,'success',['list'=>$query[0]]);
}


function getNow($dbcon)
{
	$query=PDOQuery($dbcon,'SELECT num,start_time,end_time FROM finance_year WHERE start_time<='.time().' AND end_time>='.time().' AND status=1');
	
	if(isset($query[0][0])) returnAjaxData(200,'success',['list'=>$query[0][0]]);
	else returnAjaxData(404,'No finance year is in progress');
}


function getDetail($dbcon,$num=0)
{
	$query=PDOQuery($dbcon,'SELECT num,start_time,end_time,status FROM finance_year WHERE num=?',[$num],[PDO::PARAM_INT]);
	
	returnAjaxData(200,'success',['list'=>$query[0]]);
}
