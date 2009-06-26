<?php
/***************************************************************************
 *   copyright				: (C) 2008 WeBid
 *   site					: http://www.webidsupport.com/
 ***************************************************************************/

/***************************************************************************
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version. Although none of the code may be
 *   sold. If you have been sold this script, get a refund.
 ***************************************************************************/

define('InAdmin', 1);
include '../includes/common.inc.php';
include $include_path . 'functions_admin.php';
include 'loggedin.inc.php';

unset($ERR);
$edit = false;

if (isset($_GET['action']) && !isset($_POST['action']))
{
	if ($_GET['action'] == 'edit' && isset($_GET['id']))
	{
		$query = "SELECT * FROM ". $DBPrefix . "groups WHERE id = " . intval($_GET['id']);
		$res = mysql_query($query);
		$system->check_mysql($res, $query, __LINE__, __FILE__);
		$group = mysql_fetch_assoc($res);
		$template->assign_vars(array(
				'GROUP_ID' => $group['id'],
				'EDIT_NAME' => $group['group_name'],
				'CAN_SELL_Y' => ($group['can_sell'] == 1) ? 'selected="true"' : '',
				'CAN_SELL_N' => ($group['can_sell'] == 0) ? 'selected="true"' : '',
				'CAN_BUY_Y' => ($group['can_buy'] == 1) ? 'selected="true"' : '',
				'CAN_BUY_N' => ($group['can_buy'] == 0) ? 'selected="true"' : '',
				'USER_COUNT' => $group['count']
				));
		$edit = true;
	}
	if ($_GET['action'] == 'new')
	{
		$template->assign_vars(array(
				'USER_COUNT' => 0
				));
		$edit = true;
	}
}

if (isset($_POST['action']))
{
	if ($_GET['action'] == 'edit' || is_numeric($_GET['id']))
	{
		$query = "UPDATE ". $DBPrefix . "groups SET
				group_name = '" . $system->cleanvars($_POST['group_name']) . "',
				count = " . intval($_POST['user_count']) . ",
				can_sell = " . intval($_POST['can_sell']) . ",
				can_buy = " . intval($_POST['can_buy']) . "
				WHERE id = " . intval($_POST['id']);
	}
	if ($_GET['action'] == 'new' || empty($_GET['id']))
	{
		$query = "INSERT INTO ". $DBPrefix . "groups (group_name, count, can_sell, can_buy) VALUES
				('" . $system->cleanvars($_POST['group_name']) . "', " . intval($_POST['user_count']) . ", " . intval($_POST['can_sell']) . ", " . intval($_POST['can_buy']) . ")";
	}
	$system->check_mysql(mysql_query($query), $query, __LINE__, __FILE__);
}

$query = "SELECT * FROM ". $DBPrefix . "groups";
$res = mysql_query($query);
$system->check_mysql($res, $query, __LINE__, __FILE__);

while ($row = mysql_fetch_assoc($res))
{
	$template->assign_block_vars('groups', array(
			'ID' => $row['id'],
			'NAME' => $row['group_name'],
			'CAN_SELL' => ($row['can_sell'] == 1) ? $MSG['030'] : $MSG['029'],
			'CAN_BUY' => ($row['can_buy'] == 1) ? $MSG['030'] : $MSG['029'],
			'USER_COUNT' => $row['count']
			));
}

$template->assign_vars(array(
		'B_EDIT' => $edit
		));

$template->set_filenames(array(
		'body' => 'usergroups.tpl'
		));
$template->display('body');
?>