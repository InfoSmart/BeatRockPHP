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

class Curl
{
	private static $host = "";
	private static $opts = Array();
	
	// Funci�n privada - Lanzar error.
	// - $function: Funci�n causante.
	// - $msg: Mensaje del error.
	private static function Error($code, $function, $msg = '')
	{
		BitRock::setStatus($msg, __FILE__, Array('function' => $function));
		BitRock::launchError($code);
	}
	
	// Funci�n privada - �Hay alguna conexi�n activa?
	// - $e (Bool): �Lanzar error en caso de que no haya una conexi�n activa?
	private static function Ready($e = false)
	{
		return empty(self::$host) ? false : true;
	}
	
	// Funci�n - Destruir conexi�n activa.
	public static function Crash()
	{
		if(!self::Ready())
			return false;
			
		self::$host = null;
		self::$opts = Array();
	}
	
	// Funci�n - Preparar una conexi�n cURL.
	// - $host: Host de la conexi�n.
	// - $opts (Array): Opciones para la conexi�n.
	public static function Init($host, $opts = Array())
	{
		self::Crash();
		
		if(empty($opts['agent']))
			$opts['agent'] = AGENT;
			
		if(!is_numeric($opts['timeout']))
			$opts['timeout'] = 100;
			
		if(!is_bool($opts['cookies']))
			$opts['cookies'] = false;
			
		if(!is_array($opts['params']))
			$opts['params'] = Array();
			
		if(empty($opts['header']) OR !is_array($opts['header']))
			$opts['header'] = Array();
			
		$opts['header'][] = 'Accept-Language: es-es,es;q=0.8';
		$opts['header'][] = 'Accept-Charset: ISO-8859-1,ISO-8859-15,UTF-8';
		$opts['header'][] = 'Expect: ';
		
		self::$host = $host;
		self::$opts = $opts;
		
		BitRock::log('Se ha definido la configuraci�n para una conexi�n cURL a '.$host.'.');
	}
	
	// Funci�n privada - Realizar una conexi�n cURL.
	private static function Connect()
	{
		if(!self::Ready())
			self::Error("01c", __FUNCTION__);
			
		$opts = self::$opts;
		
		if($opts['cookies'])
		{
			global $_COOKIE;
			
			$file = BIT . 'Temp' . DS . 'cookie.' . time() . '.txt';
			$cook = '';
			
			foreach($_COOKIE as $param => $value)
			{
				if(is_array($value))
					continue;
					
				$cook .= "$param=$value; ";
			}
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$host);
		curl_setopt($ch, CURLOPT_USERAGENT, $opts['agent']);
		curl_setopt($ch, CURLOPT_TIMEOUT, $opts['timeout']);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $opts['timeout']);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $opts['header']);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		
		if($opts['cookies'])
		{
			curl_setopt($ch, CURLOPT_COOKIESESSION, $cook);
			curl_setopt($ch, CURLOPT_COOKIE, $cook);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $file);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $file);
		}
		
		if(is_array($opts['params']))
		{
			foreach($opts['params'] as $param => $value)
			{
				if(!is_integer($param))
					continue;
					
				curl_setopt($ch, $param, $value);
			}
		}
		
		return $ch;
	}
	
	// Funci�n - Obtener la respuesta de la conexi�n.
	public static function Get()
	{
		$ch = self::Connect();
		curl_setopt($ch, CURLOPT_POST, false);
		$re = curl_exec($ch);
		curl_close($ch);
		
		return $re;
	}
	
	// Funci�n - Obtener las cabeceras de la conexi�n.
	public static function Headers()
	{
		$ch = self::Connect();
		curl_setopt($ch, CURLOPT_POST, false);
		curl_exec($ch);
		$re = curl_getinfo($ch, CURLINFO_HEADER_OUT);
		curl_close($ch);
		
		return $re;
	}
	
	// Funci�n - Enviar informaci�n a la conexi�n.
	// - $data (Array): Informaci�n a enviar.
	public static function Post($data)
	{
		if(!is_array($data))
			return false;
			
		foreach($data as $param => $value)
            $datas[] = "{$param}=" . urlencode($value);
			
		$datas = join('&', $datas);
		
		$ch = self::Connect();
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
		$re = curl_exec($ch);
		curl_close($ch);
		
		BitRock::log('Se han enviado datos ('.$datas.') a la conexi�n cURL');
		return $re;
	}
}
?>