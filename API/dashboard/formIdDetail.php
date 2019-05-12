<?php
/**
 * @name ITRClub-Wisecity6商赛系统-小程序后台-formID详情
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-05-12
 * @version 2019-05-12
 */
 
require_once '../publicFunc.php';

$formId=inputGet('formId',0,1);

$query=PDOQuery($dbcon,'SELECT a.*,IF(b.org_type=1,CONCAT("队伍-",(SELECT c.name FROM team c WHERE c.id=b.org_id)),IF(b.org_type=2,CONCAT("商帮-",(SELECT d.name FROM `group` d WHERE d.id=b.org_id)),"管理员")) AS org_name FROM wxmp_form_id a,wxmp_open_id b WHERE a.form_id=? AND a.open_id=b.open_id',[$formId],[PDO::PARAM_INT]);

if($query[1]!=1) returnAjaxData(404,'FormID not found');
else returnAjaxData(200,'success',$query[0][0]);