<?php
/***************************************************************************
 *   copyright				: (C) 2008 - 2016 WeBid
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
$current_page = 'tools';
include '../common.php';
include INCLUDE_PATH . 'functions_admin.php';
include 'loggedin.inc.php';
include PACKAGE_PATH . 'ckeditor/ckeditor.php';

if (isset($_POST['action']) && $_POST['action'] == 'update')
{
	// Check if the specified user exists
	$superuser = $system->cleanvars($_POST['superuser']);
	$query = "SELECT id FROM " . $DBPrefix . "users WHERE nick = :nick";
	$params = array();
	$params[] = array(':nick', $superuser, 'str');
	$db->query($query, $params);
	if ($db->numrows() == 0 && $_POST['active'] == 1)
	{
		$template->assign_block_vars('alerts', array('TYPE' => 'error', 'MESSAGE' => $ERR_025));
	}
	else
	{
		// Update database
		$query = "UPDATE " . $DBPrefix . "maintainance SET
				superuser = :superuser,
				maintainancetext = :maintainancetext,
				active = :active";
		$params = array();
		$params[] = array(':superuser', $superuser, 'str');
		$params[] = array(':maintainancetext', $system->cleanvars($_POST['maintainancetext']), 'str');
		$params[] = array(':active', $_POST['active'], 'str');
		$db->query($query, $params);
		$template->assign_block_vars('alerts', array('TYPE' => 'success', 'MESSAGE' => $MSG['_0005']));
	}
	$system->SETTINGS['superuser'] = $_POST['superuser'];
	$system->SETTINGS['maintainancetext'] = $_POST['maintainancetext'];
	$system->SETTINGS['active'] = $_POST['active'];
}
else
{
	$query = "SELECT * FROM " . $DBPrefix . "maintainance LIMIT 1";
	$db->direct_query($query);
	$data = $db->result();
	$system->SETTINGS['superuser'] = $data['superuser'];
	$system->SETTINGS['maintainancetext'] = $data['maintainancetext'];
	$system->SETTINGS['active'] = $data['active'];
}

loadblock('', $MSG['_0002']);
loadblock($MSG['_0006'], '', 'bool', 'active', $system->SETTINGS['active'], array($MSG['030'], $MSG['029']));
loadblock($MSG['003'], '', 'text', 'superuser', $system->SETTINGS['superuser'], array($MSG['030'], $MSG['029']));

$CKEditor = new CKEditor();
$CKEditor->basePath = $system->SETTINGS['siteurl'] . '/js/ckeditor/';
$CKEditor->returnOutput = true;
$CKEditor->config['width'] = 550;
$CKEditor->config['height'] = 400;

loadblock($MSG['_0004'], '', $CKEditor->editor('maintainancetext', $system->SETTINGS['maintainancetext']));

$template->assign_vars(array(
		'SITEURL' => $system->SETTINGS['siteurl'],
		'TYPENAME' => $MSG['5436'],
		'PAGENAME' => $MSG['_0001']
		));

include 'header.php';
$template->set_filenames(array(
		'body' => 'adminpages.tpl'
		));
$template->display('body');
include 'footer.php';
?>
