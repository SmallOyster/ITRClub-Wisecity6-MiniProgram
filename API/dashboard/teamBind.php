<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序后台-队伍绑定管理
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-04-20
 * @version 2019-04-21
 */
 
require_once '../publicFunc.php';

$query=PDOQuery($dbcon,'SELECT DISTINCT(b.name),b.id FROM wxmp_open_id a,team b WHERE a.team_id=b.id');
$list=$query[0];
?>

<html>
<head>
	<title>队伍绑定管理 / ITRClub-WiseCity6商赛系统小程序端后台</title>
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
		<center>ITRClub-WiseCity6 商赛系统后台(小程序端)<br>队伍绑定管理</center>
		<hr>
	</font>
	
	<table class="table table-hover table-striped table-bordered">
		<tr>
			<th>队名</th>
			<th>剩余次数</th>
			<th>操作</th>
		</tr>
		
		<?php
		foreach($list as $info){
			$countQuery=PDOQuery($dbcon,'SELECT COUNT(a.id) FROM wxmp_form_id a,wxmp_open_id b WHERE a.open_id=b.open_id AND a.status=0 AND b.team_id=?',[$info['id']],[PDO::PARAM_INT]);
			$count=$countQuery[0][0]['COUNT(a.id)'];
		?>
		<tr style="<?php if($count<=2&&$count>0)echo 'background-color:#57FAFC';elseif($count==0)echo 'background-color:#FC5AB2'; ?>">
			<td><?=$info['name'];?></td>
			<td><?=$count;?></td>
			<td><?php if($count>0){ ?><a href="formId.php?teamId=<?=$info['id'];?>" class="btn btn-success">发送消息</a><?php } ?></td>
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