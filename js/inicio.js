$(function() {		
	
	
	//$( ".fecha_input" ).datepicker( $.datepicker.regional['es'] );	

	//Evitar el reenvio de formulario
	if (window.history.replaceState) { // verificamos disponibilidad
    	window.history.replaceState(null, null, window.location.href);
	}	
	$("#formulario").submit(function(){						
		if($.trim($("#recipient-name").val()) != "") {
			var datos = {accion: "createFolder", id: null}
			$(".datos").val(JSON.stringify(datos));

			jWait('<img src="lib/img/icon-alerts/loading.gif" /><br /><br />', "Procesando Consulta");			
			return true;
		} else
			return false;		
		
	});
	/*var datos = {
		accion: "deleteFile",
		id: ""
	}
	$(".datos").val(JSON.stringify(datos));*/
	

});

function addFile(id) {
	console.log(id + "__");
}
function createFolder(id) {
	console.log(id + "__");
}
function renameFile(id) {
	console.log(id + "__");
}
function deleteFile(id) {
	var datos = {
		accion: "deleteFile",
		id: id
	}
	jConfirm("Estas seguro que quieres eliminar la carpeta?", 'Dialogo de Confirmacion', function(result) {
		if(result) {
			//$("#accion").val("deleteFile_" + id);
			$("#datos").val(JSON.stringify(datos));
			$("#form_accion").submit();
		}
	})
}

