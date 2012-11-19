<?php
/** 
*
* @Clase GSWork "class.gswork.php"
* @versión: 1.0.0	@modificado: 23/05/2012
* @autor: Alain - alain@gruposistemas.com
*
*/

class GSWork
{
	public		$meta_titulo = META_TITULO;
	public		$meta_descripcion = META_DESCRIPCION;
	private		$bloques = array();
	private		$bloques_nombres = array();
	private		$estilos = array();
	private		$scripts = array();
	/**
	* @var mysql
	**/
	protected	$db;
	/**
	* @var ClassEvaluaFormulario
	**/
	protected	$eval;
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

	function enlace($variables,$encode=true)
	{
		if(ENLACES == 'C'){
			parse_str($variables, $vector);
			return '/' . implode('/', $vector);
			
		}
		return '?'.(($encode)?htmlentities($variables):$variables);
	}
	
	function href($variables, $codificar=true)
	{
		return $this->enlace('c=vista&'.$variables, $codificar);
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
	
	function pagina404($mensaje='',$redireccion='')
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
	
	function actualSiEsMetodo($nombre, $args=array(), $clase='actual',$clase_default='')
	{
		$comp = array_diff($args, array_slice($_GET, 2));
		if($GLOBALS['metodo'] == $nombre && empty($comp)){
			echo 'class="'.$clase.'"';
		}
		else{
			echo 'class="'.$clase_default.'"';
	}
	}
	
	function actualSiEs($er, $class_si='actual', $class_no='')
	{
		
		if(!empty($_GET)){
			$str = array();
			foreach($_GET as $key=>$val){
				$str[] = "$key=$val";
			}
			$str = implode('&',$str);
		}
		else{
			$str = '';
		}
		
//		echo "ER: $er<br />";
//		echo "STR: $str<br />";
//		print_r($_GET);
			
		if(!preg_match($er,$str)){
			echo empty($class_no) ? '':'class="'.$class_no.'"';
			return false;
		}
		else{
			echo 'class="'.$class_si.'"';
			return true;			
		}
		
		
		//$querys = explode(',',$querys);
		
		
		
//		foreach($querys as $query){
//			$vars = array();
//			parse_str($query, $vars);
//			$diff = array_diff_assoc($vars, $_GET);
//			$diff = array_intersect_assoc($vars, $_GET);
//			
//			echo 'QUERY: '.$query.'<br/>';
//			print_r($vars);
//			print_r($_GET);
//			print_r($diff);
//			echo '<br />';
//			
//			if(!empty($diff)){
//				echo 'class="'.$class_si.'"';
//				return true;
//			}
//		}
//		
//		echo empty($class_no) ? '':'class="'.$class_no.'"';
//		return false;
		
		
//		$vars = array();
//		parse_str($url_query, $vars);
//		
//		$diff = array_diff($vars, array_slice($_GET,2));
//		
//		print_r($_GET);
//		print_r($vars);
//		print_r($diff);
//		die();
//		
//		if($GLOBALS['clase']!=$vars['c'] || $GLOBALS['metodo']!=$vars['m'] || empty($diff)){
//			echo 'class="'.$class_no.'"';
//		}
//		else{
//			print_r($diff);
//			die();
//			echo 'class="'.$class_si.'"';
//		}
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
	
	function abrirBloque($nombre)
	{
		array_push($this->bloques_nombres,"$nombre");
		ob_start();
}
	
	function cerrarBloque()
	{
		$nombre = array_pop($this->bloques_nombres);
		$this->bloques["$nombre"] = ob_get_clean();
	}
	
	function bloque($nombre)
	{
		echo $this->bloques["$nombre"];
	}
	
	function die_json_encode($array_vars)
	{
		$json = new stdClass();
		foreach($array_vars as $key => $value){
			$json->{"$key"} = $value;
		}
		die(json_encode($json));
	}
	
//	protected function cargarModelo($nombre){
//		require_once 'modelos/$nombre.php';
//		return new $nombre();
//	}
	
	function nav_pag($pre_enlace,$nro_paginas,$pag_actual)
	{	
		$html = '
		<input type="button" value=" &larr; " '.($pag_actual>1?'':'disabled').' onclick="document.location = \''.$pre_enlace.($pag_actual-1).'\'" class="boton left" />
        <select onchange="document.location = \''.$pre_enlace.'\'+this.value" class="left ml5">';
		for($i=0;$i<$nro_paginas;$i++){
			$html .= '<option value="'.($i+1).'" '.($pag_actual==($i+1)?'selected':'').'>&nbsp;&nbsp;&nbsp;&nbsp;'.($i+1).' / '.$nro_paginas.'&nbsp;&nbsp;</option>';
		}
		$html .= '
		</select>
		<input type="button" value=" &rarr; " '.($nro_paginas>$pag_actual?'':'disabled').' onclick="document.location = \''.$pre_enlace.($pag_actual+1).'\'" class="boton left ml5" />';	
		
		return $html;
	}

	function input_imagen($atributos=array())
	{
		extract($atributos);
		$w = empty($w) ? 75 : $w;
		$h = empty($h) ? 75 : $h;
		?>
		<div id="<?=$id?>-container" class="media" style="width: <?=$w?>px;height: <?=$h?>px"><?=$html?></div>
		<span class="input-file-falso<?=empty($value)?'':' oculto'?>"><span class="ico-left ico-folder"></span>Seleccionar...</span>
		<input type="file" name="<?=$id?>" id="<?=$id?>" rel="<?=$rel?>" class="input-file-oculto" />
		<a href="#" id="<?=$id?>-borrar" class="borrar-media<?=empty($value)?' oculto':''?>"><span class="ico-left ico-borrar"></span>Eliminar...</a>
		<input type="hidden" name="<?=$id?>_tmp" id="<?=$id?>_tmp" />
		<input type="hidden" name="<?=$id?>_actual" id="<?=$id?>_actual" value="<?=$value?>" />
		<input type="hidden" name="id_input_file" id="id_input_file" value="<?=$id?>" />
		<iframe id="frame_upload" name="frame_upload" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<input type="hidden" name="w" value="<?=$w?>" />
		<input type="hidden" name="h" value="<?=$h?>" />
		<?
	}
	
	
	function hexentities($str)
	{
		$return = '';
		for ($i = 0; $i < strlen($str); $i++) {
			$return .= '&#x' . bin2hex(substr($str, $i, 1)) . ';';
		}
		return $return;
	}
	
	function email($nombres, $email, $to, $subject, $body)
	{
		$headers = 'MIME-Version: 1.0'.PHP_EOL;
		$headers .= 'Content-type: text/html; charset=UTF-8'.PHP_EOL;
		$headers .= "From: $nombres <$email>".PHP_EOL;
		$headers .= "Reply-To: $email".PHP_EOL;
		$headers .= "X-Mailer: PHP/" . phpversion();
		if(!@mail($to, $subject, $body, $headers)){
			return false;
		}
		return true;
	}
	
//	function &loadlib($nombre){
//		require 'inc/class.'.$nombre.'.php';
//		$obj = new $nombre();
//		return $obj;
//	}
	
//	function &load($nombre){
//		require 'inc/class.'.$nombre.'.php';
//		//return call_user_func(array($nombre, 'getInstance'));
//		$obj = new $nombre();
//		return $obj;
//	}
//	
//	function __autoload($classname){
//		require 'inc/class.'.$classname.'.php';
//	}
	
	
} // fin de GSWork


// cargar clases sin include
function __autoload($classname){
	$path1 = "inc/class.$classname.php";
	$path2 = "modelo/$classname.php";
	if(file_exists($path1)){
		require $path1;
	}elseif(file_exists($path2)){
		require $path2;
	}
}

?>
