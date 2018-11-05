<?php
/*
Array ( [item_id] => 11 [item_title] => Ingeniero Ambiental 
[item_author_username] => LuisSandoval 
[item_suggestion_comments] => Estimado LuisSandoval, Le recomendamos 
que agregue más detalles en los requisitos de su anuncio. Saludos cordiales, 
Equipo de Atención al Cliente ClasificadosAmbientales.com )

*/
require_once('../includes/config.php');
require_once('../includes/functions/func.admin.php');
require_once('../includes/functions/func.sqlquery.php');
require_once('../includes/functions/func.users.php');
require_once('../includes/lang/lang_'.$config['lang'].'.php');

$url = "https://clasificadosambientales.com/admin";
if($_POST !== null){
    $item_id = $_POST['item_id'];
    $item_title = $_POST['item_title'];
    $item_author_username = $_POST['item_author_username'];
    $item_suggestion_comments = 'Clasificado: "'.$item_title.'" \n'.$_POST['item_suggestion_comments'];
    $mysqli = db_connect($config);
    $query = "call Suggestion_SendPostComments(".$item_id.", '".$item_author_username."', '".$item_suggestion_comments."') ";
    $result = mysqli_query($mysqli, $query);
    $url = $url."/post_detail.php?id=".$item_id;
    if (mysqli_num_rows($result) > 0) {
        $info = mysqli_fetch_assoc($result);
        $ResponseCode = $info['ResponseCode'];
        $ResponseText = $info['ResponseText'];
        $url = $url.'&msg='.base64_encode($ResponseText);
    }
    else{
        $url = $url.'&msg='.base64_encode('error_to_insert');
    }
    
}
else{
    $url = $url."?msg=".base64_encode('error_in_post_detail_suggestion');
}

header("Location: ".$url);
