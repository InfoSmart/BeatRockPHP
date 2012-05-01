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

$page['id'] = "ready";
require('Init.php');

$page['name'] = "Error de instalaci�n";
require('Header.php');
?>
<div class="content">
	<h2>Al parecer ya estamos listos...</h2>
	
	<p>
		Lo sentimos, pero BeatRock ha encontrado que el archivo de configuraci�n ya existe o alguien m�s ya se encuentra en la instalaci�n, como metodo de seguridad se le ha denegado el acceso.
	</p>
	
	<h3>�Qu� puedo hacer?</h3>
	
	<p>
		- Si su aplicaci�n ya esta lista, elimine el directorio de instalaci�n <b>"/Setup/"</b>.<br />
		- Elimine el archivo de configuraci�n <b>"/Kernel/Configuration.php"</b>.<br />
		- Elimine el archivo de seguridad <b>"/Setup/SECURE"</b>.<br />
		- Salga de esta p�gina y vaya por un caf�.
	</p>
</div>