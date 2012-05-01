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

class Socket
{
	private static $connection = null;	
	public static $server = false;
	public static $actions = Array();
	
	// Funci�n privada - Lanzar error.
	// - $code: C�digo de error.
	// - $function: Funci�n causante.
	// - $msg: Mensaje del error.
	private static function Error($code, $function, $msg = '')
	{		
		if(empty($msg) AND is_resource(self::$connection))
			$msg = socket_strerror(socket_last_error(self::$connection));
		
		BitRock::setStatus($msg, __FILE__, Array('function' => $function));
		BitRock::launchError($code);
		
		return false;
	}
	
	// Funci�n privada - �Hay alguna conexi�n activa?
	private static function Ready()
	{
		if(self::$connection == null OR !is_resource(self::$connection))
			return false;
			
		return true;
	}
	
	// Funci�n - Destruir conexi�n activa.
	public static function Crash()
	{
		if(!self::Ready())
			return;
		
		socket_close(self::$connection);
		BitRock::log("Se ha desconectado del servidor Socket correctamente.");
		
		self::$connection = null;
		self::$server = false;
		self::$actions = Array();
	}
	
	// Funci�n - Conectarse y preparar un servidor Socket.
	// - $host: Host de conexi�n.
	// - $port (Int): Puerto del servidor.
	// - $timeout (Int): Tiempo de ejecuci�n limite.
	// - $e (Bool): �Mostrar error en caso de que el servidor se encuentre apagado?
	public static function Connect($host, $port = 80, $timeout = 0, $e = false)
	{
		self::Crash();		
		set_time_limit($timeout);
		
		$s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or self::Error("01s", __FUNCTION__);		
		$r = socket_connect($s, $host, $port);
		
		if($r == false AND $e == true)
			self::Error("02s", __FUNCTION__);

		BitRock::log("Se ha establecido una conexi�n al servidor Socket en '$host:$port' correctamente.");		
		self::$connection = $s;
		
		return $r;
	}
	
	// Funci�n - Preparar un servidor interno.
	// - $local: Direcci�n para la conexi�n.
	// - $port (Int): Puerto de escucha.
	// - $timeout (Int): Tiempo de ejecuci�n limite.
	public static function Listen($local = '127.0.0.1', $port = 1212, $timeout = 0, $ctimeout = 5)
	{
		return new Server($local, $port, $timeout);
	}
	
	// Funci�n - Enviar datos al servidor.
	// - $data: Datos a enviar.
	// - $response (Bool): �Queremos esperar una respuesta?
	public static function Send($data, $response = false)
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);
			
		$len = strlen($data);
		$off = 0;
		
		while($off < $len)
		{
			$send = socket_write(self::$connection, substr($data, $off), $len - $off);

			if(!$send)
				break;

			$off += $send;
		}
		
		if($off < $len)
			self::Error('04s', __FUNCTION__, 'Ha ocurrido un error al mandar '.$data.' al servidor.');
		
		if(!$response)
			return true;
		
		$bytes = @socket_recv(self::$connection, $data, 2048, 0);
		return $data;
	}
	
	// Funci�n - Agregar un evento/acci�n.
	// - $from: Dato a recibir para activar evento.
	// - $action: Acci�n a realizar.
	public static function Add($from, $action = "")
	{
		if(is_array($from))
		{
			foreach($from as $param => $value)
				self::$actions[$param] = $value;
		}
		else
			self::$actions[$from] = $action;
	}
}

class Server
{
	private static $connection = null;
	private static $rsock = Array();
	private static $asock = Array();
	private static $u = 0;
	private static $t = 0;
	private static $to = 5;

	// Funci�n privada - Lanzar error.
	// - $code: C�digo de error.
	// - $function: Funci�n causante.
	// - $msg: Mensaje del error.
	private static function Error($code, $function, $msg = '')
	{		
		if(empty($msg) AND is_resource(self::$connection))
			$msg = socket_strerror(socket_last_error(self::$connection));
		
		BitRock::setStatus($msg, __FILE__, Array('function' => $function));
		BitRock::launchError($code);
		
		return false;
	}
	
	// Funci�n privada - �Hay alguna conexi�n activa?
	private static function Ready()
	{
		if(self::$connection == null OR !is_resource(self::$connection))
			return false;
			
		return true;
	}
	
	// Funci�n - Destruir conexi�n activa.
	public static function Crash()
	{
		if(!self::Ready())
			return;
		
		socket_shutdown(self::$connection);
		socket_close(self::$connection);
		
		BitRock::log("Se ha apagado el servidor correctamente.");
		self::WriteLog("SERVIDOR APAGADO CORRECTAMENTE");
		
		self::$connection = null;
		self::$rsock = Array();
		self::$asoc = Array();
		self::$u = 0;
		self::$t = 0;
		self:: $to = 5;
	}
	
	// Funci�n - Imprimir un log.
	// - $mesage: Mensaje.
	public static function WriteLog($message)
	{
		$message = iconv("ISO-8859-1", "ASCII//TRANSLIT//IGNORE", $message);;
		echo "[" . date('Y-m-d H:i:s') . "] - $message\n\r";
	}
	
