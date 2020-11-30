$(function() {		
	
	
	//$( ".fecha_input" ).datepicker( $.datepicker.regional['es'] );	

	//Evitar el reenvio de formulario
	if (window.history.replaceState) { // verificamos disponibilidad
    	window.history.replaceState(null, null, window.location.href);
	}	
	$("#form_modal").submit(function(){						
		if($.trim($("#recipient-name").val()) != "") {			
			if(($.trim($(".datos").val())) == "") {				
				var datos = {accion: "createFolder", id: null}				
				$(".datos").val(JSON.stringify(datos));				
			}	
			jWait('<img src="lib/img/icon-alerts/loading.gif" /><br /><br />', "Procesando Consulta");		
			return true;
		} else
			return false;		
		
/*
		if($.trim($("#recipient-name").val()) != "") {
			if($.trim() = "")
				console.log("crearFolder");
			else
				console.log("otra accion");
			//var datos = {accion: "createFolder", id: null}
			//$(".datos").val(JSON.stringify(datos));

			//jWait('<img src="lib/img/icon-alerts/loading.gif" /><br /><br />', "Procesando Consulta");			
			//return true;
			return false;
		} else
			return false;		
*/		
	});	
	/*$("#btnClose").click(function() {
		$(".datos").val("");
	});*/
	
	$("#formUploadFiles").submit(function(){
		
        //Obtener el numero de adjuntos
        var totalAdjuntos = $("#file-input_wrap_list").children().length;
		if(totalAdjuntos > 1) {		
			return false;
		} else if(totalAdjuntos <= 0) {
			jAlert( "Debes adjuntar 1 archivo");
			return false;	
		} else 
			return true;
	})
	

	//Cuando se cierra el modal reestablecer valores
	$('#crearCarpeta').on('hidden.bs.modal', function(event) {				
		$(".datos").val("");
		$('#titulo').text('Crear carpeta:');
		$('#texto').text('Nombre:');
		$('input[name="nombreCarpeta"]').val("");
		$('#crear').text('Crear');
	});
	$('#uploadFiles').on('hidden.bs.modal', function(event) {
		$(".datos").val("");

//		if($("#file-input_wrap_list").children().length > 0)
//			$("#file-input_wrap_list").children().remove();
	});
});

function addFile(id) {

	var datos = {
		accion: "addFile",
		id: id
	}
	$(".datos").val(JSON.stringify(datos));		
	$('#uploadFiles').modal('show');

}
function createSubFolder(id) {
	var datos = {
		accion: "createSubFolder",
		id: id
	}
	$(".datos").val(JSON.stringify(datos));

	$('#crearCarpeta').modal('show');
	$('#titulo').text('Crear Sub-Carpeta');
	$('#texto').text('Nombre');
	$('#crear').text('Crear');
}
function renameFile(id) {
	var datos = {
		accion: "renameFile",
		id: id
	}
	$(".datos").val(JSON.stringify(datos));

	$('#crearCarpeta').modal('show');
	$('#titulo').text('Renombrar');
	$('#texto').text('Nuevo nombre');
	$('#crear').text('Renombrar');
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

