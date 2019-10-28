<?php

require_once("../processamento/Conjunto.php");
require_once("../processamento/Instancia.php");

function comparar($v1, $v2) {
    if ($v1 < $v2) return -1;
    elseif ($v1 > $v2) return 1;
    return 0;
}

/**
* 
*/
class ConnectDB{
	
	private $host;
	private $port;
	private $database;
	private $user;
	private $password;
	private $con;
	private $status;

	function __construct(){
		$this->status = false;
	}

	function getHost(){
		return $this->host;
	}

	function getPort(){
		return $this->port;
	}

	function getDatabase(){
		return $this->database;
	}

	function getUser(){
		return $this->user;
	}

	function getPassword(){
		return $this->password;
	}

	function getStatus(){
		return $this->status;
	}

	function setStatus($boolean){
		$this->status = $boolean;
	}

	function initialize($host, $port, $database, $user, $password){

		$this->host = $host;
		$this->port = $port;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
	}

	function connect(){

		$host = $this->getHost();
		$port = $this->getPort();
		$db = $this->getDatabase();
		$user = $this->getUser();
		$pass = $this->getPassword();
		
		$header = " host=".$host." port=".$port." dbname=".$db." user=".$user." password=".$pass." ";
		$this->con = pg_connect($header) or die ("Error connect".pg_last_error());
		$this->setStatus(true);
	}

	function disconnect(){

		$this->setStatus(false);
	}

	function consult(){

		//Consulta padrão
		$sql = "SELECT id, title, description, location, classe, line, debt, severity, project_id
  				FROM public.technicaldebt";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());
  		//$row = pg_num_rows($result);

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		//set_time_limit(500);

		$chavesV = array();

  		$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("id");
		$instancia->adicionarVariavel("title");
		$instancia->adicionarVariavel("description");
		$instancia->adicionarVariavel("location");
		$instancia->adicionarVariavel("classe");
		$instancia->adicionarVariavel("line");
		$instancia->adicionarVariavel("debt");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("project_id");
  		$vetorDeInstancias[] = $instancia;

	    $description = " ";
	    $vetorCaracter = array("\n", ",", ";");
	    $vetorReplace = array("-", "-", "-");

		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
  			
  			$description = str_replace($vetorCaracter, $vetorReplace, $row["description"]);

			$instancia = new Instancia();
			$instancia->adicionarVariavel($row["id"]);
			$instancia->adicionarVariavel($row["title"]);
			$instancia->adicionarVariavel($description);
			$instancia->adicionarVariavel($row["location"]);
			$instancia->adicionarVariavel($row["classe"]);
			$instancia->adicionarVariavel($row["line"]);
			$instancia->adicionarVariavel($row["debt"]);
			$instancia->adicionarVariavel($row["severity"]);
			$instancia->adicionarVariavel($row["project_id"]);
			if(!in_array($row["project_id"],$chavesV)){
				$chavesV[] = $row["project_id"];
			}

  			$vetorDeInstancias[] = $instancia;
		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias);  

		usort($chavesV, "comparar");
		$conjunto->addChavesVersoes($chavesV);
		$this->setStatus(true);

