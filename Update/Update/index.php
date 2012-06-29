<?php
#####################################################
## 					 BeatRock				   	   ##
#####################################################
## Framework avanzado de procesamiento para PHP.   ##
#####################################################
## InfoSmart � 2012 Todos los derechos reservados. ##
## http://www.infosmart.mx/						   ##
#####################################################
## http://beatrock.infosmart.mx/				   ##
#####################################################

require('../Init.php');
require('./Info.php');

function CheckInit()
{
	global $Original;
	
	$result['config'] 	= is_readable('./templates/Configuration');
	$result['kernel'] 	= is_writable('../Kernel/');

	$result['curl'] 	= function_exists('curl_init');
	$result['json'] 	= function_exists('json_encode');

	$result['shorttag'] = ini_get('short_open_tag');	
	$result['php'] 		= version_compare(PHP_VERSION, '5.3.0', '>=');

	$result['beatrock'] = ($Original['version'] == '2.4.2') ? true : false;
	
	return $result;
}

$status 	= CheckInit();
$continue 	= true;

foreach($status as $param => $value)
{
	if($value == false)
	{
		$status[$param] = '?';
		$continue 		= false;
	}
	else
		$status[$param] = '>';
}

$logLink 	= 'https://docs.google.com/document/d/1HeSIBElNLH-NEdlSGqGck4I8jSBg4uevudf4ZP3DzKQ/edit';
$result 	= Core::theSession('update_status');

$page['name'] = '2.4.2 a 2.4.3';
require('Header.php');
?>
<div class="wrapper">
<div class="pre">
	<section class="left">
		<h2>Actualicemos su aplicaci�n...</h2>
		<cite>Una versi�n a�n m�s poderosa le espera...</cite>

		<p>
			Gracias por su inter�s en BeatRock, este proceso de actualizaci�n hara los cambios necesarios en su base de datos para que no haya problemas con su aplicaci�n.
		</p>

		<p>
			Antes de proseguir por favor tomese unos minutos para leer los cambios en esta versi�n de BeatRock, de esta forma podr� tener en consideraci�n los cambios que deber� efectuar en su c�digo:
		</p>

		<p style="margin: 25px 0;">
			<a href="<?=$logLink?>" target="_blank" class="ibtn">Log de cambios</a>
		</p>

		<p>
			<b>Recuerde:</b> Esta actualizaci�n solo podr� hacer cambios en su base de datos y su archivo de configuraci�n, tome en cuenta que tambi�n se crear�n un "Backup" de los mismos por si algo sucede mal.
		</p>

		<p>
			<b style="color: red">�Atenci�n!</b> Si tiene el "modo seguro" activado por favor desactivelo para evitar problemas en la actualizaci�n.
		</p>
	</section>

	<figure class="right">
		<img src="<?=RESOURCES_INS?>/system/setup/images/update.png" />
	</figure>
</div>
</div>

