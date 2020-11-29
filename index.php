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
            
            
            $json = json_decode($_POST['datos']);
            
            switch ($json->accion) {
              case 'createFolder':
                $carpetaRaiz = ($json->id == null) ? 1 : 0;                
                $sql = "
                  INSERT INTO archivo(tipo, nombre, es_carpeta, es_carpeta_raiz)
                  VALUES('carpeta', '".$_POST['nombreCarpeta']."', 1, ".$carpetaRaiz.")
                ";                
                break;
              case 'deleteFile': 
                $sql = "DELETE FROM archivo WHERE id_archivo=".$json->id;
                //echo "borrar id: " . $json->id; 
                break;   
              case 'renameFile':

                $sql = "
                  UPDATE FROM archivo 
                  SET nombre=".$_POST['nombre']."
                  WHERE id =".$json->id;

                break;
              default:  echo "error";  
              
            } 
            
            $mysqli->query($sql);
            if($mysqli->affected_rows == -1) {
              echo "                
                <div align='center'>
                  El nombre de la carpeta <b style='color: red'>".$_POST['nombreCarpeta']."</b> ya existe
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
                              c.id_carpeta, c.nombre, arc_sub.id_archivo_subcarpeta, 
                              arc_sub.sub_nombre, arc_sub.es_carpeta
                            FROM carpeta c
                            LEFT JOIN archivo_subcarpeta arc_sub ON c.id_carpeta=arc_sub.id_carpeta
                            ORDER BY c.id_carpeta, arc_sub.id_carpeta, arc_sub.es_carpeta
                          ";
                          $sql = "
                            SELECT *
                            FROM archivo a
                            LEFT JOIN enlace_carpeta_padre ecp ON a.id_archivo=ecp.id_padre
                          ";
                          //$sql="SELECT * FROM carpeta";
                          $result = $mysqli->query($sql) or die($mysqli->error . "_");
                          echo "<form action='' method='POST' id='form_accion'>";
                          while($row = $result->fetch_object()) {
                            
                            ?>
                              <tr>
                                <td>
                                    <?php 
                                    if($row->es_carpeta == 1) {
                                      echo "<i class='glyphicon glyphicon-folder-open' style='padding-right: 20px'></i>";                                      
                                      echo $row->nombre;  
                                    } else {
                                      echo "<i class='glyphicon glyphicon-file' style='padding-right: 20px'></i>";
                                      echo $row->nombre;
                                    } 
                                    ?> 
                                </td>
                                <td>                                  
                                  <a href='#' onclick='addFile(this.id);' id='<?php echo $row->id_archivo ?>' class='glyphicon glyphicon-file' style='padding: 5px'></a>
                                  <a href='#' onclick='createFolder(this.id);' id='<?php echo $row->id_archivo ?>' class='glyphicon glyphicon-folder-open' style='padding: 5px'></a>
                                  <a href='#' onclick='renameFile(this.id);' id='<?php echo $row->id_archivo ?>' class='glyphicon glyphicon-pencil' style='padding: 5px'></a>
                                  <a href='#' onclick='deleteFile(this.id);' id='<?php echo $row->id_archivo ?>' class='glyphicon glyphicon-remove' style='padding: 5px'></a>

                                </td>                          
                              </tr>
                            <?php

                          } //Fin while
                          $mysqli->close();
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
          
          //$mysqli->close();
      ?>
      
      

    
  <!-- Modal -->
  <div class="modal fade" id="crearCarpeta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>          
          <h5 class="modal-title" id="myModalLabel">
            <b>Crear carpeta</b>
          </h5>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="formulario">
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Nombre:</label>
            <input type="text" class="form-control" id="recipient-name" name="nombreCarpeta">
          </div>                
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="crear">Crear</button> 
        <input type="hidden" name="datos" class="datos" value="">
        </form>
      </div>
    </div>
  </div>
  </div>
  <!-- Fin Modal -->
  
  

  <script src="js/jquery-1.11.1.min.js" type="text/javascript"></script>
  <script src="lib/js/jquery-1.8.2.min.js"></script>  
  <script src="lib/js/jquery.alerts.mod.js" type="text/javascript"></script>
  <script src="js/bootstrap.min.js" type="text/javascript"></script>
  <script src="js/inicio.js" type="text/javascript"></script>
  <script src="js/functions.js" type="text/javascript"></script>


  </body>
</html>

