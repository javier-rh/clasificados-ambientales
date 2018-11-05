<?php
require_once('includes/config.php');
require_once('includes/classes/class.template_engine.php');
require_once('includes/classes/class.country.php');
require_once('includes/functions/func.global.php');
require_once('includes/functions/func.users.php');
require_once('includes/functions/func.sqlquery.php');
require_once('includes/lang/lang_'.$config['lang'].'.php');
require_once('includes/seo-url.php');

$mysqli = db_connect($config);
sec_session_start();

$query = "SELECT translation_of FROM `".$config['db']['pre']."pages` WHERE active = 1 AND slug='". validate_input($_GET['id']) . "' LIMIT 1";
$query_result = mysqli_query ($mysqli,$query);
$info = mysqli_fetch_array($query_result);

$query2 = "SELECT * FROM `".$config['db']['pre']."pages` WHERE translation_lang = '".$config['lang_code']."' AND translation_of='". validate_input($info['translation_of']) . "' AND active = 1 LIMIT 1";
$query_result2 = mysqli_query ($mysqli,$query2);
while ($info2 = mysqli_fetch_array($query_result2))
{
    $html = stripslashes($info2['content']);
    $name = stripslashes($info2['name']);
    $title = stripslashes($info2['title']);
    $type = $info2['type'];
}

if(!isset($title))
{
	message("Error",$lang['PAGENOTEXIST']);
}

if($type == 1)
{
	if(!isset($_SESSION['user']['id']))
	{
		message("Login to view",$lang['MUSTLOGINVIEWPAGE']);
	}
}

if(isset($_GET['basic']))
{
	$page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/html_content_no.tpl');
}
else
{
	$page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/html_content.tpl');
}
$page->SetParameter ('OVERALL_HEADER', create_header($name));
$page->SetParameter ('SITE_TITLE', $config['site_title']);
$page->SetParameter ('NAME', $name);
$page->SetParameter ('TITLE', $title);
$page->SetParameter ('HTML', $html);
$page->SetParameter ('OVERALL_FOOTER', create_footer());
$page->CreatePageEcho();
?>