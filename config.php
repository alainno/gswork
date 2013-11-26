<?php

require('inc/class.mysqli.php');
require('inc/class.evaluaFormulario.php');
require('inc/class.gswork.php');

// localizacion peru
setlocale(LC_ALL, 'es_PE');
date_default_timezone_set('America/Lima');

// controlador por defecto
define('DEF_CLASE', 'pagina');
define('DEF_METODO', 'home');

// metatags por defecto
define('META_TITULO', 'GSWork v1.0');
define('META_DESCRIPCION', '');

// Tipo de URLs: V = Variables, C = Carpetas
define('ENLACES', 'V'); 

// Carpetas por defecto
if(ENLACES == 'V'){
	define('DIR_CSS', 'css');
	define('DIR_JS', 'js');
	define('DIR_IMG', 'img');
	define('DIR_STATIC', '');
}
elseif(ENLACES == 'C'){
	define('DIR_CSS', '/css');
	define('DIR_JS', '/js');
	define('DIR_IMG', '/img');
	define('DIR_STATIC', '/');
}

// Datos de acceso a la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '1234');
define('DB_NAME', 'test');

?>
