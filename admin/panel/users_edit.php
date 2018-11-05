<?php
require_once('../../includes/config.php');
require_once('../../includes/functions/func.admin.php');
require_once('../../includes/functions/func.sqlquery.php');
require_once('../../includes/lang/lang_'.$config['lang'].'.php');
$mysqli = db_connect($config);
admin_session_start();
checkloggedadmin($config);

function check_account_exists($config,$con,$id)
{
    $row = mysqli_num_rows(mysqli_query($con, "select 1 from `".$config['db']['pre']."user` where id = '".validate_input($id)."'"));
    if($row>0){
        return TRUE;
    }
    return FALSE;
}
$check = check_account_exists($config,$mysqli,$_GET['id']);
if($check != 1)
{
    echo 'Error 404';
    exit();
}

$user = "SELECT * FROM `".$config['db']['pre']."user` where id = '".validate_input($_GET['id'])."'";
$userresult = $mysqli->query($user);
$fetchuser = mysqli_fetch_assoc($userresult);
$fetchusername  = $fetchuser['username'];
$fetchuserpic     = $fetchuser['image'];

if($fetchuserpic == "")
    $fetchuserpic = "default_user.png";

?>
<header class="slidePanel-header overlay">
    <div class="overlay-panel overlay-background vertical-align">
        <div class="service-heading">
            <h2>Editar Usuario</h2>
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
                    <form name="form2"  class="form form-horizontal" method="post" data-ajax-action="editUser" id="sidePanel_form">
                        <div class="form-body">
                            <input type="hidden" name="id" value="<?php echo $_GET['id']?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-2">
                                            <img src="../storage/profile/small_<?php echo $fetchuserpic; ?>" alt="<?php echo $fetchuser['name'];?>" style="width: 80px; border-radius: 50%">
                                        </div>
                                        <div class="col-md-10">
                                            <label class="control-label">Imagen de perfil</label>
                                            <input class="form-control input-sm" type="file" id="file" name="file" placeholder=".input-sm" />
                                            <span class="help-block"> Cambia tu foto</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputfullname">Nombre completo</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ion-person"></i></div>
                                            <input type="text" class="form-control" id="exampleInputfullname" placeholder="Nombre completo" name="name" value="<?php echo $fetchuser['name'];?>">
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Estatus</label>
                                        <select class="form-control" name="status">
                                            <option value="0" <?php echo ($fetchuser['status'] == "0")? "selected" : "" ?>>Activo</option>
                                            <option value="1" <?php echo ($fetchuser['status'] == "1")? "selected" : "" ?>>Verificado</option>
                                            <option value="2" <?php echo ($fetchuser['status'] == "2")? "selected" : "" ?>>Bloqueado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Género</label>
                                        <select class="form-control" name="sex">
                                            <option value="Male" <?php if($fetchuser['sex'] == "Male") { echo "selected"; }?>>Masculino</option>
                                            <option value="Female" <?php if($fetchuser['sex'] == "Female") { echo "selected"; }?>>Femenino</option>
                                        </select>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">País</label>
                                        <select class="form-control" name="country">
                                            <?php $country = get_country_list($config,$fetchuser['country']);
                                            foreach ($country as $value){
                                                echo '<option value="'.$value['name'].'" '.$value['selected'].'>'.$value['name'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Acerca de</label>
                                        <textarea name="about" class="form-control" ><?php echo $fetchuser['description'];?></textarea>
                                    </div>
                                </div>

                                <h4 class="box-title m-b-0">Ajustes de la cuenta</h4>
                                <hr>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputuname">Nombre de usuario</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ion-person"></i></div>
                                            <input type="text" class="form-control" id="exampleInputuname" placeholder="Usuario" name="username" value="<?php echo $fetchuser['username'];?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Email</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ion-android-mail"></i></div>
                                            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email" name="email" value="<?php echo $fetchuser['email'];?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputpwd2">Nueva contraseña</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ion-android-lock"></i></div>
                                            <input type="password" class="form-control" id="exampleInputpwd2" placeholder="Ingresa la nueva contraseña" name="password">
                                        </div>
                                    </div>
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

