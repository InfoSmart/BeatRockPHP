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

## --------------------------------------------------
##                   Emoticones
## --------------------------------------------------
## Este archivo ser� solicitado para los emoticones
## en caso de que use la funci�n Core::Smilies
## --------------------------------------------------

// Direcci�n local del archivo TXT.
$path = BIT . 'Emoticons' . DS . $G['e'] . '.txt';

// Verificar si el emoticon solicitado existe.
if(empty($G['e']) OR !file_exists($path))
	exit;

// Mostrar resultado como una imagen de tipo PNG.
Tpl::Image();

// Permitir el uso de este Script por AJAX para cualquier dominio.
// Comente la siguiente l�nea si solo desea usarlo localmente.
Tpl::AllowCross('*');

// Mostrar emoticon.
echo base64_decode(Io::Read($path));
?>