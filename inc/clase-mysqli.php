<?php

/**
 * @Clase MySQLi "class.mysqli.php"
 * @versión: 1.0.0
 */
class mysql {

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
	var $iserror = false;

	function Connect($hostname = "", $username = "", $password = "", $database = "") {
		$this->m_database = $database;
		$this->m_username = $username;
		$this->m_password = $password;
		$this->m_hostname = $hostname;

		$this->Close();

		if (!$this->m_idconnection) {
			$this->m_idconnection = mysqli_connect($this->m_hostname, $this->m_username, $this->m_password);

			if (!$this->m_idconnection) {
				$this->PrintError("No se pudo establecer conexión con el servidor.");
				exit(0);
			}

			if (!@mysqli_select_db($this->m_idconnection, $this->m_database)) {
				$this->PrintError("No se pudo seleccionar la base de datos.");
				exit(0);
			}
		}
		return true;
	}

	function IsError() {
		return $this->iserror;
	}

	function Query($sql = "", $show_error = false) {
		$this->m_sql = $sql;
		//$this->FreeResult();
		//inicio log {captura el tipo de consulta}
		/* $tok = strtolower(strtok($sql, ' '));
		  $id = (isset($_SESSION["SESS_USUA_ID"]) ? $_SESSION["SESS_USUA_ID"] : '0');
		  $usuario = (isset($_SESSION["SESS_USUA_NOMBRES"]) ? $_SESSION["SESS_USUA_NOMBRES"] : '-NO-USER-');
		  //$sql=str_replace('"','\'',$sql);
		  $sql=mysql_escape_string($sql);
		  if ("select"==$tok) {
		  $log_query="insert into log values(NULL,'$id','$usuario','select',\"$sql\",NOW())";
		  } else if ("insert"==$tok) {
		  $log_query="insert into log values(NULL,'$id','$usuario','insert',\"$sql\",NOW())";
		  } else if ("delete"==$tok) {
		  $log_query="insert into log values(NULL,'$id','$usuario','delete',\"$sql\",NOW())";
		  } else if ("update"==$tok) {
		  $log_query="insert into log values(NULL,'$id','$usuario','update',\"$sql\",NOW())";
		  }
		  if(isset($log_query))
		  @mysql_query($log_query, $this->m_idconnection); */
		/* if(isset($id_query) && !$id_query){
		  $this->PrintError("fallo de log=((".$log_query."))"); exit(0);
		  } */
		//fin log
		$this->m_idquery = @mysqli_query($this->m_idconnection, $this->m_sql);

		$this->iserror = false;

		if (!$this->m_idquery) {
			$this->iserror = true;
			if ($show_error) {
				$this->PrintError();
				exit(0);
			}
			return false;
		}
		//return true;
		return new resultado($this->m_idquery); // retornar resultado
	}

	function InsertId() {
		return mysqli_insert_id($this->m_idconnection);
	}

	function AffectedRows() {
		return mysqli_affected_rows($this->m_idconnection);
	}

	function FreeResult() {
		if ($this->m_idquery)
			@mysqli_free_result($this->m_idquery);
	}

	function FetchArray($both = true) {
		if ($both) {
			return $this->m_register = mysqli_fetch_array($this->m_idquery, MYSQLI_BOTH);
		} else {
			return $this->m_register = mysqli_fetch_array($this->m_idquery, MYSQLI_ASSOC);
		}
	}

	function FetchRow() {
		return $this->m_register = mysqli_fetch_row($this->m_idquery);
	}

	function ValorCampo($field_name) {
		return stripslashes($this->m_register[$field_name]);
	}

	function NumRows() {
		return $m_rows = mysqli_num_rows($this->m_idquery);
	}

	function NumFields() {
		return $m_cols = mysqli_num_fields($this->m_idquery);
	}

	function DataSeek($row_number) {
		mysqli_data_seek($this->m_idquery, $row_number);
	}

	function FieldName($field_index) {
		return mysqli_fetch_field_direct($this->m_idquery, $field_index)->name;
	}

	function FieldType($field_index) {
		return mysqli_fetch_field_direct($this->m_idquery, $field_index)->type;
	}

