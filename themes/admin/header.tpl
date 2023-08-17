<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>FreeTubeSite Admin</title>
<link href="{$base_url}/css/bootstrap.min.css" rel="stylesheet">
<link href="{$base_url}/themes/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script src="{$base_url}/js/jquery-1.11.0.min.js"></script>
<script src="{$base_url}/js/jquery-3.4.1.min.js"></script>
<script src="{$base_url}/js/bootstrap.min.js"></script>
<style>
.glyphicon-refresh-animate {
    -animation: spin .7s infinite linear;
    -webkit-animation: spinWebkit .7s infinite linear;
    -moz-animation: spinMoz .7s infinite linear;
}

@-webkit-keyframes spinWebkit {
    from { -webkit-transform: rotate(0deg);}
    to { -webkit-transform: rotate(360deg);}
}
@keyframes spinMoz {
    from { transform: scale(1) rotate(0deg);}
    to { transform: scale(1) rotate(360deg);}
}
@keyframes spin {
    from { transform: scale(1) rotate(0deg);}
    to { transform: scale(1) rotate(360deg);}
}
</style>
{$html_head}
<base href="{$base_url}/admin/">
</head>
<body>
<div class="container">
{include file="admin/menu_main.tpl"}

{if $err ne ""}
    <div class="alert alert-danger">{$err}</div>
{/if}

{if $msg ne ""}
    <div class="alert alert-success">{$msg}</div>
{/if}
