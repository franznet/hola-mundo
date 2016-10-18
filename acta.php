<?php
	// Incluimos librerias	
  require_once("/data/sesion.php");
  require_once("data/general.php");
  
  // Parametros fechas de vista
  $sFechaIni = (isset($_POST['fechaini'])? $_POST['fechaini'] : null);  // Fecha rango de vista inicial
  $sFechaFin = (isset($_POST['fechafin'])? $_POST['fechafin'] : null);  // Fecha rango de vista final
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="icon" href="images/favicon.ico" type="image/x-icon">
	<script src="jquery/jquery-2.2.4.min.js"></script>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"/>	
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-waitingfor.js"></script>
	<link rel="stylesheet" href="js/bootstrap-dialog.min.css"/>
	<script type="text/javascript" src="js/bootstrap-dialog.min.js"></script>
	<link rel="stylesheet" href="js/aev.css"/>
	<script type="text/javascript" src="js/aev.js"></script>
	<!-- Bootstrap Date-Picker Plugin -->
	<link rel="stylesheet" href="js/bootstrap-datepicker3.css"/>
	<script type="text/javascript" src="js/bootstrap-datepicker.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-datepicker.es.min.js"></script>
  <script src="js/jquery.redirect.js" type="text/javascript"></script>
	<link href="js/tablesorter/css/theme.bootstrap_2x.min.css" rel="stylesheet" type="text/css"/>
	<script src="js/tablesorter/js/jquery.tablesorter.min.js" type="text/javascript"></script>
	<script src="js/tablesorter/js/jquery.tablesorter.widgets.min.js" type="text/javascript"></script>
	<title>ACTAS</title>
	<script>
    $(document).ready(function(){
        var date_input=$('input[name="date"]'); //our date input has the name "date"
        var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
        date_input.datepicker({
					format: 'dd/mm/yyyy',
					container: container,
					todayHighlight: true,
					autoclose: true,
					language: "es",					
					daysOfWeekHighlighted: "0",    
        })
		});
		// TableSorter
		$.tablesorter.themes.bootstrap = {
			// these classes are added to the table. To see other table classes available,
			// look here: http://getbootstrap.com/css/#tables
			table        : 'table table-bordered table-striped',
			caption      : 'caption',
			// header class names
			header       : 'bootstrap-header azul4', // give the header a gradient background (theme.bootstrap_2.css)
			sortNone     : '',
			sortAsc      : '',
			sortDesc     : '',
			active       : '', // applied when column is sorted
			hover        : '', // custom css required - a defined bootstrap style may not override other classes
			// icon class names
			icons        : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
			iconSortNone : 'bootstrap-icon-unsorted', // class name added to icon when column is not sorted
			iconSortAsc  : 'glyphicon glyphicon-chevron-up', // class name added to icon when column has ascending sort
			iconSortDesc : 'glyphicon glyphicon-chevron-down', // class name added to icon when column has descending sort
			filterRow    : '', // filter row class; use widgetOptions.filter_cssFilter for the input/select element
			footerRow    : '',
			footerCells  : '',
			even         : '', // even row zebra striping
			odd          : ''  // odd row zebra striping
		};
    // Elimina Acta
    function EliminaActa(id_acta, fecha){
      BootstrapDialog.show({
        message: '¿Seguro que desea eliminar acta con ID: ' + id_acta.toString() + ' de Fecha ' + fecha + '?',
        buttons: [
          { label: 'Cancelar',
            icon: 'glyphicon glyphicon-remove',
            cssClass: 'btn-danger',
            action: function(dialogRef){ 
              dialogRef.close(); 
            }
          },
          { label: 'Aceptar',
            icon: 'glyphicon glyphicon-ok',
            cssClass: 'btn-primary',            
            action: function(dialogRef){
              dialogRef.enableButtons(false);
              // Eliminar Acta
              var parametros = { 'id_acta': id_acta };
              $.ajax({
                data:  parametros,
                url:   'data/ws/acta_delete.php',
                type:  'post',
                beforeSend: function (){},
                success: function (responseText){
                  var result = responseText;
                  // Es nulo?
                  if(!result.hasOwnProperty("success")){
                    result = {};
                    result.success = false;
                    result.data = responseText;
                  }
                  // Hubo algun error?
                  if (result.success){
                    // Refresca vista
                    CargarDatos();
                  }
                  else Msg.Error(result.data);                  
                },
                error: function (msg){
                  Msg.Error(msg.responseText);                  
                }
              });  
              // Cerrar dialogo 
              dialogRef.close();
            }
          }          
        ]
      });
    }
    // Modifica acta
    function ActualizaActa(id_acta){
      $.redirect('acta_edit.php', { 
        'id_operacion':'U', 
        'id_acta':id_acta, 
        'fechaini': $("#datInicio").val(),  // Seleccion de rango fecha inicial
        'fechafin':$("#datFin").val()       // Seleccion de rango fecha final
      });
      //$.post("acta_edit.php", { id_acta: id_acta },function(data){});      
      //return false;      
    }
    function AgregaActa(){
      $.redirect('acta_edit.php', { 'id_operacion':'I' });
    }
    function ReporteActa(id_acta){
      $.redirect('acta_report.php', { 'id_acta':id_acta }, "POST", "_blank");
    }    
	</script>
