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
if(!defined("BEATROCK"))
	exit;

## --------------------------------------------------
##        Funciones externas y globales
## --------------------------------------------------
## Utilice este archivo para definir funciones
## independientes del Kernel, tambi�n
## para definir procesos que se deban repetir
## en toda la aplicaci�n.
## --------------------------------------------------

#####################################################
## ADMINISTRACI�N	
#####################################################

// Si estamos o queremos visitar la administraci�n.
if($page['admin'])
{
	/*
	�No olvide descomentar esta l�nea!
	if(!LOG_IN OR $my['rank'] < 7)
	{
		header("Location: " . PATH);
		exit;
	}
	*/
	
	$page['folder'] = "admin";
	$page['subheader'] = "Admin.SubHeader";
	$page['compress'] = false;

	$count = Array(
		Array(
			'Visitas totales' => Array("site_visits", ""),
			'Visitas (Escritorio)' => Array("site_visits", "WHERE type = 'desktop'"),
			'Visitas (M�vil)' => Array("site_visits", "WHERE type = 'mobile'"),
			'Visitas (BOT)' => Array("site_visits", "WHERE type = 'bot'")
		), 
		Array(
			'Errores' => Array("site_errors"),
			'Noticias' => Array("site_news"),
			'Cronometros' => Array("site_timers"),
			'Logs' => Array("site_logs")
		),
		Array(
			'Usuarios' => Array("users"),
			'Usuarios recientes' => Array("users", "WHERE account_birthday > '" . Core::Time(15, 4, true) . "'"),
			'Usuarios �ltimas 24 hrs' => Array("users", "WHERE account_birthday > '" . Core::Time(1, 4, true) . "'"),
			'Usuarios online' => Array("users", "WHERE lastonline > '$date[f]'")
		)
	);
}

#####################################################
## DEFINICIONES GLOBALES
#####################################################

// Descomente la siguiente l�nea para finjir ser un navegador movil.
//define("TEST_MOB", true);
?>