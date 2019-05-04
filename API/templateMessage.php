<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序-发送模板消息
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-04-18
 * @version 2019-04-22
 */

require_once 'publicFunc.php';

if(isset($_GET['mod']) && $_GET['mod']){
	switch($_GET['mod']){
		case 'collectFormId':
			collectFormId($dbcon);
			break;
		case 'send':
			toSendTemplate($dbcon);
			break;
		default:
			returnAjaxData(500001,'invaild Method');
	}
}else{
	returnAjaxData(0,'Lack parameter');
}


function collectFormId($dbcon)
{
	$openId=inputGet('openId',0,1);
	$formId=inputGet('formId',0,1);

	$query=PDOQuery($dbcon,'INSERT INTO wxmp_form_id(open_id,form_id,update_ip) VALUES (?,?,?)',[$openId,$formId,getIP()],[PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_STR]);
	if($query[1]==1) returnAjaxData(200,'success',[$openId,$formId]);
	else returnAjaxData(500,'Database error',[$query]);
}


function toSendTemplate($dbcon)
{
	$remark=inputPost('remark',0,1);
	$accessToken=inputPost('accessToken',0,1);
	$templateId=inputPost('templateId',0,1);
	$openId=inputPost('openId',0,1);
	$formId=inputPost('formId',0,1);
	$data=inputPost('data',0,1);
	$page=inputPost('page',1,1);
	$emphasisKeyword=inputPost('emphasisKeyword',1,1);
	$ip=isset($_POST['ip'])?$_POST['ip']:getIP();

	$data=json_decode($data,TRUE);
	$postData=array('touser'=>$openId,'template_id'=>$templateId,'page'=>$page,'form_id'=>$formId,'data'=>$data,'emphasis_keyword'=>$emphasisKeyword);
	$postData=json_encode($postData,JSON_UNESCAPED_UNICODE);

	$url='https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$accessToken;
	$query=json_decode(curl($url,'post',$postData),true);
		
	if($query['errcode']==0){
		PDOQuery($dbcon,'UPDATE wxmp_form_id SET status=1,remark=?,update_ip=? WHERE open_id=? AND form_id=?',[$remark,$ip,$openId,$formId],[PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_STR]);
		returnAjaxData(200,'success');
	}
	else returnAjaxData(500,'system Error',['wxServerError'=>$query]);
}