</head>
<body class="">
	<!-- Cabecera -->
	<div role="navigation" class="navbar navbar-inverse navbar-default navbar-static-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="#" class="navbar-brand">
					<span class="glyphicon glyphicon-stop" aria-hidden="true"></span>
					ACTAS
				</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="home.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Inicio</a></li>					
					<li class="active"><a href="acta.php"><span class="glyphicon glyphicon-th" aria-hidden="true"></span> Actas</a></li>
          <li><a href="responsable.php"><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span> Responsable Tareas</a></li>
          <li><a href="seguimiento.php"><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span> Seguimiento Tareas</a></li>
          <li><a href="dependiente.php"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> Dependientes</a></li>
				</ul>
        <form class="navbar-form navbar-right" role="search">
					<button type="button" class="btn btn-primary btn-sm"
									aria-haspopup="true" aria-expanded="false"
									title="<?php echo $_SESSION['usuario']->nombrecompleto ?>&#013;Cambiar Contraseña" onclick="CambiarContrasenia(this);">
						<span class="glyphicon glyphicon-user active"></span>
					</button>
          <button id="btnSalir" type="button" class="btn btn-danger" data-loading-text="<span class='glyphicon glyphicon-log-out' aria-hidden='true'></span> Saliendo..." 
                  title="Salir" onclick="$.redirect('logout.php');">
            <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> SALIR
          </button>
        </form>
			</div>
		</div>
	</div>
	
	<!-- Titulo -->
	<div class="container-fluid titulo">
		<h3>Listado de Actas de Informes y Compromisos</h3>
	</div>
	
	<!-- Cuerpo -->
	<div class="container-fluid">
		<div class="row" style="padding-left: 15px; padding-right: 15px;">
			<div class="btn-group pull-rigth" style="padding: 0px;">				
				<form id="frmMain" role="form" method="post" class="form-horizontal">
					<div class="form-group form-group-sm"> <!-- RANGO DE FECHAS -->
						<label class="control-label col-md-2" style="text-align: left;">RANGO FECHAS ACTAS:</label>
						<div class="col-md-5">
							<div class="input-group" id="datepicker">
								<input id="datInicio" type="text" name="date" class="input-sm form-control" placeholder="DD/MM/YYYY" 
                       value="<?php echo (!empty($sFechaIni))? $sFechaIni:FechaInicioMes() ?>"/>
								<span class="input-group-addon">&nbsp;-&nbsp;</span>
								<input id="datFin" type="text" name="date" class="input-sm form-control" placeholder="DD/MM/YYYY" 
                       value="<?php echo (!empty($sFechaFin))? $sFechaFin:FechaFinMes() ?>"/>
							</div>
						</div>
						<div class="col-md-2">						
							<button id="btnProcesar" type="button" class="btn btn-primary btn-sm" data-loading-text="<span class='glyphicon glyphicon-repeat' aria-hidden='true'></span> Procesando..." aria-label="Left Align" autocomplete="off">
								<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> Procesar
							</button>
						</div>
					</div>
					<button id="btnAgregar" type="button" class="btn btn-success btn-xs" data-loading-text="<span class='glyphicon glyphicon-repeat' aria-hidden='true'></span> Procesando..." 
                  title="Agregar nueva acta" onclick="AgregaActa()">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> AGREGAR
					</button>
				</form>
			</div>
		</div>
		
		<div id="divResultado" class="row sombra" style="padding-left: 15px; padding-right: 15px;">
			<table id="tabActa" class="table table-hover table-striped table-bordered">
				<thead>
					<tr class="azul4">
					  <th style="width:50px;white-space:nowrap;text-align:center;">#</th>
						<th style="padding:3px;text-align:center;vertical-align:middle;"><small>ID</small></th>
						<th>Fecha</th>
						<th class="filter-select filter-exact">Nombre</th>
						<th>Cargo</th>
						<th class="filter-select filter-exact">Direcci&oacute;n</th>
						<th>Objeto</th>
						<th>Itinerario</th>
            <th>Actividades</th>
            <th>Tareas</th>
					</tr>
				</thead>
				<tbody>       
				</tbody>
				<tfoot>
          <tr class="azul3">
            <th colspan=10>TOTAL ACTA: </th>
          </tr>
        </tfoot>
			</table>
		</div>		
	</div>
	<br>
	<br>
	<br>
	
	<!-- Pie -->
	<nav class="navbar navbar-default navbar-bottom navbar-inverse footer" role="navigation" style="margin-bottom: 0px;">
    <div class="container text-center" style="width:100%;">
      <br>
      <table class="container" style="width:100%;">
        <tr>
          <td style="text-align:left;"><p style="color:#aaa">Copyright © <a href="http://www.aevivienda.gob.bo" target="_blank"><b>AGENCIA ESTATAL DE VIVIENDA</b></a>, 2016</p></td>
          <td style="text-align:right;"><p style="color:#aaa">Diseñado y Desarrollado por FranzNet</p></td>
        </tr>
      </table>
      <br>
    </div>
	</nav>
	
	<script>
    function bValidar(){
			var bResult = false;
			// Valores seleccionados
      var iFechaIni=sFechaISO($("#datInicio").val()).replace(/[-]/g,'');
      var iFechaFin=sFechaISO($("#datFin").val()).replace(/[-]/g,'');
			if(iFechaIni>iFechaFin){
				Msg.Error('¡Fecha Inicio de rango no puede ser mayor a Fecha Final, favor corregir!');
				return bResult;
			}
			
			return true;
		}
    function CargarDatos(){
      // Validacion
      if(!bValidar()) return;

      // Proceso de carga
			var parametros = { "ini": $("#datInicio").val(),
												 "fin": $("#datFin").val()
											 };
			$.ajax({
				data:  parametros,
				url:   'data/ws/acta_list.php',
				type:  'post',
				beforeSend: function () {
					waitingDialog.show('Por favor aguarde...');
				},
				success: function (responseText) {
					var result = responseText;
					
					// Es nulo?
          if (!result.hasOwnProperty("success")){
            result = {};
            result.success = false;
            result.data = responseText;
          }
					if (result.success){
						result = result.data;
						// Variables						
            trHTML='';						
						
						$.each(result, function (i, item) {
							trHTML +=
                '<tr>'+
                ' <td style="padding:0px;vertical-align:middle;text-align:center;"><small>'+(i+1)+'</small><br>'+
                '  <div class="btn-group" role="group" aria-label="...">'+
                '   <button type="button" class="btn btn-warning btn-xs" aria-label="Left Align" title="Editar acta" onclick="ActualizaActa('+result[i]['id_acta']+')">'+
                '    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>'+
                '   </button>'+
                '   <button type="button" class="btn btn-danger btn-xs" aria-label="Left Align" title="Eliminar acta" onclick="EliminaActa('+result[i]['id_acta']+',\''+result[i]['fecha']+'\')">'+
                '    <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>'+
                '   </button>'+
                '  </div>'+
                '  <div class="btn-group" role="group" aria-label="...">'+
                '   <button type="button" class="btn btn-primary btn-xs" aria-label="Left Align" title="Reporte acta" onclick="ReporteActa('+result[i]['id_acta']+')">'+
                '    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>'+
                '   </button>'+
                '  </div>'+
                ' </td>'+
                ' <td style="padding:3px;text-align:center;"><small>' + result[i]['id_acta'] + '</small></td>'+	
                ' <td>' + result[i]['fecha'] + '</td>'+
                ' <td>' + result[i]['usuario'] + '</td>'+	'<td>' + result[i]['cargo'] + '</td>'+
                ' <td>' + result[i]['direccion'] + '</td>'+	'<td>' + result[i]['objeto'] + '</td>'+
                ' <td>' + result[i]['itinerario'] + '</td>'+
                ' <td style="text-align:center">' + result[i]['actividades'] + '</td>'+	
                ' <td style="text-align:center">' + result[i]['tareas'] + '</td>'+
                '</tr>';
						});						
            $('#tabActa tbody').html(trHTML);
            $('#tabActa tfoot').html('<tr class="azul3"><th colspan=10>TOTAL TAREAS: '+result.length+'</th></tr>');
						$('#tabActa').tablesorter({
							theme : "bootstrap",
							dateFormat : "ddmmyyyy", // set the default date format
							widthFixed: true,
							headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!
							onRenderHeader: function(index){
								$(this).addClass('azul4');
							},
							widgets : [ "uitheme", "filter", "zebra" ],
							widgetOptions : {
								zebra : ["even", "odd"],
								filter_reset : ".reset",
								filter_cssFilter: "form-control",
								uitheme : "bootstrap"
							}
						});
						$('#tabActa').trigger("update");
					}
					else Msg.Error(result.data);
					waitingDialog.hide();
				},
				error: function (msg) {
					Msg.Error(msg.responseText);
					waitingDialog.hide();
				}
			});
    }
    $('#btnProcesar').click(function () {
      // Cargar datos de actas
      CargarDatos();
    });
    $("#datInicio" ).change(function() { CargarDatos(); });
    $("#datFin" ).change(function() { CargarDatos(); });
    
    $(function () {
      // Cargar datos de actas
      CargarDatos();
		});
	</script>
</body>
</html>
