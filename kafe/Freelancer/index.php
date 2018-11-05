<?php
//Check if init.php exists
if (!file_exists('../core/init.php')) {
    header('Location: ../install/');
    exit;
} else {
    require_once '../core/init.php';
}

//Start new Freelancer object
$freelancer = new Freelancer();

//Check if Freelancer is logged in
if (!$freelancer->isLoggedIn()) {
    Redirect::to('../index.php');
}

membership($freelancer->data()->freelancerid);
?>
<!DOCTYPE html>
<html lang="en-US" class="no-js">

    <!-- Include header.php. Contains header content. -->
    <?php include ('template/header.php'); ?> 
    <!-- Panel CSS -->
    <link href="../assets/css/AdminLTE/AdminLTE.min.css" rel="stylesheet" type="text/css" />	     
    <style>
        .panel.panel-dashboard .btn {
            width: 100%;
            margin: 5px 0px;
        }
    </style>
    <body class="skin-green sidebar-mini">

        <!-- ==============================================
        Wrapper Section
        =============================================== -->
        <div class="wrapper">

            <!-- Include navigation.php. Contains navigation content. -->
            <?php include ('template/navigation.php'); ?> 
            <!-- Include sidenav.php. Contains sidebar content. -->
            <?php include ('template/sidenav.php'); ?> 

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1><?php echo $lang['dashboard']; ?></h1>
                    <ol class="breadcrumb">
                        <li><a href="index.php"><i class="fa fa-dashboard"></i> <?php echo $lang['home']; ?></a></li>
                        <li class="active"><?php echo $lang['dashboard']; ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <section class="panel panel-default panel-dashboard">
                                <header class="panel-heading font-bold"><?php echo $lang['sidenav_header_2']; ?></header>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <a href="joblist.php" class="btn btn-success btn-lg"><i class="fa fa-list-ul"></i> <?php echo $lang['clasificados']; ?></a>
                                        </div>
                                        <div class="col-xs-12 col-md-6">
                                            <a href="jobinvite.php" class="btn btn-success btn-lg"><i class="fa fa-filter"></i> <?php echo $lang['invitaciones_trabajo']; ?></a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <a href="proposallist.php" class="btn btn-success btn-lg"><i class="fa fa-files-o"></i> <?php echo $lang['proposals']; ?></a>
                                        </div>
                                        <div class="col-xs-12 col-md-6">
                                            <a href="jobassigned.php" class="btn btn-success btn-lg"><i class="fa fa-address-card"></i> <?php echo $lang['clasificados_asignados']; ?></a>
                                        </div>
                                    </div>
                                </div> 
                            </section>
                        </div><!-- /.col-lg-8 -->
                        <div class="col-xs-12 col-md-6">
                            <section class="panel panel-default panel-dashboard">
                                <header class="panel-heading font-bold">&nbsp;</header>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <a href="portfoliolist.php" class="btn btn-success btn-lg"><i class="fa fa-briefcase"></i> <?php echo $lang['galeria']; ?></a>
                                        </div>
                                        <div class="col-xs-12 col-md-6">
                                            <a href="addportfolio.php" class="btn btn-success btn-lg"><i class="fa fa-plus-circle"></i> <?php echo $lang['agregar_portafolio']; ?></a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <a href="servicelist.php" class="btn btn-success btn-lg"><i class="fa fa-files-o"></i> <?php echo $lang['lista_de_servicios']; ?></a>
                                        </div>
                                        <div class="col-xs-12 col-md-6">
                                            <a href="addservice.php" class="btn btn-success btn-lg"><i class="fa fa-plus-circle"></i> <?php echo $lang['agregar_servicio']; ?></a>
                                        </div>
                                    </div>
                                </div> 
                            </section>
                        </div><!-- /.col-lg-8 -->
                    </div><!-- /.row -->
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <section class="panel panel-default panel-dashboard">
                                <header class="panel-heading font-bold"><?php echo $lang['sidenav_header_3']; ?></header>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <a href="overview.php?a=profile" class="btn btn-success btn-lg"><i class="fa fa-info-circle"></i> <?php echo $lang['mi_perfil']; ?></a>
                                        </div>
                                    </div>
                                </div> 
                            </section>
                        </div><!-- /.col-lg-8 -->
                    </div><!-- /.row -->
                    <!-- Include currency.php. Contains header content. -->
                    <?php /* include ('template/currency.php'); */ ?> 
                    <?php /*
                      <!-- Info boxes -->
                      <div class="row">
                      <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                      <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
                      <div class="info-box-content">
                      <span class="info-box-text">Today</span>
                      <span class="info-box-number small">
                      <script type="text/javascript" language="JavaScript">
                      var calendarDate = getCalendarDate();
                      document.write(calendarDate);
                      </script>

                      <form name="clock" disabled="disabled">
                      <input type="submit" class="trans" name="face" value="">
                      </form>
                      <style type="text/css">
                      input[name=face] {
                      pointer-events: none;
                      tab-index: -1;
                      }
                      </style>
                      </span>
                      </div><!-- /.info-box-content -->
                      </div><!-- /.info-box -->
                      </div><!-- /.col -->
                      <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                      <span class="info-box-icon bg-olive"><i class="fa fa-align-left"></i></span>
                      <div class="info-box-content">
                      <span class="info-box-text"><?php echo $lang['jobs']; ?> <?php echo $lang['available']; ?></span>
                      <span class="info-box-number small">
                      <?php
                      $query = DB::getInstance()->get("job", "*", ["AND" => ["active" => 1, "delete_remove" => 0, "public" => 1, "invite" => 0, "completed" => 0]]);
                      echo $query->count();
                      ?>
                      </span>
                      </div><!-- /.info-box-content -->
                      </div><!-- /.info-box -->
                      </div><!-- /.col -->
                      <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                      <span class="info-box-icon bg-mint"><i class="fa fa-filter"></i></span>
                      <div class="info-box-content">
                      <span class="info-box-text"><?php echo $lang['jobs']; ?> <?php echo $lang['invites']; ?></span>
                      <span class="info-box-number">
                      <?php
                      $query = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "invite" => 1]]);
                      echo $query->count();
                      ?>
                      </span>
                      </div><!-- /.info-box-content -->
                      </div><!-- /.info-box -->
                      </div><!-- /.col -->

                      <!-- fix for small devices only -->
                      <div class="clearfix visible-sm-block"></div>

                      <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                      <span class="info-box-icon bg-olive"><i class="fa fa-users"></i></span>
                      <div class="info-box-content">
                      <span class="info-box-text"><?php echo $lang['membership']; ?></span>
                      <span class="info-box-number">
                      <?php
                      $query = DB::getInstance()->get("membership_freelancer", "*", ["membershipid" => $freelancer->data()->membershipid]);
                      if ($query->count() === 1) {
                      $q1 = DB::getInstance()->get("membership_freelancer", "*", ["membershipid" => $freelancer->data()->membershipid]);
                      } else {
                      $q1 = DB::getInstance()->get("membership_agency", "*", ["membershipid" => $freelancer->data()->membershipid]);
                      }
                      if ($q1->count()) {
                      foreach ($q1->results() as $r1) {
                      echo $name = $r1->name . '<br/>';
                      }
                      }

                      $timestart = $freelancer->data()->membership_date;
                      $timenow = date("F j, Y, g:i a");
                      $timeend = $timestart . ' + 30 days';
                      echo datediff($timenow, $timeend) . '&nbsp;';
                      echo $lang['days'] . '&nbsp;';
                      echo $lang['left'];
                      ?></span>
                      </div><!-- /.info-box-content -->
                      </div><!-- /.info-box -->
                      </div><!-- /.col -->
                      </div><!-- /.row -->

                      <!-- Info boxes -->
                      <div class="row">
                      <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                      <span class="info-box-icon bg-olive"><i class="fa fa-align-right"></i></span>
                      <div class="info-box-content">
                      <span class="info-box-text"><?php echo $lang['jobs']; ?> <?php echo $lang['completed']; ?></span>
                      <span class="info-box-number">
                      <?php
                      $query = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "completed" => 1]]);
                      echo $query->count();
                      ?>
                      </span>
                      </div><!-- /.info-box-content -->
                      </div><!-- /.info-box -->
                      </div><!-- /.col -->
                      <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                      <span class="info-box-icon bg-mint"><i class="fa fa-money"></i></span>
                      <div class="info-box-content">
                      <span class="info-box-text"><?php echo $lang['payments']; ?> <?php echo $lang['paid']; ?></span>
                      <span class="info-box-number">
                      <?php
                      echo $currency_symbol . '&nbsp;';
                      $query = DB::getInstance()->sum("transactions", "payment", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "complete" => 1]]);
                      foreach ($query->results()[0] as $row) {
                      echo $row;
                      }
                      ?></span>
                      </div><!-- /.info-box-content -->
                      </div><!-- /.info-box -->
                      </div><!-- /.col -->
                      <div class="col-md-3 col-sm-6 col-xs-12">
                      <div class="info-box">
                      <span class="info-box-icon bg-mint"><i class="fa fa-money"></i></span>
                      <div class="info-box-content">
                      <span class="info-box-text"><?php echo $lang['payments']; ?> <?php echo $lang['received']; ?></span>
                      <span class="info-box-number">
                      <?php
                      $query = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "invite" => "0", "delete_remove" => 0, "accepted" => 1]]);
                      if ($query->count()) {
                      foreach ($query->results() as $row) {


                      $q1 = DB::getInstance()->get("milestone", "*", ["AND" => ["jobid" => $row->jobid]]);
                      if ($q1->count()) {
                      foreach ($q1->results() as $r1) {

                      $query = DB::getInstance()->sum("transactions", "payment", ["AND" => ["membershipid" => $r1->id, "freelancerid" => $r1->clientid, "complete" => 1]]);
                      foreach ($query->results()[0] as $payy) {
                      $paj[] = $payy;
                      }
                      }
                      }
                      }
                      }
                      echo $currency_symbol . '&nbsp;';
                      echo array_sum($paj);
                      ?>
                      </span>
                      </div><!-- /.info-box-content -->
                      </div><!-- /.info-box -->
                      </div><!-- /.col -->

                      <!-- fix for small devices only -->
                      <div class="clearfix visible-sm-block"></div>

                      </div><!-- /.row -->

                      <div class="row">
                      <div class="col-lg-8">
                      <section class="panel panel-default">
                      <header class="panel-heading font-bold"><?php echo $lang['jobs']; ?> <?php echo $lang['awarded']; ?></header>
                      <div class="panel-body">


                      <div class="table-responsive">
                      <table  class="table table-bordered table-striped">
                      <thead>
                      <tr>
                      <th><?php echo $lang['title']; ?></th>
                      <th><?php echo $lang['progress']; ?></th>
                      <th><?php echo $lang['date']; ?> <?php echo $lang['added']; ?></th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php
                      $query = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "invite" => "0", "delete_remove" => 0, "accepted" => 1], "LIMIT" => 4, "ORDER" => "date_added DESC"]);
                      if ($query->count()) {
                      foreach ($query->results() as $row) {

                      echo '<tr>';
                      echo '<td><a href="jobboard.php?a=overview&id=' . escape($row->jobid) . '">' . escape($row->title) . '</a></td>';
                      echo '<td><span class="label label-success"> ' . $lang['on'] . ' ' . $lang['progress'] . ' </span></td>';

                      echo '<td>' . escape(strftime("%b %d, %Y, %H : %M %p ", strtotime($row->date_added))) . '</td>';
                      echo '</tr>';
                      }
                      } else {
                      echo $lang['no_results'];
                      }
                      ?>
                      </tbody>
                      <tfoot>
                      <tr>
                      <th><?php echo $lang['title']; ?></th>
                      <th><?php echo $lang['progress']; ?></th>
                      <th><?php echo $lang['date']; ?> <?php echo $lang['added']; ?></th>
                      </tr>
                      </tfoot>
                      </table>
                      </div><!-- /.table-responsive -->

                      </div>
                      <footer class="panel-footer bg-white no-padder">
                      <div class="row text-center no-gutter">
                      <div class="col-xs-3 b-r b-light">
                      <span class="h4 font-bold m-t block">
                      <?php
                      $query = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "completed" => 1]]);
                      echo $query->count();
                      ?>
                      </span>
                      <small class="text-muted m-b block"><?php echo $lang['completed']; ?> <?php echo $lang['jobs']; ?></small>
                      </div>
                      <div class="col-xs-3 b-r b-light">
                      <span class="h4 font-bold m-t block">
                      <?php
                      $q1 = DB::getInstance()->get("message", "*", ["AND" => ["user_to" => $freelancer->data()->freelancerid, "opened" => 0, "delete_remove" => 0, "disc" => 0]]);
                      echo $q1->count();
                      ?>
                      </span> <small class="text-muted m-b block"><?php echo $lang['unread']; ?> <?php echo $lang['messages']; ?></small>
                      </div>
                      <div class="col-xs-3 b-r b-light">
                      <span class="h4 font-bold m-t block">
                      <?php
                      $q1 = DB::getInstance()->get("proposal", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid]]);
                      echo $q1->count();
                      ?>
                      </span> <small class="text-muted m-b block"><?php echo $lang['your']; ?> <?php echo $lang['proposals']; ?></small>
                      </div>
                      <div class="col-xs-3">
                      <span class="h4 font-bold m-t block">
                      <?php
                      $query = DB::getInstance()->get("transactions", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "complete" => 1]]);
                      echo $query->count();
                      ?>
                      </span> <small class="text-muted m-b block"><?php echo $lang['payments']; ?> </small>
                      </div>
                      </div>
                      </footer>
                      </section>
                      </div><!-- /.col-lg-8 -->

                      <div class="col-lg-4">
                      <section class="panel panel-default">
                      <header class="panel-heading"><?php echo $lang['payments']; ?> </header>
                      <div class="panel-body text-center">
                      <?php
                      $query = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "invite" => "0", "delete_remove" => 0, "accepted" => 1]]);
                      if ($query->count()) {
                      foreach ($query->results() as $row) {

                      $q1 = DB::getInstance()->sum("proposal", "budget", ["AND" => ["jobid" => $row->jobid, "freelancerid" => $freelancer->data()->freelancerid]]);
                      foreach ($q1->results()[0] as $r1) {
                      $r[] = $r1;
                      }

                      $q2 = DB::getInstance()->get("milestone", "*", ["AND" => ["jobid" => $row->jobid]]);
                      if ($q2->count()) {
                      foreach ($q2->results() as $r2) {

                      $query = DB::getInstance()->sum("transactions", "payment", ["AND" => ["membershipid" => $r2->id, "freelancerid" => $r2->clientid, "complete" => 1]]);
                      foreach ($query->results()[0] as $pay) {
                      $pa[] = $pay;
                      }
                      }
                      }
                      }
                      }

                      $outstanding = array_sum($r) - array_sum($pa);
                      $percentage = array_sum($pa) / array_sum($r) * 100;
                      $percentage = round($percentage, 1);
                      ?>
                      <h4><small> <?php echo $lang['payments']; ?> <?php echo $lang['received']; ?> : </small><?php echo $currency_symbol; ?> <?php echo array_sum($pa); ?></h4>
                      <small class="text-muted block"> <?php echo $lang['outstanding']; ?> <?php echo $lang['payments']; ?> : <?php echo $currency_symbol; ?> <?php echo $outstanding; ?> </small>
                      <div class="inline">
                      <input class="knob knob-front" data-angleOffset="90" data-linecap="round" value="<?php echo $percentage; ?>" style=""/>

                      </div>
                      </div>
                      <div class="panel-footer">
                      <small><?php echo $lang['outstanding']; ?> <?php echo $lang['payments']; ?>:
                      <strong><?php echo $currency_symbol; ?> <?php echo $outstanding; ?></strong>
                      </small>
                      </div>
                      </section>
                      </div>

                      </div><!-- /.row -->

                      <div class="row">
                      <div class="col-md-12">
                      <div class="box">
                      <div class="box-header with-border">
                      <h3 class="box-title"><?php echo $lang['monthly']; ?> <?php echo $lang['payments']; ?> <?php echo $lang['received']; ?> </h3>
                      <div class="box-tools pull-right">
                      <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      </div>
                      </div><!-- /.box-header -->
                      <div class="box-body">
                      <div class="row">
                      <div class="col-md-12">
                      <p class="text-center">
                      <strong><?php echo $lang['finance']; ?> : January, <?php year_now(); ?> - December, <?php year_now(); ?></strong>
                      </p>
                      <div class="chart">

                      <?php
                      $dbc = mysqli_connect(Config::get('mysql/host'), Config::get('mysql/username'), Config::get('mysql/password'), Config::get('mysql/db')) OR die('Could not connect because:' . mysqli_connect_error());

                      $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                      foreach ($months as $month) {

                      $q3 = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "invite" => "0", "delete_remove" => 0, "accepted" => 1]]);
                      if ($q3->count()) {
                      foreach ($q3->results() as $r3) {

                      $q4 = DB::getInstance()->get("milestone", "*", ["AND" => ["jobid" => $r3->jobid]]);
                      if ($q4->count()) {
                      foreach ($q4->results() as $r4) {
                      $milestoneid = $r4->id;
                      $clientid = $r4->clientid;

                      $q5 = "SELECT SUM(payment) AS value_sum FROM transactions WHERE DATE_FORMAT(date_added,'%M') = '$month' AND freelancerid = '$clientid' AND transaction_type = 4 AND membershipid = '$milestoneid' AND complete = 1 ";
                      $r5 = mysqli_query($dbc, $q5);
                      while ($row5 = mysqli_fetch_assoc($r5)) {
                      $sum_v[] = $row5['value_sum'];
                      }
                      }
                      }
                      }
                      }
                      //$row5 = mysqli_fetch_assoc($r5);
                      //$sum_v = $row5['value_sum'];
                      $sum = array_sum($sum_v);
                      if ($sum != 0) {
                      $monthvalues[] = $sum;
                      } else {
                      $monthvalues[] = 0;
                      }
                      unset($sum);
                      unset($sum_v);
                      }
                      ?>
                      <!-- Sales Chart Canvas -->
                      <canvas id="salesChart" height="400"></canvas>
                      </div><!-- /.chart-responsive -->
                      </div><!-- /.col -->

                      </div><!-- /.row -->
                      </div><!-- ./box-body -->
                      <div class="box-footer">
                      <div class="row">
                      <div class="col-sm-6 col-xs-6">
                      <div class="description-block border-right">
                      <h5 class="description-header"> <?php echo $currency_symbol; ?> <?php echo array_sum($pa); ?></h5>
                      <span class="description-text"><?php echo $lang['total']; ?> <?php echo $lang['payments']; ?> <?php echo $lang['received']; ?></span>
                      </div><!-- /.description-block -->
                      </div><!-- /.col -->
                      <div class="col-sm-6 col-xs-6">
                      <div class="description-block">
                      <h5 class="description-header">
                      <?php echo $currency_symbol; ?> <?php echo $outstanding; ?>
                      </h5>
                      <span class="description-text"><?php echo $lang['outstanding']; ?> <?php echo $lang['payments']; ?></span>
                      </div><!-- /.description-block -->
                      </div>
                      </div><!-- /.row -->
                      </div><!-- /.box-footer -->
                      </div><!-- /.box -->
                      </div><!-- /.col -->

                      </div>
                     */ ?>
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <?php include 'template/footer.php'; ?>	

        </div>
        <!-- ==============================================
            Scripts
            =============================================== -->

        <!-- jQuery 2.1.4 -->
        <script src="../assets/js/jQuery-2.1.4.min.js"></script>
        <!-- Bootstrap 3.3.6 JS -->
        <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="../assets/js/app.min.js" type="text/javascript"></script>  
    </body>
</html>