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

require('Init.php');

$page['name'] = "Bienvenido";
$page['id'] = "index";
require('Header.php');
?>
<div class="content">
	<h2>Hagamos magia con PHP.</h2>
	
	<p>
		<b>�Bienvenid@!</b> �Est� listo para m�ximizar su potencial en PHP, disminuir su tiempo de trabajo y crear aplicaciones interactivas y potentes?<br />
		BeatRock es un Framework en PHP que f�cilita el trabajo a los desarrolladores de aplicaciones web haciendo a PHP "m�s hermoso" con herramientas �tiles y potentes que pueden servirle durante su trabajo.
	</p>
	
	<p>
		BeatRock usa la tecnolog�a de PHP 5 para hacer aplicaciones sorprendentes y compatibles con distintos navegadores web, robots de b�squeda y una buena amistad con HTML 5 y CSS 3. Es sencillo, f�cil de manejar y que al igual que en PHP es solo cuesti�n de practica para encontrar su potencial y usarlo al m�ximo.
	</p>
	
	<p>
		Usando los estandares recomendados de varias compa�ias y organizaciones que crean el futuro de la Internet hemos creado BeatRock, adem�s su c�digo es sencillo y de f�cil comprensi�n para los desarrolladores semi-expertos en PHP 5 lo cual hace su edici�n m�s f�cil.
	</p>
	
	<p>
		El objetivo final de BeatRock es que los desarrolladores puedan crear aplicaciones web m�s interactivas y m�s actualizadas en cuanto a estandares de tecnolog�a web y que adem�s puedan proporcionala de manera gratuita y libre.
	</p>
	
	<p>
		BeatRock esta bajo la licencia de <a href="http://creativecommons.org/licenses/by-sa/2.5/mx/" target="_blank">Creative Commons "Atribuci�n-Licenciamiento Rec�proco"</a> que le permite <b>usarla, editarla, venderla, olerla, comerla, casarse con ella y dem�s...</b> �Haga lo que quiera con BeatRock y evolucione la Internet!
	</p>
	
	<p>
		Esta instalaci�n le ayudar� a crear la base de datos, el archivo de configuraci�n y ajustar los datos de su aplicaci�n (Nombre, descripci�n, logo, etc). <b>�No es una CMS!</b> Es un kit de desarrollo (Framework) ;)
	</p>
	
	<div class="version">
		<center><h2>Informaci�n</h2></center>
		
		<li><b>Nombre c�digo:</b> <?=$Info['code']?></li>
		<li><b>Versi�n:</b> <?=$Info['version.revision']?></li>
		<li><b>Fase:</b> <?=$Info['version.fase']?></li>
		<li><b>Fecha de creaci�n:</b> <?=$Info['date']?></li>
		<li><b>Hora de creaci�n:</b> <?=$Info['date_hour']?></li>
		<li><b>Nombre:</b> <?=$Info['version.code']?></li>

		<p class="center">
			<span><?=CheckRelease()?></span>
		</p>
	</div>
	
	<p class="center">
		<a href="./step2" class="ibtn">�Comenzar instalaci�n!</a><br />
	</p>
	
	<figure class="res">
		<a href="http://www.w3schools.com/html5/default.asp" target="_blank" title="HTML 5"><img src="http://www.w3.org/html/logo/downloads/HTML5_Logo_512.png" /></a>
		<a href="http://php.net/?beta=1" target="_blank" title="PHP"><img src="<?php echo RESOURCES_INS; ?>/system/setup/PHP.png" /></a>
		<a href="http://www.opensource.org/" target="_blank" title="Open Source"><img src="http://www.opensource.org/files/garland_logo.png" /></a>
	</figure>
</div>