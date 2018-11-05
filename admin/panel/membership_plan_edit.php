<?php
require_once('../../includes/config.php');
require_once('../../includes/functions/func.admin.php');
require_once('../../includes/functions/func.sqlquery.php');
require_once('../../includes/lang/lang_'.$config['lang'].'.php');
$mysqli = db_connect($config);
admin_session_start();
checkloggedadmin($config);

$query = "SELECT * FROM ".$config['db']['pre']."subscriptions WHERE `sub_id` = '".validate_input($_GET['id'])."' LIMIT 1";
$query_result = mysqli_query($mysqli,$query);
$info = mysqli_fetch_array($query_result);
?>

<header class="slidePanel-header overlay">
    <div class="overlay-panel overlay-background vertical-align">
        <div class="service-heading">
            <h2>Editar Plan</h2>
        </div>
        <div class="slidePanel-actions">
            <div class="btn-group-flat">
                <button type="button" class="btn btn-floating btn-warning btn-sm waves-effect waves-float waves-light margin-right-10" id="post_sidePanel_data"><i class="icon ion-android-done" aria-hidden="true"></i></button>
                <button type="button" class="btn btn-pure btn-inverse slidePanel-close icon ion-android-close font-size-20" aria-hidden="true"></button>
            </div>
        </div>
    </div>
</header>
<div class="slidePanel-inner">
    <div class="panel-body">
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">

                <div class="white-box">
                    <div id="post_error"></div>
                    <form name="form2"  class="form form-horizontal" method="post" data-ajax-action="editMembershipPlan" id="sidePanel_form">
                        <div class="form-body">
                            <input type="hidden" name="id" value="<?php echo $_GET['id']?>">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nombre del Plan</label>
                                <div class="col-sm-6">
                                    <input name="sub_title" type="Text" class="form-control" value="<?php echo stripslashes($info['sub_title']);?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Selecciona el paquete</label>
                                <div class="col-sm-6">
                                    <select name="group_id" id="group_id" class="form-control">
                                        <?php
                                        $query2 = "SELECT * FROM ".$config['db']['pre']."usergroups ORDER BY group_name ASC";
                                        $query_result2 = mysqli_query($mysqli,$query2);
                                        while ($info2 = @mysqli_fetch_array($query_result2))
                                        {
                                            if($info['group_id'] == $info2['group_id'])
                                            {
                                                ?>
                                                <option value="<?php echo $info2['group_id'];?>" selected><?php echo stripslashes($info2['group_name']);?></option>
                                            <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <option value="<?php echo $info2['group_id'];?>"><?php echo stripslashes($info2['group_name']);?></option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Plazo del Plan</label>
                                <div class="col-sm-6">
                                    <select name="sub_term" id="sub_term" class="form-control">
                                        <option value="DAILY" <?php if($info['sub_term'] == 'DAILY'){ echo 'selected'; } ?>>Por día</option>
                                        <option value="WEEKLY" <?php if($info['sub_term'] == 'WEEKLY'){ echo 'selected'; } ?>>Semanal</option>
                                        <option value="MONTHLY" <?php if($info['sub_term'] == 'MONTHLY'){ echo 'selected'; } ?>>Mensual</option>
                                        <option value="YEARLY" <?php if($info['sub_term'] == 'YEARLY'){ echo 'selected'; } ?>>Anual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Monto del Plan</label>
                                <div class="col-sm-6">
                                    <input name="sub_amount" type="Text" class="form-control" id="sub_amount" value="<?php echo stripslashes($info['sub_amount']);?>">
                                </div>
                            </div>



                            <div class="form-group">
                                <label class="col-sm-4 control-label">Imagen del Plan</label>
                                <div class="col-sm-6">
                                    <input name="sub_image" type="Text" class="form-control" id="sub_image" value="<?php echo stripslashes($info['sub_image']);?>" placeholder="Paste Image Url">
                                </div>
                            </div>
                            <div class="form-group hidden">
                                <label class="col-sm-4 control-label">Descuento</label>
                                <div class="col-sm-6">
                                    <input name="discount_badge" type="Text" class="form-control" id="discount_badge" placeholder="example : 25%"  value="<?php echo stripslashes($info['discount_badge']);?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Recomendado</label>
                                <div class="col-sm-6">
                                    <label class="css-input css-checkbox css-checkbox-primary">
                                        <input type="checkbox" name="recommended" value="yes" <?php if($info['recommended'] == 'yes') echo "checked"; ?>><span></span>
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="submit">
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>
<script>
    $(function()
    {
        // Init page helpers (BS Datepicker + BS Colorpicker + Select2 + Masked Input + Tags Inputs plugins)
        App.initHelpers('select2');
    });
</script>
