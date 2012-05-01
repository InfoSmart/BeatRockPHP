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
	
class BitRock
{
	public static $status = Array();
	public static $logs = Array();
	private static $files = Array();
	private static $dirs = Array();
	private static $inerror = false;
	public static $ignore = false;
	public static $details = Array();
	public static $modules = Array();
	
	// Funci�n - Constructor.
	public function __construct()
	{
		if(!version_compare(PHP_VERSION, '5.3.0', '>='))
			exit('BeatRock no soporta esta versi�n de PHP (' . phpversion() . '). Por favor actualiza tu plataforma de PHP a la 5.4.X o superior.');

		spl_autoload_register('BitRock::LoadMod');
		register_shutdown_function('BitRock::Shutdown');		
		
		set_exception_handler('BitRock::HaveException');		
		set_error_handler('BitRock::HaveError', E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);		

		self::Log('BeatRock ha comenzado.');
		
		self::Register(KERNEL . 'Functions.Header.php');
		self::Register(KERNEL . 'Functions.php');
		self::Register(TEMPLATES_BIT . 'Error.tpl');
		self::Register(HEADERS . 'Header.php');
		self::Register(HEADERS . 'Footer.php');
		
		self::Register(KERNEL . 'BitRock', true);
		self::Register(BIT . 'Logs', true);
		self::Register(HEADERS, true);
		self::Register(TEMPLATES, true);
		
		self::VerifyBoot();
	}
	
	// Funci�n - Registrar archivo/directorio requerido.
	// - $file: Ruta del archivo/directorio.
	// - $dir (Bool): �Es un directorio?
	public static function Register($file, $dir = false)
	{
		$dir ? self::$dirs[] = $file : self::$files[] = $file;
	}
	
	// Funci�n - Verificaci�n de inicio.
	public static function VerifyBoot()
	{
		foreach(self::$files as $f)
		{			
			if(!file_exists($f))
				self::SetStatus('El archivo necesario especificado no existe.', $f);
		}
		
		foreach(self::$dirs as $d)
		{
			if(!is_dir($d))
				self::SetStatus('El directorio especificado no existe.', $d);
		}
		
		if(!function_exists('curl_init'))
			self::SetStatus('La librer�a cURL esta desactivada en PHP, esta es necesaria para BeatRock, por favor activela para continuar.', '', Array('function' => 'curl_init'));
		
		if(!function_exists('json_decode'))
			self::SetStatus('La librer�a JSON esta desactivada en PHP, esta es necesaria para BeatRock, por favor activela para continuar.', '', Array('function' => 'json_decode'));
		
		if(!empty(self::$status['response']))
			self::LaunchError('02x');
		
		self::$files = Array();
		self::$dirs = Array();

		self::Log('La verificaci�n de inicio se ha completado.');
	}
	
	// Funci�n - Guardar log.
	// - $message: Mensaje a guardar.
	// - $type (info, warning, error, mysql): Tipo del log.
	public static function Log($message, $type = 'info')
	{
		global $config;
		
		if(!is_string($message))
			return;
			
		if($config['logs']['capture'] == false)
			return;
		
		if($type !== 'info' AND $type !== 'warning' AND $type !== 'error' AND $type !== 'mysql' AND $type !== 'memcache')
			return;
		
		if($type == 'info')
		{
			$status = 'INFO';
			$color = '#045FB4';
		}
		
		if($type == 'warning')
		{
			$status = 'ALERTA';
			$color = '#8A4B08';
		}
		
		if($type == 'error')
		{
			$status = 'ERROR';
			$color = 'red';
		}
		
		if($type == 'mysql')
		{
			$status = 'MYSQL';
			$color = '#0B610B';			
		}

		if($type == 'memcache')
		{
			$status = 'Memcache';
			$color = '#29088A';			
		}
		
		$html = '<label title="' . date('h:i:s') . '"><b style="color: '.$color.'">['.$status.']</b> - '.$message.'</label><br />';
		$text = '['.$status.'] (' . date('h:i:s') . ') - '.$message.'\r\n';
		
		self::$logs['all']['html'] .= $html;
		self::$logs['all']['text'] .= $text;		
		self::$logs[$type]['html'] .= $html;
		self::$logs[$type]['text'] .= $text;
	}
	
