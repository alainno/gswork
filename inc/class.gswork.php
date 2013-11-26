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
	
	/** Agregar css a la página.
	 * @param $estilo La URL del css.
	 */
	function agregarEstilo($estilo)
	{
		array_push($this->estilos, $estilo);
	}

	/** Agregar archivo de javascript a la página.
	 * @param $script La URL del archivo js.
	 */
	function agregarScript($script)
	{
		array_push($this->scripts, $script);
	}

	/** Imprime los tags de los css agregados.
	 */
	function masEstilos()
	{
		foreach($this->estilos as $estilo)
		{
			echo '<style type="text/css">@import "'.$estilo.'";</style>',"\n";
		}
	}

	/** Imprime los tags de los js agregados.
	 */
	function masScripts()
	{
		foreach($this->scripts as $script)
		{
			echo '<script type="text/javascript" src="'.$script.'"></script>',"\n";
		}
	}

	/** Crear enlace interno.
	 * @param $variables cadena de variables y valores a=b&c=d&...
	 * @param $encode codificar el enlace con caracteres html.
	 * @return url interna.
	 */
	function enlace($variables,$encode=true)
	{
		if(ENLACES == 'C'){
			parse_str($variables, $vector);
			return '/' . implode('/', $vector);
			
		}
		return '?'.(($encode)?htmlentities($variables):$variables);
	}
	
	/** Crear enlace abreviado para la clase vista.
	 * @param $variables cadena de variables y valores.
	 * @param $codificar codificar con caracteres html.
	 * @return url interna.
	 */
	function href($variables, $codificar=true)
	{
		return $this->enlace('c=vista&'.$variables, $codificar);
	}
	
	/** Resumir texto.
	 * @param $texto el texto a resumir.
	 * @param $limite cantidad de palabras a mostrar.
	 * @param $puntos agregar puntos y otra cadena al final.
	 * @return texto resumido.
	 */
	function resumen($texto, $limite=35, $puntos='...')
	{
		//eregi("(([^ ]* ?){0,$limite})(.*)", strip_tags($texto), $ars);
		preg_match("/(([^ ]* ?){0,$limite})(.*)/i", strip_tags($texto), $ars);
		return $ars[1] . $puntos;
	}
	
	/** Texto amigable de una fecha anterior.
	 * @param $time fecha en formato unix.
	 * @return texto amigable.
	 */
	function hace($time)
	{
		$delta = time() - $time;
		if($delta < 24 * HOUR){ return "Hoy"; }
		if($delta < 48 * HOUR) { return "Ayer"; }
		if($delta < 30 * DAY) { return "Hace " . floor($delta / DAY) . " dias"; }
		if($delta < 12 * MONTH)
		{
			$months = floor($delta / DAY / 30);
			return $months <= 1 ? "El mes pasado" : "hace " . $months . " meses";
		}
		else
		{
			$years = floor($delta / DAY / 365);
			return $years <= 1 ? "El a&ntilde;o pasado" : "hace " . $years . " a&ntilde;os";
		}
	}
	
	/** Ofuscar dirección de email.
	 * @param $email dirección de email.
	 * @param $hex ofuscar con caracteres hexadecimales.
	 */
	function ofuscarEmail($email, $hex=true)
	{
		if($hex){
			return $this->hexentities($email);
		}
		return str_replace('@', '@<span style="display:none">null</span>', $email);
	}
	
	/** Quitar tíldes y espacios a una cadena de texto, útil para urls.
	 * @param $texto texto con tíldes y espacios.
	 * @return texto sin tíldes ni espacios.
	 */
	function sinTildes($texto)
	{
		$texto = strtr($texto, 'áéíóúüñÁÉÍÓÚÜÑ ', 'aeiouunAEIOUUN_');
		$texto = preg_replace('/[^a-zA-Z0-9_]/', '-', $texto);
		return $texto;
	}
	
	/** frases para cantidades, útil para comentarios.
	 * @param $var cantidad.
	 * @param $cadena_cero frase si % es 0.
	 * @param $cadena_uno frase si % es uno.
	 * @param $cadena_mas frase si % es más.
	 * @return frase
	 */
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
	
	/** URL de la página de inicio.
	 */
	function inicio()
	{
		if(ENLACES == 'C'){
			return '/';
		}
		return './';
	}
	
	/** incluir página de la carpeta vista.
	 * @param $p nombre de la página.
	 * @param $idioma nombre de la carpeta de idioma.
	 */
	function verPagina($p, $idioma='')
	{
		$ruta = empty($idioma) ? "vista/$p.php" : "vista/$idioma/$p.php";

		if(!file_exists("$ruta")){
			$this->pagina404("La página: $p no existe.");
		}
		
		$this->pagina_actual = $p;
        $this->idioma = $idioma;
		include("$ruta");
	}
	
	/** Mostrar página de error 404.
	 * @param $mensaje mensaje de error.
	 * @param $redireccion url de retorno.
	 * @param $tiempo tiempo que se muestra en segundos.
	 */
	function pagina404($mensaje='',$redireccion='',$tiempo=2)
	{
		include('vista/404.php');
		exit(0);
	}

	/** Crear enlace abreviado a la clase pagina y método ver.
	 * 
	 * @param $pagina nombre de la página.
	 * @param $idioma id del idioma
	 * @return enlace
	 */
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
	
	// abrir bloque de código html para plantillas
	function abrirBloque($nombre)
	{
		array_push($this->bloques_nombres,"$nombre");
		ob_start();
	}
	
	// cerrar bloque de código html para plantillas
	function cerrarBloque()
	{
		$nombre = array_pop($this->bloques_nombres);
		$this->bloques["$nombre"] = ob_get_clean();
	}
	
	// mostrar bloque
	function bloque($nombre)
	{
		echo $this->bloques["$nombre"];
	}
	
	// crear cadena json a partir de un array y terminar script
	function die_json_encode($array_vars)
	{
		$json = new stdClass();
		foreach($array_vars as $key => $value){
			$json->{"$key"} = $value;
		}
		die(json_encode($json));
	}
	
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
	
	// convertir cadena a caracteres hexadecimales
	function hexentities($str)
	{
		$return = '';
		for ($i = 0; $i < strlen($str); $i++) {
			$return .= '&#x' . bin2hex(substr($str, $i, 1)) . ';';
		}
		return $return;
	}
	
	// enviar email
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
