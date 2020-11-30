<?php 
function subeArchivo() {
	$numArchivos = sizeof( $_FILES['archivo']['tmp_name'] );	
	$cincoMegas = 5242880;		
	$sizeTotalArchivos = 0;
	$error = false;

	if( $numArchivos == 1 ) {
		$sizeTotalArchivos = $_FILES['archivo']['size'][0];
		if( $sizeTotalArchivos < $cincoMegas ) {
			copy( $_FILES['archivo']['tmp_name'][0], "uploads/" . $_FILES['archivo']['name'][0] ); 
		} else {
			$error = true; 
								
		}
	} 
	return $error;
}
?>