	// Funci�n - Guardar logs.
	public static function SaveLog()
	{
		global $config;
		$save = $config['logs']['save'];
		
		if(!$save OR empty($save))
			return;
		
		if($save !== 'onerror' AND empty(self::$logs[$save]))	
			return;
		
		if($save == 'onerror')
		{
			if(empty(self::$logs['error']))
				return;
			else
				$save = 'all';
		}		
		
		$name = 'Logs-' . date('d_m_Y') . '-' . time() . '.txt';
		Io::SaveLog($name, self::$logs[$save]['text']);
	}
	
	// Funci�n - Imprimir Logs.
	// - $html (Bool): �Imprimir en formato de HTML? (M�s bonito)
	// - $type (all, error, warning, info, mysql, memcache): Tipo de Logs ha imprimir.
	public static function PrintLog($html = true, $type = 'all')
	{
		if(empty($type))
			$type = 'all';
			
		$finish = (microtime(true) - START);
		self::Log('BeatRock tardo ' . substr($finish, 0, 5) . ' segundos en ejecutarse con un uso de ' . round(memory_get_usage() / 1024,1) . ' KB de Memoria.');
		
		echo $html ? self::$logs[$type]['html'] : self::$logs[$type]['text'];
	}
	
	// Funci�n privada - Cargar un m�dulo.
	// - $name: Nombre del modulo.
	private function LoadMod($name)
	{
		if(in_array($name, self::$modules))
			return;

		$mod = $name.'.php';
		
		if(file_exists(MODS . $mod))
			require(MODS . $mod);
		else if(file_exists(MODS . 'External' . DS . $mod))
			require(MODS . 'External' . DS . $mod);
		else
		{
			self::SetStatus('No se ha podido cargar el m�dulo "'.$name.'".', $name);
			self::LaunchError('04x');
		}
		
		if($name == 'Codes')
			Codes::LoadCodes();
		if($name == 'DNS')
			require_once(MODS . 'External/SMTPValidate.php');
			
		self::$modules[] = $name;
		self::Log('Se ha cargado el m�dulo "'.$name.'" correctamente.');
	}
	
	// Funci�n - Ha ocurrido un error.
	// Variables de respuesta especificadas por el Callback.
	public static function HaveError($num, $message, $file, $line)
	{
		self::SetStatus($message, $file, Array('line' => $line));
		self::LaunchError('01x');
		
		return true;
	}
	
	// Funci�n - Ha ocurrido una excepci�n.
	// Variable de respuesta especificada por el callback.
	public static function HaveException($e)
	{
		self::SetStatus($e->getMessage(), $e->getfile(), Array('line' => $e->getline()));
		self::LaunchError('01e');
		
		return true;
	}
	
	// Funci�n - Establecer estado/informaci�n de un error.
	// - $response: Mensaje de respuesta.
	// - $file: Archivo responsable.
	// - $data (Array): M�s informaci�n...
	public static function SetStatus($response, $file, $data = Array())
	{
		self::$status['response'] = $response;
		self::$status['file'] = $file;
		
		foreach($data as $param => $value)
			self::$status[$param] = $value;
	}
	
	// Funci�n - Lanzar un error.
	// - $code: C�digo del error.
	public static function LaunchError($code)
	{
		if(self::$ignore OR self::$inerror)
		{
			self::$ignore = false;
			return;
		}
		
		self::$inerror = true;
		extract($GLOBALS);
		
		$info = Codes::GetInfo($code);
		$res = self::$status;
		
		$last = error_get_last();
		$res['last'] = $last['message'].' en "'.$last['file'].'" l�nea '.$last['line'];
		
		Client::SavePost();
		
		if(MySQL::Ready() AND $code !== '01x')
		{
			if($code == '03m')
			{								
				if($config['mysql']['repair.error'] OR $config['errors']['hidden'])
					MySQL::Repair();
					
				Core::HiddenError();
			}
			else
			{
				$report_code = Core::Random(10);

				MySQL::query_insert('site_errors', Array(
					'report_code' => $report_code,
					'code' => $code,
					'title' => $info['title'],
					'response' => _f($res['response']),
					'file' => _f($res['file']),
					'function' => $res['function'],
					'line' => $res['line'],
					'out_file' => _f($res['out_file']),
					'more' => _f(json_encode($res), false),
					'date' => time()
				));
			}
		}
			
		self::$details = Array(
			'report_code' => $report_code,
			'code' => $code,
			'info' => $info,
			'res' => $res
		);
		
		$mail_result = Core::SendError();
		self::Log('Ha ocurrido un error: '.$code.' - '.$info['title'].' - '.$info['details'], 'error');
		
		ob_flush();

		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-cache');
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		
		$html = Tpl::Process(TEMPLATES_BIT . 'Error');
		
		foreach($info as $param => $value)
			$html = str_ireplace("%$param%", $value, $html);
		
		exit($html);
	}
	
