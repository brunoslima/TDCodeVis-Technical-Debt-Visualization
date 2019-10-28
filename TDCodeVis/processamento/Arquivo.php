<?php

require_once("../processamento/Conjunto.php");
require_once("../processamento/Instancia.php");

class Arquivo{

	public static function lerCSV($nomeArquivo){

		$vatorDeInstancias;
		$vetor = array();
		$file = fopen($nomeArquivo, 'r');
		
		while (($linha = fgetcsv($file)) !== false){	
  			
			$instancia = new Instancia();

			for($i = 0; $i < count($linha); $i++){
				
				$instancia->adicionarVariavel($linha[$i]);
			}

  			$vetorDeInstancias[] = $instancia;
		}

		fclose($file);

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias);
		return $conjunto;
		
	}

	public static function salvarCSV($nomeArquivo, $conjunto){

		$file = fopen($nomeArquivo, 'w+');

		foreach ($conjunto->getDados() as $key => $value) {
			
			$elementos = $value->getVariaveis();
			$linha = implode(",",$elementos);
			fwrite($file, $linha."\n");
			
		}

		fclose($file);
		
	}

}

?>