<?
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

require '../Init.php';

## --------------------------------------------------
##                   Proxy Falso
## --------------------------------------------------
## Puede usar este archivo para mandar solicitudes
## AJAX Crossdomain, la url debe estar codificada
## con BASE64 y en el parametro "toURL" POST/GET.
## --------------------------------------------------

// Permitir el uso de este Script por AJAX para cualquier dominio.
// Comente la siguiente l�nea si solo desea usarlo localmente.
Tpl::AllowCross('*');

// Direcci�n a visitar codificada en Base64.
$url = base64_decode($RC['toUrl']);

// �No hay direcci�n?	
if(empty($url))
	exit;

// Informaci�n a enviar.
$post = $_REQUEST;

// Visitando sitio y devolviendo respuesta.
Curl::Init($url);
echo Curl::Post($post);
?>