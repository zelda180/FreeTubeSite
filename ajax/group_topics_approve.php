﻿<?php

require '../include/config.php';

$group_id = isset($_GET['group_id']) ? (int) $_GET['group_id'] : '';
$topic_id = isset($_GET['topic_id']) ? (int) $_GET['topic_id'] : '';
$ajax_debug = 0;

if (!isset($_SESSION['UID']))
{
	if ($ajax_debug) error_log("This topic cannot be approve some security reason\n",3, FREETUBESITE_DIR . '/ajax/log.txt');
	Ajax::returnJson('This topic cannot be approve some security reason','error');
	exit;
}

$sql = "SELECT * FROM
       `group_topics` AS gt,
       `groups` AS gr WHERE
        gt.group_topic_id=$topic_id AND
        gt.group_topic_group_id=$group_id AND
        gt.group_topic_group_id=gr.group_id AND
        gr.group_owner_id=$_SESSION[UID]";
$group_topic_owner = DB::fetch1($sql);

if($group_topic_owner)
{
	$sql = "UPDATE `group_topics` SET
           `group_topic_approved`='yes' WHERE
           `group_topic_id`=$topic_id AND
           `group_topic_group_id`=$group_id";
	 DB::query($sql);

	 if ($ajax_debug) error_log(" $topic_id topic approve\n",3, FREETUBESITE_DIR . '/ajax/log.txt');
	Ajax::returnJson('approve','success');

}
else
{
	if ($ajax_debug) error_log("This topic cannot be approve some security reason . $sql\n",3, FREETUBESITE_DIR . '/ajax/log.txt');
	Ajax::returnJson('This topic cannot be approve some security reason','error');
}