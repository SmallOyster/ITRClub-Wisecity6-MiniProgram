<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序-发送交易提醒
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-04-22
 * @version 2019-06-07
 */

session_start();
require_once 'publicFunc.php';

// sendTransactionTemplateMessage.php
// ApXZ8Hbnnwl-nxARQQ63uTLHpcXs9irJBsgJKc-3kFY

$teamId=inputPost('teamId',0,1);
$orderId=inputPost('orderId',0,1);
$type=inputPost('type',0,1);
$buyerName=inputPost('buyerName',0,1);
$sellerName=inputPost('sellerName',0,1);
$goodsName=inputPost('goodsName',0,1);
$num=inputPost('num',0,1);
$money=inputPost('money',0,1);
$status=inputPost('status',0,1);

if($type==0 && $buyerName!=-1) $cnType='卖出';
elseif($type==1 && $sellerName!=-1) $cnType='买入';
elseif($type==0 && $buyerName==-1) $cnType='出售给劳动者';
elseif($type==1 && $sellerName==-1) $cnType='向劳动者收购';

if($status==0) $cnStatus='待我方确认';
elseif($status==1) $cnStatus='交易完成';

$data=json_encode(['keyword1'=>['value'=>$orderId],'keyword2'=>['value'=>$cnType],'keyword3'=>['value'=>$buyerName],'keyword4'=>['value'=>$sellerName],'keyword5'=>['value'=>$goodsName],'keyword6'=>['value'=>$num],'keyword7'=>['value'=>$money],'keyword8'=>['value'=>$cnStatus],'keyword9'=>['value'=>date('Y-m-d H:i:s')],'keyword10'=>['value'=>'请立即登录网页版进行确认/退回操作，谢谢配合！']]);

// 获取队伍里每个人的一个formId
$teamQuery=PDOQuery($dbcon,'SELECT MAX(a.form_id) AS formId,a.open_id FROM wxmp_form_id a,wxmp_open_id b WHERE a.open_id=b.open_id AND b.org_type=1 AND b.org_id=? AND a.status=0 GROUP BY a.open_id',[$teamId],[PDO::PARAM_INT]);

if($teamQuery[1]>=1) $teamList=$teamQuery[0];
else returnAjaxData(1,'No People have available FormID');

if(isset($_SESSION['accessToken']) && time()<=$_SESSION['expired']) $accessToken=$_SESSION['accessToken'];
else{
	$getAccessTokenUrl='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APP_ID.'&secret='.APP_SECRET;
	$getAccessToken=curl($getAccessTokenUrl);
	$accessToken=json_decode($getAccessToken,true);
	$accessToken=$accessToken['access_token'];
	
	$_SESSION['accessToken']=$accessToken;
	$_SESSION['expired']=time()+7200;
}

$error=[];
foreach($teamList as $teamInfo){
	$req=json_decode(substr(curl('https://wx.itrclub.com/wisecity6/templateMessage.php?mod=send','post',['remark'=>'交易确认提醒-'.$orderId.'-'.$cnType,'accessToken'=>$accessToken,'templateId'=>'ApXZ8Hbnnwl-nxARQQ63uTLHpcXs9irJBsgJKc-3kFY','openId'=>$teamInfo['open_id'],'formId'=>$teamInfo['formId'],'data'=>$data,'page'=>'pages/function/index','emphasisKeyword'=>'keyword8.DATA','ip'=>getIP()]),3),true);
	array_push($error,$req);
}

echo json_encode($error);