<?php

class imagen
{
	// manejo de imagenes
	function CrearFoto($url, $ext, $newnombre=NULL, $w=0, $h=0)
	{
		/*if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)){
			die('No funciona para imagenes externas');
		}
		*/
		$ext = strtolower($ext);
		if ($ext == "jpg" || $ext == "jpeg")
			$imagen = @imagecreatefromjpeg($url);
		else if ($ext == "png"){
			$imagen = @imagecreatefrompng($url);
			imagealphablending( $imagen, true );
		}
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
		if($ext == 'png'){
			imagealphablending( $img, false );
			imagesavealpha( $img, true );
		}
		@imagecopyresampled($img, $imagen, 0, 0, $inix, 0/*/$iniy*/, $w, $h, $dx, $dy);
		
		
		if ($newnombre == NULL){
			header("Content-type: image/jpeg");
		}
		
		if($ext == 'png'){
			$result = imagepng($img, $newnombre);
		}else{
			$result = @imagejpeg($img, $newnombre, 90);
		}
		@imagedestroy($img);
		return $result;
		//echo 'sin header';
	}
	
	function resize_to($url,$destino,$w,$h)
	{
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		//$this->CrearFoto($url, $ext, $destino, $w, $h);
		return $this->abrir($url, $destino, $w, $h);
	}
	
	function resize($url, $w, $h)
	{
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		$this->CrearFoto($url, $ext, NULL, $w, $h);
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
	
	function abrir($ruta, $folder_destino, $w, $h)
	{
		// obtener el nuevo nombre
		$w = empty($w) ? '0' : $w;
		$h = empty($h) ? '0' : $h;
	
		$ext = pathinfo($ruta, PATHINFO_EXTENSION);
		$nueva_ruta = $folder_destino . '/' . basename($ruta, '.'.$ext) . '_' . $w . 'x' . $h . '.' . $ext;
		
		//$pieza = str_replace($dir, '', $archivo);
		//$nombre = $folder . str_replace('.jpg', '', $pieza) . '_' . $w . 'x' . $h . '.jpg';
		//if(!file_exists($nombre)) CrearFoto($archivo,$nombre,$w,$h);
		if(!file_exists($nueva_ruta) || (filemtime($ruta) > filemtime($nueva_ruta))) {
			$this->CrearFoto($ruta, $ext, $nueva_ruta, $w, $h);
		}
		
		return $nueva_ruta;
	}
}

?>
