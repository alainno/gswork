<?php
class ClassEvaluaFormulario {

    var $ListaMensajes;
	var $Contador=0;
	
	function NumeroMensajes(){
		return $this->Contador;
	}
    function __construct() {
		$this->Contador = 0;
        $this->resetErrorList();
    }

    function resetErrorList() {
		$this->Contador = 0;
        $this->_errorList = array();
    }

    function EsVacio($valor) {
        return (!isset($valor) || trim($valor) == '') ? true : false;
    }

    function EsCadena($valor) {
        return is_string($valor);
    }

    function EsNumero($valor) {
        return is_numeric($valor);
    }
	
	function EsNumeroPositivo($valor){
		return ($this->EsNumero($valor)&&$valor>=0);
	}
	
    function EsEntero($valor) {
       //return (intval($valor) == $valor) ? true : false;
	   return $this->is_int_val($valor);
    }
	
	function EsEnteroPositivo($valor){
		return ($this->EsEntero($valor)&&$valor>=0);
	}

	function EsUsuario($valor){
		 return preg_match('/^[a-zA-Z0-9_]+$/', $valor);
	}
	
    function EsAlfabeto($valor) {
        return preg_match('/^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$/', $valor);
    }
	function EsTexto($valor){
		return preg_match('/^[a-zA-ZáéíóúñÁÉÍÓÚÑ0-9\. ]+$/', $valor);
	}

    function EnRango($valor, $min, $max) {
        return (is_numeric($valor) && $valor >= $min && $valor <= $max) ? true : false;
    }

    function EsEmail($valor) {
        return eregi('^([a-z0-9])+([\.a-z0-9_-])*@([a-z0-9_-])+(\.[a-z0-9_-]+)*\.([a-z]{2,6})$', $valor);
    }
	
	function EsWeb($valor) {
		return eregi("https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?", $valor);
	}

    function EnArray($array, $valor) {
        return in_array($valor, $array);
    }

    function AgregarMensaje($campo, $mensaje) {
		$this->Contador++;
        $this->ListaMensajes[] = array('campo' => $campo, 'mensaje' => $mensaje);
    }
	
	/* by alain */
    function AgregarMensajeSimple($mensaje) {
		$this->Contador++;
        $this->ListaMensajes[] = array('mensaje' => $mensaje);
    }

    function EsError() {
        return (sizeof($this->ListaMensajes) > 0) ? true : false;
    }

    function __destruct() {
        unset($this->_errorList);
    }
	
	function EsFecha($fecha)
	{
		if($this->EsVacio($fecha))return false;
		  ereg('^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})( ([0-9]{1,2}):([0-9]{1,2}))?$',$fecha,$arr);
		  $h = $arr[5];
		  $i = $arr[6];
		  $d = $arr[3];
		  $m = $arr[2];
		  $y = $arr[1];
		  if(!($year = @date("Y", mktime ($h,$i,$s,$m,$d,$y)))) return false;
		  if ($y != $year)	return false;
		  else	return true;
	}
	
	function MostrarMensajes(){
		if($this->EsError()){
			header("mensaje: mensaje");
			echo '<ol>';
			foreach ($this->ListaMensajes as $listamensajes) {
				echo '<li><span style="text-decoration:underline">'.$listamensajes['campo'].'</span>: '.$listamensajes['mensaje'];
	  		}
			echo '</ol>';
			exit();
		}
	}
	
	function ImprimirMensajes(){
		if($this->EsError()){
			echo '<ol>';
			foreach ($this->ListaMensajes as $listamensajes) {
				echo '<li><span style="text-decoration:underline">'.$listamensajes['campo'].'</span>: '.$listamensajes['mensaje'];
	  		}
			echo '</ol>';
			exit();
		}
	}
	function MensajesSimple(){
		$cont = "";
		if($this->EsError()){
			$i=1;
			foreach ($this->ListaMensajes as $listamensajes) {
				$cont .= "$i.- ".utf8_encode($listamensajes['campo'])." : ".utf8_encode($listamensajes['mensaje'])."\n";
				$i++;
	  		}
		}
		return $cont;
	}

	private function is_int_val($data)
	{
		if(is_int($data) === true){
			return true;
		} else if(is_string($data) === true && is_numeric($data) === true){
			return (strpos($data, '.') === false);
		}
		return false;
	}
	
	function lanzarError($mensaje, $control='')
	{
		$json = new stdClass();
		$json->error = true;
		$json->mensaje = $mensaje;
		if(!empty($control)){
			$json->control = $control;
}
		die(json_encode($json));
	}
	
	function lanzarExito($mensaje, $redireccion='')
	{
		$json = new stdClass();
		$json->error = false;
		$json->mensaje = $mensaje;
		if(!empty($redireccion)){
			$json->redireccion = $redireccion;
		}
		die(json_encode($json));
	}
}
?>