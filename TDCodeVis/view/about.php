<html>

  <head>
    <meta charset="utf-8">
    <title>TDCodeVis - About</title>
    <link rel="stylesheet" href="../css/style.css" charset="utf-8">
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
      <div class="conteudo-about">
        <h1 id="title-about">About</h1>
        <p id="p-about">TDCodeVis is a visualization tool used as support for the monitoring and management Technical Debt in source code.</p>
        <p id="p-about">The data used by TDCodeVis comes from a database of the Technical Debt Catalog provided by the TD-Tracker tool. TDCodeVis integrates with the TD-Tracker database to provide the application of visualization techniques in the Code Debt Catalog.</p>
        <p id="p-about">The TDCodeVis was developed as a continuation of the <a href="http://www.bv.fapesp.br/pt/bolsas/169423/analise-de-qualidade-de-software-um-estudo-usando-catalogo-de-dividas-tecnicas/">project of Scientific Initiation</a> of the student Bruno Santos de Lima, developed with the support of the Foundation for State Research Support of São Paulo - FAPESP.</p>
        <p id="p-about">An example of the database used by TDCodeVis can be found at: <a href="https://github.com/brunoslima/Technical-Debt-Catalog">GitHub - Technical Debt Catalog</a>. For more information contact us by email: bruno.s.lima@unesp.br</p>
      </div>
    </section>

  </body>
</html>