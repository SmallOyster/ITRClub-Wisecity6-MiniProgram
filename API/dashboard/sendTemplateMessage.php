<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序后台-人工发送消息
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-04-20
 * @version 2019-05-10
 */

session_start();
require_once '../publicFunc.php';
if(!$_SESSION['isLoginWXMP'] || $_SESSION['isLoginWXMP']!=1) die(header('location:login.php'));

if(isset($_GET['openId'],$_GET['formId']) && $_GET['openId'] && $_GET['formId']){
	$openId=$_GET['openId'];
	$formId=$_GET['formId'];
	$query=PDOQuery($dbcon,'SELECT IF(b.org_type=1,(SELECT name FROM team c WHERE c.id=b.org_id),IF(b.org_type=2,(SELECT name FROM `group` c WHERE c.id=b.org_id),"管理员")) AS orgName FROM wxmp_open_id b WHERE b.open_id=?',[$openId],[PDO::PARAM_STR]);
	$orgName=$query[0][0]['orgName'];
	$query=PDOQuery($dbcon,'SELECT COUNT(id) FROM wxmp_form_id WHERE form_id=? AND open_id=? AND status=0',[$formId,$openId],[PDO::PARAM_STR,PDO::PARAM_STR]);
	if($query[0][0]['COUNT(id)']!=1) die(header('location:home.php'));
}else{
	die(header('location:home.php'));
}

if(isset($_SESSION['accessToken']) && time()<=$_SESSION['expired']) $accessToken=$_SESSION['accessToken'];
else{
	$getAccessTokenUrl='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APP_ID.'&secret='.APP_SECRET;
	$getAccessToken=curl($getAccessTokenUrl);
	$accessToken=json_decode($getAccessToken,true);
	$accessToken=$accessToken['access_token'];
	
	$_SESSION['accessToken']=$accessToken;
	$_SESSION['expired']=time()+7200;
}
?>

<html>
<head>
	<title>人工发送消息 / ITRClub-WiseCity6商赛系统小程序端后台</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
	th,td{
		text-align: center;
	}
	</style>
</head>

<body style="padding-top: 13px;">
	<font style="font-size:38px;line-height: 82px;">
		<center>ITRClub-WiseCity6 商赛系统后台(小程序端)<br>人工发送服务通知</center>
		<hr>
	</font>

	<div style="font-size:25px;padding-left: 15px;">
		FormID：<?=$formId;?><br>
		OpenID：<?=$openId;?><br>
		队伍名：<?=$orgName;?>
	</div>

	<hr>
	
	<center>
		<button class="btn btn-info" onclick="show(1)" style="font-size:24px;width:32%">账户余额异动提醒</button>
		<button class="btn btn-success" onclick="show(2)" style="font-size:24px;width:32%">交易提醒</button>
		<button class="btn btn-danger" onclick="show(3)" style="font-size:24px;width:32%">财年结束提醒</button>
	</center>

	<hr>

	<table id="t1" class="table table-hover table-striped table-bordered" style="display: none;">
		<tr>
			<th>账户名称</th>
			<td><input id="input_1_1" class="form-control"></td>
		</tr>
		<tr>
			<th>变动类型</th>
			<td><input id="input_1_2" class="form-control"></td>
		</tr>
		<tr>
			<th>变动金额</th>
			<td><input id="input_1_3" class="form-control"></td>
		</tr>
		<tr>
			<th>当前余额</th>
			<td><input id="input_1_4" class="form-control"></td>
		</tr>
		<tr>
			<th>变动时间</th>
			<td><input id="input_1_5" class="form-control"></td>
		</tr>
		<tr>
			<th>温馨提示</th>
			<td><input id="input_1_6" class="form-control"></td>
		</tr>
	</table>

	<table id="t2" class="table table-hover table-striped table-bordered" style="display: none;">
		<tr>
			<th>订单编号</th>
			<td><input id="input_2_1" class="form-control"></td>
		</tr>
		<tr>
			<th>交易类型</th>
			<td><input id="input_2_2" class="form-control"></td>
		</tr>
		<tr>
			<th>买家姓名</th>
			<td><input id="input_2_3" class="form-control"></td>
		</tr>
		<tr>
			<th>商品卖家</th>
			<td><input id="input_2_4" class="form-control"></td>
		</tr>
		<tr>
			<th>商品数量</th>
			<td><input id="input_2_5" class="form-control"></td>
		</tr>
		<tr>
			<th>商品详情</th>
			<td><input id="input_2_6" class="form-control"></td>
		</tr>
		<tr>
			<th>交易金额</th>
			<td><input id="input_2_7" class="form-control"></td>
		</tr>
		<tr>
			<th>交易状态</th>
			<td><input id="input_2_8" class="form-control"></td>
		</tr>
		<tr>
			<th>交易时间</th>
			<td><input id="input_2_9" class="form-control"></td>
		</tr>
		<tr>
			<th>温馨提示</th>
			<td><input id="input_2_10" class="form-control"></td>
		</tr>
	</table>

	<table id="t3" class="table table-hover table-striped table-bordered" style="display: none;">
		<tr>
			<th>比赛场次</th>
			<td><input id="input_3_1" class="form-control"></td>
		</tr>
		<tr>
			<th>比赛时间</th>
			<td><input id="input_3_2" class="form-control"></td>
		</tr>
		<tr>
			<th>比赛说明</th>
			<td><input id="input_3_3" class="form-control"></td>
		</tr>
		<tr>
			<th>温馨提示</th>
			<td><input id="input_3_4" class="form-control"></td>
		</tr>
	</table>

	<center><button id="sendBtn" class="btn btn-success" onclick="send()" style="display:none">确 认 发 送 &gt;</button></center>
	
	<hr>

	<center>
		<a href='./home.php' style="font-weight:bold;font-size:37px;line-height:40px;">&lt; 返 回 小 程 序 后 台 首 页</a>
		<hr>
		<p style="font-weight:bold;font-size:37px;line-height:40px;">
			&copy; <a href="https://www.itrclub.com?from=wc" target="_blank" style="font-size:39px;">ITRClub</a> 2017-2019
			<br><br>
		</p>
	</center>
	<!-- ./页脚版权 -->

