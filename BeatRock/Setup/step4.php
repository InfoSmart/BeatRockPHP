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

if(file_exists('../Kernel/Configuration.php') OR file_exists('./SECURE'))
{
	if($_SESSION['install']['secure'] !== true)
	{
		header('Location: ./error_ready.php');
		exit;
	}
}

$page['name'] = 'Configuraci�n de la aplicaci�n';
require('./Header.php');
?>
<div class="pre">
	<section class="left">
		<h2>�Como ser� su aplicaci�n?</h2>
		<cite>Nombre, descripci�n, el eslogan y otras partes de su aplicaci�n o sitio web.</cite>

		<p>
			Ahora proceda a darle nombre a su aplicaci�n o sitio web, as� como una descripci�n corta, un eslogan y otras configuraciones de acuerdo a lo que tratar� o las funcionalidades que tendr�.
		</p>

		<p>
			Recuerde que el proposito principal de BeatRock es ahorrarle trabajo para que usted pueda desarrollar la idea principal de su aplicaci�n directamente, algunas de estas configuraciones tendr�n efecto directo en las cabeceras donde tanto robots de indexaci�n como robots sociales podr�n obtener informaci�n de su aplicaci�n y ofrecer mejores experiencias a sus usuarios.
		</p>
	</section>

	<figure class="right">
		<img src="<?=RESOURCES_INS?>/system/setup/images/step4.png" />
	</figure>
</div>

