<?

require('inc/clase-mysql.php');
require('inc/class.evaluaFormulario.php');
require('inc/framework.php');

// localizacion peru
setlocale(LC_ALL, 'es_PE');

// controlador por defecto
define('DEF_CLASE', 'pagina');
define('DEF_METODO', 'ver');

// metatags por defecto
define('META_TITULO', 'Framework v1.0');
define('META_DESCRIPCION', '');

// Tipo de URLs: V = Variables, C = Carpetas
define('ENLACES', 'V'); 

// Carpetas por defecto
define('DIR_CSS', 'css');
define('DIR_JS', 'js');
define('DIR_IMG', 'img');
define('DIR_MULTIMEDIA', 'multimedia');

// Datos de acceso a la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '1234');
define('DB_NAME', 'test');


?>
