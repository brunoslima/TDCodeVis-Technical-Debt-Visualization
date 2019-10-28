<?php

class Conjunto{
	
	private $dados;
	private $versoes;
	private $chavesVersoes;

	function __construct(){
		
		$dados = array();
	}

	public function addInstancia($instancia){

		$this->dados[] = $instancia;
	}

	public function getDados(){
		
		return $this->dados;
	}

	public function setDados($vetorDeInstancias){
		
		$this->dados = $vetorDeInstancias;
	}

	public function setDadosIncrementos($vetorDeInstancias){
		
		for ($i=0; $i < count($vetorDeInstancias); $i++) { 
			
			$this->dados[] = $vetorDeInstancias[$i];
		}

	}

	public function exibir(){

		foreach ($this->dados as $key => $value) {
			$value->exibir();
		}
	}

	public function addListaDeVersoes($lista){

		$this->versoes = $lista;
	}

	public function addChavesVersoes($lista){

		$this->chavesVersoes = $lista;
	}

	public function getChavesVersoes(){

		return $this->chavesVersoes;
	}
}

?>