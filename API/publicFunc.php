<?php

require_once 'PDOConn.php';

define('APP_ID','wx1c90418a66c62a11');
define('APP_SECRET','');

function getIP()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif(!empty($_SERVER["REMOTE_ADDR"])){
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	else{
		$cip = "0.0.0.0";
	}
	return $cip;
}

/**
 * curl请求封装函数
 * @param  string  $url          请求URL
 * @param  string  $type         请求类型(get/post)
 * @param  array   $postData     需要POST的数据
 * @param  string  $postDataType POST数据类型(array/json)
 * @param  integer $timeout      超时秒数
 * @param  string  $userAgent    UserAgent
 * @return string                返回结果
 */
function curl($url,$type='get',$postData=array(),$postDataType='array',$timeout=5,$userAgent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36'){
	if($url=='' || $timeout <=0){
		return false;
	}

	$ch=curl_init((string)$url);
	curl_setopt($ch,CURLOPT_HEADER,false);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,(int)$timeout);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch,CURLOPT_USERAGENT,$userAgent);

	if($type=='post'){
		if($postData==array()){
			return false;
		}else if($postDataType=='json'){
			$postData=json_encode($postData);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));
		}

		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
	}

	$rtn=curl_exec($ch);
	if($rtn===false) $rtn=curl_errno($ch);
	curl_close($ch);

	return $rtn;
}


function inputGet($dataName="",$allowNull=0,$isAjax=0)
{
	if(isset($_GET[$dataName])){
		if($allowNull!=1 && $_GET[$dataName]==""){
			return $isAjax==1?returnAjaxData(0,'lack Parameter'):die();
		}else{
			return $_GET[$dataName];
		}
	}elseif($allowNull==1){
		return;
	}else{
		return $isAjax==1?returnAjaxData(0,'lack Parameter'):die();
	}
}


function inputPost($dataName="",$allowNull=0,$isAjax=0)
{
	if(isset($_POST[$dataName])){
		if($allowNull!=1 && $_POST[$dataName]==""){
			return $isAjax==1?returnAjaxData(0,'lack Parameter'):die();
		}else{
			return $_POST[$dataName];
		}
	}elseif($allowNull==1){
		return;
	}else{
		return $isAjax==1?returnAjaxData(0,'lack Parameter'):die();
	}
}


/**
 * 返回JSON数据
 * @param  string       状态码
 * @param  string       状态消息
 * @param  string/array 待返回的数据
 * @return JSON         已处理好的JSON数据
 */
function returnAjaxData($code,$msg,$data=""){
	$ret=array('code'=>$code,'message'=>$msg,'data'=>$data,'requestTime'=>time());
	die(json_encode($ret));
}