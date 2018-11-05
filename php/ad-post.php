<?php
require_once('includes/config.php');
require_once('includes/classes/class.template_engine.php');
require_once('includes/classes/class.country.php');
require_once('includes/functions/func.global.php');
require_once('includes/functions/func.users.php');
require_once('includes/functions/func.sqlquery.php');
if(isset($_GET['lang'])) {
    if ($_GET['lang'] != ""){
        change_user_lang($_GET['lang']);
    }
}
require_once('includes/lang/lang_'.$config['lang'].'.php');
require_once('includes/seo-url.php');
require_once('plugins/watermark/watermark.php');

$mysqli = db_connect($config);
sec_session_start();

if($config['recaptcha_mode']== 1){
    $recaptcha_public_key = $config['recaptcha_public_key'];
}
else{
    $recaptcha_public_key = "";
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == "post_ad") {
        ajax_post_advertise($mysqli, $config, $lang, $link);
    }
}
function ajax_post_advertise($con,$config,$lang,$link)
{
    if(isset($_POST['submit'])) {

        $errors = array();
        $item_screen = "";
        
        if($config['recaptcha_mode'] == 1){
            if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                //your site secret key
                $secret = $config['recaptcha_private_key'];
                //get verify response data
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                if ($responseData->success) {
                    $recaptcha_responce = true;
                }else{
                    $recaptcha_responce = false;
                    $recaptcha_error = $lang['RECAPTCHA_ERROR'];
                }
            }else{
                $recaptcha_responce = false;
                $recaptcha_error = $lang['RECAPTCHA_CLICK'];
            }
        }else{
            $recaptcha_responce = true;
        }

        if (empty($_POST['subcatid']) or empty($_POST['catid'])) {
            $errors[]['message'] = $lang['CAT_REQ'];
        }
        if (empty($_POST['title'])) {
            $errors[]['message'] = $lang['ADTITLE_REQ'];
        }
        if (empty($_POST['content'])) {
            $errors[]['message'] = $lang['DESC_REQ'];
        }
        if (empty($_POST['city'])) {
            $errors[]['message'] = $lang['CITY_REQ'];
        }
        if (!empty($_POST['price'])) {
            if (!is_numeric($_POST['price'])) {
                $errors[]['message'] = $lang['PRICE_MUST_NO'];
            }
        }
        if (!$recaptcha_responce) {
            $errors[]['message'] = $lang['RECAPTCHA_ERROR'];
        }
        
        /*IF : USER NOT LOGIN THEN CHECK SELLER INFORMATION*/
        if (!checkloggedin($config)) {
            if(isset($_POST['seller_name'])){
                $seller_name = $_POST['seller_name'];
                if (empty($seller_name)) {
                    $errors[]['message'] = $lang['SELLER_NAME_REQ'];
                } else {
                    if (preg_match('/[^A-Za-z\s]/', $seller_name)) {
                        $errors[]['message'] = $lang['SELLER_NAME'] . " : " . $lang['ONLY_LETTER_SPACE'];
                    } elseif ((strlen($seller_name) < 4) OR (strlen($seller_name) > 21)) {
                        $errors[]['message'] = $lang['SELLER_NAME'] . " : " . $lang['NAMELEN'];
                    }
                }
            }else{
                $errors[]['message'] = $lang['SELLER_NAME_REQ'];
            }

            if(isset($_POST['seller_email'])){
                $seller_email = $_POST['seller_email'];

                if (empty($seller_email)) {
                    $errors[]['message'] = $lang['SELLER_EMAIL_REQ'];
                } else {
                    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
                    if (!preg_match($regex, $seller_email)) {
                        $errors[]['message'] = $lang['SELLER_EMAIL'] . " : " . $lang['EMAILINV'];
                    }
                }
            }else{
                $errors[]['message'] = $lang['SELLER_EMAIL_REQ'];
            }



        }
        /*IF : USER NOT LOGIN THEN CHECK SELLER INFORMATION*/

        /*IF : USER GO TO PEMIUM POST*/
        $urgent = isset($_POST['urgent']) ? 1 : 0;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $highlight = isset($_POST['highlight']) ? 1 : 0;

        /*$payment_req = "";
        if (isset($_POST['urgent'])) {
            if (!isset($_POST['payment_id'])) {
                $payment_req = $lang['PAYMENT_METHOD_REQ'];
            }
        }
        if (isset($_POST['featured'])) {
            if (!isset($_POST['payment_id'])) {
                $payment_req = $lang['PAYMENT_METHOD_REQ'];
            }
        }
        if (isset($_POST['highlight'])) {
            if (!isset($_POST['payment_id'])) {
                $payment_req = $lang['PAYMENT_METHOD_REQ'];
            }
        }
        if (!empty($payment_req))
            $errors[]['message'] = $payment_req;*/

        /*IF : USER GO TO PEMIUM POST*/

        if (!count($errors) > 0) {
            if (isset($_POST['item_screen']) && count($_POST['item_screen']) > 0) {
                $valid_formats = array("jpg", "jpeg", "png"); // Valid image formats
                $countScreen = 0;
                foreach ($_POST['item_screen'] as $name) {
                    $filename = stripslashes($name);
                    $ext = getExtension($filename);
                    $ext = strtolower($ext);
                    if (!empty($filename)) {
                        //File extension check
                        if (in_array($ext, $valid_formats)) {
                            //Valid File extension check

                        } else {
                            $errors[]['message'] = $lang['ONLY_JPG_ALLOW'];
                        }
                        if ($countScreen == 0)
                            $item_screen = $filename;
                        elseif ($countScreen >= 1)
                            $item_screen = $item_screen . "," . $filename;
                        $countScreen++;
                    }
                }
            }
        }


        if (!count($errors) > 0) {

            if (!checkloggedin($config)) {
                $seller_name = $_POST['seller_name'];
                $seller_email = $_POST['seller_email'];

                $user_count = check_account_exists($config, $seller_email);
                if ($user_count > 0) {
                    $seller_username = get_username_by_email($config, $seller_email);

                    $json = '{"status" : "email-exist","errors" : "' . $lang['ACCAEXIST'] . '","email" : "' . $seller_email . '","username" : "' . $seller_username . '"}';
                    echo $json;
                    die();
                } else {
                    /*Create user account with givern email id*/
                    $created_username = str_replace(' ', '', $seller_name);
                    //mysql query to select field username if it's equal to the username that we check '
                    $sql = "select username from " . $config['db']['pre'] . "user where username = '" . $created_username . "'";
                    $result = mysqli_query(db_connect($config), $sql);

                    //if number of rows fields is bigger them 0 that means it's NOT available '
                    if (mysqli_num_rows($result) > 0) {
                        $username = createusernameslug($config, $created_username);
                    } else {
                        $username = $created_username;
                    }

                    $confirm_id = get_random_id();
                    $password = get_random_id();
                    $pass_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 13]);

                    // Insert user data
                    $query = "INSERT INTO " . $config['db']['pre'] . "user SET
                    status = '1',
                    name = '".validate_input($seller_name)."',
                    username = '".validate_input($username)."',
                    email = '" . validate_input($seller_email) . "',
                    password_hash='" . $pass_hash . "',
                    confirm='" . $confirm_id . "',
                    created_at = '" . date("Y-m-d H:i:s") . "',
                    updated_at = '" . date("Y-m-d H:i:s") . "'";

                    $con->query($query) OR error(mysqli_error($con));

                    $user_id = $con->insert_id;

                    /*CREATE ACCOUNT CONFIRMATION EMAIL*/
                    email_template("signup_confirm",$user_id);

                    /*SEND ACCOUNT DETAILS EMAIL*/
                    email_template("signup_details",$user_id,$password);

                    $loggedin = userlogin($config, $username, $password);
                    create_user_session($loggedin['id'], $loggedin['username'], $loggedin['password']);

                }
            }

            if (checkloggedin($config)) {

                $price = $_POST['price'];
                $phone = $_POST['phone'];
                $price = isset($_POST['price']) ? $_POST['price'] : 0;
                $phone = isset($_POST['phone']) ? $_POST['phone'] : 0;

                if(empty($_POST['price'])){
                    $price = 0;
                }

                $negotiable = isset($_POST['negotiable']) ? 1 : 0;
                $hide_phone = isset($_POST['hide_phone']) ? 1 : 0;
                $cityid = $_POST['city'];

                if($config['post_desc_editor'] == 1)
                    $description = addslashes($_POST['content']);
                else
                    $description = validate_input($_POST['content'],true);


                $citydata = get_cityDetail_by_id($config, $cityid);
                $country = $citydata['country_code'];
                $state = $citydata['subadmin1_code'];

                if(isset($_POST['location'])){
                    $location = $_POST['location'];
                }else{
                    $location = '';
                }
                $mapLat = $_POST['latitude'];
                $mapLong = $_POST['longitude'];
                $latlong = $mapLat . "," . $mapLong;
                $slug = create_post_slug($config,$_POST['title']);

                if(isset($_POST['tags'])){
                    $tags = $_POST['tags'];
                }else{
                    $tags = '';
                }

                if($config['post_auto_approve'] == 1){
                    $status = "active";
                }else{
                    $status = "pending";
                }

                // Get usergroup details
                $group_id = get_user_group($con);
                // Get membership details
                $group_get_info = get_usergroup_settings($group_id,$con);


                $urgent_project_fee = $group_get_info['urgent_project_fee'];
                $featured_project_fee = $group_get_info['featured_project_fee'];
                $highlight_project_fee = $group_get_info['highlight_project_fee'];

                $ad_duration = $group_get_info['ad_duration'];
                $timenow = date('Y-m-d H:i:s');
                $expire_time = date('Y-m-d H:i:s', strtotime($timenow . ' +'.$ad_duration.' day'));
                $expire_timestamp = strtotime($expire_time);

                $sql = "INSERT INTO " . $config['db']['pre'] . "product set
            user_id = '" . $_SESSION['user']['id'] . "',
            product_name = '" . validate_input($_POST['title']) . "',
            slug = '" . validate_input($slug) . "',
            status = '" . $status . "',
            category = '" . validate_input($_POST['catid']) . "',
            sub_category = '" . validate_input($_POST['subcatid']) . "',
            description = '" . $description . "',
            price = '" . $price . "',
            negotiable = '" . $negotiable . "',
            phone = '" . validate_input($phone) . "',
            hide_phone = '" . $hide_phone . "',
            location = '" . validate_input($location) . "',
            city = '" . validate_input($_POST['city']) . "',
            state = '" . $state . "',
            country = '" . $country . "',
            latlong = '$latlong',
            screen_shot = '" . $item_screen . "',
            tag = '" . validate_input($tags) . "',
            created_at = '$timenow',
            expire_date = '$expire_timestamp'
            ";

                mysqli_query($con, $sql) OR error(mysqli_error($con));
                $product_id = $con->insert_id;
                add_post_customField_data($_POST['catid'], $_POST['subcatid'],$product_id);

                $amount = 0;
                $trans_desc = "Make Ad ";



                $premium_tpl = "";

                if ($featured == 1) {
                    $amount = $featured_project_fee;
                    $trans_desc = $trans_desc . " Featured ";
                    $premium_tpl .= ' <div class="ModalPayment-paymentDetails">
                                            <div class="ModalPayment-label">'.$lang['FEATURED'].'</div>
                                            <div class="ModalPayment-price">
                                                <span class="ModalPayment-totalCost-price">'.$config['currency_sign'].$featured_project_fee.'</span>
                                            </div>
                                        </div>';
                }
                if ($urgent == 1) {
                    $amount = $amount + $urgent_project_fee;
                    $trans_desc = $trans_desc . " Urgent ";
                    $premium_tpl .= ' <div class="ModalPayment-paymentDetails">
                                            <div class="ModalPayment-label">'.$lang['URGENT'].'</div>
                                            <div class="ModalPayment-price">
                                                <span class="ModalPayment-totalCost-price">'.$config['currency_sign'].$urgent_project_fee.'</span>
                                            </div>
                                        </div>';
                }
                if ($highlight == 1) {
                    $amount = $amount + $highlight_project_fee;
                    $trans_desc = $trans_desc . " Highlight ";
                    $premium_tpl .= ' <div class="ModalPayment-paymentDetails">
                                            <div class="ModalPayment-label">'.$lang['HIGHLIGHT'].'</div>
                                            <div class="ModalPayment-price">
                                                <span class="ModalPayment-totalCost-price">'.$config['currency_sign'].$highlight_project_fee.'</span>
                                            </div>
                                        </div>';
                }

                if ($amount > 0) {
                    $premium_tpl .= '<div class="ModalPayment-totalCost">
                                            <span class="ModalPayment-totalCost-label">'.$lang['TOTAL'].': </span>
                                            <span class="ModalPayment-totalCost-price">'.$config['currency_sign'].$amount." ".$config['currency_code'].'</span>
                                        </div>';

                    /*These details save in session and get on payment sucecess*/
                    $title = $_POST['title'];
                    $payment_type = "premium";
                    $access_token = uniqid();

                    $_SESSION['quickad'][$access_token]['name'] = $title;
                    $_SESSION['quickad'][$access_token]['amount'] = $amount;
                    $_SESSION['quickad'][$access_token]['payment_type'] = $payment_type;
                    $_SESSION['quickad'][$access_token]['trans_desc'] = $trans_desc;
                    $_SESSION['quickad'][$access_token]['product_id'] = $product_id;
                    $_SESSION['quickad'][$access_token]['featured'] = $featured;
                    $_SESSION['quickad'][$access_token]['urgent'] = $urgent;
                    $_SESSION['quickad'][$access_token]['highlight'] = $highlight;
                    /*End These details save in session and get on payment sucecess*/

                    $url = $link['PAYMENT']."/" . $access_token;
                    $response = array();
                    $response['status'] = "success";
                    $response['ad_type'] = "package";
                    $response['redirect'] = $url;
                    $response['tpl'] = $premium_tpl;

                    echo json_encode($response, JSON_UNESCAPED_SLASHES);
                    die();
                } else {
                    unset($_POST);
                    $ad_link = $link['AD-DETAIL'] . "/" . $product_id;

                    $json = '{"status" : "success","ad_type" : "free","redirect" : "' . $ad_link . '"}';
                    echo $json;
                    die();
                }
            } else {
                $status = "error";
                $errors[]['message'] = $lang['POST_SAVE_ERROR'];
            }


        } else {
            $status = "error";
        }

        $json = '{"status" : "' . $status . '","errors" : ' . json_encode($errors, JSON_UNESCAPED_SLASHES) . '}';
        echo $json;
        die();
    }
}


