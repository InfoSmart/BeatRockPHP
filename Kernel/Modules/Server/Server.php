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

// Acci�n ilegal.
if(!defined('BEATROCK'))
	exit;

class Server
{
	// Socket del servidor.
	static $socket 			= null;
	// Array con los recursos "Connection" de las conexiones entrantes.
	static $rsock 			= array();
	// Array de los recursos de escucha de las conexiones entrantes.
	static $asock 			= array();
	// Conexiones entrantes online.
	static $count 			= 0;
	// �ltimo chequeo de Ping.
	static $ping_time 		= 0;
	// Tiempo m�ximo de inactividad de la conexi�n entrante.
	static $conn_timeout 	= 5;

	// Funci�n - Lanzar error.
	// - $code: C�digo de error.
	// - $function: Funci�n causante.
	// - $message: Mensaje del error.
	static function Error($code, $function, $message = '')
	{		
		if(empty($message) AND is_resource(self::$socket))
			$message = socket_strerror(socket_last_error(self::$socket));
		
		BitRock::SetStatus($message, __FILE__, array('function' => $function));
		BitRock::LaunchError($code);
		
		return false;
	}
	
	// Funci�n - �Hay alguna conexi�n activa?
	static function Ready()
	{
		if(self::$socket == null OR !is_resource(self::$socket))
			return false;
			
		return true;
	}
	
	// Funci�n - Destruir conexi�n activa.
	static function Kill()
	{
		if(!self::Ready())
			return;
		
		socket_shutdown(self::$socket);
		socket_close(self::$socket);
		
		BitRock::log('Se ha apagado el servidor correctamente.');
		self::Write('SERVIDOR APAGADO CORRECTAMENTE');
		
		self::$socket 			= null;
		self::$rsock 			= array();
		self::$asoc 			= array();
		self::$count 			= 0;
		self::$ping_time 		= 0;
		self::$conn_timeout 	= 5;
	}
	
	// Funci�n - Imprimir un log.
	// - $mesage: Mensaje.
	static function Write($message)
	{
		// Convertir el mensaje a ISO-8859-1
		$message = iconv('ISO-8859-1', "ASCII//TRANSLIT//IGNORE", $message);
		// Imprimir el mensaje.
		echo "[" . date('Y-m-d H:i:s') . "] - $message\n\r";
	}
	
	// Funci�n - Preparar un servidor interno.
	// - $local: Direcci�n para la conexi�n.
	// - $port (Int): Puerto de escucha.
	// - $timeout (Int): Tiempo de ejecuci�n limite.
	// - $ctimeout (Int): Tiempo de inactividad limite para las conexiones entrantes.
	function __construct($local = '127.0.0.1', $port = 1212, $timeout = 0, $ctimeout = 5)
	{
		self::Kill();		
		set_time_limit($timeout);
		
		self::Write('Preparando servidor...');
		
		// Crear el Socket para el servidor.
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or self::Error('01s', __FUNCTION__);		
		socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
		socket_bind($socket, $local, $port) or self::Error('01s', __FUNCTION__);
		
		self::Write('Conexi�n creada.');
		
		// Crear el Socket encargado de escuchar las nuevas conexiones.
		$asock = socket_listen($s) or self::Error('02s', __FUNCTION__);
		
		// Estableciendo el Socket del servidor.
		self::$socket 	= $socket;
		// Estableciendo en el array el socket de escucha.
		self::$asock 	= array($asock);

		// Estamos en un servidor, esto evitar� que se cargue el sistema de plantillas.
		Socket::$server = true;
		
		self::Write('Escuchando conexiones entrantes desde el puerto: ' . $port);
		self::Write('SERVIDOR INICIADO.');

		if(!is_numeric($ctimeout) OR $ctimeout < 0)
			$ctimeout = 5;
	
		// Estableciendo el tiempo m�ximo de inactividad.		
		self::$conn_timeout = $ctimeout;
		// Estableciendo la �ltima vez que se hizo chequeo de Ping.
		self::$ping_time 	= time();
		
		// Empezar a escuchar conexiones.
		self::Process();
	}
	
