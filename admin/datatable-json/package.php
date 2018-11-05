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
    0 =>'group_id',
    1 =>'group_name',
    2 =>'group_removable'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if( !empty($params['search']['value']) ) {
    $where .=" WHERE ";
    $where .=" ( group_name LIKE '".$params['search']['value']."%' )";
}

// getting total number records without any search
$sql = "SELECT * FROM `".$config['db']['pre']."usergroups` ";
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
    $id = $row['group_id'];
    $name = $row['group_name'];
    $removable = $row['group_removable'];
    if($removable == '1'){
        $removable = '<span class="label label-info">YES</span>';
    }
    else{
        $removable = '<span class="label label-warning">NO</span>';
    }
    $row0 = '<td>
                <label class="css-input css-checkbox css-checkbox-default">
                    <input type="checkbox" class="service-checker" value="'.$id.'" id="row_'.$id.'" name="row_'.$id.'"><span></span>
                </label>
            </td>';
    $row1 = '<td>'.$name.'</td>';
    $row2 = '<td>'.$removable.'</td>';
    $row3 = '<td class="text-center">
                <div class="btn-group">
                    <a href="#" data-url="panel/package_edit.php?id='.$id.'" data-toggle="slidePanel" class="btn btn-xs btn-default"> <i class="ion-edit"></i> Edit</a>
                </div>
            </td>';

    $value = array(
        "DT_RowId" => $id,
        0 => $row0,
        1 => $row1,
        2 => $row2,
        3 => $row3
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
