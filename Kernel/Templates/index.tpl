<!--
	Contenido de ejemplo
-->
<div class="content">
	<h2>
		<!--
			Ejemplo de m�dulos propios.
			Para ver el c�digo visita /Kernel/Modules/Site/MySite.php
			(tambi�n para ver porque no se traduce automaticamente...)
		-->
		<?=MySite::HelloWorldLang()?>
	</h2>

	<div class="c1">
		<p>
			%regards%
		</p>
		
		<p>
			%remember.path1% %PATH% %remember.path2% <b><?=PATH?></b>
		</p>

		<p>
			%watch.vars1% <b>"<?=$hello?>"</b> %watch.vars2% <b>"$$hello$$"</b>
		</p>

		<p>
			%have.felt% <b>#TEMPLATES#index.tpl</b>
		</p>

		<p>
			%view.examples% <a href="%PATH%/demos/">%examples%</a>.
		</p>

		<p>
			<!--
				Nueva forma de usar constantes.
			-->
			#ROOT#<br />
		</p>
	</div>

	<div class="c2">
		<!--
			Sistema de traducci�n en tiempo real, para ver m�s detalles
			vaya a /resources/beatrock/js/functions.page.js
		-->
		<p>
			<a data-lng="es">Espa�ol</a>
			<a data-lng="en">English</a>
			<a data-lng="pt">Portugu�s</a>
		</p>

		<p>
			%language.live%
		</p>
	</div>
</div>

<!--
	C�digo CSS de la p�gina actual [SOLO DEMOSTRACI�N] (Puede borrarlo)
	Para ver el c�digo CSS de todas las p�ginas, visita /resources/system/style.css
-->
<style>
/* Agregando fuente "Open Sans" de http://google.com/webfonts */
@import url(http://fonts.googleapis.com/css?family=Open+Sans:300,400,700,600);

/* Predeterminado */

a
{
	color: blue;
}

/* Cabecera */
header
{
	background: #F5F5F5;
	border-bottom: 1px solid #D8D8D8;
	padding: 10px 0;
}

/* Contenido */
h1
{
	color: black;
	font-family: "Open Sans", "Segoe UI Light", Tahoma, Arial;
	font-size: 35px;
	font-weight: 300;
	line-height: 45px;
}

.content h2
{
	font-family: "Open Sans", Tahoma, Arial;
	font-size: 25px;
	font-weight: 400;
}

.content a[data-lng]
{
	border-bottom: 1px solid #F2F2F2;
	display: block;
	padding: 8px 0;
	text-align: center;
}

.content a[data-lng]:last-child
{
	border-bottom: 0;
}

/* Contenido - Columnas */
.content .c1
{
	float: left;
	width: 660px;
}

.content .c2
{
	float: right;
	width: 300px;
}

/* Pie de p�gina */
footer
{
	border-top: 1px solid #D8D8D8;
	color: gray;
	font-size: 11px;
	margin-top: 10px;
	padding: 10px 0;
}
</style>