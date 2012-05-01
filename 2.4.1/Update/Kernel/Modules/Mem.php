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

class Mem
{
	public static $mem = null;

	// Funci�n privada - Lanzar error.
	// - $function: Funci�n causante.
	// - $msg: Mensaje del error.
	public static function Error($code, $function, $msg = '')
	{
		BitRock::setStatus($msg, __FILE__, Array('function' => $function));
		BitRock::launchError($code);
		
		return false;
	}

	// Funci�n privada - �Hay alguna conexi�n activa?
	public static function Ready()
	{
		return self::$mem == null ? false : true;
	}

	// Funci�n - Destruir conexi�n activa.
	public static function Crash()
	{
		if(!self::Ready())
			return;

		self::$mem->close();
		BitRock::log('Se ha desconectado del servidor Memcache correctamente.');

		self::$mem = null;
	}

	// Funci�n - Limpiar todo el cach�.
	public static function Clean()
	{
		if(!self::Ready())
			return;

		self::$mem->flush();
	}

	// Funci�n - Inicializaci�n y conexi�n al servidor Memcache.
	public static function Init()
	{
		global $config;
		$mem = $config['memcache'];

		if(empty($mem['host']) OR !is_numeric($mem['port']))
			return false;

		$memcache = new Memcache();
		$memcache->connect($mem['host'], $mem['port']) or self::Error('11m', __FUNCTION__);

		self::$mem = $memcache;

		BitRock::log('Se ha establecido una conexi�n al servidor MemCache en '.$mem['host'].' correctamente.', 'memcache');
		$session_save = "tcp://$mem[host]:$mem[port]?persisten=1&weight=2&timeout=3&retry_interval=10,  ,tcp://$mem[host]:$mem[port]  ";

		ini_set('session.save_handler', 'memcache');
		ini_set('session.save_path', $session_save);
	}

	// Funci�n - Establecer un nuevo parametro de cach�.
	public static function Set($param, $value, $expire = 0)
	{
		if(!self::Ready())
			return self::Error('13m', __FUNCTION__);

		self::$mem->set($param, $value, MEMCACHE_COMPRESSED, $expire) or self::Error('12m', __FUNCTION__, 'No se ha podido guardar "'.$value.'".');
	}

	// Funci�n - Establecer un nuevo parametro de cach� con uso de Alias.
	public static function SetM($param, $value, $expire = 0)
	{
		if(!self::Ready())
			return self::Error('13m', __FUNCTION__);

		global $site;
		$a = !empty($site['cookie_alias']) ? $site['cookie_alias'] : $_SESSION[ROOT]['cookie_alias'];

		self::$mem->set($a . $param, $value, MEMCACHE_COMPRESSED, $expire) or self::Error('12m', __FUNCTION__, 'No se ha podido guardar "'.$value.'".');
	}

	// Funci�n - Obtener un parametro de la cach�.
	public static function Get($param)
	{
		if(!self::Ready())
			return self::Error('13m', __FUNCTION__);

		//global $site;
		//$a = !empty($site['cookie_alias']) ? $site['cookie_alias'] : $_SESSION[ROOT]['cookie_alias'];

		return self::$mem->get($param);
	}

	// Funci�n - Obtener un parametro de la cach� con uso de Alias.
	public static function GetM($param)
	{
		if(!self::Ready())
			return self::Error('13m', __FUNCTION__);

		global $site;
		$a = !empty($site['cookie_alias']) ? $site['cookie_alias'] : $_SESSION[ROOT]['cookie_alias'];

		return self::$mem->get($a . $param);
	}

	// Funci�n - Eliminar una parametro de la cach�.
	public static function Delete($param)
	{
		if(!self::Ready())
			return self::Error('13m', __FUNCTION__);

		return self::$mem->delete($param);
	}

	// Funci�n - Eliminar una parametro de la cach� con uso de Alias.
	public static function DeleteM($param)
	{
		if(!self::Ready())
			return self::Error('13m', __FUNCTION__);

		global $site;
		$a = !empty($site['cookie_alias']) ? $site['cookie_alias'] : $_SESSION[ROOT]['cookie_alias'];

		return self::$mem->delete($a . $param);
	}
}
?>