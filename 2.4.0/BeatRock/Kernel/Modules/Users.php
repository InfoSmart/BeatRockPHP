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

class Users
{
	// Funci�n - Obtener la edad de un usuario por medio del a�o especificado.
	// - $year: A�o.
	public static function Age($year)
	{
		if(!is_numeric($year) || $year < 1990 || $year > date('Y'))
			return $year;
			
		return (date('Y') - $year);
	}
	
	// Funci�n - Contar los usuarios o usuarios online.
	// - $online (Bool): �Usuarios online?
	public static function Count($online = false)
	{
		if($online)
		{
			global $date;
			return query_rows("SELECT null FROM {DA}users WHERE lastonline > '$date[f]'");
		}
		else
			return query_rows("SELECT null FROM {DA}users");
	}
	
	// Funci�n - Obtener los usuarios online.
	// - $limit (Int): Limite de usuarios.
	public static function OnlineUsers($limit = 0)
	{
		global $date;
		
		$q = "SELECT * FROM {DA}users WHERE lastonline > '$date[f]'";
		
		if($limit > 0)
			$q .= " ORDER BY RAND() LIMIT $limit";
			
		return query($q);
	}
	
	// Funci�n - Verificar si un dato ya existe.
	// - $str: Dato a verificar.
	// - $type (email, username): Tipo de verificaci�n.
	public static function Exist($str, $type = 'email')
	{			
		$q = query_rows("SELECT null FROM {DA}users WHERE $type = '$str' LIMIT 1");	
		return $q > 0 ? true : false;
	}
	
	// Funci�n - Iniciar sesi�n.
	// - $id (Int): ID del usuario.
	// - $cookie (Bool): �Recordar el usuario en esta PC?
	public static function Login($id, $cookie = true)
	{
		Core::theSession('login_id', $id);
		
		if($cookie)
		{
			$s = Core::Random(50, true, true, false);
			
			self::UpdateData('cookie_session', $s, $id);
			Core::theCookie('session', $s);
		}
		
		self::Enter($id);
	}
	
	// Funci�n - Desconectarse.
	public static function Logout()
	{
		Core::delSession('login_id');
		Core::delCookie('session');		
		self::UpdateData('cookie_session', '');
	}
	
	// Funci�n - Actualizar datos de conexi�n.
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	public static function Enter($id)
	{
		self::Update(Array(
			'lastaccess' => time(),
			'lastonline' => time(),
			'ip_address' => IP
		), $id);
	}
	
	// Funci�n - Actualizar datos de desconexi�n.
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	public static function Out($id)
	{
		self::Update(Array(
			'lastonline' => '0',
			'ip_address' => IP
		), $id);
	}
	
	// Funci�n - Agregar un nuevo usuario.
	// - $username: Nombre de usuario.
	// - $password: Contrase�a en texto plano.
	// - $name: Nombre real.
	// - $email: Correo electr�nico.
	// - $birthday: Fecha de nacimiento.
	// - $photo (Direcci�n, Array): Foto de perfil.
	// - $auto (Bool): �Auto conectarse?
	// - $o (Array): Otros valores...
	public static function NewUser($username, $password, $name, $email, $birthday = "", $photo = "", $auto = true, $o = '')
	{
		if(is_array($photo))
		{
			if(!is_numeric($photo['size']))
				$photo['size'] = 80;
			
			if(empty($photo['rating']))
				$photo['rating'] = "g";
				
			$photo = self::GetGravatar($email, '', $photo['size'], $photo['default'], $photo['rating']);
		}

		if(!empty($password))
			$password = Core::Encrypte($password);
		
		$data = Array(
			'username' => $username,
			'password' => Core::Encrypte($password),
			'name' => $name,
			'email' => $email,
			'photo' => $photo,
			'birthday' => $birthday,
			'account_birthday' => time(),
			'ip_address' => IP,
			'browser' => BROWSER,
			'agent' => AGENT,
			'os' => OS
		);
		
		if(is_array($o))
		{
			foreach($o as $param => $value)
				$data[$param] = $value;
		}
		
		query_insert('users', $data);
		$id = mysql_insert_id();
		
		if($auto)
			self::Login($id);
			
		return $id;
	}
	
