<?php
/** 
*
* @Clase Framework "framework.php"
* @versión: 1.0.0	@modificado: 23/05/2012
* @autor: Alain - alain@gruposistemas.com
*
*/
class framework
{
	public		$meta_titulo = META_TITULO;
	public		$meta_descripcion = META_DESCRIPCION;
	private		$estilos = array();
	private		$scripts = array();
	//protected	$db;
	protected	$pagina_actual;
	protected	$idioma;
	protected	$clase;
	protected	$metodo;

	function __construct()
	{
		// obtenemos el nombre de la clase y el metodo actual invocados desde index.php
		$this->clase = $GLOBALS['clase'];
		$this->metodo = $GLOBALS['metodo'];
		
		if(class_exists('mysql'))
		{
			$this->db = new mysql();
			$this->db->Connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			// utf8 para mysql
			$this->db->Query('SET NAMES utf8', true);
			$this->db->Query('SET CHARACTER SET utf8', true);
			// localización
			$this->db->Query("SET time_zone = '-5:00'", true);
			$this->db->Query("SET lc_time_names = 'es_PE'", true);
		}
		
		if(class_exists('ClassEvaluaFormulario'))
		{
			$this->eval = new ClassEvaluaFormulario();
		}
	}

	function agregarEstilo($estilo)
	{
		array_push($this->estilos, $estilo);
	}

	function agregarScript($script)
	{
		array_push($this->scripts, $script);
	}

	function masEstilos()
	{
		foreach($this->estilos as $estilo)
		{
			echo '<style type="text/css">@import "'.$estilo.'";</style>',"\n";
		}
	}

	function masScripts()
	{
		foreach($this->scripts as $script)
		{
			echo '<script type="text/javascript" src="'.$script.'"></script>',"\n";
		}
	}

	function enlace($variables)
	{
		if(ENLACES == 'C'){
			parse_str($variables, $vector);
			return '/' . implode('/', $vector);
			
		}
		return '?'.htmlentities($variables);
	}
	
	function resumen($texto, $limite=35, $puntos='...')
	{
		eregi("(([^ ]* ?){0,$limite})(.*)", strip_tags($texto), $ars);
		return $ars[1] . $puntos;
	}
	
	function hace($time)
	{
		$lastDate = (time()) - $time;

		if (($lastDate = floor($lastDate / 60)) < 60) {
			$lastDate .= ( $lastDate == 1) ? ' min.' : ' mins.';
		} else if (($lastDate = floor($lastDate / 60)) < 24) {
			$lastDate .= ( $lastDate == 1) ? ' hora' : ' horas';
		} else {
			$lastDate = floor($lastDate / 24);
			$lastDate .= ( $lastDate == 1) ? ' d&iacute;a' : ' d&iacute;as';
		}
		return 'hace ' . $lastDate;
	}
	
	function ofuscarEmail($email)
	{
		return str_replace('@', '@<span style="display:none">null</span>', $email);
	}
	
	function sinTildes($texto)
	{
		$texto = strtr($texto, 'áéíóúüñÁÉÍÓÚÜÑ ', 'aeiouunAEIOUUN_');
		$texto = preg_replace('/[^a-zA-Z0-9_]/', '-', $texto);
		return $texto;
	}
	
	function fraseNumerada($var, $cadena_cero, $cadena_uno, $cadena_mas)
	{
		if ($var == 0) {
			$rpta = $cadena_cero;
		} else if ($var == 1) {
			$rpta = $cadena_uno;
		} else if ($var >= 2) {
			$rpta = $cadena_mas;
		} else {
			$rpta = 'error';
		}
		return str_replace('%', $var, $rpta);
	}
	
	function inicio()
	{
		if(ENLACES == 'C'){
			return '/';
		}
		return './';
	}
	
	function verPagina($p, $idioma='')
	{
		$ruta = empty($idioma) ? "vista/$p.php" : "vista/$idioma/$p.php";

		if(!file_exists("$ruta")){
			$this->pagina404("La página: $p no existe");
		}
		
		$this->pagina_actual = $p;
        $this->idioma = $idioma;
		include("$ruta");
	}
	
	function pagina404($mensaje='')
	{
		include('vista/404.php');
		exit(0);
	}

