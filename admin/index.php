<?php
require_once('../includes/config.php');
require_once('../includes/functions/func.admin.php');
require_once('../includes/functions/func.users.php');
require_once('../includes/functions/func.sqlquery.php');
require_once('../includes/lang/lang_'.$config['lang'].'.php');
$mysqli = db_connect($config);
admin_session_start();
checkloggedadmin($config);



$total_active_item = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."product` where status = 'active'"));
$total_pending_item = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."product` where status = 'pending'"));
$banned_user = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."user` where status = '2'"));
$total_user = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."user`"));

$query_quickpay=mysqli_query($mysqli,"select * from ".$config['db']['pre']."balance");
$quick_fetch=mysqli_fetch_array($query_quickpay);
$curbalance = $quick_fetch['current_balance'];
$totalearning = $config['currency_sign'] . " " . $quick_fetch['total_earning'];

include('header.php');
?>
<main class="app-layout-content">

    <!-- Page Content -->
    <div class="container-fluid p-y-md">
        <!-- Stats -->
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <a class="card" href="#">
                    <div class="card-block clearfix">
                        <div class="pull-right">
                            <p class="h6 text-muted m-t-0 m-b-xs">Clasificados activos</p>
                            <p class="h3 text-blue m-t-sm m-b-0"><?php echo $total_active_item; ?></p>
                        </div>
                        <div class="pull-left m-r">
                            <span class="img-avatar img-avatar-48 bg-blue bg-inverse"><i class="ion-ios-bell fa-1-5x"></i></span>
                        </div>
                    </div>
                </a>
            </div>
            <!-- .col-sm-6 -->

            <div class="col-sm-6 col-lg-3">
                <a class="card bg-green bg-inverse" href="#">
                    <div class="card-block clearfix">
                        <div class="pull-right">
                            <p class="h6 text-muted m-t-0 m-b-xs">Nuevos clasificados</p>
                            <p class="h3 m-t-sm m-b-0"><?php echo $total_pending_item; ?> nuevos</p>
                        </div>
                        <div class="pull-left m-r">
                            <span class="img-avatar img-avatar-48 bg-gray-light-o"><i class="ion-ios-information fa-1-5x"></i></span>
                        </div>
                    </div>
                </a>
            </div>
            <!-- .col-sm-6 -->
            <div class="col-sm-6 col-lg-3">
                <a class="card bg-purple bg-inverse" href="#">
                    <div class="card-block clearfix">
                        <div class="pull-right">
                            <p class="h6 text-muted m-t-0 m-b-xs">Total de usuarios</p>
                            <p class="h3 m-t-sm m-b-0"><?php echo $total_user; ?></p>
                        </div>
                        <div class="pull-left m-r">
                            <span class="img-avatar img-avatar-48 bg-gray-light-o"><i class="ion-ios-people fa-1-5x"></i></span>
                        </div>
                    </div>
                </a>
            </div>
            <!-- .col-sm-6 -->

            <div class="col-sm-6 col-lg-3">
                <a class="card bg-blue bg-inverse" href="#">
                    <div class="card-block clearfix">
                        <div class="pull-right">
                            <p class="h6 text-muted m-t-0 m-b-xs">Ingresos totales</p>
                            <p class="h3 m-t-sm m-b-0"><?php echo $totalearning; ?></p>
                        </div>
                        <div class="pull-left m-r">
                            <span class="img-avatar img-avatar-48 bg-gray-light-o"><i class="ion-cash fa-1-5x"></i></span>
                        </div>
                    </div>
                </a>
            </div>
            <!-- .col-sm-6 -->


        </div>
        <!-- .row -->
        <!-- End stats -->

        <div class="row">
            <div class="col-lg-8">
                <!-- Transactions history Widget -->
                <div class="card">
                    <div class="card-header">
                        <h4>Estadísticas de publicaciones</h4>
                    </div>
                    <div class="card-block">
                        <div style="height: 238px;"><canvas class="js-chartjs-lines4"></canvas></div>
                    </div>
                </div>
                <!-- .card -->
                <!-- End Transactions history Widget -->
            </div>
            <!-- .col-lg-8 -->

            <div class="col-lg-4">
                <!-- Weekly users Widget -->
                <div class="card">
                    <div class="card-header">
                        <h4>Usuarios semanales</h4>
                    </div>
                    <div class="card-block">
                        <div style="height: 238px;"><canvas class="js-chartjs-bars3"></canvas></div>
                    </div>
                </div>
                <!-- .card -->
                <!-- End Weekly users Widget -->
            </div>
            <!-- .col-lg-4 -->
        </div>
        <!-- .row -->

        <!-- .row -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h4>5 clasificados recientes</h4>
                    </div>
                    <div class="card-block">
                        <?php
                        $getItem = get_items($config,'','',false,1,5);
                        foreach ($getItem as $ads) {
                            $ad_id          = $ads['id'];
                            $ad_title       = $ads['product_name'];
                            $featured       = $ads['featured'];
                            $urgent         = $ads['urgent'];
                            $highlight      = $ads['highlight'];
                            $ad_category    = $ads['category'];
                            $ad_price       = $ads['price'];
                            $ad_location    = $ads['location'];
                            $ad_created_at  = $ads['created_at'];
                            $ad_pic         = $ads['picture'];
                            $ad_link        = $ads['link'];
                            $ad_author      = $ads['username'];
                            $ad_author_link = $ads['author_link'];

                            ?>
                            <div class="pro-list">
                                <div class="pro-img p-r-10">
                                    <a href="#">
                                        <img src="../storage/products/thumb/<?php echo $ad_pic; ?>" alt="<?php echo $ad_title ?>" style="width:75px; height: 75px;">
                                    </a>
                                </div>
                                <div class="pro-detail">
                                    <h5 class="m-t-0 m-b-5">
                                        <a href="post_detail.php?id=<?php echo $ad_id;?>"><?php echo $ad_title; ?></a>
                                    </h5>
                                    <p class="text-muted font-12"><?php echo $ad_created_at; ?> | <?php echo $ad_author; ?></p>
                                </div>
                            </div>

                        <?php } ?>

                        <div class="text-right">
                            <a href="posts.php" class="btn btn-sm btn-rounded btn-info m-t-10">Ver todos</a>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-md-6 col-lg-6 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Registrados recientes</h4>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Fecha</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = "SELECT * FROM `".$config['db']['pre']."user` order by id DESC LIMIT 5";
                                $result = $mysqli->query($query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td class="txt-oflo"><?php echo $row['name']; ?></td>
                                        <td><span class="label label-info label-rounded"><?php echo $row['email']; ?></span> </td>
                                        <td class="txt-oflo"><?php echo timeAgo($row['created_at']); ?></td>
                                    </tr>
                                <?php } ?>


                                </tbody>
                            </table>
                            <a href="users.php">Ver todos los usuarios</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- .container-fluid -->
    <!-- End Page Content -->

