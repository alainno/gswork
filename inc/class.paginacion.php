<?php

class Paginacion
{
	var $enlace;
	var $actual;
	var $total;
	var $por_pagina;
	var $html;
	var $hash;

//	function __construct($enlace, $total, $por_pagina, $actual){
//		$this->enlace = $enlace;
//		$this->total = $total;
//		$this->por_pagina = $por_pagina;
//		$this->actual = $actual;
//	}
	
	function iniciar($enlace, $total, $por_pagina, $actual, $hash=''){
		$this->enlace = $enlace;
		$this->total = $total;
		$this->por_pagina = $por_pagina;
		$this->actual = $actual;
		$this->hash = $hash;
	}

	function crearEnlaces(){
		if($this->total > $this->por_pagina){
			$this->paginar($this->actual, ceil($this->total/$this->por_pagina), $this->enlace);
			return $this->html;
		}
	}

	function paginar($APAG, $TPAG, $LINK)
	{
		$this->ImpAnt($APAG, $LINK);

		if($TPAG<=10) $tfin = $TPAG;
		else $tfin = 2;

		if($APAG<8&&$TPAG>10) $tfin = 8;

		for($i=1;$i<=$tfin;$i++){
			$this->ImpNum($i,$APAG, $LINK);
		}
		$tfin = 0;

		if($TPAG>10&&$TPAG>=$APAG){
			if($APAG>=8){
				$this->html .= '<span> . . . . </span>';
				if($TPAG>=$APAG+8) $tfin = 4;
				else $tfin = $TPAG-$APAG;

				if($APAG>$TPAG-6)$tini = $APAG-$TPAG+10;
				else $tini = 3;

				for($i=$APAG-$tini;$i<=$APAG+$tfin;$i++)
					$this->ImpNum($i,$APAG, $LINK);
			}

			if($TPAG-1>=$APAG+$tfin){
				$this->html .= '<span> . . . . </span>';
				for($i=$TPAG-1;$i<=$TPAG;$i++)
					$this->ImpNum($i,$APAG, $LINK);
			}
		}

		$this->ImpSig($APAG,$TPAG, $LINK);
	}

	function ImpNum($num,$pag,$LINK)
	{
		if($num==$pag){
			$this->html .= '<span class="actual">' . $num . '</span>';
		}else{
			$this->html .= '<a href="' . $LINK . $num . $this->hash . '">' . $num .'</a>';
		}
	}

	function ImpSig($pag,$total,$LINK)
	{
		if($pag!=$total){
			$this->html .= '<a href="' . $LINK . ($pag+1). $this->hash . '" class="flechad">&raquo;</a>';
		}else{
			$this->html .= '<span class="flechad">&raquo;</span>';
		}
	}

	function ImpAnt($pag, $LINK)
	{
		if($pag!=1){
			$this->html .= '<a href="' . $LINK . ($pag-1) . $this->hash . '" class="flechai">&laquo;</a>';
		}else{
			$this->html .= '<span class="flechai">&laquo;</span>';
		}
	}
}

?>
