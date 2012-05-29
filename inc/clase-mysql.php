<?php
/** 
*
* @Clase MySQL "clase-mysql.php"
* @versión: 2.0.0	@modificado: 11/09/2012
* @autor: Alain - alain@gruposistemas.com
*
*/
class mysql
{
	var $m_idconnection;
	var $m_idquery;
	var $m_hostname;
	var $m_username;
	var $m_password;
	var $m_database;
	var $m_sql;
	var $m_rows;
	var $m_cols;
	var $m_register = array();
	
	function Connect($hostname = "", $username = "", $password="", $database="")
	{
		$this->m_database 	= 	$database;
		$this->m_username 	= 	$username;
		$this->m_password 	= 	$password;
		$this->m_hostname 	= 	$hostname;
		
		$this->Close();

		if(!$this->m_idconnection){
			$this->m_idconnection = @mysql_connect($this->m_hostname, $this->m_username, $this->m_password);
			
			if(!$this->m_idconnection){
				$this->PrintError("No se pudo establecer conexi�n con el servidor.");
				exit(0);
			}

			if(!@mysql_select_db($this->m_database, $this->m_idconnection)){
				$this->PrintError("No se pudo seleccionar la base de datos.");
				exit(0);
			}
		}
		return true;
	}
	
	function Query($sql = "", $show_error = false)
	{
		$this->m_sql = $sql;
		//$this->FreeResult();
		$this->m_idquery = @mysql_query($this->m_sql, $this->m_idconnection);
		
		if(!$this->m_idquery){
			if($show_error){
				$this->PrintError();
				exit(0);
			}
			return false;
		}
		//return true;
		return new resultado($this->m_idquery); // retornar resultado
	}
	
	function InsertId(){
		return mysql_insert_id($this->m_idconnection);
	}
	
	function AffectedRows(){
		return mysql_affected_rows($this->m_idconnection);
	}
	
	function FreeResult()
	{
		if($this->m_idquery)
			@mysql_free_result($this->m_idquery);
	}
	
	function FetchArray()
	{
		return $this->m_register = mysql_fetch_array($this->m_idquery);
	}
	
	function FetchRow()
	{
		return $this->m_register = mysql_fetch_row($this->m_idquery);
	}
	
	function ValorCampo($field_name)
	{
		return stripslashes($this->m_register[$field_name]);
	}
	
	function NumRows()
	{
		return $m_rows = mysql_num_rows($this->m_idquery);
	}
	
	function NumFields()
	{
		return $m_cols = mysql_num_fields($this->m_idquery);
	}
	
	function DataSeek($row_number)
	{
		mysql_data_seek($this->m_idquery, $row_number);
	}
	
	function FieldName($field_index)
	{
		return mysql_field_name($this->m_idquery, $field_index);
	}
	
	function FieldType($field_index)
	{
		return mysql_field_type($this->m_idquery, $field_index);
	}
	
	function FieldLen($field_offset)
	{
		return mysql_field_len($this->m_idquery, $field_offset);
	}
	
	function Field($index)
	{
		return array("nombre" => $this->FieldName($index), "tipo" => $this->FieldType($index), "longitud" => $this->FieldLen($index));
	}
	
	function Close()
	{
		if($this->m_idconnection)
			mysql_close($this->m_idconnection);
	}
	
	function PrintError($error="")
	{
		if(empty($error)){
			$errno = "N&ordm; ".@mysql_errno($this->m_idconnection);
			$error = @mysql_error($this->m_idconnection);
		}
		echo "<div align='center'><strong>Error de Mysql $errno: </strong><em>$error</em></div>";
	}
	
	function GetError($error="")
	{
		if(empty($error)){
			$errno = "Nro. ".@mysql_errno($this->m_idconnection);
			$error = @mysql_error($this->m_idconnection);
		}
		return "Error de Mysql $errno: $error";
	}
	
	function Result($row, $field)
	{
		return stripslashes(mysql_result($this->m_idquery, $row, $field));
	}
	
