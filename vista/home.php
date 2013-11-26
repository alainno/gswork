<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="UTF-8" />
		<title><?php echo $this->meta_titulo; ?></title>
		<meta name="description" content="<?php echo $this->meta_descripcion; ?>" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo DIR_STATIC; ?>img/favicon.ico" />
		<link rel="SHORTCUT ICON" href="<?php echo DIR_STATIC; ?>img/favicon.ico" />
		<link rel="alternate" type="application/rss+xml" href="" title="" />
		<link rel="stylesheet" href="<?php echo DIR_STATIC; ?>css/normalize.css">
		<link rel="stylesheet" href="<?php echo DIR_STATIC; ?>css/comun.css">
		<?php $this->masEstilos(); ?>
		<script src="<?php echo DIR_STATIC; ?>js/vendor/modernizr-2.6.2.min.js"></script>
	</head>
	<body>
		<div class="envoltura">
			<header class="header">
				<div class="arriba">
					<div class="logo-container"><a href="./" class="logo"><img src="img/logo.png" alt="LOGO"></a>
					</div>
				</div>
				<nav class="menu">
					<ul>
						<li><a href="./">Inicio</a></li>
						<li><a href="listas.html">Listas</a></li>
						<li><a href="tablas.html">Tablas</a></li>
						<li><a href="formularios.html">Formularios</a></li>
						<li><a href="iconos.html">Iconos</a></li>
					</ul>
				</nav>
			</header>
			<div class="cuerpo">
				<h1>Bienvenido(a)
				</h1>
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti.
				</p>
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti.
				</p>
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti.
				</p>
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti.
				</p>
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti.
				</p>
			</div>
			<footer class="footer tac">&copy; Todos los derechos reservados, 2013
			</footer>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?php echo DIR_STATIC; ?>js/vendor/jquery-1.8.2.min.js"><\/script>')</script>
		<script type="text/javascript" src="<?php echo DIR_STATIC; ?>js/plugins.js"></script>
		<script type="text/javascript" src="<?php echo DIR_STATIC; ?>js/global.js"></script>
		<?php $this->masScripts(); ?>
	</body>
</html>