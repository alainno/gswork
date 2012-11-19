<?php

class pagina extends GSWork
{
	function __construct()
	{
		parent::__construct();
	}

	function ver($pagina = 'home', $idioma='')
	{
		$ruta = empty($idioma) ? "vista/$pagina.php" : "vista/$idioma/$pagina.php";

		if (!file_exists("$ruta")) {
			$this->pagina404("La página: $pagina no existe");
		}

		$this->pagina_actual = $pagina;
		$this->idioma = $idioma;

		include("$ruta");
	}
	
	function home()
	{
		include 'vista/home.php';
	}

}

?>
