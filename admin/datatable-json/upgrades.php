<?php
/*
Copyright (c) 2015 Devendra Katariya (bylancer.com)
*/
require_once('../../includes/config.php');
require_once('../../includes/functions/func.admin.php');
require_once('../../includes/lang/lang_'.$config['lang'].'.php');


admin_session_start();
$con = db_connect($config);

// initilize all variable
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;

//define index of column
$columns = array(
    0 =>'upgrade_id',
    1 =>'sub_id',
    2 =>'user_id'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( upgrade_id LIKE '".$params['search']['value']."%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."upgrades` ";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if(isset($where) && $where != '') {

    $sqlTot .= $where;
    $sqlRec .= $where;
}


$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

$queryTot = mysqli_query($con, $sqlTot) or die("database error:". mysqli_error($con));
$totalRecords = mysqli_num_rows($queryTot);

$queryRecords = mysqli_query($con, $sqlRec) or die("error to fetch users data");

//iterate on results row and create new index array of data
while( $row = mysqli_fetch_array($queryRecords) ) {
    //$data[] = $row;
    $id = $row['upgrade_id'];
    $start_date = date("d-m-Y",$row['upgrade_lasttime']);
    $end_date = date("d-m-Y",$row['upgrade_expires']);

    $username = 'Removed';
   $user_info = mysqli_fetch_row(mysqli_query($con,"SELECT username FROM ".$config['db']['pre']."user WHERE id='".addslashes($row['user_id'])."' LIMIT 1"));
    $username = $user_info[0];

    $sub_info = mysqli_fetch_array(mysqli_query($con,"SELECT sub_title,sub_term FROM ".$config['db']['pre']."subscriptions WHERE sub_id='".addslashes($row['sub_id'])."' LIMIT 1"));
    $sub_title = stripslashes($sub_info['sub_title']);
    $sub_term = stripslashes($sub_info['sub_term']);
    if($sub_term == 'DAILY')
    {
        $term = $lang['DAILY'];
    }
    elseif($sub_term == 'WEEKLY') {
        $term = $lang['WEEKLY'];
    }
    elseif($sub_term == 'MONTHLY')
    {
        $term = $lang['MONTHLY'];
    }
    elseif($sub_term == 'YEARLY')
    {
        $term = $lang['YEARLY'];
    }

    $row0 = '<td>
                <label class="css-input css-checkbox css-checkbox-default">
                    <input type="checkbox" class="service-checker" value="'.$id.'" id="row_'.$id.'" name="row_'.$id.'"><span></span>
                </label>
            </td>';
    $row1 = '<td><a href="'.$config['site_url'].'profile/'.$username.'" target="_blank">'.$username.'</a></td>';
    $row2 = '<td>'.$sub_title.'</td>';
    $row3 = '<td>'.$term.'</td>';
    $row4 = '<td>'.$start_date.' / '.$end_date.'</td>';

    $value = array(
        "DT_RowId" => $id,
        0 => $row0,
        1 => $row1,
        2 => $row2,
        3 => $row3,
        4 => $row4
    );
    $data[] = $value;
}

$json_data = array(
    "draw"            => intval( $params['draw'] ),
    "recordsTotal"    => intval( $totalRecords ),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
?>