if(isset($_GET['country'])) {
    if ($_GET['country'] != ""){
        change_user_country($config,$_GET['country']);
    }
}

$country_code = check_user_country($config);

$currency_info = set_user_currency($config,$country_code);
$currency_sign = $currency_info['html_entity'];

if($latlong = get_lat_long_of_country($config,$country_code)){
    $mapLat     =  $latlong['lat'];
    $mapLong    =  $latlong['lng'];
}else{
    $mapLat     =  get_option($config,"home_map_latitude");
    $mapLong    =  get_option($config,"home_map_longitude");
}

$mapLat     =  get_option($config,"home_map_latitude");
$mapLong    =  get_option($config,"home_map_longitude");

$custom_fields = get_customFields_by_catid($config,$mysqli);


// Output to template
$page = new HtmlTemplate ('templates/' . $config['tpl_name'] . '/ad-post.tpl');
$page->SetParameter ('OVERALL_HEADER', create_header($lang['POST-AD']));
$page->SetLoop ('HTMLPAGE', get_html_pages($config));
$page->SetLoop ('COUNTRYLIST',get_country_list($config));
$page->SetLoop ('CATEGORY',get_maincategory($config));
$page->SetLoop ('CUSTOMFIELDS',$custom_fields);
$page->SetParameter ('SHOWCUSTOMFIELD', (count($custom_fields) > 0) ? 1 : 0);
$page->SetParameter ('LATITUDE', $mapLat);
$page->SetParameter ('LONGITUDE', $mapLong);
$page->SetParameter ('USER_COUNTRY', strtolower($country_code));
$page->SetParameter ('USER_CURRENCY_SIGN', $currency_sign);
$page->SetParameter ('PAGE_TITLE', $lang['POST-AD']);
$page->SetParameter ('RECAPTCHA_PUBLIC_KEY', $recaptcha_public_key);

if(checkloggedin($config)) {
    // Get usergroup details
    $group_id = get_user_group($mysqli);
    if($group_id > 0) {
        // Get membership details
        $group_get_info = get_usergroup_settings($group_id,$mysqli);

        $urgent_project_fee = $group_get_info['urgent_project_fee'];
        $featured_project_fee = $group_get_info['featured_project_fee'];
        $highlight_project_fee = $group_get_info['highlight_project_fee'];
    }else{
        $urgent_project_fee = $config['urgent_fee'];
        $featured_project_fee = $config['featured_fee'];
        $highlight_project_fee = $config['highlight_fee'];
    }

    $page->SetParameter('FEATURED_FEE', $featured_project_fee);
    $page->SetParameter('URGENT_FEE', $urgent_project_fee);
    $page->SetParameter('HIGHLIGHT_FEE', $highlight_project_fee);

}
$page->SetParameter('LANGUAGE_DIRECTION', get_current_lang_direction($config));
$page->SetParameter ('OVERALL_FOOTER', create_footer());
$page->CreatePageEcho();
?>