	function FieldLen($field_offset) {
		return mysqli_fetch_field_direct($this->m_idquery, $field_offset)->length;
	}

	function Field($index) {
		return array("nombre" => $this->FieldName($index), "tipo" => $this->FieldType($index), "longitud" => $this->FieldLen($index));
	}

	function Close() {
		if ($this->m_idconnection)
			mysqli_close($this->m_idconnection);
	}

	function PrintError($error = "") {
		if (empty($error)) {
			$errno = "N&ordm; " . @mysqli_errno($this->m_idconnection);
			$error = @mysqli_error($this->m_idconnection);
		}
		echo "<div align='center'><strong>Error de Mysql $errno: </strong><em>$error</em></div>";
	}

	function GetError($error = "") {
		if (empty($error)) {
			$errno = "Nro. " . @mysqli_errno($this->m_idconnection);
			$error = @mysqli_error($this->m_idconnection);
		}
		return "Error de Mysql $errno: $error";
	}

	function Result($row, $field) {
		mysqli_data_seek($this->result, $row);
		$ceva = mysqli_fetch_array($this->result, MYSQLI_BOTH);
		return stripslashes($ceva[$field]);
	}
	
	function real_escape_string($string_to_escape)
	{
		return mysqli_real_escape_string($this->m_idconnection, $string_to_escape);
	}

	function getAllRows($both = true) {
		$allRows = array();
		while ($row = $this->FetchArray($both)) {
			$allRows[] = $row;
		}
		return $allRows;
	}

	function field_dataw() {
		$retval = array();
		while ($field = mysqli_fetch_field($this->m_idquery)) {
			$F = new stdClass();
			$F->name = $field->name;
			$F->orgname = $field->orgname;
			$F->table = $field->table;
			$F->orgtable = $field->orgtable;
			$F->type = $field->type;
			$F->default = $field->def;
			$F->max_length = $field->max_length;
			$F->isnull = $field->flags & 1 ? FALSE : TRUE;
			$F->primary_key = ($field->flags & MYSQLI_PRI_KEY_FLAG) ? 1 : 0;
			$retval[] = $F;
		}
		return $retval;
	}

	function result_array($sql, $default = array()) {
		$this->Query($sql, true);
		if ($this->NumRows() > 0) {
			return $this->getAllRows(false);
		}
		return $default;
	}

	function result_obj($sql, $default = array()) {
		$this->Query($sql, true);
		if ($this->NumRows() > 0) {
			$allRows = array();
			while ($row = $this->FetchArray(false)) {
				$allRows[] = (object) $row;
			}
			return $allRows;
		}
		return $default;
	}

	function row_array($sql, $default = array()) {
		$this->Query($sql, TRUE);
		if ($this->NumRows() > 0) {
			return $this->FetchArray(false);
		}
		return $default;
	}

	function row_obj($sql, $default = array()) {
		return (object) $this->row_array($sql, $default);
	}

	/* functiones HTML */

	function htmlTable($sql, $opciones = array(), $atributos = array(), $fini = 0, $right = TRUE) {
		$this->Query($sql, true);
		$total_opciones = count($opciones);

		/// cabecera
		$attrs = '';
		foreach ($atributos as $nombre => $valor) {
			$attrs .= " $nombre=\"$valor\"";
		}
		$html = "<table $attrs>";
		$html .= "<tr>";
		for ($field = $fini; $field < $this->NumFields(); $field++) {
			if (!preg_match("/^\(.+\)$/i", $this->FieldName($field))) {
				$html .= "<th>" . $this->FieldName($field) . "</th>";
			}
		}
		$html .= ($total_opciones == 0) ? "" : "<th colspan=\"" . $total_opciones . "\" class=\"gridoptions\">&nbsp;</th>";
		$html .= "</tr>";

		for ($row = 0; $row < $this->NumRows(); $row++) {

			$record = $this->FetchArray();
			$html .= "<tr>";
			$tds = "";
			for ($field = $fini; $field < $this->NumFields(); $field++) {
				if (!preg_match("/^\(.+\)$/i", $this->FieldName($field))) {
					if (in_array($this->FieldType($field), array("int", "real")))
						$tds .= "<td align=\"right\">";
					else
						$tds .= "<td>";
					$tds .= $record[$this->FieldName($field)] . "</td>";
				}
			}
			$optds = "";
			foreach ($opciones as $opcion => $info) {
				$titulo = $info['titulo'];
				$clase = $info['clase'];

				if (!empty($info['condicion']) && empty($record[$info['condicion']])) {
					$enlace = "javascript:void(0);";
					$clase .= ' opacidad';
				} else {
					$enlace = preg_replace("/\[([^]]+)\]/i", "{\$record['\\1']}", $info['enlace']);
					eval("\$enlace = \"$enlace\";");
				}
				$optds .= '<td align="center" class="gridoption"><a href="' . $enlace . '" class="' . $clase . '" title="' . $titulo . '">' . $opcion . '</a></td>' . "";
			}
			$html = $right ? $html . $tds . $optds : $html . $optds . $tds;
			$html .= "</tr>";
		}

		$html .= "</table>";
		return $html;
	}

