<?PHP
	// connect to the database
	ini_set("memory_limit", "256M"); 
	include "../php/clase/application.php";
	//include "../php/clase/function.php";
	$obj = new DB_mysql();
	$obj->conectar($bd, $host, $user, $pass, $log);
	$consulta="
	SELECT cod_dane 
	FROM datos_colegio 
	WHERE sede='1'
	";
	$obj->consulta($consulta);
	if($obj->numregistros()<1)//if(1)
		{
			//Error 100:no hay configuracion
		}
	else if($obj->numregistros()==1)//if(1)
		{
			$col=$obj->result_array_asoc();
			$colegio_1=$col[0]['cod_dane'];
		}
	$consulta="
	SELECT tipo, calf 
	FROM tipo_usuarios
	";
	$obj->consulta($consulta);
	if($obj->numregistros()<1)//if(1)
		{
			//Error 101:no hay tipo usuarios
		}
	else if($obj->numregistros()==1)//if(1)
		{
			$t_usuarios=$obj->result_array_asoc();
			for ($i=0; $i < $obj->numregistros(); $i++) { 
				$tucalf[$t_usuarios[$i]['tipo']]=$t_usuarios[$i]['calf'];
			}
		}
	//$obj->mensaje($_SESSION['user'].$_SESSION['user_valied'].$_SESSION['colegio']);
	if(!$_SESSION['user'] 
		|| !$_SESSION['user_valied']
		|| !$_SESSION['colegio']  
		|| !strstr($_SESSION['colegio'], $colegio_1)  
		|| $tucalf[$_SESSION['user_valied']]=='N'){

    	$b="Calificaciones/form_calificaciones.php";
		$a=str_replace($b, '',$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
		header ("location: http://".$a); 
		$obj->mensaje('Debe iniciar sesion');
        //$obj->saltar_pagina("../index.php");
        exit();
    }
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="es"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="es"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="es"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="es" class="ui-mobile-rendering"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title></title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width">
<?PHP
	include "../php/head.php";
?>
</head>
<body>
<?PHP
  	$obj->menu();
?>
<!--CONTENIDO-->
	<div class="row show-grid">
	    <div class="span2">
<?PHP
for ($i=0; $i < 18; $i++) { 
	echo "<br/>";
}
?>
	    </div>
	    <div class="span10">
	    	<form class="well form-horizontal" id="form1" action="#" method="POST" enctype="application/x-www-form-urlencoded">
	    		<fieldset>
			    	<legend>Formulario de Calificaciones</legend>
			    	<div class="alert alert-info">
			    		<button class="close" data-dismiss="alert">×</button>
					  	Todos los campos son necesarios
					</div>
					<style>
	        			.img_docente{
	        				position: absolute;
						    right: 10%;
						    z-index: 10000000;
	        			}
	        		</style>
	        		<div class="img_docente">
	        			<img id="form1-imagend" src="../fotos/anonimo.jpg" style="height:100px; width:100px;">
	        		</div>
<?PHP
/***VARIABLES***/
$id_docente="";//id del docente
/***fin-VARIABLES***/
if(((int)$_SESSION['user_valied']) == 4){
        $id_docente = $_SESSION['user'];
    }
if ($id_docente=="") {
?>
					<div class="control-group">
			      		<label class="control-label" for="form1-year">Seleccionar Año</label>
			      		<div class="controls">
			        		<select class="span5" id="form1-year" name="form1-year">
			        			<option value="x" seleted>...</option>
<?PHP 
$consulta="
	SELECT y.id_year, y.year 
FROM years AS y 
WHERE y.modificar =1
ORDER BY year DESC
	";
	$obj->consulta($consulta);
	if($obj->numregistros()<1)//if(1)
		{
			//Error 102:no hay years habilitados para modificar notas.
		}
	else if($obj->numregistros()>=1)//if(1)
		{
			$result=$obj->result_array_asoc();
			for ($a=0; $a < $obj->numregistros(); $a++) { 
				echo '<option value="'.$result[$a]['id_year'].'">'.$obj->html_decode($result[$a]['year']).'</option>';
			}
		}
?>
			        		</select>
			      		</div>
			    	</div>
			    	<div class="control-group">
			      		<label class="control-label" for="form1-docente">Seleccionar Docente</label>
			      		<div class="controls">
			        		<select class="span5" id="form1-docente" name="form1-docente">
			        			<option value="x" seleted>...</option>
			        		</select>
			      		</div>
			    	</div>
			    	<div class="control-group">
			      		<label class="control-label" for="form1-pensum">Seleccionar Materia</label>
			      		<div class="controls">
			        		<select class="span5" id="form1-pensum" name="form1-pensum">
			        			<option value="x" seleted>...</option>
			        		</select>
			      		</div>
			    	</div>
			    	<div class="control-group">
			      		<label class="control-label" for="form1-periodo">Seleccionar Periodo</label>
			      		<div class="controls">
			        		<select class="span5" id="form1-periodo" name="form1-periodo">
			        			<option value="x" seleted>...</option>
			        		</select>
			      		</div>
			    	</div>
			    	<table class="table table-striped table-bordered table-condensed" id="form1-logros">
						
					</table>

			    	
<?PHP
	}
else {
	$consulta="
	SELECT y.id_year, y.year
FROM years AS y 
INNER JOIN configura AS c ON y.year=c.year_v 
	";
	$obj->consulta($consulta);
	if($obj->numregistros()<1)//if(1)
		{
			//Error 104:No hay year vigente.
		}
	else if($obj->numregistros()==1)//if(1)
		{
			$array_years=$obj->result_array_asoc();
			echo '
				<input type="hidden" class="input-small span5" value="'.$array_years[0]['id_year'].'" id="form1-year" name="form1-year">
				<input type="hidden" class="input-small span5" value="'.$id_docente.'" id="form1-docente" name="form1-docente">
			';
		}
}
?>
				<div class="alert alert-block" style="display: none;" id="form1-alerta">
				  	<h4 class="alert-heading"></h4>
				  	<p></p>
				</div>
			</form>
		</div>
	</div>
	<!--/CONTENIDO-->
	<!--FORM-AUS-->
	<form id="form2" style="display:none;" action="#" method="POST" enctype="application/x-www-form-urlencoded">
	</form>
	<!--/FORM-AUS-->
	<script>
	var peto="form1-";
   	$(function(){
    	$('#'+peto+'year').focus();
    	$("#"+peto+"docente").change(function () {
    		var foto=$(this).find(':selected').attr('data-foto');
    		$("#"+peto+"imagend").attr('src','../fotos/'+foto);
    	});
    	$("#"+peto+"year").change(function () {
    		year();
    		docente();
    		materia();
    		periodo();
    		logros();
    	});
    	$("#"+peto+"docente").change(function () {
    		materia();
    		logros();
    	});
    	$("#"+peto+"pensum").change(function () {
    		logros();
    	});
    	$("#"+peto+"periodo").change(function () {
    		logros();
    	});
    	function year(){
				$("#"+peto+"docente").empty().prepend('<option value="x" seleted>...</option>');
				$("#"+peto+"periodo").empty().prepend('<option value="x" seleted>...</option>');
				$("#"+peto+"pensum").empty().prepend('<option value="x" seleted>...</option>');
				$("#"+peto+"pensum").empty().prepend('<option value="x" seleted>...</option>');
    	}
    	function docente(){
    		var year=$("#"+peto+"year").find(':selected').val();
    		if (year!='x') {
    			$("#form2").attr('action', 'form_calificaciones_ajax.php');
		    	var imp='<input type="hidden" value="2" name="form1-opcion">';
		    	imp+='<input type="text" value="'+year+'" name="form1-year">';
		    	$("#form2").empty().prepend(imp);
		    	$('#form2').ajaxForm({ 
					beforeSubmit: function(){
				        return true;
					},
					success:   function(data){
						//alert(data);
						if(data=='1')
						{
							//alert("2");
							alerta(
			    			'form1-alerta',
			    			'Error 106:',
			    			'No hay Docentes con pensum asignado para este año!',
			    			'1'
			    			);
						}
						else
						{
							//alert("3");
							$("#"+peto+"docente").empty().prepend('<option value="x" seleted>...</option>'+data);
						}
						return true;
					} 
				}).submit(); 
    		}
    		else{
    			$("#"+peto+"docente").empty().prepend('<option value="x" seleted>...</option>');
    		}
    	}
    	function periodo(){
    		var year=$("#"+peto+"year").find(':selected').val();
    		if (year!='x') {
	    		$("#form2").attr('action', 'form_calificaciones_ajax.php');
		    	var imp='<input type="hidden" value="3" name="form1-opcion">';
		    	imp+='<input type="text" value="'+year+'" name="form1-year">';
		    	$("#form2").empty().prepend(imp);
		    	$('#form2').ajaxForm({ 
					beforeSubmit: function(){
				        return true;
					},
					success:   function(data){
						if(data.message=='1')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 107:',
			    			'No hay Periodos creados para este año!',
			    			'1'
			    			);
						}else if(data.message=='2')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 108:',
			    			'No hay Configura!',
			    			'1'
			    			);
						}
						else
						{
							$("#"+peto+"periodo").empty().prepend('<option value="x" seleted>...</option>'+data);
						}
						return true;
					} 
				}).submit();
			}
			else{
				$("#"+peto+"periodo").empty().prepend('<option value="x" seleted>...</option>');
			}
    	}
    	function materia(){
    		var year=$("#"+peto+"year").find(':selected').val();
    		var value=$("#"+peto+"docente").find(':selected').val();
    		if (value!='x' && year!='x') {
    			$("#form2").attr('action', 'form_calificaciones_ajax.php');
		    	var imp='<input type="hidden" value="1" name="form1-opcion">';
		    	imp+='<input type="text" value="'+value+'" name="form1-docente">';
		    	imp+='<input type="text" value="'+year+'" name="form1-year">';
		    	$("#form2").empty().prepend(imp);
		    	$('#form2').ajaxForm({ 
					beforeSubmit: function(){
				        return true;
					},
					success:   function(data){
						if(data.message=='1')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 105:',
			    			'No hay pensum asignado al docente para el año seleccionado!',
			    			'1'
			    			);
						}
						else
						{
							$("#"+peto+"pensum").empty().prepend('<option value="x" seleted>...</option>'+data);
						}
						return true;
					} 
				}).submit(); 
    		}
    		else{
    			$("#"+peto+"pensum").empty().prepend('<option value="x" seleted>...</option>');
    		}
    	}
    	function logros(){
    		var year=$("#"+peto+"year").find(':selected').val();
    		var docente=$("#"+peto+"docente").find(':selected').val();
    		var periodo=$("#"+peto+"periodo").find(':selected').val();
    		var pensum=$("#"+peto+"pensum").find(':selected').val();
    		if (docente!='x' && year!='x' && periodo!='x' && pensum!='x' && periodo!="5") {
    			$("#form2").attr('action', 'form_calificaciones_ajax.php');
		    	var imp='<input type="hidden" value="4" name="form1-opcion">';
		    	imp+='<input type="text" value="'+docente+'" name="form1-docente">';
		    	imp+='<input type="text" value="'+periodo+'" name="form1-periodo">';
		    	imp+='<input type="text" value="'+pensum+'" name="form1-pensum">';
		    	imp+='<input type="text" value="'+year+'" name="form1-year">';
		    	$("#form2").empty().prepend(imp);
		    	$('#form2').ajaxForm({ 
					beforeSubmit: function(){
				        return true;
					},
					success:   function(data){
						if(data=='1')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 109:',
			    			'No existe el pensum!',
			    			'1'
			    			);
						}
						else if(data=='2')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 110:',
			    			'Existe mas de un pensum con el mismo id!',
			    			'1'
			    			);
						}else if(data=='3')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 111:',
			    			'No existe el curso origen!',
			    			'1'
			    			);
						}else if(data=='4')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 112:',
			    			'Existe mas de un curso origen!',
			    			'1'
			    			);
						}else if(data=='5')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 113:',
			    			'No existen frases!',
			    			'1'
			    			);
						}else if(data=='6')
						{
							alerta(
			    			'form1-alerta',
			    			'Error 108:',
			    			'No hay Configura!',
			    			'1'
			    			);
						}
						else
						{
							$("#"+peto+"logros").empty().prepend(data);
						}
						return true;
					} 
				}).submit(); 
    		}
    		else{
    			$("#"+peto+"logros").empty();
    		}
    	}
    });
	/*menu*/
	Ext.onReady(function() {
        new Ext.ux.Menu('menu', {
			transitionType: 'slide',
            direction: 'vertical'
        });
    });
    /*/menu*/
    /**********************/
    /**********************/
    function alerta(id, titulo, contenido, tipo){//tipo = 1:alert-error,2:alert-success,3:alert-info
    	var select=$("#"+id);
    	var clases="alert ";
    	if(tipo=='1'){ clases+="alert-error";}
    	else if(tipo=='2'){clases+="alert-success";}
    	else if(tipo=='3'){clases+="alert-info";}
    	else { return false;}
    	select.removeAttr('class').addClass(clases);
    	$("#"+id+" > h4").empty().prepend(titulo);
    	$("#"+id+" > p").empty().prepend("<em>"+fechaok(14)+"</em> => "+contenido);
    	select.show();
    	return true;
    };
    /**********************/
    function fechaok(opcion){
    	var fecha=new Date();
		var diames=fecha.getDate();
		var diasemana=fecha.getDay();
		var mes=fecha.getMonth() +1 ;
		var ano=fecha.getFullYear();
		var hora = fecha.getHours() 
		var minuto = fecha.getMinutes() 
		var segundo = fecha.getSeconds() 

		var textosemana = new Array (7); 
		textosemana[0]="Domingo";textosemana[1]="Lunes";textosemana[2]="Martes";textosemana[3]="Miércoles";textosemana[4]="Jueves";textosemana[5]="Viernes";textosemana[6]="Sábado";

		var textomes = new Array (12);
		textomes[1]="Enero";textomes[2]="Febrero";textomes[3]="Marzo";textomes[4]="Abril";textomes[5]="Mayo";textomes[6]="Junio";textomes[7]="Julio";textomes[7]="Agosto";textomes[9]="Septiembre";textomes[10]="Octubre";textomes[11]="Noviembre";textomes[12]="Diciembre";
		switch (opcion){
			case 1:
				return fecha;
			break;
			case 2:
				return diames;
			break;
			case 3:
				return diasemana;
			break;
			case 4:
				return mes;
			break;
			case 5:
				return ano;
			break;
			case 6:
				return diames + "/" + mes + "/" + ano;
			break;
			case 7:
				return textosemana[diasemana] + " " + diames + "/" + mes + "/" + ano;
			break;
			case 8:
				return textosemana[diasemana] + ", " + diames + " de " + textomes[mes] + " de " + ano;
			break;
			case 9:
				return hora;
			break;
			case 10:
				return minuto;
			break;
			case 11:
				return segundo;
			break;
			case 12:
				return hora + ":" + minuto;
			break;
			case 13:
				return hora + ":" + minuto + ":" + segundo ;
			break;
			case 14:
				return diames + "/" + mes + "/" + ano +" "+ hora + ":" + minuto + ":" + segundo ;
			break;
			default:
				return 0;
		}
    }
    </script>
</body>
</html>