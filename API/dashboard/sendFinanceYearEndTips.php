<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序-财年结束提醒
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-04-20
 * @version 2019-04-25
 */

session_start();

require_once '../publicFunc.php';

$password=inputGet('password',0,1);
$num=inputGet('num',0,1);
$endDate=inputGet('endDate',0,1);
$surplus=inputGet('surplus',0,1);
$data=json_encode(['keyword1'=>['value'=>'第 '.$num.' 财年'],'keyword2'=>['value'=>'至'.$endDate.'结束'],'keyword3'=>['value'=>'最后'.$surplus.'分钟'],'keyword4'=>['value'=>'请各位选手注意时间安排，尽快完成交易！财年结束后将禁止一切操作！']]);

if($password!='itrwc6') returnAjaxData(403,'Invaild password');

if(isset($_SESSION['accessToken']) && time()<=$_SESSION['expired']) $accessToken=$_SESSION['accessToken'];
else{
	$getAccessTokenUrl='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APP_ID.'&secret='.APP_SECRET;
	$getAccessToken=curl($getAccessTokenUrl);
	$accessToken=json_decode($getAccessToken,true);
	$accessToken=$accessToken['access_token'];
	
	$_SESSION['accessToken']=$accessToken;
	$_SESSION['expired']=time()+7200;
}

$teamQuery=PDOQuery($dbcon,'SELECT name FROM team');
$teamList=$teamQuery[0];
$openTeamQuery=PDOQuery($dbcon,'SELECT DISTINCT(b.name) FROM wxmp_open_id a,team b,wxmp_form_id c WHERE a.team_id=b.id AND c.status=0 AND a.open_id=c.open_id');
$openTeamList=$openTeamQuery[0];
$openList=[];$noOpenTeamList=[];
foreach($openTeamList as $openTeamInfo){
	array_push($openList,$openTeamInfo['name']);
}

foreach($teamList as $teamInfo){
	if(!in_array($teamInfo['name'],$openList)){
		array_push($noOpenTeamList,$teamInfo['name']);
	}
}

$formIdQuery=PDOQuery($dbcon,'SELECT MAX(form_id),open_id FROM wxmp_form_id WHERE status=0 GROUP BY open_id');
$formIdList=$formIdQuery[0];

unset($teamQuery,$openTeamQuery,$formIdQuery,$openTeamList);

$success=0;$error=[];
foreach($formIdList as $formIdInfo){
	$req=curl('https://wx.itrclub.com/wisecity6/templateMessage.php?mod=send','post',['remark'=>'财年结束提醒-第'.$num.'财年','accessToken'=>$accessToken,'templateId'=>'sccRVYT6Bn-Xto4UAKxuRArfEM3b2MZKvAhYx41FFLo','openId'=>$formIdInfo['open_id'],'formId'=>$formIdInfo['MAX(form_id)'],'data'=>$data,'page'=>'pages/function/index','emphasisKeyword'=>'keyword3.DATA','ip'=>getIP()]);
	$req=json_decode(substr($req,3),true);
	if($req['code']==200) $success++;
	else array_push($error,$req);
}

returnAjaxData(200,'success',['noUserTeam'=>$noOpenTeamList,'totalUser'=>count($formIdList),'success'=>$success,'error'=>$error]);
