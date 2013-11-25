<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Error 404</title>
		<?php if(!empty($redireccion)): ?>
		<meta http-equiv="refresh" content="2;url=<?=$redireccion?>">
		<?php endif; ?>
		<style type="text/css">
			h1,p{margin: 0;padding: 0;}
			body{background: #a1cc4d;font-family: Tahoma, Geneva, sans-serif;}
			h1{margin-bottom: 10px;}
			#caja{background: #fff url(<?php echo DIR_STATIC; ?>img/error-404.jpg) no-repeat 20px 30px;border:10px solid #89ae42;height: 140px;left: 50%;margin-left: -380px;margin-top: -100px;padding: 30px 30px 30px 170px;position: absolute;top: 50%;width: 500px;}
		</style>
    </head>
    <body>
		<div id="caja">
			<h1>&iexcl;Ups! página no encontrada</h1>
			<p><?php echo $mensaje; ?></p>
		</div>
    </body>
</html>