	// Funci�n - Verificar la carga media del CPU y Memoria.
	public static function CheckLoad()
	{
		global $site;

		$last_verify = $_SESSION['load_verify'];

		if(time() < $last_verify)
			return;
		
		$memory_limit = ini_get('memory_limit');
		$memory_load = 0;
		$apache_load = 0;
		$cpu_load = 0;

		if(!empty($memory_limit))
		{
			$memory_load = memory_get_usage() + 500000;

			if(Contains($memory_limit, 'M'))
				$memory_limit = round(str_replace('M', '', $memory_limit) * 1048576);
		}

		if($site['apache_limit'] >= 52428800)	
			$apache_load = Core::memory_usage() + 500000;

		if($site['cpu_limit'] >= 50)
			$cpu_load = Core::sys_load() + 10;

		$_SESSION['load_verify'] = (time() + (3 * 60));
		
		if($memory_load > $memory_limit OR $apache_load > $site['apache_limit'] OR $cpu_load > $site['cpu_limit'])
		{
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Cache-Control: no-cache');
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			
			foreach($GLOBALS as $g => $v)	
				unset($g, $v);
				
			echo Tpl::Process(BIT_TEMP . 'Overload');
			exit(1);
		}
	}
	
	// Funci�n - Apagado de BeatRock.
	public static function ShutDown()
	{
		$finish = (microtime(true) - START);
		
		self::log('BeatRock tardo ' . substr($finish, 0, 5) . ' segundos en ejecutarse con un uso de ' . round(memory_get_usage() / 1024,1) . ' KB de Memoria.');
		self::log('Se realizaron ' . MySQL::$querys . ' consultas durante la sesi�n actual.', 'mysql');
		self::log('Se cargaron ' . count(self::$modules) . ' m�dulos durante la sesi�n actual.');
		
		global $page;
		
		if(!empty($page['id']) AND empty(Tpl::$html) AND !Socket::$server)
		{
			Tpl::Load();
			Tpl::SaveCache();
		}
			
		if(self::$inerror == false AND !empty(Tpl::$html))
			echo Tpl::$html;

		if(is_array('Ftp', self::$modules))
			Ftp::Crash();

		if(is_array('Socket', self::$modules))
			Socket::Crash();
		
		if(MySQL::Ready())
		{			
			Site::CheckTimers();			
			MySQL::Crash();
		}
		
		if(!empty(Io::$temp))
		{
			foreach(Io::$temp as $t)
				@unlink($t);
		}		
		
		session_write_close();
		self::SaveLog();

		foreach($GLOBALS as $g => $v)	
		{
			if($g == '_COOKIE' OR $g == '_SESSION')
				continue;

			unset($g, $v);
		}
		
		// Descomente la siguiente linea para ver los �ltimos logs...
		//self::PrintLog(true);
	}
	
	// Funci�n - Guardar un Backup de toda la aplicaci�n.
	// - $db (Bool) - �Incluir un backup de la base de datos?
	public static function Backup($db = false)
	{
		$name = BIT . 'Backups' . DS . 'Backup-' . date('d_m_Y') . '-' . time() . '.zip';
		
		$a = new PclZip($name);
		$e = $a->create(ROOT);
		
		if($e == 0)
			return false;
		
		if($db)
		{
			$b = MySQL::Backup();
			$b = BIT . 'Backups' . DS . $b;
			
			Zip::Add($name, $b);
			unlink($b);
		}
		
		self::Log('Se ha creado un Backup total correctamente.');
		return $name;
	}
	
	// Funci�n - Imprimir estadisticas.
	public static function Statistics()
	{
		$finish = (microtime(true) - START);
		
		$return = 'BeatRock tardo ' . substr($finish, 0, 5) . ' segundos en ejecutarse con un uso de ' . round(memory_get_usage() / 1024,1) . ' KB de Memoria.<br />';
		$return .= 'Se realizaron ' . MySQL::$q . ' consultas durante la sesi�n actual.<br />';
		$return .= 'Se cargaron ' . count(self::$modules) . ' m�dulos durante la sesi�n actual.<br />';
		
		return $return;
	}
}
?>