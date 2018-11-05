<?php
/*
Copyright (c) 2015-2018 Devendra Katariya (bylancer.com)
Version 7.3
Date 23/Aug/2018
*/
require_once('../includes/config.php');
require_once('../includes/classes/class.template_engine.php');
require_once('../includes/functions/func.admin.php');
require_once('../includes/functions/func.sqlquery.php');
require_once('../includes/functions/func.users.php');
require_once('../includes/classes/GoogleTranslate.php');
require_once('../includes/lang/lang_'.$config['lang'].'.php');

$con = db_connect($config);
admin_session_start();
if (!isset($_SESSION['admin']['id'])) {
    exit('Access Denied.');
}

// Check if SSL enabled
$ssl = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off"
    ? true
    : false;
define("SSL_ENABLED", $ssl);

// Define SITEURL
$site_url = (SSL_ENABLED ? "https" : "http")
    . "://"
    . $_SERVER["SERVER_NAME"]
    . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
    . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

define("SITEURL", $site_url);
$config['site_url'] = dirname($site_url)."/";

require_once('../includes/seo-url.php');

//Admin Ajax Function
if(isset($_GET['action'])){

    if ($_GET['action'] == "installPayment") { installPayment($con,$config); }
    if ($_GET['action'] == "uninstallPayment") { uninstallPayment($con,$config); }
    if ($_GET['action'] == "installCountry") { installCountry($con,$config); }
    if ($_GET['action'] == "uninstallCountry") { uninstallCountry($con,$config); }

    if ($_GET['action'] == "deleteCountry") { deleteCountry(); }
    if ($_GET['action'] == "deleteState") { deleteState(); }
    if ($_GET['action'] == "deleteDistrict") { deleteDistrict(); }
    if ($_GET['action'] == "deleteCity") { deleteCity(); }

    if ($_GET['action'] == "deleteStaticPage") { deleteStaticPage($con,$config); }
    if ($_GET['action'] == "deletefaq") { deletefaq($con,$config); }
    if ($_GET['action'] == "delcoustomfield") { delcoustomfield($con,$config); }
    if ($_GET['action'] == "approveitem") { approveitem($con,$config,$lang,$link); }
    if ($_GET['action'] == "approveResubmitItem") { approveResubmitItem($con,$config,$lang,$link); }
    if ($_GET['action'] == "activeuser") { activeuser($con,$config); }
    if ($_GET['action'] == "banuser") { banuser($con,$config); }

    if ($_GET['action'] == "deleteCurrency") { deleteCurrency($con,$config); }
    if ($_GET['action'] == "deleteTimezone") { deleteTimezone($con,$config); }
    if ($_GET['action'] == "deleteMembershipPlan") { deleteMembershipPlan($con,$config); }
    if ($_GET['action'] == "deletePackage") { deletePackage($con,$config); }
    if ($_GET['action'] == "deleteLanguage") { deleteLanguage($con,$config); }
    if ($_GET['action'] == "deleteadmin") { deleteadmin($con,$config); }
    if ($_GET['action'] == "deleteMessage") { deleteMessage($con,$config); }
    if ($_GET['action'] == "deleteads") { deleteads($con,$config); }
    if ($_GET['action'] == "deleteResubmitItem") { deleteResubmitItem($con,$config); }
    if ($_GET['action'] == "deleteTransaction") { deleteTransaction($con,$config); }

    if ($_GET['action'] == "edit_langTranslation") { edit_langTranslation($con,$config); }
    if ($_GET['action'] == "langTranslation_FormFields") { langTranslation_FormFields($con,$config); }
    if ($_GET['action'] == "edit_langTranslation_custom_fields") { edit_langTranslation_custom_fields($con,$config); }
    if ($_GET['action'] == "CustomField_langTranslation_FormFields") { CustomField_langTranslation_FormFields($con,$config); }

    if ($_GET['action'] == "addNewCat") { addNewCat($con,$config); }
    if ($_GET['action'] == "editCat") { editCat($con,$config); }
    if ($_GET['action'] == "deleteCat") { deleteCat($con,$config); }

    if ($_GET['action'] == "addSubCat") { addSubCat($con,$config); }
    if ($_GET['action'] == "editSubCat") { editSubCat($con,$config); }
    if ($_GET['action'] == "delSubCat") { delSubCat($con,$config); }
    if ($_GET['action'] == "getSubCat") { getSubCat($con,$config); }

    if ($_GET['action'] == "openlocatoionPopup") { openlocatoionPopup($con,$config); }
    if ($_GET['action'] == "getlocHomemap") { getlocHomemap($con,$config); }

    if ($_GET['action'] == "editLanguageFile") { editLanguageFile(); }

}

if(isset($_POST['action'])){
    if ($_POST['action'] == "quickad_update_maincat_position") { quickad_update_maincat_position($con,$config); }
    if ($_POST['action'] == "quickad_update_subcat_position") { quickad_update_subcat_position($con,$config); }
    if ($_POST['action'] == "quickad_update_custom_field_position") { quickad_update_custom_field_position(); }
    if ($_POST['action'] == "quickad_update_custom_option_position") { quickad_update_custom_option_position(); }
    if ($_POST['action'] == "deleteusers") { deleteusers($con,$config); }
    if ($_POST['action'] == "getsubcatbyid") {getsubcatbyid($con,$config);}
    if ($_POST['action'] == "delete_custom_fields") { delete_custom_fields($con,$config); }
    if ($_POST['action'] == "delete_custom_option") { delete_custom_option($option_id); }
    if ($_POST['action'] == "save_custom_fields") { save_custom_fields($con,$config); }
    if ($_POST['action'] == "getStateByCountryID") {getStateByCountryID($con,$config);}
    if ($_POST['action'] == "getCityByStateID") {getCityByStateID($con,$config);}
    if ($_POST['action'] == "getStateByCountryIDforCityAdd") {getStateByCountryIDforCityAdd($con,$config);}
    if ($_POST['action'] == "getDistrictSelectedforCityAdd") {getDistrictSelectedforCityAdd($con,$config);}
    if ($_POST['action'] == "searchCityStateCountry") {searchCityStateCountry($con,$config);}
}

function change_language_file_settings($filePath, $newArray)
{
    $lang = array();
    // Get a list of the variables in the scope before including the file
    $new = get_defined_vars();
    // Include the config file and get it's values
    include($filePath);

    // Get a list of the variables in the scope after including the file
    $old = get_defined_vars();

    // Find the difference - after this, $fileSettings contains only the variables
    // declared in the file
    $fileSettings = array_diff($lang, $new);

    // Update $fileSettings with any new values
    $fileSettings = array_merge($fileSettings, $newArray);
    // Build the new file as a string
    $newFileStr = "<?php\n";
    foreach ($fileSettings as $name => $val) {
        // Using var_export() allows you to set complex values such as arrays and also
        // ensures types will be correct
        $newFileStr .= "\$lang['$name'] = " . var_export($val, true) . ";\n";
    }
    // Closing tag intentionally omitted, you can add one if you want

    // Write it back to the file
    file_put_contents($filePath, $newFileStr);

}

function editLanguageFile()
{
    $file_name = $_POST['file_name'];
    $filePath = '../includes/lang/lang_'.$file_name.'.php';

    if(isset($_POST['key'])){
        if(check_allow()){
            $value = htmlentities($_POST['value']);
            $newLangArray = array(
                $_POST['key'] => $value
            );
            if(file_exists($filePath)){
                change_language_file_settings($filePath, $newLangArray);
                echo 1;
                die();
            }
        }
    }
    echo 0;
    die();
}


/**
 * @param $filename
 * @return string
 */
function getFile($filename)
{
    $file = fopen($filename, 'r') or die('Unable to open file getFile!');
    $buffer = fread($file, filesize($filename));
    fclose($file);

    return $buffer;
}

/**
 * @param $filename
 * @param $buffer
 */
function writeFile($filename, $buffer)
{
    // Delete the file before writing
    if (file_exists($filename)) {
        unlink($filename);
    }
    // Write the new file
    $file = fopen($filename, 'w') or die('Unable to open file writeFile!');
    fwrite($file, $buffer);
    fclose($file);
}
/**
 * @param $rawFilePath
 * @param $filePath
 * @param $con
 * @return mixed|string
 */
function setSqlWithDbPrefix($rawFilePath, $filePath, $prefix)
{
    if (!file_exists($rawFilePath)) {
        return '';
    }

    // Read and replace prefix
    $sql = getFile($rawFilePath);
    $sql = str_replace('<<prefix>>', $prefix, $sql);

    // Write file
    writeFile($filePath, $sql);

    return $sql;
}

/**
 * @param $con
 * @param $filePath
 * @return bool
 */

