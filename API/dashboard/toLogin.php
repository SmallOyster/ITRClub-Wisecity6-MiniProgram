<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序后台-登录处理
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-05-10
 * @version 2019-05-10
 */

session_start();
require_once '../publicFunc.php';

if(isset($_POST['referByDC']) && $_POST['referByDC']=='itrwc6'){
	$_SESSION['isLoginWXMP']=1;
	die(header('location:home.php'));
}elseif(isset($_POST['APTUKey']) && $_POST['APTUKey']=='wxmp'){
	$_SESSION['isLoginWXMP']=1;
	returnAjaxData(200,'success',['url'=>'home.php']);
}else{
	returnAjaxData(403,'Failed to authority');
}

?>