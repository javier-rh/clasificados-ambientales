<?php
require_once('../includes/config.php');
require_once('../includes/functions/func.admin.php');
require_once('../includes/functions/func.sqlquery.php');
require_once('../includes/functions/func.users.php');
require_once('../includes/lang/lang_'.$config['lang'].'.php');
$mysqli = db_connect($config);
admin_session_start();
checkloggedadmin($config);

?>
<?php
/**
 * StarReviews - jQuery & Ajax powered php review and rating form
 * @author Adriaan Ebbeling
 * @version 1.0
 */


// Converts linebreaks to <br>
function mynl2br($text)
{
    return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />'));
}

$pageTitle = '';
$pageContent = '';

if (!isset($_GET['action'])) {
    $pageTitle = 'Comentarios en espera de aprobación';

    $query = mysqli_query($mysqli, "SELECT * FROM ".$config['db']['pre']."reviews WHERE publish!=1 ORDER BY date DESC") or die(mysqli_error($mysqli));
    $result = mysqli_num_rows($query);

    if ($result == 0) {
        $pageContent .= '<p>No hay comentarios pendientes en este momento, vuelve más tarde.</p>';
    } else {
        $pageContent .= '<p>Hay <strong>'.$result.'</strong> revisiones pendientes por aprobar.</p><div class="table-responsive">
        <table class="table table-bordered" id="reviews">
        <thead>
        <tr>
        <th>Estrellas</th>
        <th>Título</th>
        <th>Fecha</th>
        <th>Nombre</th>
        <th>Acciones</th>
        </tr>
        </thead>
        <tbody>';

        while ($fetch = mysqli_fetch_assoc($query)) {
            $sql = "SELECT * FROM ".$config['db']['pre']."user WHERE id='".$fetch['user_id']."' LIMIT 1";
            $query_result = mysqli_query(db_connect($config), $sql);
            $info = mysqli_fetch_assoc($query_result);
            $fullname = $info['name'];
            $username = $info['username'];

            $sql2 = "SELECT * FROM ".$config['db']['pre']."product WHERE id='".$fetch['productID']."' LIMIT 1";
            $query_result2 = mysqli_query(db_connect($config), $sql2);
            $info2 = mysqli_fetch_assoc($query_result2);
            $ad_title = $info2['product_name'];

            $pageContent .= '
            <tr>
            <td><img src="plugins/starreviews/assets/img/rating-star'.$fetch['rating'].'.png"></td>
            <td><a href="post_detail.php?id='.$fetch['productID'].'">'.$ad_title.'</a></td>
            <td>'.timeAgo($fetch['date']).'</td>
            <td>'.$fullname.'</td>
            <td style="width:250px">
            <button type="button" class="btn btn-sm btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="'.$fetch['comments'].'" data-original-title="" title="Comentarios">Ver comentarios</button>
            <div class="btn-group">
                 <a href="review.php?action=edit&id='.$fetch['reviewID'].'" class="btn btn-sm btn-primary">Editar</a>
                 <a href="review.php?action=approve&id='.$fetch['reviewID'].'" class="btn btn-sm btn-success">Aprobar</a>
                 <a href="review.php?action=delete&id='.$fetch['reviewID'].'&type=unapproved" class="btn btn-sm btn-danger">Eliminar</a>
            </div>
            </td>
            </tr>';
        }

        $pageContent .= '
        </tbody>
        </table></div>';
    }
} else {
    if ($_GET['action'] == "edit") {
        if (isset($_GET['id'])) {
            if (!empty($_GET['id'])) {
                if (isset($_POST['Submit'])) {
                    if (isset($_POST['publish'])) {
                        if ($_POST['publish'] == 1) {
                            $setPublish = 1;
                        } else {
                            $setPublish = 0;
                        }
                    } else {
                        $setPublish = 0;
                    }
                    if (mysqli_query($mysqli, "UPDATE ".$config['db']['pre']."reviews SET 
                    rating='".mysqli_real_escape_string($mysqli, $_POST['rating-new'])."', 
                    comments='".mysqli_real_escape_string($mysqli, mynl2br($_POST['comments']))."',
                    publish='".$setPublish."'
                    WHERE reviewID='".mysqli_real_escape_string($mysqli, $_GET['id'])."'"))
                    {
                        $pageContent .= '<div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <strong>Success!</strong>  Tus cambios han sido guardados satisfactoriamente. </div>';
                    } else {
                        $pageContent .= '<div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <strong>Error!</strong> Ha ocurrido un error al guardar tus cambios, intentalo una vez mas. </div>';
                    }

                }
                // Pull data from database
                $query = mysqli_query($mysqli, "SELECT * FROM ".$config['db']['pre']."reviews WHERE reviewID='".mysqli_real_escape_string($mysqli, $_GET['id'])."'") or die(mysql_error());
                $row = mysqli_fetch_array($query, MYSQLI_ASSOC);

                if ($row['rating'] == 1) { $rating_selected_1 = 'selected="selected"'; } else { $rating_selected_1 = ''; }
                if ($row['rating'] == 2) { $rating_selected_2 = 'selected="selected"'; } else { $rating_selected_2 = ''; }
                if ($row['rating'] == 3) { $rating_selected_3 = 'selected="selected"'; } else { $rating_selected_3 = ''; }
                if ($row['rating'] == 4) { $rating_selected_4 = 'selected="selected"'; } else { $rating_selected_4 = ''; }
                if ($row['rating'] == 5) { $rating_selected_5 = 'selected="selected"'; } else { $rating_selected_5 = ''; }
                if ($row['publish'] == 1) { $publish = 'checked="checked"'; } else { $publish = ''; }
                $pageTitle = 'Editar Revisión';
                $pageContent .= '
                <form role="form" action="" method="post">
                    <div class="form-group input select rating-new">
                        <label for="rating">Calificación</label>
                        <select id="rating-new" name="rating-new">
                            <option value="1" '.$rating_selected_1.'> 1 Estrella</option>
                            <option value="2" '.$rating_selected_2.'> 2 Estrellas</option>
                            <option value="3" '.$rating_selected_3.'> 3 Estrellas</option>
                            <option value="4" '.$rating_selected_4.'> 4 Estrellas</option>
                            <option value="5" '.$rating_selected_5.'> 5 Estrellas</option>
                        </select>
                    </div>
                   
                    <div class="form-group">
                        <label for="comments">Comentarios</label>
                        <textarea class="form-control" name="comments">'.$row['comments'].'</textarea>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="css-input css-checkbox css-checkbox-primary">
                                <input type="checkbox" name="publish" value="1" '.$publish.'><span></span> Publicar
                            </label>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-success" name="Submit" id="Submit">Enviar</button>
                </form>';
            }
        }
    }
    elseif ($_GET['action'] == "approve") {
        if (isset($_GET['id'])) {
            if (!empty($_GET['id'])) {
                if (mysqli_query($mysqli, "UPDATE ".$config['db']['pre']."reviews SET publish='1' WHERE reviewID='".mysqli_real_escape_string($mysqli, $_GET['id'])."'")) {
                    header("location: review.php");
                }
            }
        }
    }
    elseif ($_GET['action'] == "unapprove") {
        if (isset($_GET['id'])) {
            if (!empty($_GET['id'])) {
                if (mysqli_query($mysqli, "UPDATE ".$config['db']['pre']."reviews SET publish='0' WHERE reviewID='".mysqli_real_escape_string($mysqli, $_GET['id'])."'")) {
                    header("location: review.php?action=all-reviews");
                }
            }
        }
    }
    elseif ($_GET['action'] == "delete") {
        if (isset($_GET['id'])) {
            if(!check_allow()){
                ?>
                <script src="plugins/bower_components/jquery/dist/jquery.min.js"></script>
                <script>
                    $(document).ready(function(){
                        $('#sa-title').trigger('click');
                    });
                </script>
                <?php

            }
            else {
                if (!empty($_GET['id'])) {
                    if (mysqli_query($mysqli, "DELETE FROM ".$config['db']['pre']."reviews WHERE reviewID='".mysqli_real_escape_string($mysqli, $_GET['id'])."'")) {
                        if (isset($_GET['type'])) {
                            if (!empty($_GET['type'])) {
                                if ($_GET['type'] == "approved") {
                                    header("location: review.php?action=all-reviews");
                                } else {
                                    header("location: review.php");
                                }
                            }
                        }
                    }
                }
            }

        }
    }
    elseif ($_GET['action'] == "all-reviews") {

        $pageTitle = 'Todas las revisiones activas';

        $query = mysqli_query($mysqli, "SELECT * FROM ".$config['db']['pre']."reviews WHERE publish=1 ORDER BY date DESC") or die(mysqli_error($mysqli));
        $result = mysqli_num_rows($query);

        if ($result == 0) {
            $pageContent .= '<p>Hay revisiones <strong>aprovadas</strong> en éste momento, ve a <a href="review.php">revisiones pendientes por aprobar</a>.</p>';

        }
        else {
            $pageContent .= '<p>Hay <strong>'.$result.'</strong> revisiones aprobadas.</p><div class="table-responsive">
            <table class="table table-bordered" id="reviews">
            <thead>
            <tr>
            <th>Estrellas</th>
            <th>ID del clasificado</th>
            <th>Fecha</th>
            <th>Nombre</th>
            <th>Acciones</th>
            </tr>
            </thead>
            <tbody>';

            while ($fetch = mysqli_fetch_assoc($query)) {
                $sql = "SELECT * FROM ".$config['db']['pre']."user WHERE id='".$fetch['user_id']."' LIMIT 1";
                $query_result = mysqli_query(db_connect($config), $sql);
                $info = mysqli_fetch_assoc($query_result);
                $fullname = $info['name'];
                $username = $info['username'];

                $sql2 = "SELECT * FROM ".$config['db']['pre']."product WHERE id='".$fetch['productID']."' LIMIT 1";
                $query_result2 = mysqli_query(db_connect($config), $sql2);
                $info2 = mysqli_fetch_assoc($query_result2);
                $ad_title = $info2['product_name'];

                $pageContent .= '
                <tr>
                <td><img src="plugins/starreviews/assets/img/rating-star'.$fetch['rating'].'.png"></td>
                <td><a href="post_detail.php?id='.$fetch['productID'].'">'.$ad_title.'</a></td>
                <td>'.timeAgo($fetch['date']).'</td>
                <td>'.$fullname.'</td>
                <td>
                <button type="button" class="btn btn-sm btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="'.$fetch['comments'].'" data-original-title="" title="Comentarios">Ver comentarios</button>
                <a href="review.php?action=edit&id='.$fetch['reviewID'].'" class="btn btn-sm btn-primary">Editar</a>
                <a href="review.php?action=unapprove&id='.$fetch['reviewID'].'" class="btn btn-sm btn-success">Desprobar</a>
                <a class="btn btn-sm btn-danger" href="review.php?action=delete&id='.$fetch['reviewID'].'&type=approved">Eliminar</a>
                </td>
                </tr>';
            }

            $pageContent .= '
            </tbody>
            </table></div>';
        }
    }
}


?>

<?php include("header.php");?>
<!-- Admin stylesheet -->
<link href="plugins/starreviews/assets/css/starReviewsAdmin.css" rel="stylesheet" type="text/css"/>

<!-- Page JS Plugins CSS -->

<main class="app-layout-content">

    <!-- Page Content -->
    <div class="container-fluid p-y-md">
        <!-- Partial Table -->
        <div class="card">
            <div class="card-header">
                <h4><?php echo $pageTitle; ?></h4>
                <div class="pull-right">
                    <a class="btn btn-sm btn-warning" href="review.php">Revisiones no aprobadas</a>
                    <a class="btn btn-sm btn-success" href="review.php?action=all-reviews">Revisiones activas</a>
                </div>
            </div>
            <div class="card-block">

                <?php echo $pageContent; ?>
            </div>
            <!-- .card-block -->
        </div>
        <!-- .card -->
        <!-- End Partial Table -->

    </div>
    <!-- .container-fluid -->
    <!-- End Page Content -->

</main>


<?php include("footer.php"); ?>

<!-- jQuery Form Validator -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.34/jquery.form-validator.min.js"></script>

<!-- jQuery Barrating plugin -->
<script src="plugins/starreviews/assets/js/jquery.barrating.js"></script>

<!-- StarReviews Admin -->
<script src="plugins/starreviews/assets/js/starReviewsAdmin.js"></script>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript">
    !function ($) {

        $(function(){

            // popover
            $("[data-toggle=popover]").popover()

        })

    }(jQuery)
</script>

</body>

</html>