	// Funci�n - Preparar un servidor interno.
	// - $local: Direcci�n para la conexi�n.
	// - $port (Int): Puerto de escucha.
	// - $timeout (Int): Tiempo de ejecuci�n limite.
	// - $ctimeout (Int): Tiempo de inactividad limite para las conexiones entrantes.
	public function __construct($local = '127.0.0.1', $port = 1212, $timeout = 0, $ctimeout = 5)
	{
		self::Crash();		
		set_time_limit($timeout);
		
		self::WriteLog("Preparando conexi�n...");
		
		$s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or self::Error("01s", __FUNCTION__);		
		socket_set_option($s, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($s, $local, $port) or self::Error("01s", __FUNCTION__);
		
		self::WriteLog("Conexi�n creada.");
		
		$r = socket_listen($s) or self::Error("02s", __FUNCTION__);		
		BitRock::log("Se ha establecido una conexi�n de escucha en '$host:$port' correctamente.");		
		
		self::$connection = $s;
		Socket::$server = true;
		self::$asock = Array($s);
		
		self::WriteLog("Escuchando conexiones entrantes desde el puerto $port.");
		self::WriteLog("SERVIDOR INICIADO.");

		if(!is_numeric($ctimeout) OR $ctimeout < 0)
			$ctimeout = 5;
		
		self::$to = $ctimeout;
		self::$t = time();
		
		self::Process();
	}
	
	// Funci�n privada - Recibir conexiones.
	private static function Process()
	{
		if(!self::Ready())
			self::Error("03s", __FUNCTION__);
		
		while(true)
			self::Check();
	}
	
	// Funci�n privada - Checar sockets y conexiones.
	private static function Check()
	{
		if(!self::Ready())
			self::Error("03s", __FUNCTION__);
		
		$csocks = self::$asock;
		socket_select($csocks, $write = NULL, $except = NULL, NULL);
		
		foreach($csocks as $sock)
		{
			self::Check_Statistics();
			
			if($sock == self::$connection)
			{
				$newcon = socket_accept(self::$connection);
				
				if($newcon < 0)
					continue;
				else
				{
					$con = new Connection($newcon, self::$u);
					
					self::$rsock[self::$u] = $con;
					array_push(self::$asock, $newcon);
										
					self::WriteLog("NUEVA CONEXI�N ENTRANTE #" . self::$u);
					self::$u++;
				}
			}
			else
			{
				$bytes = @socket_recv($sock, $data, 2048, 0);
				
				if(empty($data) OR !is_numeric($bytes))
					continue;
					
				$i = array_search($sock, self::$asock);
				
				if($i == false)
					continue;
				
				$i = $i - 1;
				$a = Socket::$actions;
				$con = self::$rsock[$i];
				
				self::WriteLog("RECIBIENDO DATOS ($data)");
				
				if(isset($a[$data]))
				{
					$e = $a[$data];
					$e($con);
				}
				
				if(isset($a["*"]))
				{
					$e = $a["*"];
					$e($con, $data);
				}
				
				$con->last = time();
			}
		}
	}
	
	// Funci�n privada - Chequeo de conexiones.
	private static function Check_Statistics()
	{
		if(!self::Ready())
			self::Error("03s", __FUNCTION__);

		if(self::$t <= (time() - 5))
		{
			$count = self::Check_Ping();
			
			self::WriteLog("Actualmente hay $count conexiones activas con un uso de " . round(memory_get_usage() / 1024,1) . " KB de Memoria.");			
			self::$t = time();
		}
	}
	
	// Funci�n - Ping de conexiones.
	private static function Check_Ping()
	{
		if(!self::Ready())
			self::Error("03s", __FUNCTION__);
				
		foreach(self::$rsock as $con)
		{
			$sock = $con->c;
			
			if($sock == null)
			{
				Server::WriteLog("DESCONEXI�N #" . $con->id);
				unset(self::$rsock[$con->id]);
				
				continue;
			}
			
			if($con->last <= (time() - (self::$to * 60)))
			{
				unset(self::$rsock[$con->id]);
				$con->Crash();				
				
				continue;
			}
		}
		
		return count(self::$rsock);
	}
	
	// Funci�n - Enviar datos a todas las conexiones.
	public static function SendAll($data)
	{		
		foreach(self::$rsock as $con)
			$con->Send($data, false);
	}
}

class Connection
{
	public $connection = null;
	public $last = 0;
	
	public $id = 0;
	
	public function __construct($socket, $id)
	{
		$this->connection = $socket;
		$this->id = $id;
		$this->last = time();
	}

	// Funci�n privada - �Hay alguna conexi�n activa?
	private function Ready()
	{
		if($this->connection == null OR !is_resource($this->connection))
			return false;
			
		return true;
	}
	
	// Funci�n - Destruir conexi�n activa.
	public function Crash()
	{
		if(!$this->Ready())
			return;
		
		socket_shutdown($this->connection);
		socket_close($this->connection);
		
		Server::WriteLog("CONEXI�N #" . $this->id . " CERRADA.");
		$this->connection = null;
	}

	// Funci�n - Liberar conexi�n activa.
	public function Clean()
	{
		if(!$this->Ready())
			return;
		
		socket_close($this->connection);		
		Server::WriteLog("CONEXI�N #" . $this->id . " LIBERADA.");
	}
	
	// Funci�n - Enviar datos.
	// - $data: Datos a enviar.
	// - $response (Bool): �Queremos esperar una respuesta?
	public function Send($data, $response = false)
	{
		if(!$this->Ready())
			return false;
			
		$len = strlen($data);
		$off = 0;		
		
		while($off < $len)
		{
			$send = socket_write($this->connection, substr($data, $off), $len - $off);

			if(!$send)
				break;
			
			$off += $send;
		}
		
		if($off < $len)
		{
			Server::WriteLog("Ha ocurrido un error al intentar enviar los datos '$data'.");
			return false;
		}			

		Server::WriteLog("Se ha enviado '$data' a la conexi�n #" . $this->id . ".");
			
		if(!$response)
		{
			$this->connectionlean();
			return true;
		}
		
		$bytes = @socket_recv($this->connection, $data, 2048, 0);
		$this->connectionlean();

		return $data;
	}
}
?>