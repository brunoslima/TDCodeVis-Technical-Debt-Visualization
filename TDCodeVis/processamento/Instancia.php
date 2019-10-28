<?php

class Instancia{
	
	private $variaveis;

	function __construct(){

		$variaveis = array();
	}

    public function getVariaveis(){
    	
    	return $this->variaveis;
    }

    public function setVariaveis($variaveis){
     
        $this->variaveis = $variaveis;
    }

	public function adicionarVariavel($variavel){

		$this->variaveis[] = $variavel;

	}

	public function exibir(){

		$conteudo = "|";
		for ($i=0; $i < count($this->variaveis); $i++) { 
			
			$conteudo .= $this->variaveis[$i]."|";
		}

		echo "$conteudo<br>";
	}

}

?>