<script>
var id=0;
var total=0;
var templateId='';
var accessToken="<?=$accessToken;?>";
var formId=getURLParam('formId');
var openId=getURLParam('openId');

function show(num){
	$("#t1").attr('style','display:none');
	$("#t2").attr('style','display:none');
	$("#t3").attr('style','display:none');

	$("#t"+num).attr('style','');
	$("#sendBtn").attr('style','font-size:24px;width:98%');

	if(num==1){
		id=1;
		total=6;
		templateId='-rIa7GzcatiBXcl16HpkCw2cwIK3UBeiX-9_IJV2RjQ';
	}else if(num==2){
		id=2;
		total=10;
		templateId='ApXZ8Hbnnwl-nxARQQ63uTLHpcXs9irJBsgJKc-3kFY';
	}else if(num==3){
		id=3;
		total=4;
		templateId='sccRVYT6Bn-Xto4UAKxuRArfEM3b2MZKvAhYx41FFLo';
	}
}


function send(){
	msgData={};

	for(i=1;i<=total;i++){
		val=$("#input_"+id+"_"+i).val();
		
		if(val==""){
			alert("第 "+i+" 项内容缺失！");
			return false;
		}else{
			msgData['keyword'+i]={};
			msgData['keyword'+i].value=val;
		}
	}

	msgData=JSON.stringify(msgData);
	page=prompt("请输入点击卡片后跳转的页面路径（可空）");
	emphasisKeyword=prompt("请输入需放大的参数序号（仅填数字）（可空）");

	if(parseInt(emphasisKeyword)>=1 && parseInt(emphasisKeyword)<=10) emphasisKeyword="keyword"+emphasisKeyword+".DATA";
	else emphasisKeyword='';

	$.ajax({
		url:"../templateMessage.php?mod=send",
		type:'post',
		data:{remark:"TEST"+id,accessToken:accessToken,formId:formId,openId:openId,templateId:templateId,data:msgData,page:page,emphasisKeyword:emphasisKeyword},
		dataType:'JSON',
		success:function(ret){
			if(ret.code==200){
				alert("发送成功！");
				return;
			}else if(ret.code==500 && ret.data['wxServerError']['errcode']==41029){
				alert("此FormID已被使用！");
			}else{
				alert(ret.code+"系统错误！！！");
				console.log(ret);
			}
		}
	})
}


function getURLParam(name){
	var reg = new RegExp("(^|&)"+name+"=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if(r!=null){return decodeURI(r[2]);}
	else{return null;}
}
</script>

</body>
</html>