	function HTMLGrid($sql, $opciones = array(), $atributos = array()) {
		$this->Query($sql, true);
		$total_opciones = count($opciones);

		/// cabecera
		$attrs = '';
		foreach ($atributos as $nombre => $valor) {
			$attrs .= " $nombre=\"$valor\"";
		}

		$html = '<table' . $attrs . '>';
		$html .= '<tr>';
		for ($field = 0; $field < $this->NumFields(); $field++) {
			if (!preg_match("/^\(.+\)$/i", $this->FieldName($field))) {
				$html .= '<th>' . $this->FieldName($field) . '</th>';
			} else if (isset($opciones["{$this->FieldName($field)}"])) {
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
		for ($row = 0; $row < $this->NumRows(); $row++) {
			$record = $this->FetchArray();
			$html .= '<tr' . ($row % 2 ? ' class="par"' : '') . '>';

			for ($field = 0; $field < $this->NumFields(); $field++) {
				/*
				  if(!preg_match("/^\(.+\)$/i", $this->FieldName($field))){ // se muestra si no es campo oculto (...)
				  if(in_array($this->FieldType($field), array("int", "real")))
				  $html .= "<td align=\"right\">";
				  else
				  $html .= "<td>";
				  $html .= htmlentities($record[$this->FieldName($field)])."</td>";
				  }
				 */
				if (isset($opciones["{$this->FieldName($field)}"])) {
					$codigo = $opciones["{$this->FieldName($field)}"];
					$codigo = preg_replace("/\[([^]]+)\]/", "{\$record['$1']}", $codigo); // parsear codigo
					$codigo = addcslashes($codigo, '"\\');
					eval('$codigo = "' . $codigo . '";');
					//$html .= '<td align="center">'.utf8_encode($codigo).'</td>';
					$html .= '<td>' . ($codigo) . '</td>';
				} else if (!preg_match("/^\(.+\)$/i", $this->FieldName($field))) {
					if (in_array($this->FieldType($field), array("int", "real")))
						$html .= "<td align=\"right\">";
					else
						$html .= "<td>";
					$html .= ($record[$this->FieldName($field)]) . "</td>";
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

//	function htmlOptions($sql, $selected = -1) {
//		$this->Query($sql, true);
//		$op = '';
//		for ($row = 0; $row < $this->NumRows(); $row++) {
//			$op .= "<option ";
//			$record = $this->FetchArray();
//			$op .= 'value=' . $record[0] . ' ' . ($record[0] == $selected ? ' selected ' : '');
//			for ($field = 1; $field < $this->NumFields() - 1; $field++) {
//				$op .= $this->FieldName($field) . '="' . $record[$field] . '" ';
//			}
//			$op .= '>' . $record[$this->NumFields() - 1] . '</option>';
//		}
//		return $op;
//	}
//
//	function htmlDuOptions($sql, $selected = -1) {
//		$this->Query($sql, true);
//		$op = '';
//		for ($row = 0; $row < $this->NumRows(); $row++) {
//			$op .= "<option ";
//			$record = $this->FetchArray();
//			$op .= 'value=' . $record[0] . ' ' . ($record[0] == $selected ? ' selected ' : '');
//			for ($field = 1; $field < $this->NumFields() - 1; $field++) {
//				$op .= $this->FieldName($field) . '="' . $record[$field] . '" ';
//			}
//			$op .= '>' . $record[$this->NumFields() - 1] . '</option>';
//		}
//		return $op;
//	}
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
	
	
	function enumSelect($table, $field, $sel = -1, $reemplazo = array()) {
		$query = " SHOW COLUMNS FROM $table LIKE '$field' ";
		$result = $this->Query($query) or die($this->GetError());
		$row = $this->FetchArray();
		#extract the values
		#the values are enclosed in single quotes
		#and separated by commas
		$regex = "/'(.*?)'/";
		preg_match_all($regex, $row[1], $enum_array);
		$enum_fields = $enum_array[1];

		$html = "";
		foreach ($enum_fields as $value) {
			$html .= '<option value="' . $value . '" ' . (($value == $sel) ? 'selected' : '') . ' >' . htmlentities((empty($reemplazo) ? $value : $reemplazo[$value])) . '</option>';
		}
		return $html;
	}

	function Pagination($current, $top, $sql, $url, $var) {
		$current = (empty($current)) ? 1 : $current;

		$this->Query($sql, true);

		$pages = ceil($this->NumRows() / $top);

		$links = array();

		if ($current > 1) {
			$href = str_replace("&$var=", "&$var=" . ($current - 1), $url);
			$onclick = !strstr($href, "javascript:") ? "" : "onclick=\"$href\"";
			$href = empty($onclick) ? $href : "javascript:void(0);";
			$links[] = "<a href=\"" . $href . "\" " . $onclick . "> &laquo; Anterior</a>";
		}

		for ($i = 1; $i <= $pages; $i++) {
			$href = str_replace("&$var=", "&$var=$i", $url);
			$onclick = !strstr($href, "javascript:") ? "" : "onclick=\"$href\"";
			$href = empty($onclick) ? $href : "javascript:void(0);";
			$links[] = "<a href=\"" . $href . "\" " . $onclick . ">$i</a>";
		}

		if ($current < $pages) {
			$href = str_replace("&$var=", "&$var=" . ($current + 1), $url);
			$onclick = !strstr($href, "javascript:") ? "" : "onclick=\"$href\"";
			$href = empty($onclick) ? $href : "javascript:void(0);";
			$links[] = "<a href=\"" . $href . "\" " . $onclick . ">Siguiente &raquo;</a>";
		}

		$this->Query("$sql LIMIT " . (($current - 1) * $top) . ", $top", true);

		return implode(" ", $links);
	}

	function Insertar($tabla, $datos, $show_error = false) {
		foreach ($datos as $campo => $valor) {
			$campos[] = $campo;
			$valores[] = is_null($valor) ? 'NULL' : "'" . $valor . "'";
		}
		$sql = "INSERT INTO $tabla(" . implode(',', $campos) . ") VALUES(" . implode(", ", $valores) . ")";
		return $this->Query($sql, $show_error);
	}

	function Insertar_batch($tabla, $datosm, $show_error = true) {
		$block_val = array();
		foreach ($datosm as $datos) {
			$campos = array();
			$valores = array();
			foreach ($datos as $campo => $valor) {
				$campos[] = $campo;
				$valores[] = is_null($valor) ? 'NULL' : "'" . $valor . "'";
			}
			$block_val[] = "(" . implode(", ", $valores) . ")";
		}
		$sql = "INSERT INTO $tabla(" . implode(',', $campos) . ") VALUES" . implode(',', $block_val);

		return $this->Query($sql, $show_error);
	}

	function Actualizar($tabla, $datos, $where, $print_error=false) {
		foreach ($datos as $campo => $valor) {
			$valor = is_null($valor) ? 'NULL' : "'" . $valor . "'";
			$sets[] = $campo . "=" . $valor;
		}
		$sql = "UPDATE $tabla SET " . implode(',', $sets) . " WHERE $where";
		return $this->Query($sql,$print_error);
	}

	function Eliminar($tabla, $where, $limit = '') {
		return $this->Query("DELETE FROM {$tabla} WHERE $where $limit");
	}

	function jpaginador($pag, $npaginas) {

		$html = '<div class="jpaginador"  id="' . $pag . '"> 
            <button name="back" type="button" class="left back btn ml5" ' . ($pag > 0 ? "" : "disabled") . '> &lt;- </button>
            <select name="paglista" class="paglist left ml5 input-small">';

		for ($i = 0; $i < $npaginas; $i++) {
			$html.='<option value="' . $i . '"' . ($pag == $i ? "selected" : "") . '>
                    &nbsp;&nbsp;&nbsp;&nbsp;' . ($i + 1) . " / " . $npaginas . ' &nbsp;&nbsp;
                </option>';
		}
		$html.='</select>
            <button name="forward" type="button" class="left forward btn ml5" ' . (($pag + 1) < $npaginas ? "" : "disabled") . '> -&gt; </button>
            </div>';
		return $html;
	}

	function gridpag($sql, $pag, $opciones = array(), $atributos = array(), $cantpp = 20) {

		$strlimit = " LIMIT " . ($pag * $cantpp) . ", $cantpp";
		//$nro=$pag * $cantpp;
		//$sql = str_replace("select", "SELECT", $sql);
		$sql = preg_replace('/SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS', $sql, 1);
		$sql.=$strlimit;

		$tabla = $this->htmlTable($sql, $opciones, $atributos);

		$this->Query("SELECT FOUND_ROWS() AS total", true);
		$reg = $this->FetchArray();
		$nregistros = $reg["total"];
		//echo "----->".$nregistros;
		$npaginas = ceil($nregistros / $cantpp);

		$paginador = $this->jpaginador($pag, $npaginas);

		return array('grid' => $tabla, 'paginador' => $paginador);
	}

	function mygridpag($sql, $pag, $opciones = array(), $atributos = array(), $tot = 0, $cantpp = 20) {
		$strlimit = " LIMIT " . ($pag * $cantpp) . ", $cantpp";

		$sql.=$strlimit;

		$tabla = $this->htmlTable($sql, $opciones, $atributos, 1);

		$npaginas = ceil($tot / $cantpp);

		$paginador = $this->jpaginador($pag, $npaginas);

		return array('grid' => $tabla, 'paginador' => $paginador);
	}

	function gridpag_2($sql, $pag, $opciones = array(), $atributos = array(), $cantpp = 20) {

		$strlimit = " LIMIT " . ($pag * $cantpp) . ", $cantpp";
		//$nro=$pag * $cantpp;
		//$sql = str_replace("select", "SELECT", $sql);
		$sql = preg_replace('/SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS', $sql, 1);
		$sql.=$strlimit;

		$tabla = $this->HTMLGrid($sql, $opciones, $atributos);
		//$tabla = $this->htmlTable($sql, $opciones, $atributos);

		$this->Query("SELECT FOUND_ROWS() AS total", true);
		$reg = $this->FetchArray();
		$nregistros = $reg["total"];
		$npaginas = ceil($nregistros / $cantpp);

		$paginador = $this->jpaginador($pag, $npaginas);

		return array('grid' => $tabla, 'paginador' => $paginador);
	}

	function get_object($tabla, $where)
	{
		$res = $this->Query("SELECT * FROM $tabla WHERE $where LIMIT 1");
		if(!$res){
			return false;
		}
		else{
			return $res->fetch_object();
		}
	}
	
	function objects($tabla,$where='')
	{
		$sql = "SELECT
					*
				FROM $tabla";
		$sql .= empty($where) ? '' : " WHERE $where";
		
		$res = $this->Query($sql, true);
		if($res->num_rows() <= 0){
			return false;
		}
		else{
			return $res;
		}
	}
	
	function get_max_id($tabla,$nid)
	{
		$sql = "SELECT MAX($nid) AS maxid FROM $tabla";
		$res = $this->Query($sql, true);
		if($res->num_rows()>0){
			return $res->result(0, 'maxid') + 1;
		}
		else{
			return 1;
		}
	}
}

// fin de class mysql
//class resultado {
//
//	var $result;
//
//	function __construct($result) {
//		$this->result = $result;
//	}
//
//	function fetch_object() {
//		return mysqli_fetch_object($this->result);
//	}
//
//	function num_rows() {
//		return mysqli_num_rows($this->result);
//	}
//
//	function result($row, $field) {
//            mysqli_data_seek($this->result, $row);
//            $ceva= mysqli_fetch_array($this->result, MYSQLI_BOTH);
//            return $ceva[$field]; 
//
//	}
//
//	function data_seek($row_number) {
//		mysqli_data_seek($this->result, $row_number);
//	}
//
//}


class resultado {

	var $result;

	function __construct($result) {
		$this->result = $result;
	}

	function fetch_object() {
		return mysqli_fetch_object($this->result);
	}

	function num_rows() {
		return mysqli_num_rows($this->result);
	}

	function num_fields() {
		return mysqli_num_fields($this->result);
	}

	function field_name($field_index) {
		return mysqli_field_name($this->result, $field_index);
	}

	function result($row, $field) {
		mysqli_data_seek($this->result, $row);
		$ceva = mysqli_fetch_array($this->result, MYSQLI_BOTH);
		return $ceva[$field];
	}

	function data_seek($row_number) {
		mysqli_data_seek($this->result, $row_number);
	}

	function fetch_array() {
		return mysqli_fetch_array($this->result);
	}

	function field_type($field_index) {
		return mysqli_field_type($this->result, $field_index);
	}

	function crearTabla($opciones = array(), $atributos = array()) {
		//$this->Query($sql, true);
		$total_opciones = count($opciones);

		/// cabecera
		$attrs = '';
		foreach ($atributos as $nombre => $valor) {
			$attrs .= " $nombre=\"$valor\"";
		}

		$html = '<table' . $attrs . '>';
		$html .= '<tr>';
		for ($field = 0; $field < $this->num_fields(); $field++) {
			if (!eregi("^\(.+\)$", $this->field_name($field))) {// si no tiene parentesis mostrar
				$html .= '<th>' . $this->field_name($field) . '</th>';
			} else if (isset($opciones["{$this->field_name($field)}"])) {// si es una opcion no mostrar titulo
				$html .= '<th>&nbsp;</th>';
			}
		}

		$html .= '</tr>';

		// datos
		for ($row = 0; $row < $this->num_rows(); $row++) {
			$record = $this->fetch_array();
			$html .= '<tr>';

			for ($field = 0; $field < $this->num_fields(); $field++) {
				if (isset($opciones["{$this->field_name($field)}"])) { // si es una opcion parsear
					$codigo = $opciones["{$this->field_name($field)}"];
					$codigo = preg_replace("/\[([^]]+)\]/", "{\$record['$1']}", $codigo); // parsear codigo
					$codigo = addcslashes($codigo, '"\\');
					eval('$codigo = "' . $codigo . '";');
					//$html .= '<td align="center">'.utf8_encode($codigo).'</td>';
					$html .= '<td>' . $codigo . '</td>';
				} else if (!eregi("^\(.+\)$", $this->field_name($field))) { // si no esta oculto mostrar valor
					if (in_array($this->field_type($field), array("int", "real"))) {
						$html .= "<td align=\"right\">";
					} else {
						$html .= "<td>";
					}
					$html .= $record[$this->field_name($field)] . "</td>\n";
				}
			}
			$html .= '</tr>';
		}

		$html .= '</table>';
		return $html;
	}

	function crearOpciones($valor, $etiqueta, $selected = -1) {
		$rvalor = "";
		$html = "\n";
		while ($Array = $this->fetch_array()) {
			if ($rvalor == "")
				$rvalor = $Array[$valor];
			if ($Array[$valor] == $selected)
				$rvalor = $Array[$valor];
			$html.= "<option value='" . $Array[$valor] . "'";
			$html.= ($Array[$valor] == $selected) ? " selected" : "";
			$html.= ">" . $Array[$etiqueta] . "</option>\n";
		}
		return $html;
	}

}

// fin de la clase
?>
