<?php
	$titulo="Nueva Novedad";
	$seccion="novedades";
	$funcionValidacion = "formValidationNovedad";
	$hayImagenes = true;
	$to="show.php?t=$seccion";
?>

<div id="wrapper" style="width:500px;">
	<h1 style="margin-bottom: 20px;"><?php echo $titulo ?></h1>
	<?php 
		include('../conexion.php');		
		if( isset($_POST['nombre']) && isset($_POST['descripcion'])) {
			postString("nombre",$nombre);
			postString("descripcion", $descripcion);
			postBool("visible",$visible);		
			
			$random = randomString(6);		
			$imagen = $nombre . "-" . $random;
			$imagen = preg_replace("/[^A-Za-z0-9\-]/",'',$imagen);
			val_img("imagenGrande",$imagen,"../db/novedades/",$problemaImagenGrande,$logGrande);
			
			if ($nombre != "" && strlen($nombre)<=40 &&  strlen($descripcion)>0) {
				if ( !$problemaImagenGrande) {
					$orderBy = mysql_maximo('productos', 'orderBy') + 1;
					$descripcion =  nl2br($descripcion);
					
					$query = "INSERT INTO novedades (nombre, info, imagen, visible, orderBy, borrado) VALUES ( '$nombre', '$descripcion', '$imagen', $visible, $orderBy, 0 )";
					mysql_query($query, $conexion);
					$id = mysql_insert_id();
					if (mysql_error() == ''){
						echo '<ul id="log"> <li>Se ha cargado los datos correctamente</li></ul>';
								
						move_uploaded_file($_FILES["imagenGrande"]["tmp_name"], "../db/novedades/". $imagen .".jpg");				
						guardarActividad($_SESSION['userActual'], "El usuario ha creado la novedad: \"$nombre\" ID: $id.");
						redireccionar($to);
					}
					else {
						echo '<ul id="log" class="log"> <li>'. mysql_error().'</li></ul>';
					}
				}
				else {
					if ($logGrande != "") $logGrande = "<li>".$logGrande."</li>";

					echo '<ul id="log" class="log">'. $logGrande .'</ul>';				
				}		
			}
			else {
				$log = "Hubo problemas al completar el formulario. Intente otra vez.";
				echo '<ul id="log" class="log"><li>'. $log .'</li></ul>';
			}
		}
		else {
			echo '<ul id="log"></ul>';				
		}
	?>

	<form name="form" method="post" action="upload.php?t=<?php echo $seccion ?>"  onsubmit="return <?php echo $funcionValidacion ?>(<?php if ($hayImagenes) echo "false" ?>);" <?php if ($hayImagenes) echo 'enctype="multipart/form-data"' ?>>
		<ul>
			<li><label for="nombre">Titulo</label></li>
			<li><input type="text" style="width: 270px;" name="nombre" size="35" maxlength="30"/></li>
			
			<li><label for="descripcion" >Descripción</label></li>
			<li><textarea name="descripcion" style="max-width: 265px;width: 265px; height: 200px; margin-bottom: 10px; margin-left: 2px;" ></textarea></li>
			
			<li><label for="imagenGrande">Imagen</label></li>
			<li><input type="file" style="width: 270px;margin-bottom:20px;" name="imagenGrande" size="35"/></li>
			
			<li><label for="visible">Visible</label></li>
			<li><input type="checkbox" name="visible" value="1" checked="checked" /></li>
			<input type="button" name="Cancelar" value="Cancelar" class="submit" onclick="window.location = '<?php echo $to; ?>'" />
			<input type="submit" class="submit" name="Guardar" value="Guardar" />
		</ul>
	</form>
</div>