<?
	
	require('config.php');
	
	$clase = $_REQUEST['c'];
	$metodo = $_REQUEST['m'];
	$clase = empty($clase) ? DEF_CLASE : $clase;
	$metodo = empty($metodo) ? DEF_METODO : $metodo;
		
	$ruta = 'controlador/' . $clase . '.php';
	if(!file_exists($ruta)){
		GSWork::pagina404('El archivo "'.$ruta.'" no existe');
	}

	require_once($ruta);
	if(!class_exists($clase)){
		GSWork::pagina404('La clase "'.$clase.'" no existe');
	}

	$objeto = new $clase();
	if(!method_exists($objeto, $metodo)){
		GSWork::pagina404('El método "'.$metodo.'" no existe');
	}

	call_user_func_array(array(&$objeto, $metodo), array_slice($_GET, 2));

?>