<div class="content">	
	<form action="./actions/save_step4.php" method="POST">
		<details open>
			<summary>General</summary>

			<div class="c1">			
				<p>
					<label for="site_name">Nombre de la aplicaci�n:</label>
					<input type="text" name="site_name" id="site_name" value="<?=$site['site_name']?>" placeholder="Mi Aplicaci�n" required autofocus autocomplete="off" x-webkit-speech speech />
					
					<span>Escribe el nombre de tu aplicaci�n, la misma ser� mostrada en el titulo de la p�gina.</span>
				</p>
			
				<p>
					<label for="site_separation">Separaci�n de titulo:</label>
					<input type="text" name="site_separation" id="site_separation" value="<?=$site['site_separation']?>" placeholder="~" autocomplete="off" />
					
					<span>Escribe una separaci�n de titulo que ser� usado para por ejemplo, separar el nombre de tu aplicaci�n y el eslogan o nombre de la p�gina.</span>
				</p>

				<p>
					<label for="site_keywords">Palabras clave de la aplicaci�n:</label>
					<textarea name="site_keywords" id="site_keywords" placeholder="infosmart, beatrock" required><?=$site['site_keywords']?></textarea>
					
					<span>Escriba una serie de palabras separadas por comas (,) que indiquen referencias acerca del contenido de su aplicaci�n.</span>
				</p>				
				
				<p>
					<label>Mapa del sitio:</label>
					
					<select name="site_sitemap" class="btn">
						<option value="true">Si</option>
						<option value="false">No</option>
					</select>
					
					<span>Seleccione si su aplicaci�n tendr� un "mapa del sitio" que ser� ubicado <b><?=PATH?>/sitemap</b>.</span>
				</p>

				<p>
					<label for="site_favicon">Favicon:</label>
					<?=RESOURCES?>/images/<input type="text" name="site_favicon" id="site_favicon" value="<?=$site['site_favicon']?>" placeholder="favicon.ico" autocomplete="off" class="short" />
					
					<span>Escriba el nombre del archivo de su imagen Favicon.</span>
				</p>
			</div>

			<div class="c2">
				<p>
					<label for="site_slogan">Eslogan de la aplicaci�n:</label>
					<input type="text" name="site_slogan" id="site_slogan" value="<?=$site['site_slogan']?>" placeholder="Tecnolog�a limpia y creativa para todos" autocomplete="off" x-webkit-speech speech />
					
					<span>Escribe un eslogan para tu aplicaci�n, una frase corta que describa de lo que trata.</span>
				</p>

				<p>
					<label>Codificaci�n:</label>
					
					<select name="site_charset" class="btn" required>
						<option value="iso-8859-15">iso-8859-15</option>
						<option value="iso-8859-1">iso-8859-1</option>
						<option value="utf-8">utf-8</option>
					</select>
					
					<span>Selecciona la codificaci�n de letras para la aplicaci�n, para letras en espa�ol recomendamos usar <b>iso-8859-15</b>.</span>
				</p>

				<p>
					<label for="site_description">Descripci�n de la aplicaci�n:</label>
					<textarea name="site_description" id="site_description" placeholder="Aplicaci�n �til para todas las edades..."><?=$site['site_description']?></textarea>
					
					<span>Escriba la descripci�n de la aplicaci�n.</span>
				</p>

				<p>
					<label>RSS:</label>
					
					<select name="site_rss" class="btn">
						<option value="true">Si</option>
						<option value="false">No</option>
					</select>
					
					<span>Seleccione si su aplicaci�n tendr� un RSS de noticias que ser� ubicado en <b><?=PATH?>/rss</b>.</span>
				</p>

				<p>
					<label for="site_logo">Logo:</label>
					<?=RESOURCES?>/images/<input type="text" name="site_logo" id="site_logo" value="<?=$site['site_logo']?>" placeholder="logo.png" autocomplete="off" class="short" />
					
					<span>Escriba el nombre del archivo de su imagen Logo.</span>
				</p>
			</div>
		</details>

		<details>
			<summary>T�cnico</summary>

			<div class="c1">
				<p>
					<label for="site_compress">Compresi�n HTML:</label>

					<select name="site_compress" class="btn">
						<option value="false">Desactivado</option>
						<option value="true">Activado</option>						
					</select>

					<span>La compresi�n HTML comprime el c�digo HTML de la aplicaci�n quitando espacios innecesarios y comentarios haciendola menos pesada y m�s r�pida, sin embargo puede ocacionar problemas con JavaScript dentro del HTML.</span>
				</p>

				<p>
					<label for="cpu_limit">Limite de carga del CPU:</label>
					
					<select name="cpu_limit" class="btn" required>
						<option value="0">Desactivado</option>
						<option value="50">50%</option>
						<option value="60">60%</option>
						<option value="70">70%</option>
						<option value="80">80%</option>
						<option value="90" selected>90%</option>
						<option value="95">95%</option>
					</select>
					
					<span>Seleccione el limite de carga media del CPU (Procesador), en caso de que supere la cantidad seleccionada se mostrar� una p�gina de "Sobrecarga".</span>
				</p>

				<p>
					<label for="session_alias">Prefijo de las Sesiones:</label>
					<input type="text" name="session_alias" id="session_alias" value="<?=$site['session_alias']?>" placeholder="beatrock_" autocomplete="off" />
					
					<span>Escriba un prefijo para la definici�n de "$_SESSION", esto con el fin de evitar conflictos con otras aplicaciones.</span>
				</p>
				
				<p>
					<label for="cookie_alias">Prefijo de las Cookies:</label>
					<input type="text" name="cookie_alias" id="cookie_alias" value="<?=$site['cookie_alias']?>" placeholder="beatrock_" autocomplete="off" />
					
					<span>Escriba un prefijo para la definici�n de "$_COOKIE", esto con el fin de evitar conflictos con otras aplicaciones.</span>
				</p>

				<p>
					<label>Optimizaci�n de JavaScript:</label>
					
					<select name="site_optimized_javascript" class="btn">
						<option value="true">Si</option>
						<option value="false">No</option>					
					</select>
					
					<span>La optimizaci�n de JavaScript carga los archivos JavaScript de su aplicaci�n en el pie de p�gina de la misma.</span>
				</p>
			</div>

			<div class="c2">
				<p>
					<label for="site_recovery">Recuperaci�n avanzada:</label>

					<select name="site_recovery" class="btn">
						<option value="true">Activado</option>
						<option value="false">Desactivado</option>
					</select>

					<span>La recuperaci�n avanzada recupera el archivo de configuraci�n y la base de datos en caso de que hayan sido eliminados.</span>
				</p>

				<?php if(file_exists('../.htaccess')) { ?>
				<p>
					<label>Carga de memoria limite de Apache:</label>
					
					<select name="apache_limit" class="btn" required>
						<option value="0">Desactivado</option>
						<option value="52428800">50 MB</option>
						<option value="83886080">80 MB</option>
						<option value="104857600">100 MB</option>
						<option value="157286400">150 MB</option>
						<option value="209715200">200 MB</option>
						<option value="314572800" selected>300 MB</option>
						<option value="419430400">400 MB</option>
						<option value="524288000">500 MB</option>
						<option value="629145600">600 MB</option>
						<option value="734003200">800 MB</option>
						<option value="1073741824">1 GB</option>
						<option value="1610612736">1.5 GB</option>
						<option value="2147483648">2 GB</option>
						<option value="5368709120">5 GB</option>
					</select>
					
					<span>Seleccione la carga de memoria limite para el proceso de Apache <b>(httpd)</b>, en caso de que supere la cantidad seleccionada se mostrar� una p�gina de "Sobrecarga".</span>
				</p>
				<?php } ?>

				<p>
					<label for="cookie_duration">Duraci�n de la Cookie:</label>
					<input type="number" name="cookie_duration" id="cookie_duration" value="<?=$site['cookie_duration']?>" placeholder="300" required autocomplete="off" min="30" />
					
					<span>Especifique el tiempo de duraci�n en minutos de las Cookie.</span>
				</p>
				
				<p>
					<label for="cookie_domain">Dominio v�lido de las Cookie:</label>
					<input type="text" name="cookie_domain" id="cookie_domain" value="<?=$site['cookie_domain']?>" placeholder="infosmart.mx" autocomplete="off" />
					
					<span>Escriba el dominio en donde ser� v�lido las Cookies, dejelo en blanco para omitir esta opci�n.</span>
				</p>
			</div>
		</details>

		<details>
			<summary>Idioma y traducci�n</summary>

			<div class="c1">
				<p>
					<label for="site_language">Idioma de la aplicaci�n:</label>
					<input type="text" name="site_language" id="site_language" value="<?=$site['site_language']?>" placeholder="es" required autocomplete="off" maxlength="2" />
					
					<span>Escriba las dos primeras letras del idioma de la aplicaci�n, la misma como una referencia para robots como Google y para un estandar recomendado por la W3C.</span>
				</p>

				<p>
					<label>Obligar idioma:</label>					
					<input type="text" name="site_translate" id="site_translate" value="<?=$site['site_translate']?>" placeholder="es" autocomplete="off" maxlength="2" />
					
					<span>Si desea obligar a usar un idioma/traducci�n en su aplicaci�n indique las dos primeras letras del idioma, dejelo en blanco para usar el idioma nativo del usuario.</span>
				</p>
			</div>

			<div class="c2">
				<p>
					<label>Traducci�n inteligente:</label>
					
					<select name="site_smart_translate" class="btn">
						<option value="false">No</option>
						<option value="true">Si</option>
					</select>
					
					<span>La traducci�n inteligente traduce cualquier palabra que tenga una traducci�n en la base de datos, aunque activarlo puede ocacionar resultados no deseados en aplicaciones con contenido generado por el usuario.</span>
				</p>
			</div>
		</details>
		
		<details>
			<summary>Informaci�n</summary>

			<div class="c1">			
				<p>
					<label for="site_version">Versi�n:</label>
					<input type="text" name="site_version" id="site_version" value="<?=$site['site_version']?>" placeholder="1.0.0" required autocomplete="off" />
					
					<span>Escriba la versi�n de su aplicaci�n.</span>
				</p>

				<p>
					Use el archivo <b>humans.txt</b> para especificar los desarrolladores, dise�adores, creadores de un buen caf� y personas que participaron en la creaci�n de esta aplicaci�n. <a href="http://humanstxt.org/ES" target="_blank">M�s informaci�n</a>
				</p>
			</div>

			<div class="c2">			
				<p>
					<label for="site_revision">�ltima revisi�n:</label>
					<input type="text" name="site_revision" id="site_revision" value="<?=$site['site_revision']?>" placeholder="27 de oct de <?=date('Y')?>" required autocomplete="off" />
					
					<span>Escriba la fecha de la �ltima revisi�n o edici�n de su aplicaci�n.</span>
				</p>

				<p>
					<label for="site_publisher">Empresa / Compa�ia / Organizaci�n distribuidora:</label>
					<input type="text" name="site_publisher" id="site_publisher" value="<?=$site['site_publisher']?>" placeholder="InfoSmart" required autocomplete="off" x-webkit-speech speech />
					
					<span>Escriba la empresa / compa�ia / organizaci�n que mantiene esta aplicaci�n y se encarga de distribuirla.</span>
				</p>
			</div>			
		</details>

		<details>
			<summary>Open Graph</summary>

			<div class="c1">
				<p>
					<label for="site_locale">Lugar de la aplicaci�n:</label>
					<input type="text" name="site_locale" id="site_locale" value="<?=$site['site_locale']?>" placeholder="es_LA" required autocomplete="off" maxlength="5" />
					
					<span>Escriba el formato del lugar/ubicaci�n de la aplicaci�n. (lenguaje)_(territorio)</span>
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
			</div>

			<div class="c2">
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

				<p>
					<a href="http://ogp.me/" target="_blank">M�s informaci�n de Open Graph</a>
				</p>
			</div>			
		</details>
		
		<details>
			<summary>Tareas cronometradas</summary>

			<div class="c1">			
				<p>
					<label>Optimizaci�n de la base de datos:</label>
					
					<select name="stopwatch_optimize_db" class="btn">
						<option value="">Desactivada</option>
						<option value="1440">A diario</option>
						<option value="2880">Cada 2 d�as</option>
						<option value="5760">Cada 4 d�as</option>
						<option value="10080">Cada semana</option>
						<option value="14400">Cada semana, 3 d�as</option>
						<option value="20160">Cada 2 semanas</option>
						<option value="44640">Cada mes</option>
						<option value="89280">Cada 2 meses</option>
						<option value="133920">Cada 3 meses</option>
					</select>
					
					<span>Optimiza todas la tablas de la base de datos.</span>
				</p>

				<p>
					<label>Recuperaci�n de la base de datos:</label>
					
					<select name="stopwatch_backup_db" class="btn">
						<option value="">Desactivada</option>
						<option value="1440">A diario</option>
						<option value="2880">Cada 2 d�as</option>
						<option value="5760">Cada 4 d�as</option>
						<option value="10080">Cada semana</option>
						<option value="14400">Cada semana, 3 d�as</option>
						<option value="20160">Cada 2 semanas</option>
						<option value="44640">Cada mes</option>
						<option value="89280">Cada 2 meses</option>
						<option value="133920">Cada 3 meses</option>
					</select>
					
					<span>Crea un archivo SQL con los datos m�s recientes de la base de datos.</span>
				</p>

				<p>
					<label>Recuperaci�n de los archivos de la aplicaci�n y la base de datos:</label>
					
					<select name="stopwatch_backup_total" class="btn">
						<option value="">Desactivada</option>
						<option value="1440">A diario</option>
						<option value="2880">Cada 2 d�as</option>
						<option value="5760">Cada 4 d�as</option>
						<option value="10080">Cada semana</option>
						<option value="14400">Cada semana, 3 d�as</option>
						<option value="20160">Cada 2 semanas</option>
						<option value="44640">Cada mes</option>
						<option value="89280">Cada 2 meses</option>
						<option value="133920">Cada 3 meses</option>
					</select>
					
					<span>Crea un archivo ZIP con todos los archivos de la aplicaci�n y un SQL de los datos de la base de datos.</span>
				</p>

				<!--
				<p>
					<label>Examinaci�n de archivos malintencionados:</label>
					
					<select name="stopwatch_antimalware" class="btn">
						<option value="">Desactivada</option>
						<option value="1440">A diario</option>
						<option value="2880">Cada 2 d�as</option>
						<option value="5760">Cada 4 d�as</option>
						<option value="10080">Cada semana</option>
						<option value="14400">Cada semana, 3 d�as</option>
						<option value="20160">Cada 2 semanas</option>
						<option value="44640">Cada mes</option>
						<option value="89280">Cada 2 meses</option>
						<option value="133920">Cada 3 meses</option>
					</select>
					
					<span>Busca por archivos malintencionados y envia una copia de su aplicaci�n al servicio de <a href="https://www.virustotal.com/" target="_blank">Virus Total</a>.</span>
				</p>
				-->
			</div>

			<div class="c2">			
				<p>
					<label>Limpieza de la base de datos:</label>
					
					<select name="stopwatch_maintenance_db" class="btn">
						<option value="">Desactivada</option>
						<option value="1440">A diario</option>
						<option value="2880">Cada 2 d�as</option>
						<option value="5760">Cada 4 d�as</option>
						<option value="10080">Cada semana</option>
						<option value="14400">Cada semana, 3 d�as</option>
						<option value="20160">Cada 2 semanas</option>
						<option value="44640">Cada mes</option>
						<option value="89280">Cada 2 meses</option>
						<option value="133920">Cada 3 meses</option>
					</select>
					
					<span>Limpia las tablas "site_errors" (Errores), "site_visits" (Visitas por IP) y "site_visits_total" (Visitas totales) de la base de datos.</span>
				</p>

				<p>
					<label>Recuperaci�n de los archivos de la aplicaci�n:</label>
					
					<select name="stopwatch_backup_app" class="btn">
						<option value="">Desactivada</option>
						<option value="1440">A diario</option>
						<option value="2880">Cada 2 d�as</option>
						<option value="5760">Cada 4 d�as</option>
						<option value="10080">Cada semana</option>
						<option value="14400">Cada semana, 3 d�as</option>
						<option value="20160">Cada 2 semanas</option>
						<option value="44640">Cada mes</option>
						<option value="89280">Cada 2 meses</option>
						<option value="133920">Cada 3 meses</option>
					</select>
					
					<span>Crea un archivo ZIP con todos los archivos de la aplicaci�n.</span>
				</p>

				<p>
					<label>Limpieza de Recuperaciones, logs y archivos temporales:</label>
					
					<select name="stopwatch_maintenance_backups" class="btn">
						<option value="">Desactivada</option>
						<option value="1440">A diario</option>
						<option value="2880">Cada 2 d�as</option>
						<option value="5760">Cada 4 d�as</option>
						<option value="10080">Cada semana</option>
						<option value="14400">Cada semana, 3 d�as</option>
						<option value="20160">Cada 2 semanas</option>
						<option value="44640">Cada mes</option>
						<option value="89280">Cada 2 meses</option>
						<option value="133920">Cada 3 meses</option>
					</select>
					
					<span>Elimina los archivos dentro de los directorios "Logs", "Backups" y "Temp" del directorio "Kernel/BitRock/".</span>
				</p>
			</div>
		</details>
		
		<details>
			<summary>Otros</summary>

			<div class="c1">			
				<p>
					<input type="checkbox" name="register" value="true" /> Seleccionar una licencia para mi aplicaci�n.
					<span>Ser�s enviado a la p�gina de Creative Commons para escojer una licencia que se adapte a tu aplicaci�n, cuando termines ser�s redireccionado a la p�gina de finalizaci�n y los datos de tu licencia se guardaran en el directorio raiz de BeatRock.</span>
				</p>
			</div>			
		</details>
		
		<p>
			<input type="submit" value="Guardar y terminar" class="ibtn" />
		</p>
	</form>
</div>
<?php require('Footer.php')?>