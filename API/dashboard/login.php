<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序后台-登录
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-05-10
 * @version 2019-05-10
 */
?>

<!DOCTYPE html>
<html>

<head>
	<title>小程序管理后台 / ITRClub-WiseCity6.0商赛系统</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="author" content="ITRClub">
	<!--link rel="shortcut icon" href="favicon.ico"-->

	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<script src="https://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<style>
		.login-box{
			width: 350px;
			height: 150px;
			padding: 35px 20px;
			margin: auto;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #fefefe;
			box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.1);
			border-radius: 5px;
			border: 1px solid #c4c4c4;
		}
		.loadingwrap{
			position: fixed;
			bottom: 0;
			top: 0;
			width: 100%;
			background:rgba(0,0,0,.2);
			z-index: 10000;
		}
		.spinner {
			position: absolute;
			top: 50%;
			left: 50%;
			margin: -30px 0 0 -25px;
			height: 60px;
			text-align: center;
			font-size: 10px;
		}
		.spinner > div {
			background-color: #ffffff;
			height: 100%;
			width: 6px;
			display: inline-block;

			-webkit-animation: stretchdelay 1.2s infinite ease-in-out;
			animation: stretchdelay 1.2s infinite ease-in-out;
		}
		.spinner .rect2 {
			-webkit-animation-delay: -1.1s;
			animation-delay: -1.1s;
		}
		.spinner .rect3 {
			-webkit-animation-delay: -1.0s;
			animation-delay: -1.0s;
		}
		.spinner .rect4 {
			-webkit-animation-delay: -0.9s;
			animation-delay: -0.9s;
		}
		.spinner .rect5 {
			-webkit-animation-delay: -0.8s;
			animation-delay: -0.8s;
		}
		@-webkit-keyframes stretchdelay {
			0%, 40%, 100% { -webkit-transform: scaleY(0.4) } 
			20% { -webkit-transform: scaleY(1.0) }
		}
		@keyframes stretchdelay {
			0%, 40%, 100% {
				transform: scaleY(0.4);
				-webkit-transform: scaleY(0.4);
				}  20% {
					transform: scaleY(1.0);
					-webkit-transform: scaleY(1.0);
				}
			}
		}
	</style>
</head>

<body>
<div style="background: url('https://test.xshgzs.com/bg.jpg') center center / cover no-repeat; position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; z-index: -1;">
	<div style="width: 350px; position: absolute; bottom: 0px; right: 0px; top: 0px; left: 0px; margin: 23rem auto auto;">
		<div style="margin: 1rem;color: rgb(51, 122, 183); font-size: 21px;">
			<img src="https://www.itrclub.com/resource/index/img/logo.png" style="width: 40px; height: 40px;">&nbsp;&nbsp;&nbsp;&nbsp;ITRClub
			<p style="color: rgb(51, 122, 183); font-size: 21px; float: right; margin-top: 2px;">|&nbsp;&nbsp;Wisecity商赛系统</p>
		</div>
		<div class="login-box" style="background: rgb(0, 0, 0); opacity: 0.6;">
			<div class="form-group">
				<div class="input-group">
					<label class="input-group-addon"><i class="fa fa-fw fa-key"></i></label>
					<input type="password" class="form-control" id="APTUKey" placeholder="请输入 小程序运营者Key" onkeyup='if(event.keyCode==13)login();'>
				</div>
			</div>
			<div class="form-group">
				<button onclick="login()" class="btn btn-block" style="background-color:green;color:white;">登 录 [小程序管理后台] &gt;</button>
			</div>
		</div>
	</div>
</div>

<script>
function login(){
	if($("#APTUKey").val()==""){
		showModalTips('请输入小程序运营者Key！');
		return;
	}

	lockScreen();
	
		$.ajax({
		url:'toLogin.php',
		type:'post',
		dataType:'json',
		data:{'APTUKey':$("#APTUKey").val()},
		error:function(e){
			unlockScreen();
			showModalTips('服务器错误！<br>错误码：S'+e.status+'<hr>请于企业微信搜索“ITService”以联系技术支持！');
			console.log(e);
		},
		success:function(ret){
			unlockScreen();
			if(ret.code==200){
				window.location.href=ret.data['url'];
			}else if(ret.code==403){
				showModalTips('小程序运营者Key无效！<hr>若已遗失KEY<br>请于企业微信搜索 ITService<br>联系技术支持重置！');
			}else{
				showModalTips('系统错误！<br>错误码：A'+ret.code+'<hr>请于企业微信搜索“ITService”以联系技术支持！');
				console.log(ret);
			}
		}
	})
}


/**
* lockScreen 屏幕锁定，显示加载图标
**/
function lockScreen(content=""){
	$('body').append(
		'<div class="loadingwrap" id="loadingwrap"><div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div><br><font style="color:yellow;font-size:24px;font-weight:bold;">'+content+'</font></div></div>'
		);
}


/**
* unlockScreen 屏幕解锁
**/
function unlockScreen(){
	// 0.3s后再删除，防止闪现
	setTimeout(function(){
		$('#loadingwrap').remove();
	},500);
}


/**
* showModalTips 模态框显示提醒消息
* @param String 消息内容
* @param String 消息标题
**/
function showModalTips(msg,title='温馨提示'){
	$("#tips").html(msg);
	$("#tipsTitle").html(title);
	$("#tipsModal").modal("show");
}
</script>

<div class="modal fade" id="tipsModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="ModalTitle">温馨提示</h3>
			</div>
			<div class="modal-body">
				<font color="red" style="font-weight:bolder;font-size:24px;text-align:center;">
					<p id="tips"></p>
				</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning" onclick='isRequesting=0;$("#tipsModal").modal("hide");'>返回 &gt;</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</body>
</html>