</main>

<?php include("footer.php"); ?>

<!--Code For Chart-->
<!-- Page Plugins -->
<script src="assets/js/plugins/slick/slick.min.js"></script>
<script src="assets/js/plugins/chartjs/Chart.min.js"></script>


<?php
$today = date('Y-m-d');
$todayPost = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."product` WHERE created_at like '$today%'"));
$todayUser = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."user` WHERE created_at like '$today%'"));
$minusValue = $today;
$minusValue2 = $today;
$minusValue3 = $today;
$chartPostValue = "'".$todayPost."'";
$chartUserValue = "'".$todayUser."'";
$chartWeeklyLable = "'".date('M-d')."'";
for ($x = 0; $x <= 5; $x++) {
    $minusValue = date('M-d', strtotime('-1 day', strtotime($minusValue)));
    $chartWeeklyLable .= ",'".$minusValue."'";

    $minusValue2 = date('Y-m-d', strtotime('-1 day', strtotime($minusValue2)));
    $rows = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."product` WHERE created_at like '$minusValue2%'"));
    $chartPostValue .= ",'".$rows."'";

    $minusValue3 = date('Y-m-d', strtotime('-1 day', strtotime($minusValue3)));
    $rows = mysqli_num_rows(mysqli_query($mysqli,"select 1 from `".$config['db']['pre']."user` WHERE created_at like '$minusValue3%'"));
    $chartUserValue .= ",'".$rows."'";
}
?>

<script>
    // Lines Chart Data 3
    var $dashChartLinesData3 = {
        labels: [<?php echo $chartWeeklyLable; ?>],
        datasets: [
            {
                label: 'This Week',
                fillColor: App.hexToRgba( App.colors.blue, 50 ),
                pointColor: App.colors.blue,
                data: [<?php echo $chartUserValue; ?>]
            }
        ]
    };

    // Lines Chart Data 4
    var $dashChartLinesData4 = {
        labels: [<?php echo $chartWeeklyLable; ?>],
        datasets: [
            {
                label: 'This Week',
                strokeColor: App.colors.blue,
                pointColor: '#fff',
                pointStrokeColor: App.colors.blue,
                data: [<?php echo $chartPostValue; ?>]
            }
        ]
    };
</script>
<!--Code For Chart-->
<!-- Page JS Code -->
<script src="assets/js/pages/index.js"></script>
<script>
    $(function()
    {
        // Init page helpers (Slick Slider plugin)
        App.initHelpers('slick');
    });
</script>

</body>

</html>