function importSql($con, $filePath)
{

    try {
        $errorDetect = false;

        // Temporary variable, used to store current query
        $tmpline = '';
        // Read in entire file
        $lines = file($filePath);
        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || trim($line) == '') {
                continue;
            }
            if (substr($line, 0, 2) == '/*') {
                continue;
            }

            // Add this line to the current segment
            $tmpline .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                if (!$con->query($tmpline)) {
                    echo "<pre>Error performing query '<strong>" . $tmpline . "</strong>' : " . $con->error . " - Code: " . $con->errno . "</pre><br />";
                    $errorDetect = true;
                }
                // Reset temp variable to empty
                $tmpline = '';
            }
        }
        // Check if error is detected
        if ($errorDetect) {
            //dd('ERROR');
        }
    } catch (\Exception $e) {
        $msg = 'Error when importing required data : ' . $e->getMessage();
        echo '<pre>';
        print_r($msg);
        echo '</pre>';
        exit();
    }


    // Delete the SQL file
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    return true;
}

/**
 * Import Geonames Default country database
 * @param $con
 * @param $site_info
 * @return bool
 */
function importGeonamesSql($con,$config,$default_country)
{
    if (!isset($default_country)) return false;

    // Default country SQL file
    $filename = 'database/countries/' . strtolower($default_country) . '.sql';
    $rawFilePath = '../storage/'.$filename;
    $filePath = '../storage/installed-db/' . $filename;

    setSqlWithDbPrefix($rawFilePath, $filePath, $config['db']['pre']);

    return importSql($con, $filePath);
}

function installCountry($con,$config)
{
    $code = $_POST['id'];
    if (trim($code) != '') {
        if(check_allow()){
            if(importGeonamesSql($con,$config,$code)){
                $con->query("UPDATE `".$config['db']['pre']."countries` set active='1' WHERE `code` = '" . $code . "'");
                echo 1;
            }
            else {
                echo 0;
                die();
            }
        }else{
            echo 1;
            die();
        }
    } else {
        echo 0;
        die();
    }

}

function uninstallCountry($con,$config)
{
    $code = $_POST['id'];
    if (trim($code) != '') {
        if(check_allow()){
            if(importGeonamesSql($con,$config,$code)){
                $con->query("UPDATE `".$config['db']['pre']."countries` set active='0' WHERE `code` = '" . $code . "'");
                $con->query("DELETE FROM `".$config['db']['pre']."cities` WHERE `country_code` = '" . $code . "'");
                $con->query("DELETE FROM `".$config['db']['pre']."subadmin1` where code like '%".$code."%'");
                $con->query("DELETE FROM `".$config['db']['pre']."subadmin2` where code like '%".$code."%'");
                echo 1;
            }
            else {
                echo 0;
                die();
            }
        }else{
            echo 1;
            die();
        }
    } else {
        echo 0;
        die();
    }

}

