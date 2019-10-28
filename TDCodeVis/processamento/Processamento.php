<?php

class Processamento{

	public static function AplicarFiltroStreamgraph($conjunto, $vetorDeChaves){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
		for($i = 0; $i < count($vetorDeChaves); $i++){
			
			foreach ($conjunto->getDados() as $key => $value) {

				if($cont == 0 || $value->getVariaveis()[0] == $vetorDeChaves[$i]){
					$cont++;				
					$conjuntoFiltrado->addInstancia($value);
				}

			}

		}
		return $conjuntoFiltrado;

	}

	public static function AplicarFiltroStreamgraphIssues($conjunto, $severity){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
			
		foreach ($conjunto->getDados() as $key => $value) {

			if($cont == 0 || $value->getVariaveis()[3] == $severity){
				$cont++;				
				$conjuntoFiltrado->addInstancia($value);
			}

		}

		return $conjuntoFiltrado;
	}

	public static function AplicarFiltroStarplot($conjunto, $vetorDeChaves){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;

		for($i = 0; $i < count($vetorDeChaves); $i++){
			
			foreach ($conjunto->getDados() as $key => $value) {
				if($cont == 0 || $value->getVariaveis()[2] == $vetorDeChaves[$i]){
					$cont++;		
					$conjuntoFiltrado->addInstancia($value);
				}

			}

		}
		return $conjuntoFiltrado;

	}

	public static function AplicarFiltroVersao($conjunto, $vAtual, $posicao){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
			
		foreach ($conjunto->getDados() as $key => $value) {
			if($cont == 0 || $value->getVariaveis()[$posicao] == $vAtual){
				$cont++;		
				$conjuntoFiltrado->addInstancia($value);
			}

		}
		return $conjuntoFiltrado;

	}

	public static function AplicarFiltroStarplot2($conjunto, $vAtual){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
			
		foreach ($conjunto->getDados() as $key => $value) {
			if($cont == 0 || $value->getVariaveis()[2] == $vAtual){
				$cont++;		
				$conjuntoFiltrado->addInstancia($value);
			}

		}
		return $conjuntoFiltrado;

	}

	public static function AplicarFiltroStarplot2Versoes($conjunto, $versoes){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
			
		foreach ($conjunto->getDados() as $key => $value) {
			if($cont == 0 || in_array($value->getVariaveis()[2],$versoes)){
				$cont++;		
				$conjuntoFiltrado->addInstancia($value);
			}

		}
		return $conjuntoFiltrado;

	}

	public static function filtrarSeveridade($conjunto, $vetorDeChaves, $posicao){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
			
			foreach ($conjunto->getDados() as $key => $value) {

				if($cont == 0 || in_array($value->getVariaveis()[$posicao], $vetorDeChaves)){
					$cont++;				
					$conjuntoFiltrado->addInstancia($value);
				}
			}
		return $conjuntoFiltrado;
	}

	public static function filtrarSeveridadeTree($conjunto, $vetorDeChaves, $posicao){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
			
			foreach ($conjunto->getDados() as $key => $value) {

				if($cont == 0 || in_array($value->getVariaveis()[$posicao],$vetorDeChaves) || $value->getVariaveis()[$posicao] == ""){
					$cont++;				
					$conjuntoFiltrado->addInstancia($value);
				}

			}
		return $conjuntoFiltrado;

	}

	public static function filtrarRetrabalho($conjunto, $tempo, $posicao){

		$conjuntoProcessado = new Conjunto();
		$cont = 0;
			
		foreach ($conjunto->getDados() as $key => $value) {

			$vetor = array();

			if($cont == 0) $conjuntoProcessado->addInstancia($value);
			else if($value->getVariaveis()[$posicao] <= $tempo){
				
				$conjuntoProcessado->addInstancia($value);
			}

			$cont++;
		}

		return $conjuntoProcessado;
	}

	public static function transformarSeveridade($conjunto, $posicao){

		$conjuntoFiltrado = new Conjunto();
		$cont = 0;
			
		foreach ($conjunto->getDados() as $key => $value) {

			$vetor = array();

			if($cont == 0) $conjuntoFiltrado->addInstancia($value);
			else if($value->getVariaveis()[$posicao] == "BLOCKER"){
				for ($i=0; $i < count($value->getVariaveis()); $i++) {
					if($i == $posicao) $vetor[] = "5";
					else $vetor[] = $value->getVariaveis()[$i];
				}
				$instancia = new Instancia();
				$instancia->setVariaveis($vetor);
				$conjuntoFiltrado->addInstancia($instancia);
			}
			else if($value->getVariaveis()[$posicao] == "CRITICAL"){
				for ($i=0; $i < count($value->getVariaveis()); $i++) {
					if($i == $posicao) $vetor[] = "4";
					else $vetor[] = $value->getVariaveis()[$i];
				}
				$instancia = new Instancia();
				$instancia->setVariaveis($vetor);
				$conjuntoFiltrado->addInstancia($instancia);
			}
			else if($value->getVariaveis()[$posicao] == "MAJOR"){
				for ($i=0; $i < count($value->getVariaveis()); $i++) {
					if($i == $posicao) $vetor[] = "3";
					else $vetor[] = $value->getVariaveis()[$i];
				}
				$instancia = new Instancia();
				$instancia->setVariaveis($vetor);
				$conjuntoFiltrado->addInstancia($instancia);
			}
			else if($value->getVariaveis()[$posicao] == "MINOR"){
				for ($i=0; $i < count($value->getVariaveis()); $i++) {
					if($i == $posicao) $vetor[] = "2";
					else $vetor[] = $value->getVariaveis()[$i];
				}
				$instancia = new Instancia();
				$instancia->setVariaveis($vetor);
				$conjuntoFiltrado->addInstancia($instancia);
			}
			else if($value->getVariaveis()[$posicao] == "INFO"){
				for ($i=0; $i < count($value->getVariaveis()); $i++) {
					if($i == $posicao) $vetor[] = "1";
					else $vetor[] = $value->getVariaveis()[$i];
				}
				$instancia = new Instancia();
				$instancia->setVariaveis($vetor);
				$conjuntoFiltrado->addInstancia($instancia);
			}
			$cont++;
		}
		return $conjuntoFiltrado;

	}



	public static function retirarDadosFaltantes($conjunto){

		$conjuntoProcessado = new Conjunto();
		$cont = 0;
			
		foreach ($conjunto->getDados() as $key => $value) {

			$vetor = array();

			if($cont == 0) $conjuntoProcessado->addInstancia($value);
			else if($value->getVariaveis()[5] != "\N"){
				
				$conjuntoProcessado->addInstancia($value);
			}

			$cont++;
		}

		return $conjuntoProcessado;
	}


	public static function transformarProject($conjunto){

		$conjuntoFiltrado = $conjunto;
		$cont = 0;
		
		$chavesV = array();
		$versoes = array();

		foreach ($conjunto->getDados() as $key => $value) {


			if($cont != 0){
				
				if(!in_array($value->getVariaveis()[7],$chavesV)){
					$chavesV[] = $value->getVariaveis()[7];
				}

			}

			$cont++;

		}

		usort($chavesV, "comparar");
		$conjuntoFiltrado->addChavesVersoes($chavesV);
		return $conjuntoFiltrado;

	}

}

?>