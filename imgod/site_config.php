<?
$page['admin'] 	= true;
require '../Init.php';

$correct = _SESSION('admin_correct');

if($correct == true)
{
	Tpl::JavaAction('Kernel.ShowBox("correct");');
	_DELSESSION('admin_correct');
}

$page['id']		= array('nav', 'site_config');
$page['name']	= 'Administraci�n: Configuraci�n del sitio'
?>