function deleteCity()
{
    global $config;
    $con = db_connect($config);
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."cities` ";
        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow()){
            mysqli_query($con,$sql);
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteState()
{
    global $config;
    $con = db_connect($config);
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql1 = "DELETE FROM `".$config['db']['pre']."subadmin1` ";
        $sql2 = "DELETE FROM `".$config['db']['pre']."subadmin2` ";
        $sql3 = "DELETE FROM `".$config['db']['pre']."cities` ";
        foreach ($_POST['list'] as $value)
        {
            $pieces = explode(".", $value);
            $country = $pieces[0];
            $subadmin1 = $pieces[1];
            if($count == 0)
            {
                $sql1.= "WHERE `code` = '" . $value . "'";
                $sql2.= "WHERE code LIKE '" . $value . "%'" ;
                $sql3.= "WHERE country_code = '".$country."' and subadmin1_code = '".$subadmin1."'";
            }
            else
            {
                $sql1.= " OR `code` = '" . $value . "'";
                $sql2.= " OR `code` LIKE '" . $value . "%'";
                $sql3.= " OR country_code = '".$country."' and subadmin1_code = '".$subadmin1."'";
            }

            $count++;
        }
        $sql1.= " LIMIT " . count($_POST['list']);

        if(check_allow()){
            mysqli_query($con,$sql1);
            mysqli_query($con,$sql2);
            mysqli_query($con,$sql3);
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteDistrict()
{
    global $config;
    $con = db_connect($config);
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql1 = "DELETE FROM `".$config['db']['pre']."subadmin2` ";
        $sql2 = "DELETE FROM `".$config['db']['pre']."cities` ";
        foreach ($_POST['list'] as $value)
        {
            $pieces = explode(".", $value);
            $country = $pieces[0];
            $subadmin1 = $pieces[1];
            $subadmin2 = $pieces[2];
            if($count == 0)
            {
                $sql1.= "WHERE `code` = '" . $value . "'";
                $sql2.= "WHERE country_code = '".$country."' and subadmin1_code = '".$subadmin1."' and subadmin2_code = '".$subadmin2."'";
            }
            else
            {
                $sql1.= " OR `code` = '" . $value . "'";
                $sql2.= " OR country_code = '".$country."' and subadmin1_code = '".$subadmin1."' and subadmin2_code = '".$subadmin2."'";
            }

            $count++;
        }
        $sql1.= " LIMIT " . count($_POST['list']);

        if(check_allow()){
            mysqli_query($con,$sql1);
            mysqli_query($con,$sql2);
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteCountry()
{
    global $config;
    $con = db_connect($config);
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."countries` ";
        $sql2 = "DELETE FROM `".$config['db']['pre']."cities` ";
        $sql3 = "DELETE FROM `".$config['db']['pre']."subadmin1` ";
        $sql4 = "DELETE FROM `".$config['db']['pre']."subadmin2` ";
        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `code` = '" . $value . "'";
                $sql2.= "WHERE `country_code` = '" . $value . "'";
                $sql3.= "WHERE code like '%".$value."%'";
                $sql4.= "WHERE code like '%".$value."%'";
            }
            else
            {
                $sql.= " OR `code` = '" . $value . "'";
                $sql2.= " OR `country_code` = '" . $value . "'";
                $sql3.= " OR code like '%".$value."%'";
                $sql4.= " OR code like '%".$value."%'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow()){
            mysqli_query($con,$sql);
            mysqli_query($con,$sql2);
            mysqli_query($con,$sql3);
            mysqli_query($con,$sql4);
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function installPayment($con,$config)
{
    $id = $_POST['id'];
    if (trim($id) != '') {
        if(check_allow())
            $con->query("UPDATE `".$config['db']['pre']."payments` set payment_install='1' WHERE `payment_id` = '" . $id . "'");
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function uninstallPayment($con,$config)
{
    $id = $_POST['id'];
    if (trim($id) != '') {
        if(check_allow())
            $con->query("UPDATE `".$config['db']['pre']."payments` set payment_install='0' WHERE `payment_id` = '" . $id . "'");
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function delete_ad_by_id($con,$config,$product_id){
    if(check_allow()){
        $qry1 = "DELETE FROM `".$config['db']['pre']."product` WHERE id = '$product_id' LIMIT 1";
        $qry2 = "SELECT screen_shot FROM `".$config['db']['pre']."product` WHERE id = '$product_id' LIMIT 1";

        if ($res = $con->query($qry2)) {
            while ($fetch = mysqli_fetch_assoc($res)) {

                $uploaddir =  "../storage/products/";
                $screen_sm = explode(',',$fetch['screen_shot']);
                foreach ($screen_sm as $value)
                {
                    $value = trim($value);
                    //Delete Image From ../storage ----
                    $filename1 = $uploaddir.$value;
                    if(file_exists($filename1)){
                        $filename1 = $uploaddir.$value;
                        $filename2 = $uploaddir."small_".$value;
                        unlink($filename1);
                        unlink($filename2);
                    }
                }
            }
        }
        mysqli_query($con,$qry1);
        return true;
    }
    else{
        return false;
    }
}

function delete_resubmitad_by_id($con,$config,$product_id){
    if(check_allow()){
        $reqry1 = "DELETE FROM `".$config['db']['pre']."product_resubmit` WHERE product_id = '$product_id' LIMIT 1";
        $reqry2 = "SELECT screen_shot FROM `".$config['db']['pre']."product_resubmit` WHERE product_id = '$product_id' LIMIT 1";

        if ($res = $con->query($reqry2)) {
            while ($fetch = mysqli_fetch_assoc($res)) {

                $uploaddir =  "../storage/products/";
                $screen_sm = explode(',',$fetch['screen_shot']);
                foreach ($screen_sm as $value)
                {
                    $value = trim($value);
                    //Delete Image From ../storage ----
                    $filename1 = $uploaddir.$value;
                    if(file_exists($filename1)){
                        $filename1 = $uploaddir.$value;
                        $filename2 = $uploaddir."small_".$value;
                        unlink($filename1);
                        unlink($filename2);
                    }
                }
            }
        }

        mysqli_query($con,$reqry1);
        return true;
    }
    else{
        return false;
    }
}

function deleteStaticPage($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."pages` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deletefaq($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."faq_entries` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `faq_id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `faq_id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function delcoustomfield($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."custom_fields` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `custom_id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `custom_id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);
        if(check_allow()) {
            mysqli_query($con, $sql);
        }

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function approveResubmitItem($con,$config,$lang,$link)
{
    $id = $_POST['id'];
    if (trim($id) != '') {
        if(check_allow()) {
            $sql = "SELECT * FROM `" . $config['db']['pre'] . "product_resubmit` WHERE `product_id` = '" . $_POST['id'] . "' LIMIT 1";
            $result = $con->query($sql);
            $info = mysqli_fetch_assoc($result);
            $sql2 = "SELECT screen_shot FROM `" . $config['db']['pre'] . "product` WHERE `id` = '" . $_POST['id'] . "' LIMIT 1";
            $result2 = $con->query($sql2);
            $row = mysqli_num_rows($result2);
            if($row > 0){
                $info2 = mysqli_fetch_assoc($result2);

                $a1 = explode(',', $info2['screen_shot']);
                $a2 = explode(',', $info['screen_shot']);
                $arr = array_diff($a1, $a2);
                $uploaddir =  "../storage/products/";
                foreach ($arr as $value)
                {
                    $value = trim($value);
                    //Delete Image From ../storage ----
                    $filename1 = $uploaddir.$value;
                    if(file_exists($filename1)){
                        $filename1 = $uploaddir.$value;
                        $filename2 = $uploaddir."small_".$value;
                        unlink($filename1);
                        unlink($filename2);
                    }
                }

                $sql3 = "UPDATE " . $config['db']['pre'] . "product set
                    user_id         = '" . $info['user_id'] . "',
                    product_name    = '" . $info['product_name'] . "',
                    category        = '" . $info['category'] . "',
                    sub_category    = '" . $info['sub_category'] . "',
                    description     = '" . $info['description'] . "',
                    price           = '" . $info['price'] . "',
                    negotiable      = '" . $info['negotiable'] . "',
                    phone           = '" . $info['phone'] . "',
                    hide_phone      = '" . $info['hide_phone'] . "',
                    location        = '" . $info['location'] . "',
                    city            = '" . $info['city'] . "',
                    state           = '" . $info['state'] . "',
                    country         = '" . $info['country'] . "',
                    latlong         = '" . $info['latlong'] . "',
                    screen_shot     = '" . $info['screen_shot'] . "',
                    tag             = '" . $info['tag'] . "',
                    custom_fields   = '" . $info['custom_fields'] . "',
                    custom_types    = '" . $info['custom_types'] . "',
                    custom_values   = '" . $info['custom_values'] . "',
                    created_at      = '" . $info['created_at'] . "',
                    contact_phone = '" . $info['contact_phone'] . "',
                    contact_email = '" . $info['contact_email'] . "',
                    contact_chat = '" . $info['contact_chat'] . "'
                    WHERE id = '" . $info['product_id'] . "'
                    ";

                $con->query($sql3);

                $con->query("DELETE FROM `" . $config['db']['pre'] . "product_resubmit` WHERE `product_id` = '" . $_POST['id'] . "' LIMIT 1");

                //Resubmission approve Email to seller
                $product_id = $_POST['id'];
                $item_title = $info['product_name'];
                $item_author_id = $info['user_id'];

                /*SEND RESUBMISSION AD APPROVE EMAIL*/
                email_template("re_ad_approve",$item_author_id,null,$product_id,$item_title);

            }else{
                echo 0;
                die();
            }
        }
        echo 1;
        die();

    }
    else {
        echo 0;
        die();
    }

}

function approveitem($con,$config,$lang,$link)
{
    $id = $_POST['id'];
    if (trim($id) != '') {
        if(check_allow()){
            $con->query("UPDATE `".$config['db']['pre']."product` set status='active' WHERE `id` = '".$id."'");

            $query = "SELECT product_name,user_id from `".$config['db']['pre']."product` WHERE `id` = '".$id."' LIMIT 1";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $info = mysqli_fetch_assoc($result);

                //Ad approve Email to seller
                $product_id = $_POST['id'];
                $item_title = $info['product_name'];
                $item_author_id = $info['user_id'];

                /*SEND RESUBMISSION AD APPROVE EMAIL*/
                email_template("ad_approve",$item_author_id,null,$product_id,$item_title);
            }
        }

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }



}

function activeuser($con,$config)
{
    $id = $_POST['id'];
    if (trim($id) != '') {
        if(check_allow())
            $con->query("UPDATE `".$config['db']['pre']."user` set status='0' WHERE `id` = '" . $id . "'");
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function banuser($con,$config)
{
    $id = $_POST['id'];
    if (trim($id) != '') {
        if(check_allow())
            $con->query("UPDATE `".$config['db']['pre']."user` set status='2' WHERE `id` = '" . $id . "'");
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteusers($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."user` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteCurrency($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."currencies` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteTimezone($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."time_zones` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteMembershipPlan($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."subscriptions` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `sub_id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `sub_id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deletePackage($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."usergroups` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `group_id` = '" . $value . "' and group_removable = '1' ";
            }
            else
            {
                $sql.= " OR `group_id` = '" . $value . "'  and group_removable = '1' ";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteLanguage($con,$config)
{
    if(isset($_POST['id']))
    {
        $id = $_POST['id'];

        $sql = "DELETE FROM `".$config['db']['pre']."languages` WHERE `id` = '" . $id . "' LIMIT 1";

        if(check_allow()){
            $query = mysqli_query($con,"Select file_name from `".$config['db']['pre']."languages` where id = '" . $id . "'");
            $fetch = mysqli_fetch_assoc($query);
            $file_name = $fetch['file_name'];
            $file = '../includes/lang/lang_'.$file_name.'.php';
            if(file_exists($file))
                unlink($file);
            mysqli_query($con,$sql);

            echo 1;
            die();
        }
    } else {
        echo 0;
        die();
    }

}

function deleteadmin($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."admins` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteMessage($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."messages` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `message_id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `message_id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteads($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."product` ";
        $sql2 = "SELECT screen_shot FROM `".$config['db']['pre']."product` ";
        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
                $sql2.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
                $sql2.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);
        $sql2.= " LIMIT " . count($_POST['list']);

        if(check_allow()){
            if ($result = $con->query($sql2)) {
                while ($row = mysqli_fetch_assoc($result)) {

                    $uploaddir =  "../storage/products/";
                    $screen_sm = explode(',',$row['screen_shot']);
                    foreach ($screen_sm as $value)
                    {
                        $value = trim($value);
                        //Delete Image From ../storage ----
                        $filename1 = $uploaddir.$value;
                        if(file_exists($filename1)){
                            $filename1 = $uploaddir.$value;
                            $filename2 = $uploaddir."small_".$value;
                            unlink($filename1);
                            unlink($filename2);
                        }
                    }
                }
            }

            mysqli_query($con,$sql);
        }

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteResubmitItem($con,$config)
{
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."product_resubmit` ";
        $sql2 = "SELECT screen_shot FROM `".$config['db']['pre']."product_resubmit` ";
        $sql3 = "SELECT screen_shot FROM `".$config['db']['pre']."product` ";
        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `product_id` = '" . $value . "'";
                $sql2.= "WHERE `product_id` = '" . $value . "'";
                $sql3.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `product_id` = '" . $value . "'";
                $sql2.= " OR `product_id` = '" . $value . "'";
                $sql3.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);
        $sql2.= " LIMIT " . count($_POST['list']);
        $sql3.= " LIMIT " . count($_POST['list']);

        if(check_allow()){
            if ($result = $con->query($sql2)) {
                while ($row = mysqli_fetch_assoc($result)) {


                    $result3 = $con->query($sql3);
                    $row3 = mysqli_fetch_assoc($result3);

                    $uploaddir =  "../storage/products/";
                    $screen_sm = explode(',',$row['screen_shot']);
                    $re_screen = explode(',',$row3['screen_shot']);
                    $arr = array_diff($screen_sm,$re_screen);

                    foreach ($arr as $value)
                    {
                        $value = trim($value);
                        //Delete Image From Storage ----
                        $filename1 = $uploaddir.$value;
                        if(file_exists($filename1)){
                            $filename1 = $uploaddir.$value;
                            $filename2 = $uploaddir."small_".$value;
                            unlink($filename1);
                            unlink($filename2);
                        }
                    }
                }
            }

            mysqli_query($con,$sql);
        }

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}

function deleteTransaction($con,$config)
{
    echo $_POST['id'];
    if(isset($_POST['id']))
    {
        $_POST['list'][] = $_POST['id'];
    }

    if (is_array($_POST['list'])) {

        $count = 0;
        $sql = "DELETE FROM `".$config['db']['pre']."transaction` ";

        foreach ($_POST['list'] as $value)
        {
            if($count == 0)
            {
                $sql.= "WHERE `id` = '" . $value . "'";
            }
            else
            {
                $sql.= " OR `id` = '" . $value . "'";
            }

            $count++;
        }
        $sql.= " LIMIT " . count($_POST['list']);

        if(check_allow())
            mysqli_query($con,$sql);

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }

}
/**********************
 * @param $con
 * @param $config
 * Manage Categories  add/edit//delete function
 */



function edit_langTranslation($con,$config)
{
    $id = $_POST['id'];
    $cattype = $_POST['cat_type'];
    if(check_allow()){
        foreach ($_POST['value'] as $items) {

            $code = $items['code'];
            $title = $items['title'];
            $slug = $items['slug'];

            if($slug == "")
                $slug = create_category_slug($config,$title);
            else
                $slug = create_category_slug($config,$slug);

            $sql = "SELECT id FROM `".$config['db']['pre']."category_translation` where translation_id = '$id' AND lang_code = '$code'  AND category_type = '$cattype' LIMIT 1";
            $query = mysqli_query($con,$sql);
            $rowcount = mysqli_num_rows($query);
            $title = mysqli_real_escape_string($con,$title);

            if($rowcount != 0){
                $info = mysqli_fetch_array($query);
                $a = "UPDATE `".$config['db']['pre']."category_translation` set title = '$title',slug = '$slug' where id = '".$info['id']."' LIMIT 1";
                mysqli_query($con,$a);

            }else{
                $a = "INSERT into `".$config['db']['pre']."category_translation` set lang_code = '$code',title = '$title',slug = '$slug',category_type = '$cattype', translation_id = '$id' ";
                mysqli_query($con,$a);
            }
        }
        echo 1;
        die();
    }
    echo 0;
    die();
}

function langTranslation_FormFields($con,$config)
{
    $id = $_POST['id'];
    $type = $_POST['cat_type'];
    $field_tpl = '<input type="hidden" id="category_id" value="'.$id.'"><input type="hidden" id="category_type" value="'.$type.'">';
    if ($id) {
        $sql = "SELECT id,code,name FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
        $query = mysqli_query($con,$sql);
        $rows = mysqli_num_rows($query);
        if($rows > 0){
            while($fetch = mysqli_fetch_array($query)){
                $sql2 = "SELECT * FROM `".$config['db']['pre']."category_translation` where lang_code = '".$fetch['code']."' and 	translation_id = '$id' and category_type = '$type' LIMIT 1";
                $query2 = mysqli_query($con,$sql2);
                $info = mysqli_fetch_assoc($query2);

                if($type == "custom_option"){
                    $field_tpl .= '
<div class="row translate_row">
    <div class="col-md-12 col-sm-12">
        <div class="form-group">
            <label class="col-md-3 control-label">' . $fetch['name'] . '</label>
            <div class="col-md-9">
                <input type="text" value="' . $info['title'] . '" class="form-control cat_title" placeholder="In ' . $fetch['name'] . '">
                <input type="hidden" class="lang_code" value="' . $fetch['code'] . '">
            </div>
        </div>
    </div>
</div>
';
                }else{
                    $field_tpl .= '
<div class="row translate_row">
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            <label class="col-md-3 control-label">' . $fetch['name'] . '</label>
            <div class="col-md-9">
                <input type="text" value="' . $info['title'] . '" class="form-control cat_title" placeholder="In ' . $fetch['name'] . '">
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            <label class="col-md-3 control-label">Slug</label>
            <div class="col-md-9">
                <input type="text" value="' . $info['slug'] . '" class="form-control cat_slug" placeholder="Slug">
            </div>
        </div>
    </div>
    <input type="hidden" class="lang_code" value="' . $fetch['code'] . '">
</div>
';
                }

            }
        }else{
            $field_tpl .= '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            No language activated. Your site run with single language. </div>';
        }
        echo $field_tpl;
        die();
    } else {
        echo 0;
        die();
    }
}

function addNewCat($con,$config)
{
    $name = $_POST['name'];
    $icon = $_POST['icon'];
    $slug = $_POST['slug'];
    if (trim($name) != '' && is_string($name)) {
        if($slug == "")
            $slug = create_category_slug($config,$name);
        else
            $slug = create_category_slug($config,$slug);

        $query = "Insert into `".$config['db']['pre']."catagory_main` set cat_name='".$name."', slug='".$slug."',icon='".$icon."'";
        if(check_allow()){
            $con->query($query);
            $id = $con->insert_id;
            /*
            $query = "UPDATE `".$config['db']['pre']."catagory_main` SET `cat_order` = '" . $id . "' WHERE `cat_id` = '" . $id . "'";
            $con->query($query);

            $type = "main";
            $sql = "SELECT id,code,name FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
            $query = mysqli_query($con,$sql);
            mysqli_num_rows($query);
            while($fetch = mysqli_fetch_array($query)){

                $source = 'en';
                $target = $fetch['code'];

                $trans = new GoogleTranslate();
                $title = $trans->translate($source, $target, $name);
                $slug = create_category_translation_slug($config,$title);
                $title = mysqli_real_escape_string($con,$title);
                $slug = mysqli_real_escape_string($con,$slug);

                $sql2 = "Insert into `".$config['db']['pre']."category_translation` set lang_code = '".$fetch['code']."', translation_id = '$id', category_type = '$type', title = '$title', slug='".$slug."'";
                $query2 = mysqli_query($con,$sql2);
            }*/
        }
        else {
            $id = 1;
        }
        echo $name . ',' . $id . ',' . $icon. ',' . $slug;
        die();
    } else {
        echo 0;
        die();
    }
}

function editCat($con,$config)
{
    $name = $_POST['name'];
    $icon = $_POST['icon'];
    $slug = $_POST['slug'];
    $id = $_POST['id'];
    if (trim($name) != '' && is_string($name) && trim($id) != '') {
        if($slug == "")
            $slug = create_category_slug($config,$name);
        else
            $slug = create_category_slug($config,$slug);

        $query = "UPDATE `".$config['db']['pre']."catagory_main` SET `cat_name` = '".$name."',`icon` = '" . $icon . "',`slug` = '" . $slug . "' WHERE `cat_id` = '" . $id . "'";
        if(check_allow()){
            $con->query($query);

            /*$type = "main";
            $sql = "SELECT id,code,name FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
            $query = mysqli_query($con,$sql);
            mysqli_num_rows($query);
            while($fetch = mysqli_fetch_array($query)){

                $source = 'en';
                $target = $fetch['code'];

                $trans = new GoogleTranslate();
                $title = $trans->translate($source, $target, $name);
                $slug = create_category_translation_slug($config,$title);
                $title = mysqli_real_escape_string($con,$title);
                $slug = mysqli_real_escape_string($con,$slug);

                $new_sql = "SELECT 1 FROM `".$config['db']['pre']."category_translation` WHERE lang_code = '".$fetch['code']."' and translation_id = '$id' and category_type = '$type'";
                $newquery = mysqli_query($con,$new_sql);
                if($newquery){
                    if(mysqli_num_rows($newquery) > 0){
                        $sql2 = "UPDATE `".$config['db']['pre']."category_translation` set title = '$title', slug='".$slug."' WHERE lang_code = '".$fetch['code']."' and translation_id = '$id' and category_type = '$type'";
                        $query2 = mysqli_query($con,$sql2);
                    }else{
                        $sql2 = "Insert into `".$config['db']['pre']."category_translation` set lang_code = '".$fetch['code']."', translation_id = '$id', category_type = '$type', title = '$title', slug='".$slug."'";
                        $query2 = mysqli_query($con,$sql2);
                    }
                }

            }*/
        }
        echo $name . ',' . $icon;
        die();
    } else {
        echo 0;
        die();
    }
}

function deleteCat($con,$config)
{
    $id = $_POST['id'];
    if (trim($id) != '') {
        if(check_allow()){
            if ($con->query("DELETE FROM `".$config['db']['pre']."catagory_main` WHERE `cat_id` = '" . $id . "'")) {
                $con->query("DELETE FROM `".$config['db']['pre']."category_translation` WHERE `translation_id` = '" . $id . "' and category_type = 'main' ");
                $query = "SELECT sub_cat_id FROM `".$config['db']['pre']."catagory_sub` WHERE `main_cat_id` = '" . $id . "'";
                $query_result = mysqli_query ($con, $query) OR error(mysqli_error($con));
                while($row = $query_result->fetch_assoc()) // use fetch_assoc here
                {
                    $id = $row['sub_cat_id'];
                    $con->query("DELETE FROM `".$config['db']['pre']."catagory_sub` WHERE `sub_cat_id` = '" . $id . "'");
                    $con->query("DELETE FROM `".$config['db']['pre']."category_translation` WHERE `translation_id` = '" . $id . "' and category_type = 'sub' ");
                }

                echo 1;
                die();
            } else {
                echo 0;
                die();
            }
        }
        else{
            echo 1;
        }
    } else {
        echo 0;
        die();
    }
}

function quickad_update_maincat_position($con,$config)
{
    $position = $_POST['position'];
    if (is_array($position)) {
        $count = 0;
        foreach($position as $catid){

            $query = "UPDATE `".$config['db']['pre']."catagory_main` SET `cat_order` = '".$count."' WHERE `cat_id` = '" . $catid . "'";
            if(check_allow()){
                $con->query($query);
            }
            $count++;
        }

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }
}

function quickad_update_subcat_position($con,$config)
{
    $position = $_POST['position'];
    if (is_array($position)) {
        $count = 0;
        foreach($position as $catid){

            $query = "UPDATE `".$config['db']['pre']."catagory_sub` SET `cat_order` = '".$count."' WHERE `sub_cat_id` = '" . $catid . "'";
            if(check_allow()){
                $con->query($query);
            }
            $count++;
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }
}

function quickad_update_custom_field_position()
{
    global $con,$config;
    $position = $_POST['position'];
    if (is_array($position)) {
        $count = 0;
        foreach($position as $custom_id){

            $query = "UPDATE `".$config['db']['pre']."custom_fields` SET `custom_order` = '".$count."' WHERE `custom_id` = '" . validate_input($custom_id) . "'";
            if(check_allow()){
                $con->query($query) OR error(mysqli_error($con));
            }
            $count++;
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }
}

function quickad_update_custom_option_position()
{
    if(!check_allow()){
        echo 1;
        die();
    }
    global $con,$config,$lang;
    $position = $_POST['position'];
    if (is_array($position)) {
        $count = 0;
        $position = implode(',',$position);
        $custom_id = $_POST['field_id'];
        $sql = "UPDATE `".$config['db']['pre']."custom_fields` SET `custom_options` = '".$position."' WHERE `custom_id` = '" . validate_input($custom_id) . "'";
        if (!mysqli_query($con,$sql)) {
            $status = "error";
            $message = "Error : " . mysqli_error($con);
        } else{
            $status = "success";
            $message = $lang['SAVED_SUCCESS'];
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }
}

function addSubCat($con,$config)
{
    $name = $_POST['name'];
    $cat_id = $_GET['mainid'];
    if (trim($name) != '' && is_string($name) && trim($cat_id) != '') {
        $slug = create_sub_category_slug($config,$name);
        $query = "Insert into `".$config['db']['pre']."catagory_sub` set sub_cat_name='".$name."', slug='".$slug."', main_cat_id='".$cat_id."'";
        if(check_allow()){
            $con->query($query);
            $id = $con->insert_id;

            $query = "UPDATE `".$config['db']['pre']."catagory_sub` SET `cat_order` = '" . $id . "' WHERE `sub_cat_id` = '" . $id . "'";
            $con->query($query);

            $type = "sub";
            $sql = "SELECT id,code,name FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
            $query = mysqli_query($con,$sql);
            mysqli_num_rows($query);
            while($fetch = mysqli_fetch_array($query)){

                $source = 'en';
                $target = $fetch['code'];

                $trans = new GoogleTranslate();
                $title = $trans->translate($source, $target, $name);
                $slug = create_category_translation_slug($config,$title);
                $title = mysqli_real_escape_string($con,$title);
                $slug = mysqli_real_escape_string($con,$slug);

                $sql2 = "Insert into `".$config['db']['pre']."category_translation` set lang_code = '".$fetch['code']."', translation_id = '$id', category_type = '$type', title = '$title', slug='".$slug."'";
                $query2 = mysqli_query($con,$sql2);
            }
        }
        else{
            $id =1;
        }

        echo $name . ',' . $id;
        die();
    } else {
        echo 0;
        die();
    }
}

function editSubCat($con,$config)
{
    $name = $_GET['title'];
    $slug = $_GET['slug'];
    $id = $_GET['id'];
    $photo_show = $_GET['photo_show'];
    $price_show = $_GET['price_show'];
    if (trim($name) != '' && is_string($name) && trim($id) != '') {

        if($slug == "")
            $slug = create_category_slug($config,$name);
        else
            $slug = create_category_slug($config,$slug);

        $query = "UPDATE `".$config['db']['pre']."catagory_sub` SET `sub_cat_name` = '".$name."',`slug` = '".$slug."', `photo_show` = '".$photo_show."', `price_show` = '".$price_show."' WHERE `sub_cat_id` = '" . $id . "'";
        if(check_allow()){
            $con->query($query);

            $type = "sub";
            $sql = "SELECT id,code,name FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
            $query = mysqli_query($con,$sql);
            mysqli_num_rows($query);
            while($fetch = mysqli_fetch_array($query)){

                $source = 'en';
                $target = $fetch['code'];

                $trans = new GoogleTranslate();
                $title = $trans->translate($source, $target, $name);
                $slug = create_category_translation_slug($config,$title);
                $title = mysqli_real_escape_string($con,$title);
                $slug = mysqli_real_escape_string($con,$slug);

                $new_sql = "SELECT 1 FROM `".$config['db']['pre']."category_translation` WHERE lang_code = '".$fetch['code']."' and translation_id = '$id' and category_type = '$type'";
                $newquery = mysqli_query($con,$new_sql);
                if($newquery){
                    if(mysqli_num_rows($newquery) > 0){
                        $sql2 = "UPDATE `".$config['db']['pre']."category_translation` set title = '$title', slug='".$slug."' WHERE lang_code = '".$fetch['code']."' and translation_id = '$id' and category_type = '$type'";
                        $query2 = mysqli_query($con,$sql2);
                    }else{
                        $sql2 = "Insert into `".$config['db']['pre']."category_translation` set lang_code = '".$fetch['code']."', translation_id = '$id', category_type = '$type', title = '$title', slug='".$slug."'";
                        $query2 = mysqli_query($con,$sql2);
                    }
                }

            }
        }

        echo 1;
        die();
    } else {
        echo 0;
        die();
    }
}

function delSubCat($con,$config)
{
    $subCatids = $_POST['subCatids'];
    if (is_array($subCatids)) {
        foreach ($subCatids as $subCatid) {
            if(check_allow()){
                $con->query("DELETE FROM `".$config['db']['pre']."catagory_sub` WHERE `sub_cat_id` = '" . $subCatid . "'");
                $con->query("DELETE FROM `".$config['db']['pre']."category_translation` WHERE `translation_id` = '" . $subCatid . "' and category_type = 'sub'");
            }
        }
        echo 1;
        die();
    } else {
        echo 0;
        die();
    }
}

function getSubCat($con,$config)
{
    $id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
    if ($id > 0) {
        $query = "SELECT * FROM `".$config['db']['pre']."catagory_sub` WHERE main_cat_id = ".$id." ORDER by cat_order ASC";
    } else {
        $query = "SELECT * FROM `".$config['db']['pre']."catagory_sub` ORDER by cat_order ASC";
    }
    $tags = '<div class="panel-group ui-sortable" id="services_list" role="tablist" aria-multiselectable="true">';

    if ($result = $con->query($query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['sub_cat_name'];
            $slug = $row['slug'];
            $sub_id = $row['sub_cat_id'];
            $photo_show = $row['photo_show'];
            $price_show = $row['price_show'];
            $photo_hide_selected = ($photo_show == 0)? "selected" :  "";
            $price_hide_selected = ($price_show == 0)? "selected" :  "";
            $userlangselect = (get_option( $config, "userlangsel") == '1')? "show" :  "hidden";

            $tags .= ' <div class="panel panel-default quickad-js-collapse" data-service-id="' . $sub_id . '">
                                        <div class="panel-heading" role="tab" id="s_' . $sub_id . '">
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-10">
                                                    <div class="quickad-flexbox">
                                                        <div class="quickad-flex-cell quickad-vertical-middle"
                                                             style="width: 1%">
                                                            <i class="quickad-js-handle quickad-icon quickad-icon-draghandle quickad-margin-right-sm quickad-cursor-move ui-sortable-handle"
                                                               title="Reorder"></i>
                                                        </div>
                                                        <div class="quickad-flex-cell quickad-vertical-middle">
                                                            <a role="button"
                                                               class="panel-title collapsed quickad-js-service-title"
                                                               data-toggle="collapse" data-parent="#services_list"
                                                               href="#service_' . $sub_id . '" aria-expanded="false"
                                                               aria-controls="service_' . $sub_id . '">
                                                                '.$name.' </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-xs-2">
                                                    <div class="quickad-flexbox">
                                                        <div class="quickad-flex-cell quickad-vertical-middle text-right"
                                                             style="width: 10%">
                                                            <label class="css-input css-checkbox css-checkbox-default m-t-0 m-b-0">
                                                                <input type="checkbox" id="checkbox'.$sub_id.'" name="check-all" value="'.$sub_id.'"  class="service-checker"><span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="service_' . $sub_id . '" class="panel-collapse collapse" role="tabpanel"
                                             style="height: 0">
                                            <div class="panel-body">
                                                <form method="post" id="' . $sub_id . '">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label for="title_' . $sub_id . '">Title</label>
                                                                <input name="title" value="'.$name.'" id="title_' . $sub_id . '"
                                                                       class="form-control" type="text">
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label for="slug_' . $sub_id . '">Slug</label>
                                                                <input name="slug" value="'.$slug.'" id="slug_' . $sub_id . '"
                                                                       class="form-control" type="text">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label for="photo_' . $sub_id . '">Photo field Enable/Disable</label>
                                                                <select name="photo_show" class="form-control">
                                                                   <option value="1">Enable</option>
                                                                    <option value="0" '.$photo_hide_selected.'>Disable</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <div class="form-group">
                                                                <label for="price_' . $sub_id . '">Price Enable/Disable</label>
                                                                <select name="price_show" class="form-control">
                                                                    <option value="1">Enable</option>
                                                                    <option value="0" '.$price_hide_selected.'>Disable</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel-footer">
                                                    <input name="id" value="' . $sub_id . '" type="hidden">
                                                    <button type="button"
                                                                class="'.$userlangselect.' btn btn-lg btn-warning quickad-cat-lang-edit" data-category-id="'.$sub_id.'" data-category-type="sub"> <span
                                                                class="ladda-label"><i class="fa fa-language"></i> Edit Language</span></button>
                                                        <button type="button"
                                                                class="btn btn-lg btn-success ladda-button ajax-subcat-edit"
                                                                data-style="zoom-in" data-spinner-size="40" onclick="editSubCat('.$sub_id.');"><span
                                                                class="ladda-label">Save</span></button>
                                                        <button class="btn btn-lg btn-default js-reset" type="reset">Reset
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>';

        }

        $tags .= '</div>';
        echo $tags;
        die();
    } else {
        echo 0;
        die();
    }
}

function getsubcatbyid($con,$config)
{
    $id = isset($_POST['catid']) ? $_POST['catid'] : 0;
    $selectid = isset($_POST['selectid']) ? $_POST['selectid'] : "";

    $query = "SELECT * FROM `" . $config['db']['pre'] . "catagory_sub` WHERE main_cat_id = " . $id;
    if ($result = $con->query($query)) {

        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['sub_cat_name'];
            $sub_id = $row['sub_cat_id'];
            if($selectid == $sub_id){
                $selected_text = "selected";
            }
            else{
                $selected_text = "";
            }
            echo '<option value="'.$sub_id.'" '.$selected_text.'>'.$name.'</option>';
        }


    }
}

function CustomField_langTranslation_FormFields($con,$config)
{
    $id = $_POST['id'];
    $field_tpl = '<input type="hidden" id="field_id" value="'.$id.'">';
    if ($id) {
        $sql2 = "SELECT translation_lang,translation_name FROM `".$config['db']['pre']."custom_fields` where custom_id = '$id' LIMIT 1";
        $query2 = mysqli_query($con,$sql2);
        $info = mysqli_fetch_assoc($query2);
        $translation_lang = explode(',',$info['translation_lang']);
        $translation_name = explode(',',$info['translation_name']);

        $count = 0;
        foreach($translation_lang as $key=>$value)
        {
            if($value != '')
            {
                $translation[$translation_lang[$key]] = $translation_name[$key];

                $count++;
            }
        }

        $sql = "SELECT id,code,name FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
        $query = mysqli_query($con,$sql);
        $num = mysqli_num_rows($query);
        if($num > 0){
            while($fetch = mysqli_fetch_array($query)){
                $trans_name = (isset($translation[$fetch['code']]))? $translation[$fetch['code']] : '';
                $count = 0;

                $field_tpl .= '
<div class="form-group">
    <label class="col-md-3 control-label">'.$fetch['name'].'</label>
    <div class="col-md-7">
        <input type="text" value="'.$trans_name.'" data-lang-code="'.$fetch['code'].'" class="form-control title_code" placeholder="In '.$fetch['name'].'">
    </div>
</div>';
            }
        }else{
            $field_tpl .= '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            No language activated. Your site run with single language.</div>';
        }

        echo $field_tpl;
        die();
    } else {
        echo 0;
        die();
    }
}

function edit_langTranslation_custom_fields($con,$config)
{
    $id = $_POST['id'];
    $trans_lang = implode(',', $_POST['trans_lang']);
    $trans_name = implode(',', $_POST['trans_name']);
    if($_POST['id']){
        if(check_allow()){
            $sql = "SELECT custom_id FROM `".$config['db']['pre']."custom_fields` where custom_id = '$id' LIMIT 1";
            $query = mysqli_query($con,$sql);
            $rowcount = mysqli_num_rows($query);
            $trans_name = mysqli_real_escape_string($con,$trans_name);
            if($rowcount != 0){
                $info = mysqli_fetch_array($query);
                $a = "UPDATE `".$config['db']['pre']."custom_fields` set translation_lang = '$trans_lang',translation_name = '$trans_name' where custom_id = '".$id."' LIMIT 1";
                mysqli_query($con,$a);

            }
            echo 1;
            die();
        }
    }

    echo 0;
    die();
}

function delete_custom_fields($con,$config){
    if(isset($_POST['id'])){
        if(!check_allow()){
            echo 1;
            die();
        }
        $id = $_POST['id'];
        $q = "SELECT custom_options FROM `".$config['db']['pre']."custom_fields` WHERE custom_id = '".validate_input($id)."' LIMIT 1";
        $query_result = @mysqli_query ($con,$q) OR error(mysqli_error($con));
        $info = @mysqli_fetch_array($query_result);
        $options = explode(',',stripslashes($info['custom_options']));
        foreach($options as $option_id)
        {
            $type = "custom_option";
            $query = "DELETE FROM `" . $config['db']['pre'] . "custom_options` WHERE option_id = '".validate_input($option_id)."' LIMIT 1";
            delete_language_translation($config,$type,$option_id);
            $con->query($query);
        }

        $sql = "DELETE FROM `" . $config['db']['pre'] . "custom_fields` WHERE custom_id = '".validate_input($id)."' LIMIT 1";
        $con->query($sql);
        echo 1;
        die();
    }
    echo 0;
    die();
}
function delete_custom_option($option_id){
    global $con,$config;
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }else if(isset($option_id)){
        $id = $option_id;
    }

    if(isset($id)){
        if(!check_allow()){
            echo 1;
            die();
        }
        $type = "custom_option";
        $query = "DELETE FROM `" . $config['db']['pre'] . "custom_options` WHERE option_id = '".$id."' LIMIT 1";
        delete_language_translation($config,$type,$id);
        $con->query($query);
        echo 1;
        die();
    }
    echo 0;
    die();
}

function save_custom_fields_with_auto_translation($con,$config)
{
    if(!isset($_POST['fields'])){
        echo 0;
        die();
    }

    if(!check_allow()){
        echo 1;
        die();
    }

    $fields = json_decode($_POST['fields'], true);
    $count = 0;
    foreach($fields as $custom) {
        $id = $custom['id'];
        $type = $custom['type'];
        $title = $custom['label'];
        $required = empty($custom['required'])? 0 : $custom['required'];
        $allcat = $custom['allcat'];
        $maincat = $custom['maincat'];
        $category = $custom['services'];

        if (is_array($allcat)) {
            $allcat = implode(',', $custom['allcat']);
        }
        if (is_array($maincat)) {
            $maincat = implode(',', $custom['maincat']);
        }
        if (is_array($category)) {
            $category = implode(',', $custom['services']);
        }

        if ($type == 'text-field' or $type == 'textarea') {
            $options = "";
        } else {
            if (!isset($custom['items'])) {
                $custom['items'] = json_decode($custom['items'], true);
            }
            $custom_option = array();
            $i = 0;
            foreach ($custom['items'] as $items) {

                $opt_id = $items['id'];
                $opt_title = $items['value'];

                $query = "SELECT * FROM `" . $config['db']['pre'] . "custom_options` WHERE option_id = " . $opt_id;
                $result = $con->query($query);
                $num_rows = mysqli_num_rows($result);
                if($num_rows > 0){
                    $query = "UPDATE `" . $config['db']['pre'] . "custom_options` SET `title` = '".$opt_title."' WHERE option_id = '".$opt_id."' LIMIT 1";
                    $con->query($query);
                }else{
                    $query = "INSERT INTO `" . $config['db']['pre'] . "custom_options` SET `title` = '".$opt_title."' ";
                    $con->query($query);
                    $opt_id = $con->insert_id;
                }

                $trnas_type = "custom_option";
                $sql = "SELECT id,code,name FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
                $query = mysqli_query($con,$sql);
                mysqli_num_rows($query);
                while($fetch = mysqli_fetch_array($query)){

                    $source = 'en';
                    $target = $fetch['code'];

                    $trans = new GoogleTranslate();
                    $trans_title = $trans->translate($source, $target, $opt_title);
                    $trans_title = mysqli_real_escape_string($con,$trans_title);

                    $new_sql = "SELECT 1 FROM `".$config['db']['pre']."category_translation` WHERE lang_code = '".$fetch['code']."' and translation_id = '$opt_id' and category_type = '$trnas_type'";
                    $newquery = mysqli_query($con,$new_sql);
                    if($newquery){
                        if(mysqli_num_rows($newquery) > 0){
                            $sql2 = "UPDATE `".$config['db']['pre']."category_translation` set title = '$trans_title' WHERE lang_code = '".$fetch['code']."' and translation_id = '$opt_id' and category_type = '$trnas_type'";
                            $query2 = mysqli_query($con,$sql2) OR error(mysqli_error($con));
                        }else{
                            $sql2 = "Insert into `".$config['db']['pre']."category_translation` set lang_code = '".$fetch['code']."', translation_id = '$opt_id', category_type = '$trnas_type', title = '$trans_title'";
                            $query2 = mysqli_query($con,$sql2) OR error(mysqli_error($con));
                        }
                    }


                }

                $custom_option[$i] = $opt_id;
                $i++;
            }

            $options = implode(',', $custom_option);
        }

        if(check_allow()){
            $exist = get_customField_exist_id($config,$id);
            if($exist > 0){
                $query = "UPDATE `" . $config['db']['pre'] . "custom_fields` SET `custom_anycat` = '".$allcat."',`custom_catid` = '".$maincat."',`custom_subcatid` = '".$category."',`custom_title` = '".$title."', `custom_type` = '".$type."',`custom_required` = '".$required."',`custom_options` = '".$options."' WHERE custom_id = '".$id."' LIMIT 1";
                $con->query($query) OR error(mysqli_error($con));
            }else{
                $lang_code = array();
                $lang_title = array();
                $sql = "SELECT code FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
                $result = $con->query($sql) OR error(mysqli_error($con));
                mysqli_num_rows($result);
                while($fetch = mysqli_fetch_array($result)){

                    $source = 'en';
                    $target = $fetch['code'];
                    $lang_code[] = $fetch['code'];
                    $trans = new GoogleTranslate();
                    $trans_title = $trans->translate($source, $target, $title);
                    $trans_title = mysqli_real_escape_string($con,$trans_title);
                    $lang_title[] = $trans_title;

                }
                $trans_lang = implode(',', $lang_code);
                $trans_name = implode(',', $lang_title);

                $query = "INSERT INTO `" . $config['db']['pre'] . "custom_fields` SET translation_lang = '$trans_lang', translation_name = '$trans_name', `custom_anycat` = '".$allcat."',`custom_catid` = '".$maincat."',`custom_subcatid` = '".$category."',`custom_title` = '".$title."', `custom_type` = '".$type."',`custom_required` = '".$required."',`custom_options` = '".$options."' ";
                $con->query($query) OR error(mysqli_error($con));

                $id = $con->insert_id;
                $query = "UPDATE `".$config['db']['pre']."custom_fields` SET `custom_order` = '".$id ."' WHERE custom_id = '".$id."' LIMIT 1";
                $con->query($query) OR error(mysqli_error($con));
            }
        }
        $count++;
    }

    echo 1;
    die();
}

function save_custom_fields($con,$config)
{
    if(!isset($_POST['fields'])){
        echo 0;
        die();
    }

    if(!check_allow()){
        echo 1;
        die();
    }

    $fields = json_decode($_POST['fields'], true);
    $count = 0;
    foreach($fields as $custom) {
        $id = $custom['id'];
        $type = $custom['type'];
        $title = $custom['label'];
        $required = empty($custom['required'])? 0 : $custom['required'];
        $allcat = $custom['allcat'];
        $maincat = $custom['maincat'];
        $category = $custom['services'];

        if (is_array($allcat)) {
            $allcat = implode(',', $custom['allcat']);
        }
        if (is_array($maincat)) {
            $maincat = implode(',', $custom['maincat']);
        }
        if (is_array($category)) {
            $category = implode(',', $custom['services']);
        }

        if ($type == 'text-field' or $type == 'textarea') {
            $options = "";
        } else {
            if (!isset($custom['items'])) {
                $custom['items'] = json_decode($custom['items'], true);
            }
            $custom_option = array();
            $i = 0;
            foreach ($custom['items'] as $items) {

                $opt_id = $items['id'];
                $opt_title = $items['value'];

                $query = "SELECT * FROM `" . $config['db']['pre'] . "custom_options` WHERE option_id = " . $opt_id;
                $result = $con->query($query) OR error(mysqli_error($con));
                $num_rows = mysqli_num_rows($result);
                if($num_rows > 0){
                    $query = "UPDATE `" . $config['db']['pre'] . "custom_options` SET `title` = '".$opt_title."' WHERE option_id = '".$opt_id."' LIMIT 1";
                    $con->query($query) OR error(mysqli_error($con));
                }else{
                    $query = "INSERT INTO `" . $config['db']['pre'] . "custom_options` SET `title` = '".$opt_title."' ";
                    $con->query($query) OR error(mysqli_error($con));
                    $opt_id = $con->insert_id;
                }

                $custom_option[$i] = $opt_id;
                $i++;
            }

            $options = implode(',', $custom_option);
        }

        if(check_allow()){
            $exist = get_customField_exist_id($config,$id);
            if($exist > 0){
                $query = "UPDATE `" . $config['db']['pre'] . "custom_fields` SET `custom_anycat` = '".$allcat."',`custom_catid` = '".$maincat."',`custom_subcatid` = '".$category."',`custom_title` = '".$title."', `custom_type` = '".$type."',`custom_required` = '".$required."',`custom_options` = '".$options."' WHERE custom_id = '".$id."' LIMIT 1";
                $con->query($query) OR error(mysqli_error($con));
            }else{
                $lang_code = array();
                $lang_title = array();
                $sql = "SELECT code FROM `".$config['db']['pre']."languages` where active = '1' and code != 'en'";
                $result = $con->query($sql) OR error(mysqli_error($con));
                mysqli_num_rows($result);
                while($fetch = mysqli_fetch_array($result)){

                    $source = 'en';
                    $target = $fetch['code'];
                    $lang_code[] = $fetch['code'];
                    /*$trans = new GoogleTranslate();
                    $trans_title = $trans->translate($source, $target, $title);*/
                    $trans_title = $title;
                    $trans_title = mysqli_real_escape_string($con,$trans_title);
                    $lang_title[] = $trans_title;

                }
                $trans_lang = implode(',', $lang_code);
                $trans_name = implode(',', $lang_title);

                $query = "INSERT INTO `" . $config['db']['pre'] . "custom_fields` SET translation_lang = '$trans_lang', translation_name = '$trans_name', `custom_anycat` = '".$allcat."',`custom_catid` = '".$maincat."',`custom_subcatid` = '".$category."',`custom_title` = '".$title."', `custom_type` = '".$type."',`custom_required` = '".$required."',`custom_options` = '".$options."' ";
                $con->query($query) OR error(mysqli_error($con));

                $id = $con->insert_id;
                $query = "UPDATE `".$config['db']['pre']."custom_fields` SET `custom_order` = '".$id ."' WHERE custom_id = '".$id."' LIMIT 1";
                $con->query($query) OR error(mysqli_error($con));
            }
        }
        $count++;
    }

    echo 1;
    die();
}

function getStateByCountryID($con,$config)
{
    $country_id = isset($_POST['id']) ? $_POST['id'] : 0;
    $selectid = isset($_POST['selectid']) ? $_POST['selectid'] : "";

    $query = "SELECT id,code,name FROM `".$config['db']['pre']."subadmin1` WHERE country_code = '".$country_id."' ORDER BY name";
    if ($result = $con->query($query)) {

        $list = '<option value="">Select State</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $state_id = $row['id'];
            $state_code = $row['code'];
            if($selectid == $state_code){
                $selected_text = "selected";
            }
            else{
                $selected_text = "";
            }
            $list .= '<option value="'.$state_code.'" '.$selected_text.'>'.$name.'</option>';
        }

        echo $list;
    }
}

function getStateByCountryIDforCityAdd($con,$config)
{
    $country_id = isset($_POST['id']) ? $_POST['id'] : 0;
    $selectid = isset($_POST['selectid']) ? $_POST['selectid'] : "";

    $query = "SELECT id,code,name FROM `".$config['db']['pre']."subadmin1` WHERE country_code = '".$country_id."' ORDER BY name";
    if ($result = $con->query($query)) {

        $list = '<option value="">Select State</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $state_id = $row['id'];
            $code = $row['code'];
            if($selectid == $code){
                $selected_text = "selected";
            }
            else{
                $selected_text = "";
            }
            $list .= '<option value="'.$code.'" '.$selected_text.'>'.$name.'</option>';
        }

        echo $list;
    }
}

function getDistrictSelectedforCityAdd($con,$config)
{
    $code = isset($_POST['id']) ? $_POST['id'] : 0;
    $selectid = isset($_POST['selectid']) ? $_POST['selectid'] : "";

    $query = "SELECT id,code,name FROM `".$config['db']['pre']."subadmin2` WHERE subadmin1_code = '".$code."' ORDER BY name";
    if ($result = $con->query($query)) {

        $list = '<option value="">Select District</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $state_id = $row['id'];
            $code = $row['code'];

            if($selectid == $code){
                $selected_text = "selected";
            }
            else{
                $selected_text = "";
            }
            $list .= '<option value="'.$code.'" '.$selected_text.'>'.$name.'</option>';
        }

        echo $list;
    }
}

function getCityByStateID($con,$config)
{
    $state_id = isset($_POST['id']) ? $_POST['id'] : 0;
    $selectid = isset($_POST['selectid']) ? $_POST['selectid'] : "";

    //$state_code = substr($state_id,3);
    $country_code = substr($state_id,0,2);
    $query = "SELECT id ,name FROM `".$config['db']['pre']."cities` WHERE subadmin1_code = '".$state_id."' and country_code = '$country_code'" ;
    $result = $con->query($query);
    if ($result){
        if(mysqli_num_rows($result) > 0){

            $list = '<option value="">Select City</option>';
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['name'];
                $id = $row['id'];
                if($selectid == $id){
                    $selected_text = "selected";
                }
                else{
                    $selected_text = "";
                }
                $list .= '<option value="'.$id.'" '.$selected_text.'>'.$name.'</option>';
            }
            echo $list;
            die();
        }
    }else{
        echo $list = '<option value="">Select City</option>';
        die();
    }
}

function searchCityStateCountry($con,$config)
{
    $dataString = isset($_POST['dataString']) ? $_POST['dataString'] : "";
    $sortname = check_user_country($config);

    $query = "SELECT c.id, c.asciiname, c.subadmin1_code, s.asciiname AS statename
FROM `".$config['db']['pre']."cities` AS c
INNER JOIN `".$config['db']['pre']."subadmin1` AS s ON s.code = c.subadmin1_code
 WHERE c.asciiname like '%$dataString%' and c.country_code = '$sortname'
 ORDER BY
  CASE
    WHEN c.asciiname = '$dataString' THEN 1
    WHEN c.asciiname LIKE '$dataString%' THEN 2
    WHEN c.asciiname LIKE '%$dataString' THEN 4
    ELSE 3
  END
 LIMIT 20";

    $result = mysqli_query($con,$query);
    $total = mysqli_num_rows($result);
    $list = '<ul class="searchResgeo">';
    if ($total > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cityid = $row['id'];
            $cityname = $row['asciiname'];
            $stateid = $sortname.".".$row['subadmin1_code'];
            $countryid = $sortname;
            $statename = $row['statename'];

            $list .= '<li><a href="#" class="title selectme" data-cityid="'.$cityid.'" data-stateid="'.$stateid.'"data-countryid="'.$countryid.'" data-name="'.$cityname.', '.$statename.'">'.$cityname.', <span class="color-9">'.$statename.'</span></a></li>';
        }
        $list .= '</ul>';
        echo $list;
    }
    else{
        echo '<ul class="searchResgeo"><li><span class="noresult">No results found</span></li>';
    }
}
/**********************
 * @param $con
 * @param $config
 * Google map location function
 */

function getlocHomemap($con,$config)
{
    global $lang,$link;
    $appr = 'active';

    if(isset($_GET['serachStr'])){
        $serachStr = $_GET['serachStr'];
    }
    else{
        $serachStr = '';
    }
    /*if(isset($_GET['location'])){
        $location = $_GET['location'];
    }
    else{
        $location = '';
    }*/
    if(isset($_GET['country'])){
        $country = $_GET['country'];
    }
    else{
        $country = '';
    }
    if(isset($_GET['state'])){
        $state = $_GET['state'];
    }
    else{
        $state = '';
    }
    if(!empty($_GET['city'])){
        $city = $_GET['city'];
    }
    else{
        if(!empty($_GET['locality'])){
            $city = $_GET['locality'];
        }else{
            $city = '';
        }
    }
    if(isset($_GET['searchBox'])){
        $searchBox = $_GET['searchBox'];
    }
    else{
        $searchBox = '';
    }

    if(isset($_GET['catid'])){
        $catid = $_GET['catid'];
    }
    else{
        $catid = '';
    }


    $where = "";



    if ($city != '') {

        if ($serachStr != '') {
            $where .= "AND p.product_name LIKE '%$serachStr%'";
        }

        if ($searchBox != '') {
            $where .= " AND p.category = '$searchBox' ";
        }

        if ($catid != '') {
            $where .= " AND p.sub_category = '$catid' ";
        }

        $query = "SELECT p.*,c.name AS cityname, s.name AS statename, a.name AS countryname
        FROM `".$config['db']['pre']."countries` AS a
        INNER JOIN `".$config['db']['pre']."states` AS s ON s.country_id = a.id
        INNER JOIN `".$config['db']['pre']."cities` AS c ON c.state_id = s.id
        INNER JOIN `".$config['db']['pre']."product` AS p ON p.city = c.id Where c.name = '$city' and p.status = 'active' $where";
    }
    else{

        if ($serachStr != '') {
            $where .= "AND product_name LIKE '%$serachStr%'";
        }

        if ($searchBox != '') {
            $where .= " AND category = '$searchBox' ";
        }

        if ($catid != '') {
            $where .= " AND sub_category = '$catid' ";
        }

        $query = "SELECT * FROM `".$config['db']['pre']."product`  WHERE `status` = '$appr' $where ";
    }

    $query_result = mysqli_query ($con, $query);

    $data = array();
    $i = 0;
    if ($query_result->num_rows > 0) {

        while ($row = mysqli_fetch_array($query_result))
            $results[] = $row;

        foreach($results as $result){
            $id = $result['id'];
            $featured = $result['featured'];
            $urgent = $result['urgent'];
            $highlight = $result['highlight'];
            $title = $result['product_name'];
            $cat = $result['category'];
            $price = $result['price'];
            $pics = $result['screen_shot'];
            $location = $result['location'];
            $latlong = $result['latlong'];
            $desc = $result['description'];
            $url = $link['AD-DETAIL']."/".$id;

            $caticonquery = "SELECT * FROM `".$config['db']['pre']."catagory_main`  WHERE `cat_id` = '$cat' LIMIT 1";
            $caticonres = mysqli_query ($con, $caticonquery);
            $fetch = mysqli_fetch_array($caticonres);
            $catIcon = $fetch['icon'];
            $catname = $fetch['cat_name'];

            $map = explode(',', $latlong);
            $lat = $map[0];
            $long = $map[1];

            $p = explode(',', $pics);
            $pic = $p[0];
            $pic = '../storage/products/'.$pic;

            $data[$i]['id'] = $id;
            $data[$i]['latitude'] = $lat;
            $data[$i]['longitude'] = $long;
            $data[$i]['featured'] = $featured;
            $data[$i]['title'] = $title;
            $data[$i]['location'] = $location;
            $data[$i]['category'] = $catname;
            $data[$i]['cat_icon'] = $catIcon;
            $data[$i]['marker_image'] = $pic;
            $data[$i]['url'] = $url;
            $data[$i]['description'] = $desc;


            $i++;
        }
        echo json_encode($data);
    } else {
        echo '0';
    }
    die();
}

function openlocatoionPopup($con,$config)
{
    /*$query = "SELECT a.*, b.name AS cat FROM `".$config['db']['pre']."product` AS a INNER JOIN `".$config['db']['pre']."category` AS b ON a.category = b.id WHERE a.id = '" . $_POST['id'] . "' LIMIT 1";*/
    $query = "SELECT * FROM `".$config['db']['pre']."product` WHERE id = '" . $_POST['id'] . "' LIMIT 1";
    $query_result = mysqli_query ($con, $query);
    $data = array();
    $i = 0;
    if ($query_result->num_rows > 0) {
        while ($result = mysqli_fetch_array($query_result)) {
            $id = $result['id'];
            $featured = $result['featured'];
            $urgent = $result['urgent'];
            $highlight = $result['highlight'];
            $title = $result['product_name'];
            $cat = $result['category'];
            $price = $result['price'];
            $pics = $result['screen_shot'];
            $location = $result['location'];
            $latlong = $result['latlong'];
            $desc = $result['description'];
            $url = $config['site_url']."ad/".$id;

            $caticonquery = "SELECT * FROM `".$config['db']['pre']."catagory_main`  WHERE `cat_id` = '$cat' LIMIT 1";
            $caticonres = mysqli_query ($con, $caticonquery);
            $fetch = mysqli_fetch_array($caticonres);
            $catIcon = $fetch['icon'];
            $catname = $fetch['cat_name'];

            $map = explode(',', $latlong);
            $lat = $map[0];
            $long = $map[1];

            $p = explode(',', $pics);
            $pic = $p[0];
            $pic = '../storage/products/'.$pic;


            echo '<div class="item gmapAdBox" data-id="' . $id . '" style="margin-bottom: 0px;">
                    <a href="' . $url . '" style="display: block;position: relative;">
                     <div class="card small">
                        <div class="card-image waves-effect waves-block waves-light">
                          <img class="activator" src="' . $pic . '">
                        </div>
                        <div class="card-content">
                            <div class="label label-default">' . $catname . '</div>
                          <span class="card-title activator grey-text text-darken-4 mapgmapAdBoxTitle">' . $title . '</span>
                          <p class="mapgmapAdBoxLocation">' . $location . '</p>
                        </div>
                      </div>

                    </a>
                </div>';

        }
    } else {
        echo false;
    }
    die();
}
?>