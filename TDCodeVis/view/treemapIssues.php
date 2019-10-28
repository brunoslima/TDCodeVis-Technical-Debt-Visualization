<?php
require_once("../logica/interacaoTreemapIssues.php");
?>

<html>
  
  <head>    
    <meta charset="utf-8">
    <title>TDCodeVis - Treemap - Issues</title>
    <link rel="stylesheet" href="../css/treemap.css" charset="utf-8">
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
      <div class="conteudo-treemap">
        <svg width="1100" height="660"></svg>
        <script src="../js/treemapIssues.js" charset="utf-8"></script>
      </div>
    </section>

    <section class="legenda-issues">
      <h1>Issues Title: <?php echo "$issue"?> </h1>
        <form action="treemapIssues.php" method="POST">
          <input id="btn-estilo2" type="submit" name="back" value="Go back">
        </form>
    </section>

  </body>

</html>