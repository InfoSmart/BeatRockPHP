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

$page['gzip'] = false;
require('../Init.php');

function CheckReady()
{
	if(file_exists('../Kernel/Configuration.php') OR file_exists('./SECURE'))
	{
		if($_SESSION['install']['secure'] !== true)
		{
			header("Location: ./error_ready");
			exit;
		}
	}
}

CheckReady();

if($G['do'] == "save")
{
	foreach($_POST as $param => $value)
	{
		$ps = explode("_", $param);
		
		if($ps[0] == "site" OR $ps[0] == "session" OR $ps[0] == "cookie")
		{
			if(is_array($value))
			{
				foreach($value as $par => $val)
					$value[$par] = htmlentities($val);

				$value = json_encode($value);
			}

			Site::Update($param, mysql_real_escape_string($value));
		}
		else if($ps[0] == "stopwatch")
		{
			$param = "$ps[1]_$ps[2]";
			
			if($value < 1440 OR empty($value))
				continue;
				
			query_insert('site_timers', Array(
				'action' => $param,
				'time' => $value,
				'nexttime' => (time() + ($value * 60))
			));
		}
	}
	
	if($P['register'] == "true")
	{
		$exit = urlencode("http://" . str_replace("/step4?do=save", "", URL) . "/finish?license_url=[license_url]&license_name=[license_name]");
		$style = urlencode("https://resources.infosmart.mx/system/setup/style.commons.css");
		$icon = urlencode("https://resources.infosmart.mx/Logo.png");
		
		Core::Redirect("https://creativecommons.org/choose/?partner=InfoSmart&exit_url=$exit&stylesheet=$style&partner_icon_url=$icon");
	}
	
	Core::Redirect("/Setup/finish");
}

