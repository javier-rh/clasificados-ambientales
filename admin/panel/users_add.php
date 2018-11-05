<?php
require_once('../../includes/config.php');
require_once('../../includes/functions/func.admin.php');
require_once('../../includes/functions/func.sqlquery.php');
require_once('../../includes/lang/lang_'.$config['lang'].'.php');
$mysqli = db_connect($config);
admin_session_start();
checkloggedadmin($config);
?>
<header class="slidePanel-header overlay">
    <div class="overlay-panel overlay-background vertical-align">
        <div class="service-heading">
            <h2>Agregar Usuario</h2>
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
        <form name="form2"  class="form form-horizontal" method="post" data-ajax-action="addUser" id="sidePanel_form">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Imagen de perfil<code>*</code></label>
                            <input class="form-control input-sm" type="file" id="file" name="file" placeholder=".input-sm" />
                            <span class="help-block">Sólo imagenes <code>jpg</code></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="exampleInputfullname">Nombre completo</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="ion-person"></i></div>
                                <input type="text" class="form-control" id="exampleInputfullname" placeholder="Nombre completo" name="name" value="">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Género</label>
                            <select class="form-control" name="sex">
                                <option value="Male">Masculino</option>
                                <option value="Female">Femenino</option>
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">País</label>
                            <select class="form-control" name="country">
                                <?php
                                $country = get_country_list($config);
                                foreach ($country as $value){
                                    echo '<option value="'.$value['name'].'">'.$value['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Acerca de</label>
                            <textarea name="about" class="form-control" ></textarea>
                        </div>
                    </div>

                    <h4 class="box-title m-b-0">Ajustes de la cuenta</h4>
                    <hr>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="exampleInputuname">Nombre de usuario</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="ion-person"></i></div>
                                <input type="text" class="form-control" id="exampleInputuname" placeholder="Usuario" name="username" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="ion-android-mail"></i></div>
                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email" name="email" value="">
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

