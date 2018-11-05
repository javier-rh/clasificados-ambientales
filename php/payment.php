<?php
require_once('includes/config.php');
require_once('includes/classes/class.template_engine.php');
require_once('includes/classes/class.country.php');
require_once('includes/functions/func.global.php');
require_once('includes/functions/func.users.php');
require_once('includes/functions/func.sqlquery.php');
require_once('includes/lang/lang_'.$config['lang'].'.php');
require_once("includes/lib/curl/curl.php");
require_once("includes/lib/curl/CurlResponse.php");
require_once('includes/seo-url.php');
$mysqli = db_connect($config);
sec_session_start();

if(!checkloggedin($config))
{
    error($lang['PAGE-NOT-FOUND'], __LINE__, __FILE__, 1);
    exit();
}


if(isset($_POST['payment_method_id']))
{
    $access_token = $_POST['token'];

    $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
    if (isset($payment_type)) {
        $query = "SELECT * FROM `".$config['db']['pre']."payments` WHERE payment_id='" . validate_input($_POST['payment_method_id']) . "' AND payment_install='1' LIMIT 1";
        $query_result = @mysqli_query ($mysqli,$query) OR error(mysqli_error($mysqli));
        $info = @mysqli_fetch_array($query_result);
        $folder = $info['payment_folder'];

        $_SESSION['quickad'][$access_token]['folder'] = $folder;

        if($folder == "2checkout"){
            $_SESSION['quickad'][$access_token]['firstname'] = $_POST['checkoutCardFirstName'];
            $_SESSION['quickad'][$access_token]['lastname'] = $_POST['checkoutCardLastName'];
            $_SESSION['quickad'][$access_token]['BillingAddress'] = $_POST['checkoutBillingAddress'];
            $_SESSION['quickad'][$access_token]['BillingCity'] = $_POST['checkoutBillingCity'];
            $_SESSION['quickad'][$access_token]['BillingState'] = $_POST['checkoutBillingState'];
            $_SESSION['quickad'][$access_token]['BillingZipcode'] = $_POST['checkoutBillingZipcode'];
            $_SESSION['quickad'][$access_token]['BillingCountry'] = $_POST['checkoutBillingCountry'];
        }

        require_once('includes/payments/' . $folder . '/pay.php');
    }else{

        error($lang['INVALID-PAYMENT_PROCESS'], __LINE__, __FILE__, 1);
        exit();
    }
}
else if(isset($_GET['token'])) {
    $access_token = $_GET['token'];

    if (isset($_SESSION['quickad'][$access_token]['payment_type'])) {
        $_SESSION['quickad'][$access_token]['name'];
        $_SESSION['quickad'][$access_token]['payment_type'];
        $payment_types = array();

        $query = "SELECT * FROM `".$config['db']['pre']."payments` WHERE payment_install='1'";
        $query_result = @mysqli_query ($mysqli,$query) OR error(mysqli_error($mysqli));
        while ($info = @mysqli_fetch_array($query_result))
        {
            $payment_types[$info['payment_id']]['id'] = $info['payment_id'];
            $payment_types[$info['payment_id']]['title'] = $info['payment_title'];
            $payment_types[$info['payment_id']]['folder'] = $info['payment_folder'];
            $payment_types[$info['payment_id']]['desc'] = $info['payment_desc'];
        }

        $product_id = $_SESSION['quickad'][$access_token]['product_id'];
        $amount = $_SESSION['quickad'][$access_token]['amount'];
        $title = $_SESSION['quickad'][$access_token]['name'];
        $trans_desc = $_SESSION['quickad'][$access_token]['trans_desc'];
        $payment_type = $_SESSION['quickad'][$access_token]['payment_type'];
        // assign posted variables to local variables
        $bank_information = nl2br(get_option($config,'company_bank_info'));
        $userdata = get_user_data($config,$_SESSION['user']['username']);
        $email = $userdata['email'];

        $page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/payment.tpl');
        $page->SetParameter ('OVERALL_HEADER', create_header($lang['UPGRADES']));
        $page->SetLoop ('PAYMENT_TYPES', $payment_types);
        $page->SetParameter ('BANK_INFO', $bank_information);
        $page->SetParameter ('ORDER_TITLE', $title);
        $page->SetParameter ('ORDER_DESC', $trans_desc);
        $page->SetParameter ('PAYMENT_TYPE', $payment_type);
        $page->SetParameter ('AMOUNT', $amount);
        $page->SetParameter ('TOKEN', $access_token);
        $page->SetParameter ('EMAIL', $email);
        $page->SetParameter ('OVERALL_FOOTER', create_footer());
        $page->CreatePageEcho();
    }else{
        error($lang['INVALID-PAYMENT_PROCESS'], __LINE__, __FILE__, 1);
        exit();
    }
}
else
{
    error($lang['PAGE-NOT-FOUND'], __LINE__, __FILE__, 1);
    exit();
}

?>
