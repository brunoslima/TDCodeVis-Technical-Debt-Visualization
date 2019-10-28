<?php
require_once("../logica/interacaoStarplotSeverity.php");
?>

<html>
  
  <head>    
    <meta charset="utf-8">
    <title>TDCodeVis - Starplot - Severity</title>
    <link rel="stylesheet" href="../css/starplot.css" charset="utf-8">
    <link rel="stylesheet" href="../css/style.css" charset="utf-8">
    <script type="text/javascript" src="../js/d3_v3/d3.min.js"></script>
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
      
      <div id="body">
        <div id="chart"></div>
      </div>
      <script src="../js/starplotSeverity.js" charset="utf-8"></script>
      <script src="../js/scriptStarplotSeverity.js" charset="utf-8"></script>

      <div class="filtro2">
        <!--<h1>Current Version: <?php echo "$vAtual";?></h1>-->
        <h2>Select vesion:</h2>
        <form action="starplotSeverity.php" method="POST">
          <?php for ($i=0; $i < count($version); $i++) { $v = $i+1; ?>
            <input type="checkbox" name="version[]" value="<?php echo "$v";?>"><?php echo "$v";?><br>
          <?php } ?>
          <input id="btn-estilo" type="submit" name="filtrar" value="Filter">
      </div>

    </section>
    
  </body>

</html>