$page['name'] = "Configuraci�n de la aplicaci�n";
require('./Header.php');
?>
<div class="content">
	<p>
		Como �ltimo paso es necesario que configures los ajustes generales de tu aplicaci�n.0
	</p>
	
	<form action="./step4?do=save" method="POST">
		<section>
			<h2>General</h2>
			
			<p>
				<label for="site_name">Nombre de la aplicaci�n:</label>
				<input type="text" name="site_name" id="site_name" value="<?=$site['site_name']?>" placeholder="Mi Aplicaci�n" required autofocus autocomplete="off" x-webkit-speech speech />
				
				<span>Escribe el nombre de tu aplicaci�n, la misma ser� mostrada en el titulo de la p�gina.</span>
			</p>
			
			<p>
				<label for="site_separation">Separaci�n de titulo:</label>
				<input type="text" name="site_separation" id="site_separation" value="<?=$site['site_separation']?>" placeholder="~" autocomplete="off" />
				
				<span>Escribe una separaci�n de titulo que ser� usado para por ejemplo seprar el nombre de tu aplicaci�n con el eslogan o nombre de la p�gina.</span>
			</p>
			
			<p>
				<label for="site_slogan">Eslogan de la aplicaci�n:</label>
				<input type="text" name="site_slogan" id="site_slogan" value="<?=$site['site_slogan']?>" placeholder="Tecnolog�a limpia y creativa para todos" autocomplete="off" x-webkit-speech speech />
				
				<span>Escribe un eslogan para tu aplicaci�n, una frase corta que describa de lo que trata.</span>
			</p>
			
			<p>
				<label>Codificaci�n de letras:</label>
				
				<select name="site_charset" class="btn" required>
					<option value="iso-8859-15">iso-8859-15</option>
					<option value="iso-8859-1">iso-8859-1</option>
					<option value="utf-8">utf-8</option>
				</select>
				
				<span>Selecciona la codificaci�n de letras para la aplicaci�n, para letras en espa�ol es recomendable usar <b>iso-8859-15</b>.</span>
			</p>
			
			<p>
				<label for="site_language">Idioma de la aplicaci�n:</label>
				<input type="text" name="site_language" id="site_language" value="<?=$site['site_language']?>" placeholder="es" required autocomplete="off" maxlength="2" />
				
				<span>Escriba las dos primeras letras del idioma de la aplicaci�n, la misma es usada para una referencia en robots como Google.</span>
			</p>
			
			<p>
				<label for="site_description">Descripci�n de la aplicaci�n:</label>
				<textarea name="site_description" id="site_description" placeholder="Aplicaci�n �til para todas las edades..."><?=$site['site_description']?></textarea>
				
				<span>Escriba la descripci�n de la aplicaci�n.</span>
			</p>
			
			<p>
				<label for="site_keywords">Palabras clave de la aplicaci�n:</label>
				<textarea name="site_keywords" id="site_keywords" placeholder="infosmart, beatrock" required><?=$site['site_keywords']?></textarea>
				
				<span>Escriba una serie de palabras separadas por coma (,) que indiquen sobre de lo que trata su aplicaci�n.</span>
			</p>
			
			<p>
				<label for="site_analytics">Analitica:</label>
				<textarea name="site_analytics" id="site_analytics" class="code"><?=$site['site_analytics']?></textarea>
				
				<span>Escriba un c�digo para la Analitica, el mismo ser� incluido al final de todas las p�ginas de su aplicaci�n.</span>
			</p>
			
			<p>
				<label for="site_favicon">Favicon:</label>
				<?=RESOURCES?>/images/<input type="text" name="site_favicon" id="site_favicon" value="<?=$site['site_favicon']?>" placeholder="favicon.ico" autocomplete="off" class="short" />
				
				<span>Escriba el nombre del archivo de su imagen Favicon.</span>
			</p>
			
			<p>
				<label for="site_logo">Logo:</label>
				<?=RESOURCES?>/images/<input type="text" name="site_logo" id="site_logo" value="<?=$site['site_logo']?>" placeholder="logo.png" autocomplete="off" class="short" />
				
				<span>Escriba el nombre del archivo de su imagen Logo.</span>
			</p>
			
			<p>
				<label for="session_alias">Prefijo de las Sesiones:</label>
				<input type="text" name="session_alias" id="session_alias" value="<?=$site['session_alias']?>" placeholder="beatrock_" autocomplete="off" />
				
				<span>Escriba un Prefijo para las "$_SESSION", esto con el fin de evitar conflictos con otras aplicaciones.</span>
			</p>
			
			<p>
				<label for="cookie_alias">Prefijo de las Cookies:</label>
				<input type="text" name="cookie_alias" id="cookie_alias" value="<?=$site['cookie_alias']?>" placeholder="beatrock_" autocomplete="off" />
				
				<span>Escriba un Prefijo para las "$_COOKIE", esto con el fin de evitar conflictos con otras aplicaciones.</span>
			</p>
			
			<p>
				<label for="cookie_duration">Duraci�n de la Cookie:</label>
				<input type="number" name="cookie_duration" id="cookie_duration" value="<?=$site['cookie_duration']?>" placeholder="300" required autocomplete="off" min="30" />
				
				<span>Escriba el tiempo de duraci�n en segundos de las Cookie.</span>
			</p>
			
			<p>
				<label for="cookie_domain">Dominio v�lido de las Cookie:</label>
				<input type="text" name="cookie_domain" id="cookie_domain" value="<?=$site['cookie_domain']?>" placeholder="infosmart.mx" autocomplete="off" />
				
				<span>Escriba el dominio en donde ser� v�lido las Cookies, dejelo en blanco para omitir esta opci�n.</span>
			</p>
			
			<p>
				<label>Aplicaci�n disponible para m�vil:</label>
				
				<select name="site_mobile" class="btn">
					<option value="true">Si</option>
					<option value="false">No</option>
				</select>
				
				<span>Seleccione si su aplicaci�n esta disponible para moviles.</span>
			</p>
			
			<p>
				<label>Mapa del sitio:</label>
				
				<select name="site_sitemap" class="btn">
					<option value="true">Si</option>
					<option value="false">No</option>
				</select>
				
				<span>Seleccione si su aplicaci�n tendr� un "mapa del sitio" en <?=PATH?>/sitemap.</span>
			</p>
			
			<p>
				<label>RSS:</label>
				
				<select name="site_rss" class="btn">
					<option value="true">Si</option>
					<option value="false">No</option>
				</select>
				
				<span>Seleccione si su aplicaci�n tendr� un RSS.</span>
			</p>
			
			<p>
				<label>Obligar idioma:</label>
				
				<input type="text" name="site_translate" id="site_translate" value="<?=$site['site_translate']?>" placeholder="es" autocomplete="off" maxlength="2" />
				
				<span>Si desea obligar a usar un idioma/traducci�n en su aplicaci�n indique las dos primeras letras del idioma, dejelo en blanco para usar el idioma nativo del usuario y si se encuentra una traducci�n del mismo.</span>
			</p>
			
			<p>
				<label>Traducci�n inteligente:</label>
				
				<select name="site_smart_translate" class="btn">
					<option value="false">No</option>
					<option value="true">Si</option>
				</select>
				
				<span>La traducci�n inteligente traduce cualquier palabra que tenga una traducci�n en la base de datos, aunque activarlo puede ocacionar resultados no deseados en aplicaciones con contenido generado por el usuario.</span>
			</p>
			
			<p>
				<label>Optimizaci�n de JavaScript:</label>
				
				<select name="site_bottom_javascript" class="btn">
					<option value="true">Si</option>
					<option value="false">No</option>					
				</select>
				
				<span>La optimizaci�n de JavaScript carga los archivos JavaScript de su aplicaci�n en el pie de p�gina de la misma, esto hace que su aplicaci�n cargue de manera m�s r�pida pero no le permite realizar acciones JavaScript de los archivos en el HTML (Antes del pie de p�gina)</span>
			</p>
		</section>
		
		<section>
			<h2>Derechos de autor</h2>
			
			<p>
				<label for="site_version">Versi�n:</label>
				<input type="text" name="site_version" id="site_version" value="<?=$site['site_version']?>" placeholder="1.0.0" required autocomplete="off" />
				
				<span>Escriba la versi�n de su aplicaci�n.</span>
			</p>
			
			<p>
				<label for="site_revision">�ltima revisi�n:</label>
				<input type="text" name="site_revision" id="site_revision" value="<?=$site['site_revision']?>" placeholder="27 de oct de <?=date('Y')?>" required autocomplete="off" />
				
				<span>Escriba la fecha de la �ltima revisi�n o edici�n de su aplicaci�n.</span>
			</p>
			
			<p>
				<label for="site_author">Autor / Desarrollador</label>
				<input type="text" name="site_author" id="site_author" value="<?=$site['site_author']?>" placeholder="Iv�n Bravo Bravo" required autocomplete="off" x-webkit-speech speech />
				
				<span>Escriba el nombre del autor / desarrollador / webmaster de la aplicaci�n.</span>
			</p>
			
			<p>
				<label for="site_publisher">Empresa / Compa�ia / Organizaci�n distribuidora:</label>
				<input type="text" name="site_publisher" id="site_publisher" value="<?=$site['site_publisher']?>" placeholder="InfoSmart" required autocomplete="off" x-webkit-speech speech />
				
				<span>Escriba la empresa / compa�ia / organizaci�n que mantiene esta aplicaci�n y se encarga de distribuirla.</span>
			</p>
		</section>

		<section>
			<h2>Open Graph</h2>

			<p>
				<label for="site_locale">Lugar de la aplicaci�n:</label>
				<input type="text" name="site_locale" id="site_locale" value="<?=$site['site_locale']?>" placeholder="es_ES" required autocomplete="off" maxlength="5" />
				
				<span>Escriba el formato del lugar/ubicaci�n de la aplicaci�n. (lenguaje)_(territorio)</span>
			</p>

			<p>
				<br />
				<b>�Atenci�n!</b> Por ahora Facebook desconoce algunas ubicaciones como <b>"es_MX"</b> que al estar configuradas en tu aplicaci�n ocacionan que "Open Graph" no funcione correctamente, al publicar tu aplicaci�n utiliza la <a href="https://developers.facebook.com/tools/debug" target="_blank">herramienta "debugger"</a> para verificar si tu ubicaci�n es "aceptada". Si no es as� utiliza ubicaciones m�s conocidas como <b>es_ES</b> o <b>en_US</b>. 
				<br />
			</p>

			<p>
				<label for="site_type">Tipo de aplicaci�n:</label>

				<select name="site_type" id="site_type" class="btn">
					<option value="website">Sitio web normal</option>
					<option value="music.album">Aplicaci�n musical para un alb�m</option>
					<option value="music.radio_station">Aplicaci�n para estaci�n de radio</option>
					<option value="video.movie">Aplicaci�n para una pelicula</option>
					<option value="video.tv_show">Aplicaci�n para un Show de TV</option>
					<option value="video.other">Aplicaci�n visual normal</option>
					<option value="book">Aplicaci�n para un libro</option>
					<option value="profile">Blog personal / Perfil</option>
				</select>

				<span>Selecciona que tipo de aplicaci�n crear�s, si es una aplicaci�n independiente propia selecciona "Sitio web normal".</span>
			</p>

			<div class="oo" data-to="music.album">
				<p>
					<label for="music:album">Alb�m:</label>
					<input type="text" name="site_og[music:album]" disabled />
				</p>

				<p>
					<label for="music:musician">M�sico/Artista/Banda:</label>
					<input type="text" name="site_og[music:musician]" disabled />
				</p>

				<p>
					<label for="music:release_date">Fecha de lanzamiento:</label>
					<input type="text" name="site_og[music:release_date]" disabled />
				</p>
			</div>

			<div class="oo" data-to="music.radio_station">
				<p>
					<label for="music:creator">Creador de la radio:</label>
					<input type="text" name="site_og[music:creator]" disabled />
				</p>
			</div>

			<div class="oo" data-to="video.movie">
				<p>
					<label for="video:actor">Actor principal:</label>
					<input type="text" name="site_og[video:actor]" disabled />
				</p>

				<p>
					<label for="video:director">Director:</label>
					<input type="text" name="site_og[video:director]" disabled />
				</p>

				<p>
					<label for="video:writer">Escritor:</label>
					<input type="text" name="site_og[video:writer]" disabled />
				</p>

				<p>
					<label for="video:duration">Duraci�n:</label>
					<input type="text" name="site_og[video:duration]" disabled />
				</p>

				<p>
					<label for="video:release_date">Fecha de lanzamiento:</label>
					<input type="text" name="site_og[video:release_date]" disabled />
				</p>

				<p>
					<label for="video:tag">Palabras clave:</label>
					<input type="text" name="site_og[video:tag]" disabled />
				</p>
			</div>

			<div class="oo" data-to="video.tv_show">
				<p>
					<label for="video:actor">Actor principal:</label>
					<input type="text" name="site_og[video:actor]" disabled />
				</p>

				<p>
					<label for="video:director">Director:</label>
					<input type="text" name="site_og[video:director]" disabled />
				</p>

				<p>
					<label for="video:writer">Escritor:</label>
					<input type="text" name="site_og[video:writer]" disabled />
				</p>

				<p>
					<label for="video:release_date">Fecha de lanzamiento:</label>
					<input type="text" name="site_og[video:release_date]" disabled />
				</p>

				<p>
					<label for="video:tag">Palabras clave:</label>
					<input type="text" name="site_og[video:tag]" disabled />
				</p>
			</div>

			<div class="oo" data-to="book">
				<p>
					<label for="book:author">Autor:</label>
					<input type="text" name="site_og[book:author]" disabled />
				</p>

				<p>
					<label for="book:release_date">Fecha de lanzamiento:</label>
					<input type="text" name="site_og[book:release_date]" disabled />
				</p>

				<p>
					<label for="book:tag">Palabras clave:</label>
					<input type="text" name="site_og[book:tag]" disabled />
				</p>
			</div>

			<div class="oo" data-to="profile">
				<p>
					<label for="profile:first_name">Nombre:</label>
					<input type="text" name="site_og[profile:first_name]" disabled />
				</p>

				<p>
					<label for="profile:last_name">Apellidos:</label>
					<input type="text" name="site_og[profile:last_name]" disabled />
				</p>

				<p>
					<label for="profile:username">Nombre en la web (Nombre de usuario):</label>
					<input type="text" name="site_og[profile:username]" disabled />
				</p>

				<p>
					<label for="profile:gender">Sexo:</label>
					<select name="profile:gender" disabled>
						<option value="male">Hombre</option>
						<option value="female">Mujer</option>
					</select>
				</p>
			</div>

			<p>
				<a href="http://ogp.me/" target="_blank">M�s informaci�n de Open Graph</a>
			</p>
		</section>
		
		<section>
			<h2>Cronometros</h2>
			
			<p>
				A continuaci�n puede activar algunos cronometros, tareas que ser�n ejecutadas cada cierto tiempo.
			</p>
			
			<p>
				<label>Optimizaci�n de la base de datos:</label>
				
				<select name="stopwatch_optimize_db" class="btn">
					<option value="">Desactivada</option>
					<option value="1440">Cada d�a</option>
					<option value="2880">Cada 2 d�as</option>
					<option value="5760">Cada 4 d�as</option>
					<option value="10080">Cada semana</option>
					<option value="14400">Cada semana, 3 d�as</option>
					<option value="20160">Cada 2 semanas</option>
					<option value="44640">Cada mes</option>
				</select>
				
				<span>Optimiza todas la tablas de la base de datos.</span>
			</p>
			
			<p>
				<label>Limpieza de la base de datos:</label>
				
				<select name="stopwatch_maintenance_db" class="btn">
					<option value="">Desactivada</option>
					<option value="1440">Cada d�a</option>
					<option value="2880">Cada 2 d�as</option>
					<option value="5760">Cada 4 d�as</option>
					<option value="10080">Cada semana</option>
					<option value="14400">Cada semana, 3 d�as</option>
					<option value="20160">Cada 2 semanas</option>
					<option value="44640">Cada mes</option>
				</select>
				
				<span>Limpia las tablas "site_errors" (Errores) y "site_visits" (Visitas) de la base de datos.</span>
			</p>
			
			<p>
				<label>Recuperaci�n de la base de datos:</label>
				
				<select name="stopwatch_backup_db" class="btn">
					<option value="">Desactivada</option>
					<option value="1440">Cada d�a</option>
					<option value="2880">Cada 2 d�as</option>
					<option value="5760">Cada 4 d�as</option>
					<option value="10080">Cada semana</option>
					<option value="14400">Cada semana, 3 d�as</option>
					<option value="20160">Cada 2 semanas</option>
					<option value="44640">Cada mes</option>
				</select>
				
				<span>Crea un archivo SQL con los datos m�s recientes de la base de datos.</span>
			</p>
			
			<p>
				<label>Recuperaci�n de los archivos de la aplicaci�n:</label>
				
				<select name="stopwatch_backup_app" class="btn">
					<option value="">Desactivada</option>
					<option value="1440">Cada d�a</option>
					<option value="2880">Cada 2 d�as</option>
					<option value="5760">Cada 4 d�as</option>
					<option value="10080">Cada semana</option>
					<option value="14400">Cada semana, 3 d�as</option>
					<option value="20160">Cada 2 semanas</option>
					<option value="44640">Cada mes</option>
				</select>
				
				<span>Crea un archivo ZIP con todos los archivos de la aplicaci�n.</span>
			</p>
			
			<p>
				<label>Recuperaci�n de los archivos de la aplicaci�n y la base de datos:</label>
				
				<select name="stopwatch_backup_total" class="btn">
					<option value="">Desactivada</option>
					<option value="1440">Cada d�a</option>
					<option value="2880">Cada 2 d�as</option>
					<option value="5760">Cada 4 d�as</option>
					<option value="10080">Cada semana</option>
					<option value="14400">Cada semana, 3 d�as</option>
					<option value="20160">Cada 2 semanas</option>
					<option value="44640">Cada mes</option>
				</select>
				
				<span>Crea un archivo ZIP con todos los archivos de la aplicaci�n y un SQL de los datos de la base de datos.</span>
			</p>
			
			<p>
				<label>Limpieza de Recuperaciones, logs y archivos temporales:</label>
				
				<select name="stopwatch_maintenance_backups" class="btn">
					<option value="">Desactivada</option>
					<option value="1440">Cada d�a</option>
					<option value="2880">Cada 2 d�as</option>
					<option value="5760">Cada 4 d�as</option>
					<option value="10080">Cada semana</option>
					<option value="14400">Cada semana, 3 d�as</option>
					<option value="20160">Cada 2 semanas</option>
					<option value="44640">Cada mes</option>
				</select>
				
				<span>Elimina los archivos dentro de los directorios "Logs", "Backups" y "Temp" del directorio "Kernel/BitRock/".</span>
			</p>
		</section>
		
		<section>
			<h2>Otros</h2>
			
			<p>
				<input type="checkbox" name="register" value="true" /> Seleccionar una licencia para mi aplicaci�n.
				<span>Ser�s enviado a la p�gina de Creative Commons para escojer una licencia que se adapte a tu aplicaci�n, cuando termines ser�s redireccionado a la p�gina de finalizaci�n y los datos de tu licencia se guardaran en el directorio raiz de BeatRock.</span>
			</p>
			
		</section>
		
		<p>
			<input type="submit" name="save" value="Guardar y terminar" class="ibtn" />
		</p>
	</form>
</div>
<?php require('Footer.php')?>