	// Funci�n - Agregar un nuevo servicio.
	// - $id: Identificaci�n de servicio.
	// - $service (facebook, twitter, google): Servicio.
	// - $name: Nombre de identificaci�n de usuario.
	// - $info: Informaci�n obtenida del servicio.
	public static function NewService($id, $service, $name, $info)
	{
		$hash = Core::Random(80);
		
		query_insert('users_services', Array(
			'identification' => $id,
			'service' => $service,
			'name' => $name,
			'user_hash' => $hash,
			'info' => _f($info, false),
			'date' => time()
		));
		
		return $hash;
	}
	
	// Funci�n - �Existe el servicio?
	// - $id: Identificaci�n del servicio.
	// - $service (facebook, twitter, google): Servicio.
	public static function ServiceExist($id, $service)
	{
		$q = query_rows("SELECT null FROM {DA}users_services WHERE identification = '$id' AND service = '$service' LIMIT 1");
		return $q > 0 ? true : false;
	}
	
	// Funci�n - Actualizar varios datos de un servicio.
	// - $data (Array): Datos a actualizar.
	// - $id: ID del servicio.
	public static function UpdateService($data, $id)
	{
		query_update('users_services', $data, Array(
			"id = '$id'"
		), 1);
	}
	
	// Funci�n - Obtener todos los datos de un servicio.
	// - $id: Identificaci�n del servicio.
	// - $service (facebook, twitter, google): Servicio.
	public static function Service($id, $service)
	{
		$q = query("SELECT * FROM {DA}users_services WHERE (id = '$id' OR identification = '$id') AND service = '$service' LIMIT 1");
		return num_rows() > 0 ? fetch_assoc() : false;
	}
	
	// Funci�n - Obtener los datos de un usuario con servicio.
	// - $hash: Hash del servicio.
	// - $service: Servicio.
	public static function UserService($hash, $service)
	{
		$q = query("SELECT * FROM {DA}users WHERE user_hash = '$hash' AND service = '$service' LIMIT 1");
		return num_rows() > 0 ? fetch_assoc() : false;
	}

	// Funci�n - Obtener los servicios conectados a un usuario.
	// - $hash: Hash del usuario.
	public static function GetServices($hash)
	{
		$q = query("SELECT * FROM {DA}users_services WHERE user_hash = '$hash' ORDER BY id DESC");
		return num_rows() > 0 ? $q : false;
	}

	// Funci�n - Eliminar un servicio.
	// $id: Identificaci�n del servicio.
	public static function DeleteService($id)
	{
		query("DELETE FROM {DA}users_services WHERE id = '$id' LIMIT 1");
	}
	
	// Funci�n - �Existe el usuario?
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	// - $row: Parametro a verificar existencia.
	public static function UserExist($id, $row = 'id')
	{
		$q = query_rows("SELECT null FROM {DA}users WHERE id = '$id' OR username = '$id' OR email = '$id' OR $row = '$id' LIMIT 1");		
		return $q > 0 ? true : false;
	}
	
	// Funci�n - Actualizar varios datos de un usuario.
	// - $data (Array): Datos a actualizar.
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	// - $row: Parametro.
	public static function Update($data, $id = '', $row = 'id')
	{
		if(empty($id))
			$id = MY_ID;
			
		if(!is_numeric($id))
			$id = self::Data('id', $id, $row);
			
		query_update('users', $data, Array(
			"id = '$id'"
		), 1);
	}
	
	// Funci�n - Actualizar dato de un usuario.
	// - $data: Parametro a actualizar.
	// - $new: Valor nuevo.
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	// - $row: Parametro.
	public static function UpdateData($data, $new, $id = '', $row = 'id')
	{
		if(empty($id))
			$id = MY_ID;
			
		if(!is_numeric($id))
			$id = self::Data('id', $id, $row);
			
		query_update('users', Array(
			$data => $new
		), Array(
			"id = '$id'"
		), 1);
	}
	
	// Funci�n - Obtener el dato de un usuario.
	// - $data: Parametro a obtener.
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	public static function Data($data, $id = '', $row = 'id')
	{
		if(empty($id))
			$id = MY_ID;
			
		return query_get("SELECT $data FROM {DA}users WHERE id = '$id' OR username = '$id' OR email = '$id' OR $row = '$id' LIMIT 1", $data);
	}
	
	
	// Funci�n - Obtener el dato de un usuario con solo una condici�n.
	// - $data: Parametro a obtener.
	// - $id: Valor del usuario a cumplir la condici�n.
	// - $row: Parametro de condici�n.
	public static function Only($data, $id = '', $row = 'id')
	{
		if(empty($id))
			$id = MY_ID;
			
		return query_get("SELECT $data FROM {DA}users WHERE $row = '$id' LIMIT 1", $data);
	}
	
