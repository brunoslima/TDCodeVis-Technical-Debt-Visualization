<?php

class Normalizacao{

	public static function ajustarTempoRetrabalho($conjunto, $coluna){

		$c = new Conjunto();
		$v = array();
		$elementos = array();
		foreach ($conjunto->getDados() as $key => $value) {
			
			$elemento = $value->getVariaveis()[$coluna];
			if(is_numeric($value->getVariaveis()[$coluna]) && ($value->getVariaveis()[0] == "Rework Time")){
				
				$elemento = $value->getVariaveis()[$coluna] / 60;
			}

			$i = new Instancia();
			$i->adicionarVariavel($value->getVariaveis()[0]);
			$i->adicionarVariavel($value->getVariaveis()[1]);
			$i->adicionarVariavel($elemento);
			$v[] = $i;
		}

		$c->setDados($v);
		return $c;
	}
	
	public static function Coluna($conjunto, $coluna){

		$maior = Normalizacao::maior($conjunto, $coluna);
		$menor = Normalizacao::menor($conjunto, $coluna);

		$c = new Conjunto();
		$v = array();
		$elementos = array();
		foreach ($conjunto->getDados() as $key => $value) {
			
			$elemento = $value->getVariaveis()[$coluna];
			if(is_numeric($value->getVariaveis()[$coluna])){
				
				$elemento = $value->getVariaveis()[$coluna];
				$elemento = ($elemento - $menor) / ($maior - $menor);
			}

			$i = new Instancia();
			$i->adicionarVariavel($value->getVariaveis()[0]);
			$i->adicionarVariavel($value->getVariaveis()[1]);
			$i->adicionarVariavel($elemento);
			$v[] = $i;
		}

		$c->setDados($v);
		return $c;
	}

	public static function Coluna2($conjunto, $coluna){

		$maior = Normalizacao::maior($conjunto, $coluna);
		$menor = Normalizacao::menor($conjunto, $coluna);

		$c = new Conjunto();
		$v = array();
		$elementos = array();
		foreach ($conjunto->getDados() as $key => $value) {
			
			$elemento = $value->getVariaveis()[$coluna];
			if(is_numeric($value->getVariaveis()[$coluna])){
				
				$elemento = $value->getVariaveis()[$coluna];
				$elemento = ($elemento - $menor) / ($maior - $menor);
			}

			$i = new Instancia();
			$i->adicionarVariavel($value->getVariaveis()[0]);
			$i->adicionarVariavel($elemento);
			$i->adicionarVariavel($value->getVariaveis()[2]);
			$v[] = $i;
		}

		$c->setDados($v);
		return $c;
	}

	public static function Coluna3($conjunto, $coluna){

		$maior = Normalizacao::maior($conjunto, $coluna);
		$menor = Normalizacao::menor($conjunto, $coluna);

		$c = new Conjunto();
		$v = array();
		$elementos = array();
		$cont = 0;
		foreach ($conjunto->getDados() as $key => $value) {
			
			$elemento = $value->getVariaveis()[$coluna];
			if(is_numeric($value->getVariaveis()[$coluna])){
				
				$elemento = $value->getVariaveis()[$coluna];
				$elemento = ($elemento - $menor) / ($maior - $menor);
			}

			$i = new Instancia();
			$i->adicionarVariavel($value->getVariaveis()[0]);
			$i->adicionarVariavel($value->getVariaveis()[1]);
			if($cont == 0) $i->adicionarVariavel($elemento);
			else $i->adicionarVariavel(number_format($elemento, 10, '.', ',')."");
			$i->adicionarVariavel($value->getVariaveis()[3]);
			$v[] = $i;
			$cont++;
		}

		$c->setDados($v);
		return $c;
	}


	public static function ColunaPorChave($conjunto){

		$chaves = array();

		$cont = 0;
		foreach ($conjunto->getDados() as $key => $value) {

			if($cont != 0 && !in_array($value->getVariaveis()[0],$chaves)){
				
				$chaves[] = $value->getVariaveis()[0];
			}
			$cont++;
		}

		$conjuntoNormalizado = new Conjunto();

		for($i = 0; $i < count($chaves); $i++){
			
			$aux = array();
			$conjuntoAux = new Conjunto();
			foreach ($conjunto->getDados() as $key => $value) {

				if($i == 0 || $value->getVariaveis()[0] == $chaves[$i]){
								
					$conjuntoAux->addInstancia($value);
				}

			}

			echo "<br><br><br>";
			$conjuntoAux->exibir();
			$maior = Normalizacao::maior($conjuntoAux, 2);
			$menor = Normalizacao::menor($conjuntoAux, 2);

			$v = array();
			$elementos = array();
			foreach ($conjuntoAux->getDados() as $key => $value) {
			
				$elemento = $value->getVariaveis()[2];
				if(is_numeric($value->getVariaveis()[2])){
					
					$elemento = $value->getVariaveis()[2];
					$elemento = ($elemento - $menor) / ($maior - $menor);
				}

				$ins = new Instancia();
				$ins->adicionarVariavel($value->getVariaveis()[0]);
				$ins->adicionarVariavel($value->getVariaveis()[1]);
				$ins->adicionarVariavel($elemento);
				$v[] = $ins;
			}

			echo "<br>$i";

			$conjuntoNormalizado->setDadosIncrementos($v);
		}	

		return $conjuntoNormalizado;

	}

	public static function porColunas(){

	}

	public static function porLinhas(){

	}

	public static function zScore(){

	}

	public static function menor($conjunto, $coluna){

		$elementos = array();
		foreach ($conjunto->getDados() as $key => $value) {
			
			if(is_numeric($value->getVariaveis()[$coluna])){
				$elementos[] = $value->getVariaveis()[$coluna];
			}
		}

		return min($elementos);
	}

	public static function maior($conjunto, $coluna){

		$elementos = array();
		foreach ($conjunto->getDados() as $key => $value) {
			
			if(is_numeric($value->getVariaveis()[$coluna])){
				$elementos[] = $value->getVariaveis()[$coluna];
			}
		}

		return max($elementos);
	}

}

?>