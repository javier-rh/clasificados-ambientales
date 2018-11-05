<?php
$freelancer = new Freelancer();
$freelancerid = $freelancer->data()->freelancerid;

$basename = basename($_SERVER["REQUEST_URI"], ".php");
$editname = basename($_SERVER["REQUEST_URI"]);
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo escape($freelancer->data()->imagelocation); ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p><?php echo escape($freelancer->data()->name); ?></p>
                <!-- Status -->
                <a href="profile.php?a=profile"><i class="fa fa-circle text-success"></i> <?php echo $lang['online']; ?></a>
            </div>
        </div>


        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header"><?php echo $lang['sidenav_header_2']; ?></li>
            <!-- Optionally, you can add icons to the links -->
            <li class="<?php echo $active = ($basename == 'index') ? 'active' : ''; ?>">
                <a href="index.php"><i class='fa fa-dashboard'></i> <span><?php echo $lang['dashboard']; ?></span></a>
            </li>     
            <li class="treeview<?php echo $active = ($basename == 'joblist') ? ' active' : ''; ?>">
                <a href="joblist.php"><i class='fa fa-align-left'></i> <span><?php echo $lang['clasificados']; ?></span></a>
            </li>        
            <li class="treeview<?php
            echo $active = ($basename == 'jobinvite') ? ' active' : '';
            echo $active = ($editname == 'viewinvite.php?id=' . Input::get('id') . '') ? ' active' : '';
            ?>">
                <a href="jobinvite.php"><i class='fa fa-filter'></i> <span><?php echo $lang['invitaciones_trabajo']; ?></span>
                    <span class="label label-info pull-right">
                        <?php
                        $q1 = DB::getInstance()->get("job", "*", ["AND" => ["freelancerid" => $freelancer->data()->freelancerid, "opened" => 0, "delete_remove" => 0, "invite" => "1"]]);
                        echo $q1->count();
                        ?></span></a>
            </li>     
            <li class="treeview<?php
            echo $active = ($basename == 'proposallist') ? ' active' : '';
            echo $active = ($editname == 'addproposal.php?id=' . Input::get('id') . '') ? ' active' : '';
            echo $active = ($editname == 'editproposal.php?id=' . Input::get('id') . '') ? ' active' : '';
            echo $active = ($editname == 'viewproposal.php?id=' . Input::get('id') . '') ? ' active' : '';
            ?>">
                <a href="proposallist.php"><i class='fa  fa-files-o'></i> <span><?php echo $lang['proposals']; ?></span></a>
            </li>     
            <li class="<?php
                echo $active = ($basename == 'jobassigned') ? ' active' : '';
                echo $active = ($editname == 'jobboard.php?a=' . Input::get('a') . '&id=' . Input::get('id') . '') ? ' active' : '';
                ?>">
                <a href="jobassigned.php"><i class='fa fa-address-card'></i> <span><?php echo $lang['clasificados_asignados']; ?></span></a>
            </li>    
            <li class="header"><?php echo $lang['membership']; ?> & <?php echo $lang['payments']; ?></li>   
            <li class="<?php
            echo $active = ($basename == 'membership') ? ' active' : '';
            echo $active = ($editname == 'membership.php?id=' . Input::get('id') . '') ? ' active' : '';
                ?>">
                <a href="membership.php"><i class='fa fa-user'></i> <span><?php echo $lang['choose']; ?> <?php echo $lang['membership']; ?></span></a>
            </li>
            <li class="treeview<?php
            echo $active = ($basename == 'paymentspaid') ? ' active' : '';
            echo $active = ($basename == 'paymentsreceived') ? ' active' : '';
                ?>">
                <a href="#"><i class='fa fa-usd'></i> <span><?php echo $lang['lista_pagos']; ?></span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="paymentspaid.php"><?php echo $lang['pagos_realizados']; ?></a></li>
                    <li><a href="paymentsreceived.php"><?php echo $lang['pagos_recibidos']; ?></a></li>
                </ul>
            </li> 
            <li class="treeview<?php
            echo $active = ($basename == 'withdraw') ? ' active' : '';
            echo $active = ($editname == 'withdraw.php?id=' . Input::get('id') . '') ? ' active' : '';
            echo $active = ($basename == 'schedule') ? ' active' : '';
                ?>">
                <a href="#"><i class='fa fa-usd'></i> <span><?php echo $lang['retiros_programados']; ?></span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="withdraw.php"><?php echo $lang['elegir_tipo_retiro']; ?></a></li>
                    <li><a href="schedule.php"><?php echo $lang['programa_tu_pago']; ?></a></li>
                </ul>
            </li>  
            <li class="<?php echo $active = ($basename == 'withpayments') ? ' active' : ''; ?>">
                <a href="withpayments.php"><i class='fa fa-align-left'></i> <span><?php echo $lang['withdrawals']; ?></span></a>
            </li>          
            <li class="header"><?php echo $lang['sidenav_header_3']; ?></li>          
            <li class="treeview<?php echo $active = ($basename == 'overview') ? ' active' : ''; ?>">
                <a href="overview.php?a=profile"><i class='fa fa-info-circle'></i> <span><?php echo $lang['mi_perfil']; ?></span></a>
            </li> 
            <li class="treeview<?php
            echo $active = ($basename == 'portfoliolist') ? ' active' : '';
            echo $active = ($basename == 'addportfolio') ? ' active' : '';
            echo $active = ($editname == 'editportfolio.php?id=' . Input::get('id') . '') ? ' active' : '';
                ?>">
                <a href="#"><i class='fa fa-briefcase'></i> <span><?php echo $lang['portafolio']; ?></span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="portfoliolist.php"><?php echo $lang['galeria']; ?></a></li>
                    <li><a href="addportfolio.php"><?php echo $lang['agregar_portafolio']; ?></a></li>
                </ul>
            </li>

            <li class="treeview<?php
            echo $active = ($basename == 'servicelist') ? ' active' : '';
            echo $active = ($basename == 'addservice') ? ' active' : '';
            echo $active = ($editname == 'editservice.php?id=' . Input::get('id') . '') ? ' active' : '';
                ?>">
                <a href="#"><i class='fa fa-files-o'></i> <span><?php echo $lang['servicios']; ?></span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="servicelist.php"><?php echo $lang['lista_de_servicios']; ?></a></li>
                    <li><a href="addservice.php"><?php echo $lang['agregar_servicio']; ?></a></li>
                </ul>
            </li>

            <?php
            $query = DB::getInstance()->get("freelancer", "*", ["freelancerid" => $freelancerid, "LIMIT" => 1]);
            if ($query->count()) {
                foreach ($query->results() as $row) {
                    $membershipid = $row->membershipid;
                }
            }

            $q = DB::getInstance()->get("membership_freelancer", "*", ["membershipid" => $membershipid]);
            if ($q->count() === 1) {
                $q1 = DB::getInstance()->get("membership_freelancer", "*", ["membershipid" => $membershipid]);
            } else {
                $q1 = DB::getInstance()->get("membership_agency", "*", ["membershipid" => $membershipid]);
            }
            if ($q1->count()) {
                foreach ($q1->results() as $r1) {
                    $team_membership = $r1->team;
                }
            }
            ?>		

            <?php if ($team_membership === '1'): ?>

                <li class="treeview<?php
            echo $active = ($basename == 'teamlist') ? ' active' : '';
            echo $active = ($basename == 'addteam') ? ' active' : '';
            echo $active = ($editname == 'editteam.php?id=' . Input::get('id') . '') ? ' active' : '';
            ?>">
                    <a href="#"><i class='fa fa-users'></i> <span><?php echo $lang['team']; ?></span> <i class="fa fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="teamlist.php"><?php echo $lang['team']; ?> <?php echo $lang['list']; ?></a></li>
                        <li><a href="addteam.php"><?php echo $lang['add']; ?> <?php echo $lang['team']; ?></a></li>
                    </ul>
                </li>	

            <?php endif; ?>

            <li class="header"><?php echo $lang['sidenav_header_4']; ?></li>   
            <li class="treeview<?php
            echo $active = ($basename == 'inbox') ? ' active' : '';
            echo $active = ($basename == 'compose') ? ' active' : '';
            echo $active = ($basename == 'sent') ? ' active' : '';
            echo $active = ($basename == 'favorite') ? ' active' : '';
            echo $active = ($basename == 'trash') ? ' active' : '';
            echo $active = ($editname == 'message.php?id=' . Input::get('id') . '') ? ' active' : '';
            ?>">
                <a href="#">
                    <i class="fa fa-envelope"></i> <span><?php echo $lang['mailbox']; ?></span>
                    <span class="label label-info pull-right" style="margin-right: 20px;">
<?php
$q1 = DB::getInstance()->get("message", "*", ["AND" => ["user_to" => $freelancer->data()->freelancerid, "opened" => 0, "delete_remove" => 0, "disc" => 0]]);
echo $q1->count();
?></span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="active"><a href="inbox.php"><?php echo $lang['inbox']; ?> 
                            <span class="label label-info pull-right">
<?php
$q1 = DB::getInstance()->get("message", "*", ["AND" => ["user_to" => $freelancer->data()->freelancerid, "opened" => 0, "delete_remove" => 0, "disc" => 0]]);
echo $q1->count();
?></span></a>
                    </li>
                    <li>
                        <a href="compose.php"><?php echo $lang['compose']; ?></a>
                    </li>
                </ul>
            </li>

        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>