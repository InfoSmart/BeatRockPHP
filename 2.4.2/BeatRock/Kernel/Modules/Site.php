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

class Site
{
	// Funci�n - Obtener la configuraci�n del sitio.
	public static function GetConfig()
	{
		$sql = query('SELECT var,result FROM {DA}site_config');
		
		while($row = fetch_assoc())
		{
			$i = 0;
			
			foreach($row as $param => $value)
			{
				$i++;
				
				if($i == 1)
					$p = $value;
				else
					$v = $value;
			}
			
			$site[$p] = $v;
		}
		
		return $site;
	}
	
	// Funci�n - Actualizar configuraci�n del sitio.
	// - $var: Variable.
	// - $value: Nuevo valor.
	public static function Update($var, $value)
	{
		global $site;
		
		Update('site_config', Array(
			'result' => $value
		), Array(
			'var = "'.$var.'"'
		));
		
		$site[$var] = $value;
	}
	
	// Funci�n - Agregar una nueva visita.
	public static function AddVisit()
	{
		$host = Client::Get('host');
		$browser = Client::Get('browser');
		$type = 'desktop';
			
		if(Core::IsMobile())
			$type = 'mobile';
			
		if(Core::IsBOT())
			$type = 'bot';

		Insert('site_visits_total', Array(
			'ip' => IP,
			'host' => $host,
			'agent' => _f(AGENT),
			'browser' => $browser,
			'path' => _f(PATH_NOW),
			'referer' => _f(FROM),
			'phpid' => session_id(),
			'type' => $type,
			'date' => time()
		));

		if(Core::theSession('visit_me') == 'true')
			return;
		
		$n = Rows("SELECT null FROM {DA}site_visits WHERE ip = '".IP."' OR host = '$host' OR phpid = '".session_id()."' LIMIT 1");
		
		if($n !== 0)
			return;

		Insert('site_visits', Array(
			'ip' => IP,
			'host' => $host,
			'agent' => _f(AGENT),
			'browser' => $browser,
			'referer' => _f(FROM),
			'phpid' => session_id(),
			'type' => $type,
			'date' => time()
		));
		
		Core::theSession('visit_me', 'true');
		query("UPDATE {DA}site_config SET result = result + 1 WHERE var = 'site_visits' LIMIT 1");
	}
	
	// Funci�n - Checar cronometros.
	public static function CheckTimers()
	{
		$q = query("SELECT id,action,time,nexttime FROM {DA}site_timers");
		
		while($row = fetch_assoc())
		{
			if($row['time'] == '0' OR $row['nexttime'] >= time())
				continue;
				
			self::DoTimer($row['action']);
			$next = Core::Time($row['time']);
			
			Update('site_timers', Array(
				'nexttime' => $next,
			), Array(
				"id = '$row[id]'"
			));
		}
	}
	
	// Funci�n - Ejecutar cronometro.
	// - $a: Cronometro.
	public static function DoTimer($a)
	{
		if(empty($a))
			return;
			
		switch($a)
		{
			case 'optimize_db':
				MySQL::Optimize();
			break;
			
			case 'maintenance_db':
				query('TRUNCATE TABLE {DA}site_visits');
				query('TRUNCATE TABLE {DA}site_errors');
				query('TRUNCATE TABLE {DA}site_logs');
			break;
			
			case 'backup_db':
				MySQL::Backup();
			break;
			
			case 'backup_app':
				BitRock::Backup();
			break;
			
			case 'backup_total':
				BitRock::Backup(true);
			break;
			
			case 'maintenance':
				Io::EmptyDir(Array(BIT . 'Logs', BIT . 'Backups', BIT . 'Temp', BIT . 'Cache'));
			break;
		}
		
		BitRock::log('Se ha ejecutado el cronometro "'.$a.'" con �xito.');
	}
	
	// Funci�n - Obtener datos.
	// - $a (countrys, maps, news): Tipo de datos a obtener.
	// - $limit (Int): Limite de valores a obtener.
	public static function Get($a = 'countrys', $limit = 0)
	{
		if($a !== 'countrys' AND $a !== 'maps' AND $a !== 'news')
			return false;	
			
		$q = 'SELECT * FROM {DA}site_'.$a.' ORDER BY ';
		$q .= $a == 'countrys' ? 'name ASC' : 'id DESC';
			
		if($limit !== 0)
			$q .= ' LIMIT ' . $limit;
			
		return query($q);
	}
	
	// Funci�n - Obtener noticia.
	// - $id (Int): ID de la noticia.
	public static function GetNew($id)
	{
		$q = query("SELECT * FROM {DA}site_news WHERE id = '$id' LIMIT 1");		
		return num_rows() > 0 ? $q : false;
	}
	
	// Funci�n - Guardar logs actuales.
	public static function SaveLog()
	{
		$logs = BitRock::$logs['all']['text'];
		
		if(empty($logs))
			return;
			
		Insert('site_logs', Array(
			'logs' => _f($logs, false),
			'phpid' => session_id(),
			'path' => _f(PATH),
			'date' => time()
		));
	}
	
	// Funci�n - Obtener traducciones.
	// - $lang: C�digo de lenguaje.
	public static function GetTranslations($lang = '')
	{		
		if(empty($lang))
			$lang = LANG;
			
		$q = query("SELECT var,original,translated,language FROM {DA}site_translate WHERE language = '$lang'");
		return num_rows() > 0 ? $q : false;
	}
	
	// Funci�n - Obtener traducciones.
	// - $lang: C�digo de lenguaje.
	public static function GetTranslation($lang = '')
	{		
		if(empty($lang))
			$lang = LANG;
			
		$q = query("SELECT id,var,original,translated,language FROM {DA}site_translate WHERE language = '$lang'");
		
		if(num_rows() > 0)
		{
			while($row = fetch_assoc($q))
				$result[$row['var']] = $row['translated'];
		}
		
		return $result;
	}	
	
	// Funci�n - Obtener Cach� de p�gina.
	// - $page: P�gina.
	public static function GetCache($page)
	{
		$q = query("SELECT id,page,time FROM {DA}site_cache WHERE page = '$page' LIMIT 1");
		return num_rows() > 0 ? fetch_assoc($q) : false;
	}	
	
	/*####################################################
	##	FUNCIONES PERSONALIZADAS						##
	####################################################*/
}
?>