	function enlacePagina($pagina, $idioma='')
	{
		if(!empty($idioma)){
			$var_idioma = '&idioma='.$idioma;
		}
		else if(!empty($this->idioma)){
			$var_idioma = '&idioma='.$this->idioma;
		}
		else{
			$var_idioma = '';
		}
		return $this->enlace('c=pagina&m=ver&p='.$pagina.$var_idioma);
	}
	
	function actualSiEsPagina($nombre, $clase='actual', $er=false)
	{
		$nombres = explode(',', $nombre);
		if(($er && preg_match($nombre, $this->pagina_actual)) || (is_array($nombres) && in_array($this->pagina_actual, $nombres)) || $this->pagina_actual == $nombre){
			echo 'class="'.$clase.'"';
		}
	}
	
	function actualSiEsClase($nombre, $clase='actual')
	{
		if($GLOBALS['clase'] == $nombre){
			echo 'class="'.$clase.'"';
		}
	}
	
	function actualSiEsMetodo($nombre, $args=array(), $clase='actual')
	{
		$comp = array_diff($args, array_slice($_GET, 2));
		if($GLOBALS['metodo'] == $nombre && empty($comp)){
			echo 'class="'.$clase.'"';
		}
	}
	
	// crear parrafos a partir de saltos de linea
	function wpautop($pee, $br = 1)
	{
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		// Space things out a little
		$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
		$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
		$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
		if ( strpos($pee, '<object') !== false ) {
			$pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
			$pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
		}
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		// make paragraphs, including one at the end
		$pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
		$pee = '';
		foreach ( $pees as $tinkle )
			$pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
		//$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
		$pee = preg_replace( '|<p>|', "$1<p>", $pee );
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
		if ($br) {
			$pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $pee);
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
			$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
		}
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
		if (strpos($pee, '<pre') !== false)
			$pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'clean_pre', $pee );
		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
		//$pee = preg_replace('/<p>\s*?(' . get_shortcode_regex() . ')\s*<\/p>/s', '$1', $pee); // don't auto-p wrap shortcodes that stand alone
		return $pee;
	}
	
	// manejo de imagenes
	function CrearFoto($url, $ext, $newnombre=NULL, $w=0, $h=0)
	{
		if ($ext == "jpg" || $ext == "jpeg")
			$imagen = @imagecreatefromjpeg($url);
		else if ($ext == "png")
			$imagen = @imagecreatefrompng($url);
		else if ($ext == 'gif')
			$imagen = @imagecreatefromgif($url);
		else
			return false;

		if (!$imagen)
			return false;

		$ancho = @imagesx($imagen);
		$alto = @imagesy($imagen);
		if ($w == 0 && $h == 0) {
			$w = $ancho;
			$h = $alto;
		}

		$inix = 0;
		$iniy = 0;
		$dx = $ancho;
		$dy = $alto;

		if ($h == 0 && $w > 0)
			$h = $w * ($alto / $ancho);
		else if ($w == 0 && $h > 0)
			$w = $h * ($ancho / $alto);
		else {
			$tmp = $h * $ancho / $w;
			$inix = 0;
			$iniy = ($alto - $tmp) / 2;
			$dx = $ancho;
			$dy = $tmp;
			if ($tmp > $alto) {
				$tmp = $w * $alto / $h;
				$inix = ($ancho - $tmp) / 2;
				$iniy = 0;
				$dx = $tmp;
				$dy = $alto;
			}
		}

		$img = @imagecreatetruecolor($w, $h);
		@imagecopyresampled($img, $imagen, 0, 0, $inix, $iniy, $w, $h, $dx, $dy);
		if ($newnombre == NULL
			)header("Content-type: image/jpeg");
		$result = @imagejpeg($img, $newnombre, 90);
		@imagedestroy($img);
		return $result;
	}

	function AbrirFoto($archivo, $dir, $w, $h, $folder='thumbs/')
	{
		$w = empty($w) ? '0' : $w;
		$h = empty($h) ? '0' : $h;
		//$nombre = $folder.'/'.$id.'_'.$w.'x'.$h.'.jpg';
		$pieza = str_replace($dir, '', $archivo);
		$nombre = $folder . str_replace('.jpg', '', $pieza) . '_' . $w . 'x' . $h . '.jpg';
		//if(!file_exists($nombre)) CrearFoto($archivo,$nombre,$w,$h);
		if (!file_exists($nombre) /* || (filemtime($archivo) > filemtime($nombre)) */) {
			$this->CrearFoto($archivo, 'jpg', $nombre, $w, $h);
		}
		return $nombre;
	}
	
}
?>