	function getAllRows()
	{
		$allRows = array();
		while($row = $this->FetchArray()){
			$allRows[] = $row;
		}
		return $allRows;
	}
	
	/* functiones HTML */
	function htmlTable($sql, $opciones = array(), $atributos = array())
	{
		$this->Query($sql, true);
		$total_opciones  = count($opciones);
		
		/// cabecera
		$attrs = '';
		foreach($atributos as $nombre => $valor){
			$attrs .= " $nombre=\"$valor\"";
		}
		$html = "<table $attrs>\n";
		$html .= "<tr>\n";
		for($field = 0; $field < $this->NumFields(); $field++){
			if(!eregi("^\(.+\)$", $this->FieldName($field))){
				$html .= "<th>".htmlentities($this->FieldName($field))."</th>\n";
			}
		}
		$html .= ($total_opciones == 0) ? "" : "<th colspan=\"".$total_opciones."\">&nbsp;</th>\n";
		$html .= "</tr>\n";
		
		for($row = 0; $row < $this->NumRows(); $row++){
			
			$record = $this->FetchArray();
			$html .= "<tr>\n";
			
			for($field = 0; $field < $this->NumFields(); $field++){
				if(!eregi("^\(.+\)$", $this->FieldName($field))){
					if(in_array($this->FieldType($field), array("int", "real")))
						$html .= "<td align=\"right\">";
					else
						$html .= "<td>";
					$html .= htmlentities($record[$this->FieldName($field)])."</td>\n";
				}
			}
			
			foreach($opciones as $opcion => $info)
			{
				$titulo = $info['titulo'];
				$clase = $info['clase'];
				
				if(!empty($info['condicion']) && empty($record[$info['condicion']]))
				{
					$enlace = "javascript:void(0);";
					$clase .= ' opacidad';
				}
				else
				{
					$enlace = ereg_replace("\[([^]]+)\]", "{\$record['\\1']}", $info['enlace']);
					eval("\$enlace = \"$enlace\";");
				}
				$html .= '<td align="center"><a href="' . $enlace . '" class="' . $clase . '" title="' . $titulo . '">' . $opcion . '</a></td>' . "\n";
			}
			
			$html .= "</tr>\n";
		}
		
		$html .= "</table>\n";
		return $html;
	}