	// Funci�n - Obtener todos los datos de un usuario.
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	// - $row: Parametro.
	public static function User($id = '', $row = 'id')
	{
		if(empty($id))
			$id = MY_ID;
			
		$q = query("SELECT * FROM {DA}users WHERE id = '$id' OR username = '$id' OR email = '$id' OR $row = '$id' LIMIT 1");
		return num_rows() > 0 ? fetch_assoc() : false;
	}
	
	// Funci�n - Verificar los datos de un usuario.
	// - $id: Identificaci�n (Nombre de usuario/Correo electr�nico).
	// - $password: Contrase�a sin codificar.
	// - $service (String/Bool): Servicio predeterminado.
	public static function Verify($id, $password, $service = false)
	{
		if(!empty($password))
			$password = Core::Encrypte($password);

		$query = "SELECT null FROM {DA}users WHERE (username = '$id' OR email = '$id') AND password = '$password' ";

		if($service !== false)
			$query .= "AND service = '$service' ";

		$query .= 'LIMIT 1';
		
		$q = query_rows($query);
		return $q > 0 ? true : false;
	}
	
	// Funci�n - Checar sesi�n actual.
	public static function CheckSession()
	{
		$sId = _f(Core::theSession('login_id'));
		
		if(empty($sId) OR !self::UserExist($sId))
			goto nosession;
		
		global $my, $ms;
			
		$my = self::User($sId);		
		$ms = @json_decode(Core::theSession('service_info'), true);
		
		foreach($my as $param => $value)
			Tpl::Set('my_' . $param, $value);
			
		if(is_array($ms))
		{
			foreach($ms as $param => $value)
				Tpl::Set('ms_' . $param, $value);
		}
		
		define('MY_ID', $my['id']);
		define('MY_RANK', $my['rank']);
		define('MY_USERNAME', $my['username']);
		define('MY_NAME', $my['name']);
		define('LOG_IN', true);
		
		self::Update(Array(
			'lastonline' => time(),
			'ip_address' => IP,
			'browser' => BROWSER,
			'agent' => AGENT,
			'lasthost' => HOST,
			'os' => OS
		));
		
		return true;
		
		nosession: 
		{
			define('MY_ID', '0');
			define('MY_RANK', '0');
			define('MY_USERNAME', '');
			define('MY_NAME', '');
			define('LOG_IN', false);			
			return false;
		}
	}
	
	// Funci�n - Checar cookie actual.
	public static function CheckCookie()
	{
		if(LOG_IN)
			return false;
			
		$mS = _f(Core::theCookie('session'));
		$ch = self::Only('id', $mS, 'cookie_session');
		
		if(empty($mS) OR !self::UserExist($mS, 'cookie_session') OR !is_numeric($ch))
		{
			self::Logout();
			return false;
		}
		
		self::Login($ch);
		self::CheckSession();	
	}
	
	// Funci�n - Verificar si un usuario se encuentra online.
	// - $id: ID/Nombre de usuario/Correo electr�nico del usuario.
	// - $t (Bool): �Devolver en texto?
	public static function IsOnline($id = '', $t = false)
	{
		global $date;
		
		if(empty($id))
			$id = MY_ID;
		
		$online = self::Data("lastonline", $id);
		
		if($online >= $date['f'])
		{
			if($t)
				return "Online";
			else
				return true;
		}
		
		if($t)
			return "Offline";
		else
			return false;
	}
	
	// Funci�n - Obtener el "Gravatar" de un usuario.
	// - $email: Correo electr�nico.
	// - $to: Ruta del archivo de destino.
	// - $size (Int): Tama�o de ancho y altura.
	// - $default (Ruta, mm, identicon, monsterid, wavatar, retro): Imagen predeterminada en caso de que el usuario no use el servicio de Gravatar.
	// - $rating (g, pg, r, x): Clasificaci�n m�xima de las imagenes.
	public static function GetGravatar($email, $to = '', $size = 40, $default = 'mm', $rating = 'g')
	{
		$email = md5(strtolower(trim($email)));
		$default = urlencode($default);
		
		$gravatar = "http://www.gravatar.com/avatar/$email?s=$size&d=$default&r=$rating";
		
		if(!empty($to))
			Io::Write($to, $gravatar);
			
		return $gravatar;
	}
	
	/*####################################################
	##	FUNCIONES PERSONALIZADAS						##
	####################################################*/
}
?>