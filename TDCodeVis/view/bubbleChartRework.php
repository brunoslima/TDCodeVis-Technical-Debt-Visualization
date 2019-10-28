<?php
require_once("../logica/interacaoBubbleChartRework.php");
?>

<html>
  
  <head>    
    <meta charset="utf-8">
    <title>TDCodeVis - Bubble Chart - Rework</title>
    <link rel="stylesheet" href="../css/bubblechart.css" charset="utf-8">
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
      <div class="conteudo-bubblechart">
        <svg id="vis" width="1100" height="660"></svg>
        <script src="../js/bubbleChartRework.js" charset="utf-8"></script>
      </div>
      <div class="filtro-bubblechart">
        
        <form action="bubbleChartRework.php" method="POST">
          
          <h2>Select rework view mode:</h2>
          <input type="radio" name="mode" value="absolut" <?php if($_SESSION['reworkMode'] == 'absolut')echo 'checked';?>> Absolute Amount<br>
          <input type="radio" name="mode" value="individual" <?php if($_SESSION['reworkMode'] == 'individual')echo 'checked';?>> Individual Amount

          <h1>Current Version: <?php echo "$vAtual";?></h1>
          <h2>Select vesion:</h2>
          <select name="versoes">
            <?php for ($i=0; $i < count($version); $i++) { ?>
            <option value="<?php echo "$version[$i]-$i";?>"><?php $v = $i+1; echo "$v";?></option>
            <?php } ?>
          </select>

          <h2>Select severity:</h2>
          <input type="checkbox" name="key[]" value="5" checked>Blocker<br>
          <input type="checkbox" name="key[]" value="4" checked>Critical<br>
          <input type="checkbox" name="key[]" value="3" checked>Major<br>
          <input type="checkbox" name="key[]" value="2" checked>Minor<br>
          <input type="checkbox" name="key[]" value="1" checked>Info<br>

          <input id="btn-estilo" type="submit" name="filtrar" value="Filter">
        </form>
      </div>
    </section>

    <div class="legenda">
      <h2>SUBTITLE:</h2>
      <div id=circulo1></div><h1>Blocker</h1>
      <div id=circulo2></div><h1>Critical</h1>
      <div id=circulo3></div><h1>Major</h1>
      <div id=circulo4></div><h1>Minor</h1>
      <div id=circulo5></div><h1>Info</h1>
    </div>

    <section class="modal-page">
      <section class="modal-box">
        <div class="close">X</div>
        <script>
          document.querySelector('.close').addEventListener('click',
            function(){
              document.querySelector('.modal-page').style.display = 'none';
              document.querySelector('.modal-box')
            });
        </script>
      </section>
    </section>

  </body>

</html>