<?php
require_once('../../includes/config.php');
require_once('../../includes/functions/func.admin.php');
require_once('../../includes/functions/func.sqlquery.php');

$mysqli = db_connect($config);
admin_session_start();
checkloggedadmin($config);
?>
<header class="slidePanel-header overlay">
    <div class="overlay-panel overlay-background vertical-align">
        <div class="service-heading">
            <h2>Agregar Paquete</h2>
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
                    <form name="form2"  class="form form-horizontal" method="post" data-ajax-action="addMembershipPackage" id="sidePanel_form">
                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nombre del Paquete</label>
                                <div class="col-sm-6">
                                    <input name="group_name" type="Text" class="form-control" placeholder="Package Name">
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-4 control-label">Duración del clasificado</label>
                                <div class="col-sm-6">
                                    <input name="ad_duration" type="Text" class="form-control" id="ad_duration" placeholder="Ad Duration" value="3">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Tarifa de clasificado destacado</label>
                                <div class="col-sm-6">
                                    <input name="featured_project_fee" type="Text" class="form-control" id="featured_project_fee" placeholder="Tarifa de clasificado destacado" value="10">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Tarifa de clasificado urgente</label>
                                <div class="col-sm-6">
                                    <input name="urgent_project_fee" type="Text" class="form-control" id="urgent_project_fee" placeholder="Tarifa de clasificado urgente" value="10">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Tarifa de clasificado importante</label>
                                <div class="col-sm-6">
                                    <input name="highlight_project_fee" type="Text" class="form-control" id="highlight_project_fee" placeholder="Tarifa de clasificado importante" value="10">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Removible</label>
                                <div class="col-sm-6">
                                    <label class="css-input css-checkbox css-checkbox-primary">
                                        <input type="checkbox" name="recommended" value="1" ><span></span>
                                    </label>
                                </div>
                            </div>
                            <h3 class="heading">Opción del Paquete (Activalo si quieres permitirlo)</h3>
                            <div class="form-group">
                                <div class="inside" style="padding: 0 20px">
                                    <label class="css-input css-checkbox css-checkbox-primary">
                                        <input type="checkbox" name="top_search_result" value="yes" checked><span></span>
                                        Top en resultados de búsqueda y categoría.
                                    </label>
                                    <br>
                                    <label class="css-input css-checkbox css-checkbox-primary">
                                        <input type="checkbox" name="show_on_home" value="yes"><span></span>
                                        Mostrar clasificado en página de inicio en la sección de clasificados premium.
                                    </label>
                                    <br>
                                    <label class="css-input css-checkbox css-checkbox-primary">
                                        <input type="checkbox" name="show_in_home_search" value="yes"><span></span>
                                        Mostrar clasificado en la lista de resultados de búsqueda de la página de inicio.
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