	function HTMLGrid($sql, $opciones = array(), $atributos = array())
	{
		$this->Query($sql, true);
		$total_opciones  = count($opciones);
		
		/// cabecera
		$attrs = '';
		foreach($atributos as $nombre => $valor){
			$attrs .= " $nombre=\"$valor\"";
		}
		
		$html = '<table'.$attrs.'>';
		$html .= '<tr>';
		for($field = 0; $field < $this->NumFields(); $field++)
		{
			if(!eregi("^\(.+\)$", $this->FieldName($field)))
			{
				$html .= '<th>'.$this->FieldName($field).'</th>';
			}
			else if(isset($opciones["{$this->FieldName($field)}"]))
			{
				$html .= '<th>&nbsp;</th>';
			}
		}
		/*
		foreach($opciones as $opcion => $codigo)
		{
			$html .= '<th>'.htmlentities(is_string($opcion)?$opcion:'').'</th>';
		}
		*/
		$html .= '</tr>';
		
		// datos
		for($row = 0; $row < $this->NumRows(); $row++)
		{	
			$record = $this->FetchArray();
			$html .= '<tr'.($row%2 ? ' class="par"' : '').'>';
			
			for($field = 0; $field < $this->NumFields(); $field++)
			{
				/*
				if(!eregi("^\(.+\)$", $this->FieldName($field))){ // se muestra si no es campo oculto (...)
					if(in_array($this->FieldType($field), array("int", "real")))
						$html .= "<td align=\"right\">";
					else
						$html .= "<td>";
					$html .= htmlentities($record[$this->FieldName($field)])."</td>\n";
				}
				*/
				if(isset($opciones["{$this->FieldName($field)}"]))
				{
					$codigo = $opciones["{$this->FieldName($field)}"];
					$codigo = preg_replace("/\[([^]]+)\]/", "{\$record['$1']}", $codigo); // parsear codigo
					$codigo = addcslashes($codigo, '"\\');
					eval('$codigo = "'.$codigo.'";');
					//$html .= '<td align="center">'.utf8_encode($codigo).'</td>';
					$html .= '<td>'.($codigo).'</td>';
				}
				else if(!eregi("^\(.+\)$", $this->FieldName($field)))
				{
					if(in_array($this->FieldType($field), array("int", "real")))
						$html .= "<td align=\"right\">";
					else
						$html .= "<td>";
					$html .= ($record[$this->FieldName($field)])."</td>\n";
				}
			}
			/*
			foreach($opciones as $opcion => $codigo)
			{
				$codigo = preg_replace("/\[([^]]+)\]/", "{\$record['$1']}", $codigo); // parsear codigo
				$codigo = addcslashes($codigo, '"\\');
				eval('$codigo = "'.$codigo.'";');
				$html .= '<td align="center">'.$codigo.'</td>';
			}
			*/
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		return $html;
	}
	/*
	function htmlOptions($sql, $valor, $etiqueta, $selected=-1)
	{
		$this->Query($sql);
		$rvalor="";
		echo "\n";
		while($Array = $this->FetchArray()){
			if($rvalor == "")
				$rvalor = $Array[$valor];
			if($Array[$valor] == $selected)
				$rvalor = $Array[$valor];
			echo "<option value='".$Array[$valor]."'";
			echo ($Array[$valor] == $selected)?" selected":"";
			echo ">".$Array[$etiqueta]."</option>\n";
		}
		return ($rvalor=="")?"0":$rvalor;
	}
	 */
	function htmlOptions($sql, $valor, $etiqueta, $selected=-1)
	{
		$this->Query($sql);
		$rvalor="";
		$html= "\n";
		while($Array = $this->FetchArray()){
			if($rvalor == "")
				$rvalor = $Array[$valor];
			if($Array[$valor] == $selected)
				$rvalor = $Array[$valor];
			$html.= "<option value='".$Array[$valor]."'";
			$html.= ($Array[$valor] == $selected)?" selected":"";
			$html.= ">".$Array[$etiqueta]."</option>\n";
		}
		return $html;
	}
	
	function Pagination($current, $top, $sql, $url, $var)
	{
		$current = (empty($current)) ? 1 : $current;
		
		$this->Query($sql, true);
		
		$pages = ceil($this->NumRows() / $top);
		
		$links = array();
		
		if($current > 1){
			$href = str_replace("&$var=", "&$var=".($current - 1), $url);
			$onclick = !strstr($href, "javascript:") ? "" : "onclick=\"$href\"";
			$href = empty($onclick) ? $href : "javascript:void(0);";
			$links[] = "<a href=\"".$href."\" ".$onclick."> &laquo; Anterior</a>";
		}
		
		for($i = 1; $i <= $pages; $i++){
				$href = str_replace("&$var=", "&$var=$i", $url);
				$onclick = !strstr($href, "javascript:") ? "" : "onclick=\"$href\"";
				$href = empty($onclick) ? $href : "javascript:void(0);";
				$links[] = "<a href=\"".$href."\" ".$onclick.">$i</a>";
		}
		
		if($current < $pages){
			$href = str_replace("&$var=", "&$var=".($current + 1), $url);
			$onclick = !strstr($href, "javascript:") ? "" : "onclick=\"$href\"";
			$href = empty($onclick) ? $href : "javascript:void(0);";
			$links[] = "<a href=\"".$href."\" ".$onclick.">Siguiente &raquo;</a>";
		}
			
		$this->Query("$sql LIMIT ".(($current - 1) * $top).", $top", true);
		
		return implode(" ", $links);
	}
	
	/* comodines */
	function Insertar($tabla, $datos, $show_error=false)
	{
		foreach($datos as $campo => $valor){
			$campos[] = $campo;
			$valores[] = is_null($valor) ? 'NULL' : "'$valor'";
		}
		$sql = "INSERT INTO $tabla(" . implode(',', $campos) . ") VALUES(" . implode(",", $valores) . ")";
		if(!$show_error)
		{
			return $this->Query($sql);
		}
		$this->Query($sql, true);
	}
	
	function Actualizar($tabla, $datos, $where)
	{
		foreach($datos as $campo => $valor){
			$valor = is_null($valor) ? 'NULL': "'".$valor."'";
			$sets[] = $campo . "=" . $valor;
		}	
		$sql = "UPDATE $tabla SET " . implode(',', $sets) . " WHERE $where";
		return $this->Query($sql);
	}

	function Eliminar($tabla, $where, $limit='LIMIT 1')
	{
		return $this->Query("DELETE FROM {$tabla} WHERE $where $limit");
	}
} // fin de class mysql

class resultado
{
	var $result;
	