<div class="content index">

	<div class="wrapper">
	<div id="process-form">
	<section class="version">
		<h2>�A donde nos vamos actualizar?</h2>

		<div class="left">		
			<li><b>Nombre c�digo:</b> <?=$Info['code']?></li>
			<li><b>Versi�n:</b> <?=$Info['version.revision']?></li>
			<li><b>Fase:</b> <?=$Info['version.fase']?></li>
			<li><b>Fecha de creaci�n:</b> <?=$Info['date']?></li>
			<li><b>Hora de creaci�n:</b> <?=$Info['date_hour']?></li>
			<li><b>Nombre:</b> <?=$Info['version.code']?></li>
		</div>

		<div class="right">
			<span class="version">v<?=$Info['version.revision']?></span>
		</div>
	</section>

	<section>
		<h2>�Esta preparado?</h2>

		<table cellspacing="0" cellpadding="0" class="intable">
			<thead>
				<tr>
					<th>Funci�n / Caracter�stica</th>
					<th>Estado</th>
					<th>M�s informaci�n</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<th>Carpeta "/Kernel/" escribible</th>
					<th class="icon"><?=$status['kernel']?></th>
					<th>
						Permite la creaci�n del archivo de configuraci�n.
					</th>
				</tr>

				<tr>
					<th>Archivo "/Setup/Configuration" leible</th>
					<th class="icon"><?=$status['config']?></th>
					<th>
						Permite la lectura de la plantilla del archivo de configuraci�n.
					</th>
				</tr>

				<tr>
					<th>Librer�a cURL</th>
					<th class="icon"><?=$status['curl']?></th>
					<th>
						<a href="http://www.codigogratis.com.ar/como-habilitar-curl-en-xampp-enabling-curl-on-xampp/" target="_blank">Instalar en Xampp</a> - <a href="http://www.pressthered.com/how_to_install_php_curl_on_iis/" target="_blank">Instalar en IIS (En Ingles)</a>
					</th>
				</tr>

				<tr>
					<th>Librer�a JSON</th>
					<th class="icon"><?=$status['json']?></th>
					<th></th>
				</tr>

				<tr>
					<th>Etiqueta corta de PHP</th>
					<th class="icon"><?=$status['shorttag']?></th>
					<th>
						Hace que la instalaci�n y algunas partes de BeatRock funcionen correctamente. - <a href="http://www.cristalab.com/tutoriales/usar-etiqueta-corta-en-php-5-c27491l/" target="_blank">Activar</a>
					</th>
				</tr>

				<tr>
					<th>Versi�n de PHP - <?=phpversion()?></th>
					<th class="icon"><?=$status['php']?></th>
					<th>
						BeatRock solamente es compatible con las versiones 5.3 o superiores de PHP.
					</th>
				</tr>

				<tr>
					<th>Versi�n de BeatRock - <?=$Original['version']?></th>
					<th class="icon"><?=$status['beatrock']?></th>
					<th>
						Debe tener la versi�n <b>2.4.2</b> de BeatRock para actualizarse a esta versi�n.
					</th>
				</tr>
			</tbody>
		</table>
	</section>

	<p>
		<strong style="color: red">�Atenci�n!</strong> Dependiendo de la situaci�n es probable que el archivo de configuraci�n no se actualize correctamente, al terminar la actualizaci�n verifica si el archivo <b>"Configuration.json"</b> se encuentra correctamente escrito.
	</p>

	<p>
		<strong style="color: red">Nota:</strong> Si al terminar la actualizaci�n te da un error relacionado con la tabla "site_translate" no te preocupes, todo se hizo correcto y podr�s continuar actualizando los archivos.
	</p>
	
	<p class="center">
	<? if($continue) { ?>	
		<a href="./actions/update.php" class="ibtn igreen">Comenzar actualizaci�n</a>
	<? } else { ?>
		<a class="ibtn ired">Es necesario que cumpla con los requisitos anteriores</a>
	<? } ?>
	</p>
	</div>

	<section id="complete" hidden>
		<h1>Disfrute de su nuevo poder ;)</h1>

		<p>
			Todo ha salido estupendo y hemos actualizado correctamente la base de datos de su aplicaci�n, ahora puede proceder a actualizar los archivos de BeatRock.
		</p>

		<p>
			Gracias por su inter�s en el proyecto y recuerde eliminar la carpeta <b>/Update/</b> ;)
		</p>

		<p>
			<a href="<?=PATH?>" class="ibtn iblue">Continuar</a>
		</p>
	</section>
	</div>

</div>

<script>
<?
if(!empty($result))
{
	if($result == 'OK')
	{
	?>
	$('.pre').hide();
	$('#process-form').hide();
	$('#complete').removeAttr('hidden');
	<?
	} else {
	?>
	alert('�Ha ocurrido un error durante la actualizaci�n! Intentelo nuevamente.');
	<?
} }
?>
</script>