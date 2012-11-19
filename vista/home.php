<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="UTF-8" />
		<title><?=$this->meta_titulo?></title>
		<meta name="description" content="<?=$this->meta_descripcion?>" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="<?=DIR_IMG?>/favicon.ico" />
		<link rel="SHORTCUT ICON" href="<?=DIR_IMG?>/favicon.ico" />
		<link rel="alternate" type="application/rss+xml" href="" title="" />
		<link rel="stylesheet" href="<?=DIR_CSS?>/normalize.css">
		<link rel="stylesheet" href="<?=DIR_CSS?>/comun.css">
		<? $this->masEstilos(); ?>
		<?=$this->bloque('estilos')?>
        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
        <![endif]-->
	</head>

	<body>
		<div class="envoltura">
			<header class="header"><h1>Hola mundo!</h1></header>
			<div class="cuerpo">Contenido</div>
			<footer class="footer">Pie de página</footer>
		</div>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?=DIR_JS?>/vendor/jquery-1.8.2.min.js"><\/script>')</script>
		<script type="text/javascript" src="<?=DIR_JS?>/plugins.js"></script>
		<script type="text/javascript" src="<?=DIR_JS?>/global.js"></script>
		<? $this->masScripts(); ?>
		<?= $this->bloque('scripts'); ?>	
	</body>
</html>