	function __construct($result)
	{
		$this->result = $result;
	}

	function fetch_object()
	{
		return mysql_fetch_object($this->result);
	}
	
	function num_rows()
	{
		return mysql_num_rows($this->result);
	}
	
	function num_fields()
	{
		return mysql_num_fields($this->result);
	}
	
	function field_name($field_index)
	{
		return mysql_field_name($this->result, $field_index);
	}
	
	function result($row, $field)
	{
		return stripslashes(mysql_result($this->result, $row, $field));
	}
	
	function data_seek($row_number)
	{
		mysql_data_seek($this->result, $row_number);
	}
	
	function fetch_array()
	{
		return mysql_fetch_array($this->result);
	}
	
	function field_type($field_index)
	{
		return mysql_field_type($this->result, $field_index);
	}
	
	function crearTabla($opciones = array(), $atributos = array())
	{
		//$this->Query($sql, true);
		$total_opciones  = count($opciones);
		
		/// cabecera
		$attrs = '';
		foreach($atributos as $nombre => $valor){
			$attrs .= " $nombre=\"$valor\"";
		}
		
		$html = '<table'.$attrs.'>';
		$html .= '<tr>';
		for($field = 0; $field < $this->num_fields(); $field++)
		{
			if(!eregi("^\(.+\)$", $this->field_name($field)))// si no tiene parentesis mostrar
			{
				$html .= '<th>'.$this->field_name($field).'</th>';
			}
			else if(isset($opciones["{$this->field_name($field)}"]))// si es una opcion no mostrar titulo
			{
				$html .= '<th>&nbsp;</th>';
			}
		}

		$html .= '</tr>';
		
		// datos
		for($row = 0; $row < $this->num_rows(); $row++)
		{	
			$record = $this->fetch_array();
			$html .= '<tr>';
			
			for($field = 0; $field < $this->num_fields(); $field++)
			{
				if(isset($opciones["{$this->field_name($field)}"])) // si es una opcion parsear
				{
					$codigo = $opciones["{$this->field_name($field)}"];
					$codigo = preg_replace("/\[([^]]+)\]/", "{\$record['$1']}", $codigo); // parsear codigo
					$codigo = addcslashes($codigo, '"\\');
					eval('$codigo = "'.$codigo.'";');
					//$html .= '<td align="center">'.utf8_encode($codigo).'</td>';
					$html .= '<td>'.$codigo.'</td>';
				}
				else if(!eregi("^\(.+\)$", $this->field_name($field))) // si no esta oculto mostrar valor
				{
					if(in_array($this->field_type($field), array("int", "real"))){
						$html .= "<td align=\"right\">";
					}else{
						$html .= "<td>";
					}
					$html .= $record[$this->field_name($field)]."</td>\n";
				}
			}
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		return $html;
	}
}

?>