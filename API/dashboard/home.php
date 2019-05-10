<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序-后台首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-04-20
 * @version 2019-05-10
 */

session_start();
require_once '../publicFunc.php';
if(!$_SESSION['isLoginWXMP'] || $_SESSION['isLoginWXMP']!=1) die(header('location:login.php'));

$financeYear=json_decode(curl('https://wisecity.itrclub.com/api/financeYear/getNow'),true);
$financeYear=isset($financeYear['data']['list'])?$financeYear['data']['list']:'';

if($financeYear==''){
	$start=0;
}else{
	$start=1;
	$startDate=time();
	$endDate=$financeYear['end_time'];

	$hour=floor(($endDate-$startDate)%86400/3600);
	$minute=floor(($endDate-$startDate)%86400/60)-$hour*60;
	$second=floor(($endDate-$startDate)%86400%60);
}
?>

<html>
<head>
	<title>小程序后台 / ITRClub-WiseCity6商赛系统小程序端</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body style="padding-top: 13px;padding-left: 20px;">
	<font style="font-size:38px;line-height: 82px;">
		<center>ITRClub-WiseCity6 商赛系统后台(小程序端)<br>小 程 序 后 台 首 页</center>
		<hr>
		<?php if($start==1){ ?>
		当前财年：第 <?=$financeYear['num'];?> 财年 (结束时：<?=date('Y-m-d H:i:s',$endDate);?>)<br>
		剩余时间：<?=$hour;?> 时 <?=$minute;?> 分 <?=$second;?> 秒
		<?php if($hour<1 && $minute<=20){ ?>
		<br><button class="btn btn-primary" style="font-size:33px;" onclick="sendFinanceYearEndTips()">推送服务通知：财年结束提醒</button>
		<?php } ?>
		<?php }else{ ?>财 年 已 结 束 ！<?php } ?>
		<hr>
		▲ <a href='teamBind.php'>用户绑定管理</a><br>
		▲ <a href='formId.php'>FormID管理</a><br>
		<hr>
		▲ <a href='https://wisecity.itrclub.com/dc/dashborad' target="_blank">开 发 者 中 心 (Web端)</a><br>
	</font>

	<hr>

	<center>
		<p style="font-weight:bold;font-size:37px;line-height:40px;">
			&copy; <a href="https://www.itrclub.com?from=wc" target="_blank" style="font-size:39px;">ITRClub</a> 2017-2019
			<br><br>
		</p>
	</center>
	<!-- ./页脚版权 -->

<?php if($start==1){ ?>
<script>
function sendFinanceYearEndTips(){
	password=prompt("请输入 消息推送密钥KEY");
	
	if(password.length<6){
		alert("请正确输入 消息推送密钥KEY！");
		return;
	}
	
	$.ajax({
		url:"sendFinanceYearEndTips.php",
		data:{password:password,num:"<?=$financeYear['num'];?>",endDate:"<?=date('Y-m-d H:i:s',$endDate);?>",surplus:"<?=$minute;?>"},
		dataType:"json",
		error:function(e){
			console.log(e);
			alert("服务器错误！");
		},
		success:function(ret){
		console.log(JSON.stringify(ret));
			if(ret.code==200){
				msg="发送成功！\n\n";

				if(ret.data['noUserTeam']!=[]) msg+="未通知到的队伍："+ret.data['noUserTeam'].join('|')+"\n\n";
				if(ret.data['totalUser']!=ret.data['success']) msg+="共绑定人数："+ret.data['totalUser']+"\n已发成功数："+ret.data['success'];
				else msg+="共发数量："+ret.data['success'];

				alert(msg);
				console.log(ret);
				return;
			}else if(ret.code==403){
				alert("请正确输入 消息推送密钥KEY！");
			}else{
				console.log(ret);
				alert(ret.code+"系统错误！");
			}
		}
	})
}
</script>
<?php } ?>

</body>
</html>