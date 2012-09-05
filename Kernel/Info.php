<?
#####################################################
## 					 BeatRock
#####################################################
## Framework avanzado de procesamiento para PHP.
#####################################################
## InfoSmart � 2012 Todos los derechos reservados.
## http://www.infosmart.mx/	
#####################################################
## http://beatrock.infosmart.mx/
#####################################################

// Acci�n ilegal.
if(!defined('BEATROCK'))
	exit;

## ------------------------------------------------------------
##           Informaci�n de versi�n de BeatRock.
## ------------------------------------------------------------
## Informaci�n acerca de la versi�n del Kernel y detalles
## acerca de su creaci�n.
## ------------------------------------------------------------

## ---------------------------------------------------------
## Nombre del Kernel.
## Si ha hecho modificaciones, cree su propio "Code Name".
## ---------------------------------------------------------

$Info['name'] = 'BeatRock';
$Info['code'] = 'Mentalist';

## ---------------------------------------------------------
## Versi�n del Kernel.
## ---------------------------------------------------------

$Info['mayor'] 		= '2';
$Info['minor'] 		= '4';
$Info['micro'] 		= '5';
$Info['revision'] 	= '003';

## ---------------------------------------------------------
## Fase del desarrollo.
## Alpha -> BETA -> PP -> RC -> Producci�n
## ---------------------------------------------------------

$Info['fase'] 		= 'BETA';
$Info['fase_ver'] 	= '2';

## ---------------------------------------------------------
## Fecha de creaci�n.
## ---------------------------------------------------------

$Info['date'] 		= '02.09.2012';
$Info['date_hour'] 	= '10:45 PM';

## ---------------------------------------------------------
## Nombres.
## ---------------------------------------------------------

$Info['version'] 			= "$Info[mayor].$Info[minor].$Info[micro]";
$Info['version.name'] 		= "$Info[name] v$Info[version]";
$Info['version.code'] 		= "$Info[name] \"$Info[code]\" v$Info[version]";
$Info['version.revision'] 	= "$Info[version] Revisi�n:  $Info[revision]";
$Info['version.date'] 		= "$Info[version] - $Info[date] $Info[date_hour]";
$Info['version.fase'] 		= "$Info[fase] $Info[fase_ver]";
$Info['version.full'] 		= $Info['version.code'] . " $Info[fase]$Info[fase_ver] Revisi�n: $Info[revision] - $Info[date] $Info[date_hour]";

// Sea bueno y no modifique o elimine esta l�nea.
header('X-Powered-By: BeatRock v'.$Info['version'].': http://beatrock.infosmart.mx/');
?>