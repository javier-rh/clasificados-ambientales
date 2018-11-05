<?php
require_once('includes/config.php');
require_once('includes/classes/class.template_engine.php');
require_once('includes/classes/class.country.php');
require_once('includes/functions/func.global.php');
require_once('includes/functions/func.users.php');
require_once('includes/functions/func.sqlquery.php');
require_once("includes/lib/curl/curl.php");
require_once("includes/lib/curl/CurlResponse.php");
require_once('includes/lang/lang_'.$config['lang'].'.php');
require_once('includes/seo-url.php');
sec_session_start();

$con = db_connect($config);

if(checkloggedin($config))
{
	//$check_upgrade = mysqli_num_rows(mysqli_query($con,"SELECT * FROM ".$config['db']['pre']."upgrades WHERE user_id='".validate_input($_SESSION['user']['id'])."' LIMIT 1"));

    if(isset($_POST['upgrade']))
    {
        $query = "SELECT * FROM `".$config['db']['pre']."subscriptions` WHERE sub_id='".validate_input($_POST['upgrade'])."' LIMIT 1";
        $query_result = @mysqli_query ($con,$query) OR error(mysqli_error($con));
        $info = @mysqli_fetch_array($query_result);
        $title = $info['sub_title'];
        $amount = $info['sub_amount'];
        $term = $info['sub_term'];
        $payment_type = "subscr";

        if(isset($_POST['payment_method_id']))
        {
            $access_token = uniqid();
            $_SESSION['quickad'][$access_token]['name'] = $title." ".$lang['MEMBERSHIPPLAN'];
            $_SESSION['quickad'][$access_token]['amount'] = $amount;
            $_SESSION['quickad'][$access_token]['payment_type'] = $payment_type;
            $_SESSION['quickad'][$access_token]['sub_id'] = $_POST['upgrade'];




            $query = "SELECT * FROM `".$config['db']['pre']."payments` WHERE payment_id='" . validate_input($_POST['payment_method_id']) . "' AND payment_install='1' LIMIT 1";
            $query_result = @mysqli_query ($con,$query) OR error(mysqli_error($con));
            $info = @mysqli_fetch_array($query_result);
            $folder = $info['payment_folder'];

            if($folder == "2checkout"){
                $_SESSION['quickad'][$access_token]['firstname'] = $_POST['checkoutCardFirstName'];
                $_SESSION['quickad'][$access_token]['lastname'] = $_POST['checkoutCardLastName'];
                $_SESSION['quickad'][$access_token]['BillingAddress'] = $_POST['checkoutBillingAddress'];
                $_SESSION['quickad'][$access_token]['BillingCity'] = $_POST['checkoutBillingCity'];
                $_SESSION['quickad'][$access_token]['BillingState'] = $_POST['checkoutBillingState'];
                $_SESSION['quickad'][$access_token]['BillingZipcode'] = $_POST['checkoutBillingZipcode'];
                $_SESSION['quickad'][$access_token]['BillingCountry'] = $_POST['checkoutBillingCountry'];
            }

            $_SESSION['quickad'][$access_token]['folder'] = $folder;

            require_once('includes/payments/' . $folder . '/pay.php');
        }
        else
        {
            $payment_types = array();

            $query = "SELECT * FROM `".$config['db']['pre']."payments` WHERE payment_install='1'";
            $query_result = @mysqli_query ($con,$query) OR error(mysqli_error($con));
            while ($info = @mysqli_fetch_array($query_result))
            {
                $payment_types[$info['payment_id']]['id'] = $info['payment_id'];
                $payment_types[$info['payment_id']]['title'] = $info['payment_title'];
                $payment_types[$info['payment_id']]['folder'] = $info['payment_folder'];
                $payment_types[$info['payment_id']]['desc'] = $info['payment_desc'];
            }

            $period = 0;
            if($term == 'DAILY') {
                $period = 86400;
            }
            elseif($term == 'WEEKLY') {
                $term = 604800;
            }
            elseif($term == 'MONTHLY') {
                $period = 2678400;
            }
            elseif($term == 'YEARLY') {
                $period = 31536000;
            }

            $expires = (time()+$period);
            $start_date = date("d-m-Y",time());
            $expiry_date = date("d-m-Y",$expires);

            // assign posted variables to local variables
            $bank_information = nl2br(get_option($config,'company_bank_info'));
            $userdata = get_user_data($config,$_SESSION['user']['username']);
            $email = $userdata['email'];

            $page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/membership_payment.tpl');
            $page->SetParameter ('OVERALL_HEADER', create_header($lang['UPGRADES']));
            $page->SetLoop ('PAYMENT_TYPES', $payment_types);
            $page->SetParameter ('UPGRADE', $_POST['upgrade']);

            $page->SetParameter ('SUB_ID', $_POST['upgrade']);
            $page->SetParameter ('BANK_INFO', $bank_information);
            $page->SetParameter ('START_DATE', $start_date);
            $page->SetParameter ('EXPIRY_DATE', $expiry_date);
            $page->SetParameter ('ORDER_TITLE', $title);
            $page->SetParameter ('AMOUNT', $amount);
            $page->SetParameter ('EMAIL', $email);
            $page->SetParameter ('OVERALL_FOOTER', create_footer());
            $page->CreatePageEcho();
        }
    }
	elseif(check_user_upgrades($con,$_SESSION['user']['id']))
	{
		$upgrades = array();
	
		if(isset($_GET['change_plan']) && $_GET['change_plan'] == "changeplan")
		{
            $sub_info = get_user_membership_detail($con,$_SESSION['user']['id']);


            $query = "SELECT * FROM `".$config['db']['pre']."subscriptions`";
            $query_result = @mysqli_query ($con,$query) OR error(mysqli_error($con));
            while ($info = @mysqli_fetch_array($query_result))
            {
                if($info['sub_id'] == $sub_info['sub_id'])
                {
                    $sub_types[$info['sub_id']]['Selected'] = 1;
                }
                else
                {
                    $sub_types[$info['sub_id']]['Selected'] = 0;
                }

                if($info['sub_term'] == 'DAILY')
                {
                    $sub_types[$info['sub_id']]['term'] = $lang['DAILY'];
                }
                elseif($info['sub_term'] == 'WEEKLY')
                {
                    $sub_types[$info['sub_id']]['term'] = $lang['WEEKLY'];
                }
                elseif($info['sub_term'] == 'MONTHLY')
                {
                    $sub_types[$info['sub_id']]['term'] = $lang['MONTHLY'];
                }
                elseif($info['sub_term'] == 'YEARLY')
                {
                    $sub_types[$info['sub_id']]['term'] = $lang['YEARLY'];
                }

                $sub_types[$info['sub_id']]['id'] = $info['sub_id'];
                $sub_types[$info['sub_id']]['title'] = $info['sub_title'];
                $sub_types[$info['sub_id']]['recommended'] = $info['recommended'];
                $sub_types[$info['sub_id']]['cost'] = $info['sub_amount'];

                $query2 = "SELECT * FROM ".$config['db']['pre']."usergroups where group_id ='".validate_input($info['group_id'])."' LIMIT 1";
                $query_result2 = @mysqli_query ($con,$query2) OR error(mysqli_error($con));
                $info2 = @mysqli_fetch_array($query_result2);

                $sub_types[$info['sub_id']]['duration'] = $info2['ad_duration'];
                $sub_types[$info['sub_id']]['featured_fee'] = $info2['featured_project_fee'];
                $sub_types[$info['sub_id']]['urgent_fee'] = $info2['urgent_project_fee'];
                $sub_types[$info['sub_id']]['highlight_fee'] = $info2['highlight_project_fee'];
                $sub_types[$info['sub_id']]['top_search_result'] = $info2['top_search_result'];
                $sub_types[$info['sub_id']]['show_on_home'] = $info2['show_on_home'];
                $sub_types[$info['sub_id']]['show_in_home_search'] = $info2['show_in_home_search'];
            }

            $page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/membership_plan.tpl');
            $page->SetParameter ('OVERALL_HEADER', create_header($lang['UPGRADES']));
            $page->SetLoop ('SUB_TYPES', $sub_types);
            $page->SetParameter ('OVERALL_FOOTER', create_footer());
            $page->CreatePageEcho();
	
			exit;
		}
		else
		{
			$query = "SELECT * FROM `".$config['db']['pre']."upgrades` WHERE user_id='".validate_input($_SESSION['user']['id'])."' ";
			$query_result = @mysqli_query ($con,$query) OR error(mysqli_error($con));
			while ($info = @mysqli_fetch_array($query_result))
			{
				$sub_info = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `".$config['db']['pre']."subscriptions` WHERE sub_id='".validate_input($info['sub_id'])."' LIMIT 1"));

				$upgrades[$info['upgrade_id']]['id'] = $info['upgrade_id'];
				$upgrades[$info['upgrade_id']]['title'] = $sub_info['sub_title'];
				$upgrades[$info['upgrade_id']]['cost'] = $sub_info['sub_amount'];

				if($sub_info['sub_term'] == 'DAILY')
				{
					$upgrades[$info['upgrade_id']]['term'] = $lang['DAILY'];
				}
                elseif($sub_info['sub_term'] == 'WEEKLY') {
                    $upgrades[$info['upgrade_id']]['term'] = $lang['WEEKLY'];
                }
				elseif($sub_info['sub_term'] == 'MONTHLY')
				{
					$upgrades[$info['upgrade_id']]['term'] = $lang['MONTHLY'];
				}
				elseif($sub_info['sub_term'] == 'YEARLY')
				{
					$upgrades[$info['upgrade_id']]['term'] = $lang['YEARLY'];
				}

                $upgrades[$info['upgrade_id']]['start_date'] = date("d-m-Y",$info['upgrade_lasttime']);
                $upgrades[$info['upgrade_id']]['expiry_date'] = date("d-m-Y",$info['upgrade_expires']);
			}


			$page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/membership_current.tpl');
			$page->SetParameter ('OVERALL_HEADER', create_header($lang['UPGRADES']));
			$page->SetLoop ('UPGRADES', $upgrades);
            $page->SetParameter ('MYADS', myads_count($config,$_SESSION['user']['id']));
            $page->SetParameter ('ACTIVEADS', active_ads_count($config,$_SESSION['user']['id']));
            $page->SetParameter ('PENDINGADS', pending_ads_count($config,$_SESSION['user']['id']));
            $page->SetParameter ('HIDDENADS', hidden_ads_count($config,$_SESSION['user']['id']));
            $page->SetParameter ('FAVORITEADS', favorite_ads_count($config,$_SESSION['user']['id']));
            $page->SetParameter ('RESUBMITADS', resubmited_ads_count($config,$_SESSION['user']['id']));
			$page->SetParameter ('OVERALL_FOOTER', create_footer());
			$page->CreatePageEcho();
			exit;
		}
	}
	else
	{
		$sub_types = array();

        $query = "SELECT * FROM `".$config['db']['pre']."subscriptions`";

        $query_result = @mysqli_query ($con,$query) OR error(mysqli_error($con));
        while ($info = @mysqli_fetch_array($query_result))
        {
            $sub_types[$info['sub_id']]['Selected'] = 0;
            $sub_types[$info['sub_id']]['id'] = $info['sub_id'];
            $sub_types[$info['sub_id']]['title'] = $info['sub_title'];
            $sub_types[$info['sub_id']]['recommended'] = $info['recommended'];
            $sub_types[$info['sub_id']]['cost'] = $info['sub_amount'];

            if($info['sub_term'] == 'DAILY')
            {
                $sub_types[$info['sub_id']]['term'] = $lang['DAILY'];
            }
            elseif($info['sub_term'] == 'WEEKLY')
            {
                $sub_types[$info['sub_id']]['term'] = $lang['WEEKLY'];
            }
            elseif($info['sub_term'] == 'MONTHLY')
            {
                $sub_types[$info['sub_id']]['term'] = $lang['MONTHLY'];
            }
            elseif($info['sub_term'] == 'YEARLY')
            {
                $sub_types[$info['sub_id']]['term'] = $lang['YEARLY'];
            }

            $query2 = "SELECT * FROM ".$config['db']['pre']."usergroups where group_id ='".validate_input($info['group_id'])."' LIMIT 1";
            $query_result2 = @mysqli_query ($con,$query2) OR error(mysqli_error($con));
            $info2 = @mysqli_fetch_array($query_result2);

            $sub_types[$info['sub_id']]['duration'] = $info2['ad_duration'];
            $sub_types[$info['sub_id']]['featured_fee'] = $info2['featured_project_fee'];
            $sub_types[$info['sub_id']]['urgent_fee'] = $info2['urgent_project_fee'];
            $sub_types[$info['sub_id']]['highlight_fee'] = $info2['highlight_project_fee'];
            $sub_types[$info['sub_id']]['top_search_result'] = $info2['top_search_result'];
            $sub_types[$info['sub_id']]['show_on_home'] = $info2['show_on_home'];
            $sub_types[$info['sub_id']]['show_in_home_search'] = $info2['show_in_home_search'];
        }

        $page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/membership_plan.tpl');
        $page->SetParameter ('OVERALL_HEADER', create_header($lang['UPGRADES']));
        $page->SetLoop ('SUB_TYPES', $sub_types);
        $page->SetParameter ('OVERALL_FOOTER', create_footer());
        $page->CreatePageEcho();

	}
}
else
{
    headerRedirect($link['LOGIN']);
}
?>