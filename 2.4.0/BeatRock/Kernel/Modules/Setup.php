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

class Setup
{	
	// Funci�n - Inicializar.
	public static function Init()
	{	
		if(!file_exists(KERNEL . 'Configuration.php'))
		{
			$result = self::RestoreBackup();
			
			if(!$result)
			{
				if(file_exists('./Setup' . DS . 'index.php'))
					Core::Redirect('./Setup/');
				else if(file_exists('../Setup' . DS . 'index.php'))
					Core::Redirect('../Setup/');
				else
					BitRock::LaunchError('03x');
			}
		}
		
		if(PHP_OS !== 'WINNT')
		{
			clearstatcache();

			if(!is_writable(BIT . 'Backups'))
				@chmod(BIT . 'Backups', 0777);
				
			if(!is_writable(BIT . 'Logs'))
				@chmod(BIT . 'Logs', 0777);
				
			if(!is_writable(BIT . 'Temp'))
				@chmod(BIT . 'Temp', 0777);
				
			if(!is_writable(BIT . 'Cache'))
				@chmod(BIT . 'Temp', 0777);
		}
		
		require(KERNEL . 'Configuration.php');		
		
		self::Verify($config);		
		self::Apply($config);
		
		return $config;
	}
	
	// Funci�n privada - �Soportamos GZIP?
	private static function SupportGzip()
	{
		global $Kernel;
		
		if(empty($_SERVER['HTTP_ACCEPT_ENCODING']))
			return false;
		else if(ini_get('zlib.output_compression') == 'On' OR ini_get('zlib.output_compression_level') > 0 OR ini_get('output_handler') == 'ob_gzhandler')
			return false;
		else if(extension_loaded('zlib') AND (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) AND $Kernel['gzip'] !== false)
			return true;
		else
			return false;
	}
	
	// Funci�n privada - Aplicar configuraci�n.
	// - $config: Configuraci�n.
	private static function Apply($config)
	{
		$site = $config['site'];
		
		if(SSL == 'on')
		{
			error_reporting(E_CORE_ERROR & E_RECOVERABLE_ERROR);
			
			if($config['server']['ssl'] == false AND $config['server']['ssl'] !== null)
			{
				Client::SavePost();
				Core::Redirect('http://' . URL);
			}
			
			$protocol = 'https://';
			BitRock::log('Se esta usando el procolo de conexi�n segura.');
		}
		else
		{
			if($config['server']['ssl'] == true)
			{
				Client::SavePost();				
				Core::Redirect('https://' . URL);
			}
			
			$protocol = 'http://';
			BitRock::log('Se esta usando el procolo de conexi�n normal.');
		}
		
		if($config['server']['gzip'] AND $page['gzip'] !== false OR $page['gzip'] == true)
		{
			if(self::SupportGzip())
			{
				ob_start('ob_gzhandler');
				BitRock::log('Se esta usando la compresi�n GZIP �Perfecto!');
			}
			else
			{
				ini_set('zlib.output_compression', 'On');
				ob_start('Core::Compress');
			}
		}
		else		
			ob_start();
		
		if(is_integer($config['errors']['report']))
			error_reporting($config['errors']['report']);
		
		if(!empty($site['country']))
			date_default_timezone_set($site['country']);
		
		define('PATH', $protocol . $site['path']);
		define('PATH_NS', 'http://' . $site['path']);
		define('PATH_SSL', 'https://' . $site['path']);
		
		define('RESOURCES', $protocol . $site['resources']);
		define('RESOURCES_SYS', $protocol . $site['resources.sys']);
		
		define('ADMIN', $protocol . $site['admin']);
		define('DB_ALIAS', $config['mysql']['alias']);
		
		define('PROTOCOL', $protocol);
		define('PATH_NOW', PROTOCOL . URL);
		
		BitRock::log('Se han aplicado las condiciones del archivo de configuraci�n con �xito.');
	}
	
	// Funci�n privada - Verificar configuraci�n.
	// - $config: Configuraci�n.
	private static function Verify($config)
	{
		BitRock::$status = Array();
		$file = KERNEL . 'Configuration.php';
		
		$mysql = $config['mysql'];
		$site = $config['site'];
		$security = $config['security'];
		
		if(empty($mysql['host']) OR empty($mysql['user']) OR empty($mysql['pass']) OR empty($mysql['name']))
			BitRock::setStatus('Los datos para la conexi�n al servidor MySQL no estan completos.', $file);
		else if(empty($site['path']) OR empty($site['resources']))
			BitRock::setStatus('Los datos de ubicaci�n de la aplicaci�n no estan completos.', $file);
		else if($security['level'] < 0 OR $security['level'] > 5)
			BitRock::setStatus('El nivel de codificaci�n es inv�lido.', $file);
		
		if($mysql['user'] == 'root' OR $mysql['user'] == 'admin')
			BitRock::log('Por seguridad es recomendable que cambie el usuario para la conexi�n al servidor MySQL.', 'warning');
		if(empty($security['hash']))
			BitRock::log('No se ha definido una cadena de seguridad para la codificaci�n, por favor defina alguna para aumentar el nivel de seguridad.', 'warning');
			
		if(!empty(BitRock::$status['response']))
			BitRock::launchError('03x');
	}

	// Funci�n privada - Restaurar el archivo de configuraci�n.
	private static function RestoreBackup()
	{
		BitRock::log('Se ha detectado que el archivo de configuraci�n no existe, se intentar� restaurar autom�ticamente.', 'warning');
		
		if(file_exists(KERNEL . 'Configuration.Backup.php'))
			return Io::Copy(KERNEL . 'Configuration.Backup.php', KERNEL . 'Configuration.php');
		
		$ab = !Mem::Ready() ? Core::theSession('backup_config') : Mem::GetM('backup_config');
			
		if(empty($ab))
			return false;
			
		BitRock::log('El archivo de configuraci�n de recuperaci�n no existe, pero se ha restaurado desde la informaci�n de recuperaci�n del usuario.');
		return Io::Write(KERNEL . 'Configuration.php', $ab);
	}
}
?>