	// Funci�n privada - Recibir conexiones.
	static function Process()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);
		
		// Bucle infinito para escuchar conexiones.
		while(true)
			self::Check();
	}
	
	// Funci�n privada - Checar sockets y conexiones.
	static function Check()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);
		
		$asock = self::$asock;
		// Seleccionando socket por socket.
		socket_select($asock, $write = NULL, $except = NULL, NULL);
		
		foreach($asock as $conn)
		{
			// Antes de todo chechar las estadisticas y el Ping.
			self::Check_Statistics();
			
			// Si el Socket a revisar es el Socket que escucha nuevas conexiones...
			if($conn == self::$socket)
			{
				// �Alguien se quiere conectar a nuestro servidor?
				$incoming = socket_accept(self::$socket);
				
				// Hasta ahora nadie...
				if($incoming < 0)
					continue;
				// Si!
				else
				{
					// Inicializando instancia "Connection" para la nueva conexi�n.
					$newconn = new Connection($incoming, self::$count);
					
					// Guardando esta nueva instancia en $rsock
					self::$rsock[self::$count] = $newconn;
					// Guarando el Socket de esta conexi�n en $asock
					array_push(self::$asock, $incoming);
						
					// Uno m�s en nuestro servidor ;)			
					self::Write('NUEVA CONEXI�N ENTRANTE #' . self::$count);
					++self::$count;
				}
			}
			// Si el Socket a revisar es un Socket de usuario.
			else
			{
				// Revisar si se ha recibido informaci�n del Socket. (Paquetes)
				$bytes = @socket_recv($conn, $data, 2048, 0);
				
				// Al parecer no... �Siguiente!
				if(empty($data) OR !is_numeric($bytes))
					continue;
				
				// Obtener la ID del Socket.
				$i = array_search($conn, self::$asock);
				
				// Mmm... �Se le fue el Internet?
				if($i == false)
					continue;
				
				// FIXME - Un ajuste de ID.
				$i 			= $i - 1;
				// Obtener lista de acciones.
				$actions 	= Socket::$actions;
				// Obtener el recurso "Connection" de este Socket.
				$connection = self::$rsock[$i];
				
				self::Write('RECIBIENDO DATOS ('.$data.')');
				
				// Al parecer hay una acci�n para esta informaci�n/paquete.
				if(isset($actions[$data]))
				{
					// Ejecutar acci�n.
					$callback = $actions[$data];
					$callback($connection);
				}
					
				// Al parecer hay una acci�n a ejecutar al recibir cualquier informaci�n/paquete.
				if(isset($actions['*']))
				{
					// Ejecutar acci�n.
					$callback = $actions['*'];
					$callback($connection, $data);
				}
				
				// �ltima actividad de la conexi�n/usuario.
				$connection->last = time();
			}
		}
	}
	
	// Funci�n - Chequeo de conexiones.
	static function Check_Statistics()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);

		// A�n no es tiempo de checar.
		if(self::$ping_time > (time() - 5))
			return;

		// Checar Ping y obtener usuarios online.
		$count = self::Check_Ping();
			
		self::Write("Actualmente hay $count conexiones activas con un uso de " . round(memory_get_usage() / 1024,1) . " KB de Memoria.");
		// Estableciendo la �ltima vez que se hizo chequeo de Ping.
		self::$ping_time = time();
	}
	
	// Funci�n - Ping de conexiones.
	static function Check_Ping()
	{
		if(!self::Ready())
			self::Error('03s', __FUNCTION__);
			
		// Verificando conexi�n por conexi�n.
		foreach(self::$rsock as $conn)
		{
			$connection = $conn->socket;
			
			// La conexi�n se ha desconectado pac�ficamente, quitarla de la lista.
			if($connection == null)
			{
				self::Write('DESCONEXI�N #' . $conn->id);
				unset(self::$rsock[$conn->id]);
				
				continue;
			}
			
			// La conexi�n ha pasado el tiempo de inactividad, quitarla de la lista.
			if($conn->last <= (time() - (self::$conn_timeout * 60)))
			{
				unset(self::$rsock[$conn->id]);
				$conn->Kill();				
				
				continue;
			}
		}
		
		// Devolver usuarios online.
		return count(self::$rsock);
	}
	
	// Funci�n - Enviar datos a todas las conexiones.
	static function SendAll($data)
	{		
		foreach(self::$rsock as $conn)
			$conn->Send($data, false);
	}

	// Funci�n - Agregar una acci�n.
	// - $data: Dato a recibir para activar acci�n.
	// - $action: Funci�n a realizar.
	static function AddAction($data, $action = '')
	{
		if(is_array($data))
		{
			foreach($data as $key => $value)
				self::$actions[$key] = $value;
		}
		else
			self::$actions[$data] = $action;
	}
}
?>