		return $conjunto;

	}

	function consultPath($versao){

		//Consulta padrão
		$sql = "SELECT location, COUNT(location) FROM public.technicaldebt WHERE project_id='$versao' GROUP BY location ORDER BY location";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());
  		//$row = pg_num_rows($result);

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

  		$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("id");
		$instancia->adicionarVariavel("value");
		$instancia->adicionarVariavel("severity");

  		$vetorDeInstancias[] = $instancia;
  		$path = array();
  		$conjuntoPath = array();

		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
  			
			$path = explode("/",$row["location"]);

			$literal = "";
			for ($i=0; $i < count($path)-1 ; $i++) { 
				
				$literal .= $path[$i];
				if(!in_array($literal, $conjuntoPath)){
					
					$conjuntoPath[] = $literal;
					$instancia = new Instancia();
					$instancia->adicionarVariavel($literal);
					$instancia->adicionarVariavel("");
					$instancia->adicionarVariavel("");
					$vetorDeInstancias[] = $instancia;
				}
				$literal = $literal."/";
			}

			$literal = $literal.$path[count($path)-1];

  			$sql = "SELECT severity, COUNT(severity) FROM public.technicaldebt where project_id='$versao' and location = '$literal' GROUP BY severity";
    		$resultSeverity = pg_query($sql) or die ("Error query".pg_last_error());

    		$severidades = array();
    		while ($rowSeverity = pg_fetch_array($resultSeverity, null, PGSQL_ASSOC)){

    			$severidades[] = $rowSeverity["severity"];
    		}

    		//Atribuindo a maior severidade
    		$s = "none";
    		if(in_array("BLOCKER", $severidades)) $s = "5";
    		else if(in_array("CRITICAL", $severidades)) $s = "4";
    		else if(in_array("MAJOR", $severidades)) $s = "3";
    		else if(in_array("MINOR", $severidades)) $s = "2";
    		else if(in_array("INFO", $severidades)) $s = "1";


			$instancia = new Instancia();
			$instancia->adicionarVariavel($literal);
			$instancia->adicionarVariavel($row["count"]);
			$instancia->adicionarVariavel($s);	
  			$vetorDeInstancias[] = $instancia;
		}


		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultPath2($versao){

		//Consulta padrão
		$sql = "SELECT title, COUNT(title) as amount FROM public.technicaldebt WHERE project_id='$versao' GROUP BY title ORDER BY title;";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());
  		//$row = pg_num_rows($result);

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

  		$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("id");
		$instancia->adicionarVariavel("value");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("description");
  		$vetorDeInstancias[] = $instancia;

		$instancia = new Instancia();
		$instancia->adicionarVariavel("issues");
		$instancia->adicionarVariavel("");
		$instancia->adicionarVariavel("");
		$instancia->adicionarVariavel("");
		$vetorDeInstancias[] = $instancia;

		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
  			
  			$title = $row["title"];

  			$sql = "SELECT distinct severity, description FROM public.technicaldebt WHERE title='$title' and project_id ='$versao';";
    		$resultSeverity = pg_query($sql) or die ("Error query".pg_last_error());

    		$severidades = array();
    		$description = " ";
    		$vetorCaracter = array("\n", ",", ";");
    		$vetorReplace = array("-", "-", "-");
    		while ($rowSeverity = pg_fetch_array($resultSeverity, null, PGSQL_ASSOC)){

    			$severidades[] = $rowSeverity["severity"];
    			$description .= "<br>".str_replace($vetorCaracter, $vetorReplace, $rowSeverity["description"]);
    		}

    		$s = "none";
    		if(in_array("BLOCKER", $severidades)) $s = "5";
    		else if(in_array("CRITICAL", $severidades)) $s = "4";
    		else if(in_array("MAJOR", $severidades)) $s = "3";
    		else if(in_array("MINOR", $severidades)) $s = "2";
    		else if(in_array("INFO", $severidades)) $s = "1";


			$instancia = new Instancia();
			$instancia->adicionarVariavel("issues/".$row["title"]);
			$instancia->adicionarVariavel($row["amount"]);
			$instancia->adicionarVariavel($s);
			$instancia->adicionarVariavel($description);	
  			$vetorDeInstancias[] = $instancia;
		}


		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultPathIssues($versao,$title){

		$sql = "SELECT severity FROM public.technicaldebt WHERE project_id='$versao' and title = '$title' group by severity";

		$result = pg_query($sql) or die ("Error query".pg_last_error());
  		//$severity = pg_num_rows($result);
  		$severity = "";
		while ($rowSeverity = pg_fetch_array($result, null, PGSQL_ASSOC)){	
			$severity = $rowSeverity["severity"];
		}

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$s = "none";
    	if("BLOCKER" == $severity) $s = "5";
    	else if("CRITICAL" == $severity) $s = "4";
    	else if("MAJOR" == $severity) $s = "3";
    	else if("MINOR" == $severity) $s = "2";
    	else if("INFO" == $severity) $s = "1";

		//Consulta padrão
		$sql = "SELECT location, COUNT(location) FROM public.technicaldebt WHERE project_id='$versao' and title = '$title' GROUP BY location ORDER BY location";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());
  		//$row = pg_num_rows($result);

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

  		$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("id");
		$instancia->adicionarVariavel("value");
		$instancia->adicionarVariavel("severity");

  		$vetorDeInstancias[] = $instancia;
  		$path = array();
  		$conjuntoPath = array();

		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
  			
			$path = explode("/",$row["location"]);

			$literal = "";
			for ($i=0; $i < count($path)-1 ; $i++) { 
				
				$literal .= $path[$i];
				if(!in_array($literal, $conjuntoPath)){
					
					$conjuntoPath[] = $literal;
					$instancia = new Instancia();
					$instancia->adicionarVariavel($literal);
					$instancia->adicionarVariavel("");
					$instancia->adicionarVariavel("");
					$vetorDeInstancias[] = $instancia;
				}
				$literal = $literal."/";
			}

			$literal = $literal.$path[count($path)-1];

    		//Atribuindo a maior severidade
			$instancia = new Instancia();
			$instancia->adicionarVariavel($literal);
			$instancia->adicionarVariavel($row["count"]);
			$instancia->adicionarVariavel($s);	
  			$vetorDeInstancias[] = $instancia;
		}


		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultTreePath($versao){

		$conjunto = new Conjunto();

	  	$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("name");
		$instancia->adicionarVariavel("parent");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("project");
	  	$vetorDeInstancias[] = $instancia;

		//for ($i=0; $i < count($versoes); $i++) { 
  			
  			//$version = $versoes[$i];

  			$sql = "SELECT location FROM public.technicaldebt WHERE project_id='$versao' GROUP BY location ORDER BY location";

	  		$result = pg_query($sql) or die ("Error query".pg_last_error());
	  		//$row = pg_num_rows($result);

	  		//Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			$conjuntoPath[] = array();
			while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
  			
				$path = explode("/",$row["location"]);
				$location = $row["location"];

				$literal = "";
				$pai = "";
				for ($i=0; $i < count($path) ; $i++) { 
					
					$literal = $path[$i];
					if($i != 0) $pai = $path[$i-1];
					else $pai = "null";
					$stringDeTrabalho = $literal.",".$pai;

					if(!in_array($stringDeTrabalho, $conjuntoPath)){
						
						$conjuntoPath[] = $stringDeTrabalho;
						$instancia = new Instancia();
						$instancia->adicionarVariavel($literal);
						$instancia->adicionarVariavel($pai);
						$instancia->adicionarVariavel("null");
						$vetorDeInstancias[] = $instancia;
					}
				}


				$sql2 = "SELECT distinct title, severity FROM public.technicaldebt WHERE project_id='$versao' and location='$location' ORDER BY title;";

	  			$result2 = pg_query($sql2) or die ("Error query".pg_last_error());
	  			//$row = pg_num_rows($result);

	  			//Resolvendo problema de alocação de memoria
				ini_set('memory_limit', '-1');
				set_time_limit(500);

				while ($rowTitle = pg_fetch_array($result2, null, PGSQL_ASSOC)){	
	  			
	  				$instancia = new Instancia();
					$instancia->adicionarVariavel($rowTitle["title"]);
					$instancia->adicionarVariavel($path[count($path)-1]);
					$instancia->adicionarVariavel($rowTitle["severity"]);
	  				$vetorDeInstancias[] = $instancia;
				}

		}//Fim do while

		$conjunto->setDados($vetorDeInstancias);

		//}//Fim do for para as versões

		return $conjunto;

	}

	function consultTreePath2($versao){ //Retirar se não utilizar o tree

		$conjunto = new Conjunto();

	  	$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("name");
		$instancia->adicionarVariavel("parent");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("project");
	  	$vetorDeInstancias[] = $instancia;

		//for ($i=0; $i < count($versoes); $i++) { 
  			
  			//$version = $versoes[$i];


			//$sql2 = "SELECT distinct title, severity FROM public.technicaldebt WHERE project_id='$versao' and location='$location' ORDER BY title;";

	  		//	$result2 = pg_query($sql2) or die ("Error query".pg_last_error());
	  			//$row = pg_num_rows($result);

	  			//Resolvendo problema de alocação de memoria
			//	ini_set('memory_limit', '-1');
			//	set_time_limit(500);

			//while ($rowTitle = pg_fetch_array($result2, null, PGSQL_ASSOC)){	
	  			
	  				$instancia = new Instancia();
					$instancia->adicionarVariavel("423");
					$instancia->adicionarVariavel("null");
					$instancia->adicionarVariavel("MAJOR");
	  				$vetorDeInstancias[] = $instancia;
			//}

  			$sql = "SELECT distinct location FROM public.technicaldebt WHERE project_id='$versao' and title='423' ORDER BY location";

	  		$result = pg_query($sql) or die ("Error query".pg_last_error());
	  		//$row = pg_num_rows($result);

	  		//Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			$conjuntoPath[] = array();
			while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
  			
				$path = explode("/",$row["location"]);
				$location = $row["location"];

				$literal = "";
				$pai = "";
				for ($i=0; $i < count($path) ; $i++) { 
					
					$literal = $path[$i];
					if($i != 0) $pai = $path[$i-1];
					else $pai = "423";
					$stringDeTrabalho = $literal.",".$pai;

					if(!in_array($stringDeTrabalho, $conjuntoPath)){
						
						$conjuntoPath[] = $stringDeTrabalho;
						$instancia = new Instancia();
						$instancia->adicionarVariavel($literal);
						$instancia->adicionarVariavel($pai);
						$instancia->adicionarVariavel("null");
						$vetorDeInstancias[] = $instancia;
					}
				}

			//$instancia = new Instancia();
			//$instancia->adicionarVariavel($row["location"]);
			//$instancia->adicionarVariavel("423");
			//$instancia->adicionarVariavel("MAJOR");
			//$vetorDeInstancias[] = $instancia;

		}//Fim do while

		$conjunto->setDados($vetorDeInstancias);

		//}//Fim do for para as versões

		return $conjunto;

	}

	function consultIssuesAmount($versoes){

		$conjunto = new Conjunto();

	  	$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("title");
		$instancia->adicionarVariavel("amount");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("project");
		$instancia->adicionarVariavel("description");
	  	$vetorDeInstancias[] = $instancia;

  		for ($i=0; $i < count($versoes); $i++) { 
  			
  			$version = $versoes[$i];

			//Consulta padrão
			$sql = "SELECT title, COUNT(title) as amount FROM public.technicaldebt WHERE project_id='$version' GROUP BY title ORDER BY title;";

	  		$result = pg_query($sql) or die ("Error query".pg_last_error());
	  		//$row = pg_num_rows($result);

	  		//Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
	  			
	  			$title = $row["title"];

	  			$sql = "SELECT distinct severity, description FROM public.technicaldebt WHERE title='$title' and project_id='$version';";
	    		$resultSeverity = pg_query($sql) or die ("Error query".pg_last_error());

	    		$severidades = array();
	    		$description = " ";
	    		$vetorCaracter = array("\n", ",", ";");
	    		$vetorReplace = array("-", "-", "-");
	    		while ($rowSeverity = pg_fetch_array($resultSeverity, null, PGSQL_ASSOC)){

	    			$severidades[] = $rowSeverity["severity"];
	    			$description .= "<br>".str_replace($vetorCaracter, $vetorReplace, $rowSeverity["description"]);
	    		}

	    		$s = "none";
	    		if(in_array("BLOCKER", $severidades)) $s = "5";
	    		else if(in_array("CRITICAL", $severidades)) $s = "4";
	    		else if(in_array("MAJOR", $severidades)) $s = "3";
	    		else if(in_array("MINOR", $severidades)) $s = "2";
	    		else if(in_array("INFO", $severidades)) $s = "1";


				$instancia = new Instancia();
				$instancia->adicionarVariavel($row["title"]);
				$instancia->adicionarVariavel($row["amount"]);
				$instancia->adicionarVariavel($s);
				$instancia->adicionarVariavel($i+1);
				$instancia->adicionarVariavel($description);	
	  			$vetorDeInstancias[] = $instancia;

			}//Fim do while
			$conjunto->setDados($vetorDeInstancias); 

		}//Fim do for para as versões

		return $conjunto;

	}

	function consultIssuesRework($versoes){

		$conjunto = new Conjunto();

	  	$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("title");
		$instancia->adicionarVariavel("rework");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("project");
		$instancia->adicionarVariavel("description");
	  	$vetorDeInstancias[] = $instancia;

  		for ($i=0; $i < count($versoes); $i++) { 
  			
  			$version = $versoes[$i];

			//Consulta padrão
			$sql = "SELECT title, SUM(CAST ( debt AS numeric )) as rework FROM public.technicaldebt WHERE project_id='$version' GROUP BY title ORDER BY title";

	  		$result = pg_query($sql) or die ("Error query".pg_last_error());
	  		//$row = pg_num_rows($result);

	  		//Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
	  			
	  			$title = $row["title"];

	  			$sql = "SELECT distinct severity, description FROM public.technicaldebt WHERE title='$title' and project_id='$version';";
	    		$resultSeverity = pg_query($sql) or die ("Error query".pg_last_error());

	    		$severidades = array();
	    		$description = " ";
	    		$vetorCaracter = array("\n", ",", ";");
	    		$vetorReplace = array("-", "-", "-");
	    		while ($rowSeverity = pg_fetch_array($resultSeverity, null, PGSQL_ASSOC)){

	    			$severidades[] = $rowSeverity["severity"];
	    			$description .= "<br>".str_replace($vetorCaracter, $vetorReplace, $rowSeverity["description"]);
	    		}

	    		$s = "none";
	    		if(in_array("BLOCKER", $severidades)) $s = "5";
	    		else if(in_array("CRITICAL", $severidades)) $s = "4";
	    		else if(in_array("MAJOR", $severidades)) $s = "3";
	    		else if(in_array("MINOR", $severidades)) $s = "2";
	    		else if(in_array("INFO", $severidades)) $s = "1";


				$instancia = new Instancia();
				$instancia->adicionarVariavel($row["title"]);
				$instancia->adicionarVariavel($row["rework"]);
				$instancia->adicionarVariavel($s);
				$instancia->adicionarVariavel($i+1);
				$instancia->adicionarVariavel($description);	
	  			$vetorDeInstancias[] = $instancia;

			}//Fim do while
			$conjunto->setDados($vetorDeInstancias); 

		}//Fim do for para as versões

		return $conjunto;

	}

	function consultIssuesRework2($versoes){

		$conjunto = new Conjunto();

	  	$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("title");
		$instancia->adicionarVariavel("rework");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("project");
		$instancia->adicionarVariavel("description");
	  	$vetorDeInstancias[] = $instancia;

  		for ($i=0; $i < count($versoes); $i++) { 
  			
  			$version = $versoes[$i];

			//Consulta padrão
			$sql = "SELECT distinct debt, title, severity, description FROM public.technicaldebt WHERE project_id='$version' order by title";

	  		$result = pg_query($sql) or die ("Error query".pg_last_error());
	  		//$row = pg_num_rows($result);

	  		//Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
	  			
	    		$description = " ";
	    		$vetorCaracter = array("\n", ",", ";");
	    		$vetorReplace = array("-", "-", "-");
	    		//while ($rowSeverity = pg_fetch_array($resultSeverity, null, PGSQL_ASSOC)){

	    		$row["severity"];
	    		$description = "<br>".str_replace($vetorCaracter, $vetorReplace, $row["description"]);
	    		//}

	    		$s = "none";
	    		if("BLOCKER" == $row["severity"]) $s = "5";
	    		else if("CRITICAL" == $row["severity"]) $s = "4";
	    		else if("MAJOR" == $row["severity"]) $s = "3";
	    		else if("MINOR" == $row["severity"]) $s = "2";
	    		else if("INFO" == $row["severity"]) $s = "1";


				$instancia = new Instancia();
				$instancia->adicionarVariavel($row["title"]);
				$instancia->adicionarVariavel($row["debt"]);
				$instancia->adicionarVariavel($s);
				$instancia->adicionarVariavel($i+1);
				$instancia->adicionarVariavel($description);	
	  			$vetorDeInstancias[] = $instancia;

			}//Fim do while
			$conjunto->setDados($vetorDeInstancias); 

		}//Fim do for para as versões

		return $conjunto;

	}

	function consultAmount(){

		$vetorDeInstancias = array();
		
		$instancia = new Instancia();
		$instancia->adicionarVariavel("key");
		$instancia->adicionarVariavel("project");
		$instancia->adicionarVariavel("value");
		
  		$vetorDeInstancias[] = $instancia;

  		$sql = "SELECT SUM( CAST (debt as int) ) as rework FROM public.technicaldebt group by project_id order by project_id";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel("Rework Time");
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["rework"]);
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as amount FROM public.technicaldebt group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel("Amount");
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["amount"]);
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as blocker FROM public.technicaldebt where severity = 'BLOCKER' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel("Blocker");
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["blocker"]);
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as critical FROM public.technicaldebt where severity = 'CRITICAL' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel("Critical");
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["critical"]);
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

				$sql = "SELECT COUNT(id) as major FROM public.technicaldebt where severity = 'MAJOR' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel("Major");
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["major"]);
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as minor FROM public.technicaldebt where severity = 'MINOR' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel("Minor");
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["minor"]);
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;

		}

		$sql = "SELECT COUNT(id) as info FROM public.technicaldebt where severity = 'INFO' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel("Info");
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["info"]);
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultAmount2(){

		$vetorDeInstancias = array();
		
		$instancia = new Instancia();
		$instancia->adicionarVariavel("axis");
		$instancia->adicionarVariavel("value");
		$instancia->adicionarVariavel("description");
		
  		$vetorDeInstancias[] = $instancia;

		$sql = "SELECT COUNT(id) as amount FROM public.technicaldebt group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["amount"]);
			$instancia->adicionarVariavel("Amount");
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as blocker FROM public.technicaldebt where severity = 'BLOCKER' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["blocker"]);
			$instancia->adicionarVariavel("Blocker");
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as critical FROM public.technicaldebt where severity = 'CRITICAL' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["critical"]);
			$instancia->adicionarVariavel("Critical");
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as major FROM public.technicaldebt where severity = 'MAJOR' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["major"]);
			$instancia->adicionarVariavel("Major");
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$sql = "SELECT COUNT(id) as minor FROM public.technicaldebt where severity = 'MINOR' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["minor"]);
			$instancia->adicionarVariavel("Minor");
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;

		}

		$sql = "SELECT COUNT(id) as info FROM public.technicaldebt where severity = 'INFO' group by project_id order by project_id";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$contVersion = 1;
		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
			
			$instancia = new Instancia();
			$instancia->adicionarVariavel($contVersion);
			$instancia->adicionarVariavel($row["info"]);
			$instancia->adicionarVariavel("Info");
	 		$vetorDeInstancias[] = $instancia;
	 		$contVersion++;
		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultAmount3($conjunto){

		$vetorDeInstancias = array();
		
		$instancia = new Instancia();
		$instancia->adicionarVariavel("axis");
		$instancia->adicionarVariavel("value");
		$instancia->adicionarVariavel("description");
		
  		$vetorDeInstancias[] = $instancia;

  		for ($i=0; $i < count($conjunto); $i++) { 
  			
  			$version = $conjunto[$i];

  			$sql = "SELECT COUNT(id) as amount, severity FROM public.technicaldebt where project_id = '$version' group by severity order by severity";

			$result = pg_query($sql) or die ("Error query".pg_last_error());

	  		//Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			$amount = array();

			while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
				
				if($row["severity"] == "INFO"){
					$amount[] = $row["amount"];
				}
				else{
					$instancia = new Instancia();
					$instancia->adicionarVariavel($row["severity"]);
					$instancia->adicionarVariavel($row["amount"]);
					$instancia->adicionarVariavel($i+1);
		 			$vetorDeInstancias[] = $instancia;
				}
			}

			for ($j=0; $j < count($amount); $j++) { 
				$instancia = new Instancia();
				$instancia->adicionarVariavel("INFO");
				$instancia->adicionarVariavel($amount[$j]);
				$instancia->adicionarVariavel($i+1);
		 		$vetorDeInstancias[] = $instancia;
			}

		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultAmountIssues($quantTotalVersao){

		$vetorDeInstancias = array();
		
		$instancia = new Instancia();
		$instancia->adicionarVariavel("key");
		$instancia->adicionarVariavel("project");
		$instancia->adicionarVariavel("value");
		$instancia->adicionarVariavel("severity");

  		$vetorDeInstancias[] = $instancia;

		$sql = "SELECT distinct title, severity FROM public.technicaldebt";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		while ($rowTitle = pg_fetch_array($result, null, PGSQL_ASSOC)){

			$title = $rowTitle["title"];
			$severity = $rowTitle["severity"];

			$sql2 = "SELECT COUNT(id) as amount, project_id FROM public.technicaldebt where title = '$title' group by project_id order by project_id";

			$result2 = pg_query($sql2) or die ("Error query".pg_last_error());

			 //Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			$contVersion = 0;
			while ($row = pg_fetch_array($result2, null, PGSQL_ASSOC)){
			
				$contVersion++;
				$instancia = new Instancia();
				$instancia->adicionarVariavel($title);
				$instancia->adicionarVariavel($contVersion);
				$instancia->adicionarVariavel($row["amount"]);
				$instancia->adicionarVariavel($severity);
	 			$vetorDeInstancias[] = $instancia;
	 			//$contVersion++;
			}

			if($contVersion < $quantTotalVersao){

				for ($i=$contVersion+1; $i <= $quantTotalVersao; $i++) { 
					
					$instancia = new Instancia();
					$instancia->adicionarVariavel($title);
					$instancia->adicionarVariavel($i);
					$instancia->adicionarVariavel(0);
					$instancia->adicionarVariavel($severity);
	 				$vetorDeInstancias[] = $instancia;
				}
			}

		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultReleaseFinal($version){

		$sql = "SELECT id, title, location, classe, line, debt, severity, project_id FROM public.technicaldebt WHERE project_id='$version'";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());
  		//$row = pg_num_rows($result);

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$vetorDeInstancias = array();
		
		$instancia = new Instancia();
		$instancia->adicionarVariavel("id");
		$instancia->adicionarVariavel("title");
		$instancia->adicionarVariavel("location");
		$instancia->adicionarVariavel("classe");
		$instancia->adicionarVariavel("line");
		$instancia->adicionarVariavel("debt");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("project_id");

  		$vetorDeInstancias[] = $instancia;

		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){

			$instancia = new Instancia();
			$instancia->adicionarVariavel($row["id"]);
			$instancia->adicionarVariavel($row["title"]);
			$instancia->adicionarVariavel($row["location"]);
			$instancia->adicionarVariavel($row["classe"]);
			$instancia->adicionarVariavel($row["line"]);
			$instancia->adicionarVariavel($row["debt"]);
			$instancia->adicionarVariavel($row["severity"]);
			$instancia->adicionarVariavel($row["project_id"]);
	 		$vetorDeInstancias[] = $instancia;

		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;
		
	}

	function consultParallel($version){

		$vetorDeInstancias = array();
		
		$instancia = new Instancia();
		$instancia->adicionarVariavel("Rework Time");
		$instancia->adicionarVariavel("title");
		$instancia->adicionarVariavel("severity");
		
  		$vetorDeInstancias[] = $instancia;

  		$sql = "SELECT debt, title, severity FROM public.technicaldebt where project_id = '$version'";

		$result = pg_query($sql) or die ("Error query".pg_last_error());

	  	//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$amount = array();

		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
				
			$instancia = new Instancia();
			$instancia->adicionarVariavel($row["debt"]);
			$instancia->adicionarVariavel($row["title"]);
			$instancia->adicionarVariavel($row["severity"]);
		 	$vetorDeInstancias[] = $instancia;
		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias); 
		return $conjunto;

	}

	function consultSeverity($versao, $filtro ,$vetorfiltro){

		$contID = 0;

  		$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("id");
		$instancia->adicionarVariavel("severity");
		$instancia->adicionarVariavel("debt");
  		$vetorDeInstancias[] = $instancia;

		$sql = "SELECT severity FROM public.technicaldebt where project_id='$versao' and debt<>'' group by severity";

  		$result = pg_query($sql) or die ("Error query".pg_last_error());
  		//$row = pg_num_rows($result);

  		//Resolvendo problema de alocação de memoria
		ini_set('memory_limit', '-1');
		set_time_limit(500);

		$vetorseverity = array();

		while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
  			
  			$vetorseverity[] = $row["severity"];

		}

		if(in_array("BLOCKER", $vetorseverity)){

			if(!$filtro || (in_array(5, $vetorfiltro))){

				$contID++;

				$sql = "SELECT debt FROM public.technicaldebt where project_id='$versao' and debt<>'' and severity='BLOCKER'";

		  		$result = pg_query($sql) or die ("Error query".pg_last_error());
		  		//$row = pg_num_rows($result);

		  		//Resolvendo problema de alocação de memoria
				ini_set('memory_limit', '-1');
				set_time_limit(500);

				while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
		  			
		  			$instancia = new Instancia();
					$instancia->adicionarVariavel($contID);
					$instancia->adicionarVariavel(5);
					$instancia->adicionarVariavel($row["debt"]);
					$vetorDeInstancias[] = $instancia;

				}

			}

		}

		if(in_array("CRITICAL", $vetorseverity)){

			if(!$filtro || (in_array(4, $vetorfiltro))){

				$contID++;

				$sql = "SELECT debt FROM public.technicaldebt where project_id='$versao' and debt<>'' and severity='CRITICAL'";

		  		$result = pg_query($sql) or die ("Error query".pg_last_error());
		  		//$row = pg_num_rows($result);

		  		//Resolvendo problema de alocação de memoria
				ini_set('memory_limit', '-1');
				set_time_limit(500);

				while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
		  			
		  			$instancia = new Instancia();
					$instancia->adicionarVariavel($contID);
					$instancia->adicionarVariavel(4);
					$instancia->adicionarVariavel($row["debt"]);
					$vetorDeInstancias[] = $instancia;

				}

			}

		}

		if(in_array("MAJOR", $vetorseverity)){

			if(!$filtro || (in_array(3, $vetorfiltro))){

				$contID++;

				$sql = "SELECT debt FROM public.technicaldebt where project_id='$versao' and debt<>'' and severity='MAJOR'";

		  		$result = pg_query($sql) or die ("Error query".pg_last_error());
		  		//$row = pg_num_rows($result);

		  		//Resolvendo problema de alocação de memoria
				ini_set('memory_limit', '-1');
				set_time_limit(500);

				while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
		  			
		  			$instancia = new Instancia();
					$instancia->adicionarVariavel($contID);
					$instancia->adicionarVariavel(3);
					$instancia->adicionarVariavel($row["debt"]);
					$vetorDeInstancias[] = $instancia;

				}

			}

		}

		if(in_array("MINOR", $vetorseverity)){

			if(!$filtro || (in_array(2, $vetorfiltro))){

				$contID++;

				$sql = "SELECT debt FROM public.technicaldebt where project_id='$versao' and debt<>'' and severity='MINOR'";

		  		$result = pg_query($sql) or die ("Error query".pg_last_error());
		  		//$row = pg_num_rows($result);

		  		//Resolvendo problema de alocação de memoria
				ini_set('memory_limit', '-1');
				set_time_limit(500);

				while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
		  			
		  			$instancia = new Instancia();
					$instancia->adicionarVariavel($contID);
					$instancia->adicionarVariavel(2);
					$instancia->adicionarVariavel($row["debt"]);
					$vetorDeInstancias[] = $instancia;

				}

			}

		}

		if(in_array("INFO", $vetorseverity)){

			if(!$filtro || (in_array(1, $vetorfiltro))){

				$contID++;

				$sql = "SELECT debt FROM public.technicaldebt where project_id='$versao' and debt<>'' and severity='INFO'";

		  		$result = pg_query($sql) or die ("Error query".pg_last_error());
		  		//$row = pg_num_rows($result);

		  		//Resolvendo problema de alocação de memoria
				ini_set('memory_limit', '-1');
				set_time_limit(500);

				while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
		  			
		  			$instancia = new Instancia();
					$instancia->adicionarVariavel($contID);
					$instancia->adicionarVariavel(1);
					$instancia->adicionarVariavel($row["debt"]);
					$vetorDeInstancias[] = $instancia;

				}

			}

		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias);  
		return $conjunto;

	}

	function consultSeverity2($versoes, $severity){


  		$vetorDeInstancias = array();
		$instancia = new Instancia();
		$instancia->adicionarVariavel("id");
		//$instancia->adicionarVariavel("version");
		$instancia->adicionarVariavel("debt");
		$instancia->adicionarVariavel("severity");
  		$vetorDeInstancias[] = $instancia;

		for ($i=0; $i < count($versoes); $i++) { 

			$versao = $versoes[$i];
			$sql = "SELECT debt FROM public.technicaldebt where project_id='$versao' and debt<>'' and severity='$severity'";

		  	$result = pg_query($sql) or die ("Error query".pg_last_error());
		  	//$row = pg_num_rows($result);

		  	//Resolvendo problema de alocação de memoria
			ini_set('memory_limit', '-1');
			set_time_limit(500);

			while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)){	
		  			
		  		$instancia = new Instancia();
				$instancia->adicionarVariavel($i+1);
				//$instancia->adicionarVariavel($i+1);
				$instancia->adicionarVariavel($row["debt"]);
				$instancia->adicionarVariavel($severity);
				$vetorDeInstancias[] = $instancia;
			}

		}

		$conjunto = new Conjunto();
		$conjunto->setDados($vetorDeInstancias);  
		return $conjunto;

	}

}

?>