<?php
require_once("../logica/interacaoBoxplotSeverity.php");
?>

<html>
  
  <head>    
    <meta charset="utf-8">
    <title>TDCodeVis - Box Plot - Severity</title>
    <link rel="stylesheet" href="../css/boxplot.css" charset="utf-8">
    <link rel="stylesheet" href="../css/style.css" charset="utf-8">
    <script type="text/javascript" src="../js/d3_v4/d3.min.js"></script>
  </head>

  <body>

    <header class="cabecalho">
      <h1 id="title">TDCodeVis</h1>
    </header>

    <section class="menu">
      <ul class="menuprincipal">
        <li><a href="home.php"><img src="../imagens/home.png">Home</a></li>
        <li><a href=""><img src="../imagens/file.png">File</a>
          <ul class="submenu">
            <!--<li><a href="">Import CSV</a></li>-->
            <li><a href="../dados/catalog-tdcodevis.csv">Export CSV</a></li>
          </ul>
        </li>
        <li><a href="connection.php"><img src="../imagens/database.png">Connection Database</a></li>
        <li><a href=""><img src="../imagens/vis.png">Visualization - Amount</a>
          <ul class="submenu">
            <li><a href="starplotSeverity.php">Starplot</a></li>
            <li><a href="treemap.php">Treemap</a></li>
            <li><a href="boxplotSeverity.php">Box Plot - Severity</a></li>
            <li><a href="boxplotVersions.php">Box Plot - Versions</a></li>
          </ul>
        </li>
        <li><a href=""><img src="../imagens/vis.png">Visualization - Issues</a>
          <ul class="submenu">
            <li><a href="bubbleChartAmount.php">Bubble Chart - Amount</a></li>
            <li><a href="bubbleChartRework.php">Bubble Chart - Rework</a></li>
          </ul>
        </li>
        <li><a href="about.php"><img src="../imagens/about.png">About</a></li>
      </ul>
    </section>

    <section class="conteudo">
      <svg id="boxplot"></svg>
      <script src="../js/boxplotSeverity.js" charset="utf-8"></script>
        <!--<div class="legenda-boxplot">
        <?php
            $html = ""; 
            foreach ($vetorDeChaves as $value) {
              if($value == 5) $html .= "<p>BLOCKER</p>";
              else if($value == 4) $html .= "<p>CRITICAL</p>";
              else if($value == 3) $html .= "<p>MAJOR</p>";
              else if($value == 2) $html .= "<p>MINOR</p>";
              else if($value == 1) $html .= "<p>INFO</p>";
            }
            echo "$html";
        ?>
        </div>-->
      <div class="filtro-boxplot">
        <h3>Current Version: <?php echo "$vAtual";?></h3>
        <form action="boxplotSeverity.php" method="POST">
          <h3>Select vesion:</h3>
          <select name="versoes">
            <?php for ($i=0; $i < count($version); $i++) { ?>
            <option value="<?php echo "$version[$i]-$i";?>"><?php $v = $i+1; echo "$v";?></option>
            <?php } ?>
          </select><br>
          <h3>Select severity:</h3>
          <input type="checkbox" name="severity[]" value="5">Blocker<br>
          <input type="checkbox" name="severity[]" value="4">Critical<br>
          <input type="checkbox" name="severity[]" value="3">Major<br>
          <input type="checkbox" name="severity[]" value="2">Minor<br>
          <input type="checkbox" name="severity[]" value="1">Info<br>
          <input id="btn-estilo" type="submit" name="filtrar" value="Filter">
        </form>
      </div>
    </section>

  </body>

</html>