<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8" />
		<title><?=$this->meta_titulo?></title>
		<meta name="description" content="<?=$this->meta_descripcion?>" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="<?=DIR_IMG?>/favicon.ico" />
		<link rel="SHORTCUT ICON" href="<?=DIR_IMG?>/favicon.ico" />
		<link rel="alternate" type="application/rss+xml" href="" title="" />
		<style type="text/css">@import "<?=DIR_CSS?>/comun.css";</style>
		<? $this->masEstilos(); ?>
		<!--[if lt IE 9]>
		<style type="text/css">@import "<?=DIR_CSS?>/ie.css";</style>
		<script type="text/javascript">
		   document.createElement("nav"); 
		   document.createElement("header"); 
		   document.createElement("footer"); 
		   document.createElement("section"); 
		   document.createElement("article"); 
		   document.createElement("aside"); 
		   document.createElement("hgroup"); 
		</script>
		<![endif]-->
		<script type="text/javascript" src="<?=DIR_JS?>/jquery.js"></script>
		<script type="text/javascript" src="<?=DIR_JS?>/plugins.js"></script>
		<script type="text/javascript" src="<?=DIR_JS?>/global.js"></script>
		<? $this->masScripts(); ?>
	</head>

	<body>
		<div id="envoltura">
			<header id="cabecera">
				<h1>Hola Mundo!</h1>
			</header>
			<div class="cuerpo">
				<?
					
				?>
				<blockquote class="cita">hola mundo</blockquote>
				<ul class="lista">
					<li>item 1</li>
					<li>item 2</li>
					<li>item 3</li>
				</ul>
				<ol class="lista">
					<li>item 1</li>
					<li>item 2</li>
					<li>item 3</li>
				</ol>
				<table class="tabla">
					<tr>
						<th>col 1</th>
						<th>col 2</th>
						<th>col 3</th>
					</tr>
					<tr>
						<td>1,1</td>
						<td>1,2</td>
						<td>1,3</td>
					</tr>
					<tr>
						<td>2,1</td>
						<td>2,2</td>
						<td>2,3</td>
					</tr>
				</table>
				<form>
					<label>Nombre</label>
					<input type="text" />
					<span>Recomendacion</span>
				</form>
			</div>
			<footer id="pie"></footer>
		</div>
	</body>
</html>