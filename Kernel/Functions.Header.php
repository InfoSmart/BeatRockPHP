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

// Acci�n ilegal.
if(!defined('BEATROCK'))
	exit;

## --------------------------------------------------
##        Funciones de cabecera
## --------------------------------------------------
## Utilice este archivo para definir la 
## implementaci�n de recursos CSS/JS/Meta en
## su aplicaci�n, utilice la variable $page[id]
## para separar recursos de p�ginas �nicas.
## --------------------------------------------------
	
#####################################################
## IMPLEMENTACI�N DE RECURSOS RECOMENDADO.
#####################################################

// Agregando jQuery.
Tpl::addjQuery();

// Agregando el estilo predeterminado y el Kernel en JavaScript.
Tpl::myStyle('style', true);
Tpl::myScript('functions.kernel', true);

// Si queremos RSS...
if($site['site_rss'] == "true")
	Tpl::addStuff('<link rel="alternate" type="application/rss+xml" title="%site_name%: RSS" href="%PATH%/rss" />');

#####################################################
## AGREGANDO ESTILOS SEG�N P�GINA
#####################################################

Tpl::myStyle('style.page');
//Tpl::myStyle('style.forms');
	
Tpl::myScript('functions.page');

#####################################################
## OTROS
#####################################################

Tpl::addMoreHTML('itemscope');
Tpl::addMoreHead('prefix', 'og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#');
?>