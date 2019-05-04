<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序API-绑定队伍
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-04-21
 * @version 2019-05-04
 */

require_once 'publicFunc.php';

$openId=inputPost('openId',0,1);
$mod=inputPost('mod',0,1);

switch($mod){
	case 'bind':
		toBind($dbcon,$openId,inputPost('userName',0,1),inputPost('password',0,1));
		break;
	case 'unbind':
		toUnbind($dbcon,$openId);
		break;
	default:
		returnAjaxData(5001,'Invaild mod');
}


function toBind($dbcon,$openId,$userName,$password){
	$query=PDOQuery($dbcon,'SELECT role_name,org_id,real_name FROM user WHERE user_name=? AND password=?',[$userName,sha1($password)],[PDO::PARAM_STR,PDO::PARAM_STR]);

	if($query[1]==1){
		$orgId=$query[0][0]['org_id'];
		$roleName=$query[0][0]['role_name'];

		if($roleName=='admin'){
			$orgType=0;
			$returnData=['role'=>'admin','realName'=>$query[0][0]['real_name']];
		}elseif($roleName=='group'){
			$query2=PDOQuery($dbcon,'SELECT name FROM `group` WHERE id=?',[$orgId],[PDO::PARAM_INT]);

			if($query2[1]==1){
				$orgType=2;
				$groupName=$query2[0][0]['name'];
				$returnData=['role'=>'group','groupId'=>$orgId,'groupName'=>$groupName,'realName'=>$query[0][0]['real_name']];
			}else{
				returnAjaxData(404,'Group not found');
			}
		}else{
			$orgType=1;
			$query2=PDOQuery($dbcon,'SELECT a.name AS teamName,b.name AS groupName FROM team a,`group` b WHERE a.id=? AND a.group_id=b.id',[$orgId],[PDO::PARAM_INT]);

			if($query2[1]==1){
				$teamName=$query2[0][0]['teamName'];
				$groupName=$query2[0][0]['groupName'];
				$returnData=['role'=>'team','teamId'=>$orgId,'teamName'=>$teamName,'groupName'=>$groupName];
			}else{
				returnAjaxData(404,'Team not found');
			}
		}

		$query3=PDOQuery($dbcon,'INSERT INTO wxmp_open_id(open_id,org_type,org_id) VALUES (?,?,?)',[$openId,$orgType,$orgId],[PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT]);

		if($query3[1]==1) returnAjaxData(200,'success',$returnData);
		else returnAjaxData(500,'Failed to bind user');
	}else{
		returnAjaxData(403,'Failed to authorize');
	}
}


function toUnbind($dbcon,$openId){
	$query=PDOQuery($dbcon,'DELETE FROM wxmp_open_id WHERE open_id=?',[$openId],[PDO::PARAM_STR]);
	
	if($query[1]==1) returnAjaxData(200,'success');
	else returnAjaxData(500,'error',[$query]);
}
