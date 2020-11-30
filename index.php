<?php include("funciones.php") ?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inicio</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/bootstrap-theme.css" rel="stylesheet" type="text/css"/>

    <!-- Estilos propios en comun para todos-->
    <link href="css/style.css" rel="stylesheet" type="text/css"/>	  

		<link href="lib/css/jquery.alerts.css" rel="StyleSheet" type="text/css" />

    <!-- Estilos individuales solo para este archivo-->
    <link href="css/inicio.css" rel="stylesheet" type="text/css"/>
    <link href="css/glyphicons.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
      /*Para aplicar estilos a los cargados*/
      .MultiFile-label {
        margin: 5px 0;
        padding: 0.3em;
        border: 1px solid #FF6;
        background-color: #FFC;
        width: 98%;
        font-weight: bold;
      }
      .MultiFile-remove {
        float: right;
      }    
      a.MultiFile-remove  {
        text-decoration: none;
        color: #FFF;
        background-color: #C00;
        padding: 0 0.2em;
      }
      /*Para cambiar el boton de subir archivos por una imagen*/      
      .image-upload .multi {
        display: none;
      }
            
    </style>
    
      
    
  </head>
  <body>
      <h1 align="center">Manejador de archivos</h1>
      <br />
      <div class="container">
        <div class="row">
          <div class="col-xs-12" align="center">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#crearCarpeta">Crear carpeta</button>
          </div>
        </div>  
      </div><br/>

      <?php       
          
          $mysqli = new Mysqli("localhost", "root", "", "file_manager");

          if(!empty($_POST)) {
            $ejecutarSql = true;
            
            $json = json_decode($_POST['datos']);
            
            switch ($json->accion) {
              case 'createFolder':
                //$carpetaRaiz = ($json->id == null) ? 1 : 0;                
                $sql = "
                  INSERT INTO archivo(tipo, nombre, es_carpeta, id_padre)
                  VALUES('carpeta', '".$_POST['nombreCarpeta']."', 1, 0)
                ";                
                break;
              case 'createSubFolder':
                $sql = "
                  INSERT INTO archivo(tipo, nombre, es_carpeta, id_padre)
                  VALUES('carpeta', '".$_POST['nombreCarpeta']."', 1, ".$json->id.")
                ";
                break;
              case 'deleteFile': 
                $sql = "
                  SELECT 1 
                  FROM archivo
                  WHERE id_padre=".$json->id;
                $result = $mysqli->query($sql) or die($mysqli->error . "_");
                if($result->num_rows > 0) {
                  $ejecutarSql = false;
                  echo "                
                    <div align='center'>
                      <b style='color: red'>No se puede eliminar carpeta porque contiene archivos</b>
                    </div>                
                  ";
                } else {
                  $sql = "SELECT nombre, es_carpeta FROM archivo WHERE id_archivo=".$json->id;
                  $result = $mysqli->query($sql) or die($mysqli->error . "_");
                  if($result->num_rows > 0) {
                    $row = $result->fetch_object();
                    if($row->es_carpeta == 0)
                      unlink("./uploads/".$row->nombre);                                          
                    
                  }
                  $sql = "DELETE FROM archivo WHERE id_archivo=".$json->id;                
                  
                }
                break;   
              case 'renameFile':
                $sql = "
                  UPDATE archivo 
                  SET nombre='".$_POST['nombreCarpeta']."'
                  WHERE id_archivo =".$json->id;
                break;
              case 'addFile':
                $_POST['nombreCarpeta'] = $_FILES['archivo']['name'][0];
                $result = subeArchivo();
                if($result == true) {
                  $ejecutarSql = false;
                  echo "                
                    <div align='center'>
                      <b style='color: red'>No se puedo subir el archivo ya que pesan mas de 5 megas</b>
                    </div>                
                  ";
                }
                $sql = "
                  INSERT INTO archivo(tipo, nombre, es_carpeta, id_padre)
                  VALUES('archivo', '".$_FILES['archivo']['name'][0]."', 0, ".$json->id.")
                ";    

                break;
              default:  exit("Error default");  
            } 
            if($ejecutarSql)
              $mysqli->query($sql);

            if($mysqli->affected_rows == -1) {
              echo "                
                <div align='center'>
                  El nombre <b style='color: red'>".$_POST['nombreCarpeta']."</b> ya existe
                </div>                
              ";
            } 
            
              
                     
          }

          
          $sql = "SELECT 1 FROM archivo";
          $result = $mysqli->query($sql) or die($mysqli->error . "_");
          if($result->num_rows > 0) {
            ?>
            <div class="container">
              <div class="row">                
                <div class="col-xs-12 col-sm-offset-1 col-sm-10" align="center">
                  <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th style="min-width: 200px">Nombre</th>                          
                          <th style="min-width: 100px">Edicion</th>                          
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          
                          
                          $sql = "
                            SELECT 
                              c.id_archivo as id_padre, c.nombre as nombre_padre,
                                null as id_hijo, c.nombre as nombre_hijo, c.tipo, c.es_carpeta,
                                c.id_archivo
                            FROM archivo c
                            WHERE c.id_padre=0
                            UNION ALL
                            SELECT 
                              b.id_padre, a.nombre as nombre_padre, 
                                b.id_archivo as id_hijo, b.nombre as nombre_hijo, b.tipo, b.es_carpeta,
                                b.id_archivo
                            FROM archivo a
                            JOIN (
                              SELECT *
                                FROM archivo
                            ) b ON a.id_archivo=b.id_padre
                            ORDER BY id_padre, id_archivo
                          ";
                          $result = $mysqli->query($sql) or die($mysqli->error . "_");
                          $sangria = 0;
                          $id_padreAnt = "";
                          $tabla = array();
                          $i = 0;

                          while($row = $result->fetch_object()) {
                            if($i != 0) {
                              $posEncontrado = array_search($row->id_padre, array_column($tabla, 'id_hijo'));     
                              if($row->id_padre != $row->id_hijo && $posEncontrado != "") {
                                
                                //Encontro su registro padre
                                array_splice($tabla, $posEncontrado+1, 0, 
                                  array(array(
                                    "id_padre" => $row->id_padre,
                                    "nombre_padre" => $row->nombre_padre,
                                    "id_hijo" => $row->id_hijo,
                                    "nombre_hijo" => $row->nombre_hijo,
                                    "tipo" => $row->tipo,
                                    "es_carpeta" => $row->es_carpeta,
                                    "id_archivo" => $row->id_archivo,
                                    "sangria" => $tabla[$posEncontrado]["sangria"] + 1
                                  ))
                                );
                              } else {
                                $sangria = ($id_padreAnt != $row->id_padre) ? 0: 1;         
                                $tabla[] = array(
                                  "id_padre" => $row->id_padre,
                                  "nombre_padre" => $row->nombre_padre,
                                  "id_hijo" => $row->id_hijo,
                                  "nombre_hijo" => $row->nombre_hijo,
                                  "tipo" => $row->tipo,
                                  "es_carpeta" => $row->es_carpeta,
                                  "id_archivo" => $row->id_archivo,
                                  "sangria" => $sangria
                                );
                              }

                            } else {
                              //Entra solo la primera vez
                              $tabla[] = array(
                                "id_padre" => $row->id_padre,
                                "nombre_padre" => $row->nombre_padre,
                                "id_hijo" => $row->id_hijo,
                                "nombre_hijo" => $row->nombre_hijo,
                                "tipo" => $row->tipo,
                                "es_carpeta" => $row->es_carpeta,
                                "id_archivo" => $row->id_archivo,
                                "sangria" => $sangria       
                              );
                            }   
                            $id_padreAnt = $row->id_padre;  
                            $i++;  
                          } //Fin while
                          $mysqli->close();

                          echo "<form action='' method='POST' id='form_accion'>";
                          foreach($tabla as $key => $array) {
                            ?>     
                              <tr>
                                <td> 
                                  <?php  
                                  if($array['es_carpeta'] == 1) {                   
                                    echo "<i class='glyphicon glyphicon-folder-open' style='padding-right: 10px; margin-left:".(($array['sangria'])*30)."px'></i>";
                                    echo $array['nombre_hijo'];
                                  } else {
                                    echo "<i class='glyphicon glyphicon-file' style='padding-right: 10px; margin-left:".(($array['sangria'])*30)."px'></i>";
                                    echo $array['nombre_hijo'];
                                  } 
                                  ?>
                                </td>
                                <td>                             
                                  <?php
                                  if($array['es_carpeta'] == 1) { ?>
                                    <a href='#' onclick='addFile(this.id);' id='<?php echo $array["id_archivo"] ?>' class='glyphicon glyphicon-file' style='padding: 5px'></a>
                                    <a href='#' onclick='createSubFolder(this.id);' id='<?php echo $array["id_archivo"] ?>' class='glyphicon glyphicon-folder-open' style='padding: 5px'></a>
                                    <?php
                                  }  ?>
                                  <a href='#' onclick='renameFile(this.id);' id='<?php echo $array["id_archivo"] ?>' class='glyphicon glyphicon-pencil' style='padding: 5px'></a>
                                  <a href='#' onclick='deleteFile(this.id);' id='<?php echo $array["id_archivo"] ?>' class='glyphicon glyphicon-remove' style='padding: 5px'></a>
                                  
                                </td>                          
                              </tr>
                            <?php
                          } //fin del foreach
                        ?>
                        <input type="hidden" name="datos" class="datos" id="datos" value="">
                        </form>

                      </tbody>
                      <tfoot></tfoot>
                    </table>
                  </div>
                </div>
              </div>  
            </div>  
            <?php
          } 
      ?>
      
      

    
  <!-- Modal -->
  <div class="modal fade" id="crearCarpeta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>          
          <h5 class="modal-title" id="myModalLabel">
            <b id="titulo">Crear carpeta</b>
          </h5>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="form_modal">
          <div class="form-group">
            <label for="recipient-name" id="texto" class="col-form-label">Nombre:</label>
            <input type="text" class="form-control" id="recipient-name" name="nombreCarpeta">
          </div>                
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnClose">Closer</button>
        <button type="submit" class="btn btn-success" id="crear">Crear</button> 
        <input type="hidden" name="datos" class="datos" id="datos" value="">
        </form>
      </div>
    </div>
  </div>
  </div>
  <!-- Fin Modal -->
  <!-- Modal upload files-->
  <form action="" id="formUploadFiles" method="post" enctype="multipart/form-data">
  <div class="modal fade" id="uploadFiles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>          
            <h5 class="modal-title" id="myModalLabel">
              <b id="titulo">Cargar Archivos</b>
            </h5>
        </div>
        <div class="modal-body image-upload">
          Click para subir archivo (max 5 Megas):
          <label for="file-input">
              <img src="lib/img/icon-alerts/b_image.png"/>
          </label>  
          <input type="file" id="file-input" class="multi" name="archivo[]" />
          <input type="hidden" name="datos" class="datos" id="datos" value="">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Cargar</button>
        </div>
      </div>
    </div>
  </div>
  
  </form>
  <!-- Fin Modal -->

  <script src="js/jquery-1.11.1.min.js" type="text/javascript"></script>
  <script src="lib/js/jquery-1.8.2.min.js"></script>  
  <script src="lib/js/jquery.alerts.mod.js" type="text/javascript"></script>
  <script src="js/bootstrap.min.js" type="text/javascript"></script>
  <script src='lib/js/jquery.MultiFile.js' type="text/javascript" language="javascript"></script>
  <script src="js/inicio.js" type="text/javascript"></script>
  


  </body>
</html>

