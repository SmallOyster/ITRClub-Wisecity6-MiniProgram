<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序-发送异动提醒
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-11
 * @version 2019-05-12
 */

session_start();
require_once 'publicFunc.php';

$orgType=inputPost('orgType',0,1);
$orgId=inputPost('orgId',0,1);
$moneyType=inputPost('moneyType',0,1);
$type=inputPost('type',1,1);
$num=inputPost('num',0,1);
$ip=inputPost('ip',0,1);

// 获取队伍里每个人的一个formId
$formIdQuery=PDOQuery($dbcon,'SELECT MAX(a.form_id) AS formId,a.open_id AS openId FROM wxmp_form_id a,wxmp_open_id b WHERE a.open_id=b.open_id AND b.org_type=? AND b.org_id=? AND a.status=0 GROUP BY a.open_id',[$orgType,$orgId],[PDO::PARAM_INT,PDO::PARAM_INT]);
if($formIdQuery[1]>=1) $formIdList=$formIdQuery[0];
else returnAjaxData(1,'No People have available FormID');

// 获取AccessToken
if(isset($_SESSION['accessToken']) && time()<=$_SESSION['expired']) $accessToken=$_SESSION['accessToken'];
else{
	$getAccessTokenUrl='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APP_ID.'&secret='.APP_SECRET;
	$getAccessToken=curl($getAccessTokenUrl);
	$accessToken=json_decode($getAccessToken,true);
	$accessToken=$accessToken['access_token'];
	
	$_SESSION['accessToken']=$accessToken;
	$_SESSION['expired']=time()+7200;
}

// 查询账户余额
$orgQuery=PDOQuery($dbcon,'SELECT num FROM treasury WHERE org_type=? AND org_id=? AND money_type=?',[$orgType,$orgId,$moneyType],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
if($orgQuery[1]==1) $surplus=$orgQuery[0][0]['num'];
else returnAjaxData(2,'Failed to found wallet');

// 查询币种名称
if($moneyType!=0){
	if(strlen($moneyType)>1){
		$bankId=substr($moneyType,0,1);
		$moneyType=substr($moneyType,1,1);
		
		$bankQuery=PDOQuery($dbcon,'SELECT name FROM `group` WHERE bank_id=?',[$bankId],[PDO::PARAM_INT]);
		
		if($moneyType!=0) $currencyQuery=PDOQuery($dbcon,'SELECT bank_name FROM `group` WHERE bank_id=?',[$moneyType],[PDO::PARAM_INT]);
		else $currencyQuery=[[['bank_name'=>'黄金']],1];
		
		if($bankQuery[1]==1 && $currencyQuery[1]==1) $moneyType=$bankQuery[0][0]['name'].'-'.$currencyQuery[0][0]['bank_name'];
		else $moneyType='未知币种2';
	}else{
		$currencyQuery=PDOQuery($dbcon,'SELECT bank_name FROM `group` WHERE bank_id=?',[$moneyType],[PDO::PARAM_INT]);
		if($currencyQuery[1]==1) $moneyType='央行-'.$currencyQuery[0][0]['bank_name'];
		else $moneyType='未知币种';
	}
}else{
	$moneyType='央行-黄金';
}

$data=json_encode(array(
	'keyword1'=>['value'=>$moneyType],
	'keyword2'=>['value'=>$type],
	'keyword3'=>['value'=>number_format($num,2,'.','')],
	'keyword4'=>['value'=>$surplus],
	'keyword5'=>['value'=>date('Y-m-d H:i:s')],
	'keyword6'=>['value'=>'如有疑问，请先自行登录网页版查询异动记录，再向主席团询问，谢谢配合！']
));


$error=[];$success=0;
foreach($formIdList as $formIdInfo){
	$postData=array(
		'remark'=>'余额异动-'.$type.'-'.$moneyType.$num,
		'accessToken'=>$accessToken,
		'templateId'=>'-rIa7GzcatiBXcl16HpkCw2cwIK3UBeiX-9_IJV2RjQ',
		'openId'=>$formIdInfo['openId'],
		'formId'=>$formIdInfo['formId'],
		'data'=>$data,
		'page'=>'pages/function/index',
		'emphasisKeyword'=>'keyword4.DATA',
		'ip'=>$ip
	);
	$req=json_decode(substr(curl('https://wx.itrclub.com/wisecity6/templateMessage.php?mod=send','post',$postData),3),true);
	if($req['code']==200) $success++;
	else array_push($error,$req);
}

if($success==count($formIdList)) returnAjaxData(200,'success',['total'=>$success]);
else returnAjaxData(500,'System error',['total'=>count($formIdList),'success'=>$success,'error'=>$error]);