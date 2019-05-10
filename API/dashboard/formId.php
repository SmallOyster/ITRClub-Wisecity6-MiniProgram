<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序后台-formID管理
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-04-20
 * @version 2019-05-10
 */
 
session_start();
require_once '../publicFunc.php';
if(!$_SESSION['isLoginWXMP'] || $_SESSION['isLoginWXMP']!=1) die(header('location:login.php'));

$page=isset($_GET['page'])?$_GET['page']:1;

$sql='FROM wxmp_form_id a,wxmp_open_id b WHERE a.open_id=b.open_id ';

if(isset($_GET['openId']) && $_GET['openId']) $sql.='AND a.open_id="'.$_GET['openId'].'" ';
if(isset($_GET['orgType']) && $_GET['orgType']) $sql.='AND b.org_type="'.$_GET['orgType'].'" ';
if(isset($_GET['orgId']) && $_GET['orgId']) $sql.='AND b.org_id="'.$_GET['orgId'].'" ';
$sql.='ORDER BY a.status,a.open_id,a.update_time DESC';

$sql1='SELECT a.id,a.open_id,a.form_id,a.status,a.create_time,a.update_time,IF(b.org_type=1,(SELECT name FROM team c WHERE c.id=b.org_id),IF(b.org_type=2,(SELECT name FROM `group` c WHERE c.id=b.org_id),"管理员")) AS orgName '.$sql.' LIMIT '.(($page-1)*50).',50';
$sql2='SELECT COUNT(a.id) '.$sql;

$query=PDOQuery($dbcon,$sql1);
$list=$query[0];
$query2=PDOQuery($dbcon,$sql2);
$totalPage=ceil($query2[0][0]['COUNT(a.id)']/50);
?>

<html>
<head>
	<title>FormID管理 / ITRClub-WiseCity6商赛系统小程序端后台</title>
	<meta charset="utf-8">
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
		<center>ITRClub-WiseCity6 商赛系统后台(小程序端)<br>表单FormID管理</center>
		<hr>
	</font>

	<font style="font-size:20px;">
		&nbsp;&nbsp; 页码：
		<?php
		if(isset($_GET['openId']) || isset($_GET['orgType']) || isset($_GET['orgId'])){
			$queryString=strpos($_SERVER["QUERY_STRING"],'&page')==false?$_SERVER["QUERY_STRING"]:substr($_SERVER["QUERY_STRING"],0,strpos($_SERVER["QUERY_STRING"],'&page'));
			for($i=1;$i<=$totalPage;$i++){echo '<a href="?'.$queryString.'&page='.$i.'">'.$i.'</a> | ';}
		}else{
			for($i=1;$i<=$totalPage;$i++){echo '<a href="?page='.$i.'">'.$i.'</a> | ';}
		}
		?>
	</font>

	<hr>
	
	<table class="table table-hover table-striped table-bordered">
		<tr>
			<th>序</th>
			<th>openId</th>
			<th>组织名</th>
			<th>是否已发</th>
			<th>创建时</th>
			<th>修改时</th>
			<th>操作</th>
		</tr>
		
		<?php foreach($list as $info){ ?>
		<tr>
			<td><?=$info['id'];?></td>
			<td><?=substr($info['open_id'],0,12).'...';?></td>
			<td><?=$info['orgName'];?></td>
			<td><?php if($info['status']==0){echo 'x';}else{echo '√';}?></td>
			<td><?=$info['create_time'];?></td>
			<td><?=$info['update_time'];?></td>
			<td><button class="btn btn-info" onclick="show()">详情</button><?php if($info['status']==0){ ?> <a href="sendTemplateMessage.php?openId=<?=$info['open_id'];?>&formId=<?=$info['form_id'];?>" class="btn btn-success">发送</a><?php } ?></td>
		</tr>
		<?php } ?>
	</table>
	
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
</body>
</html>