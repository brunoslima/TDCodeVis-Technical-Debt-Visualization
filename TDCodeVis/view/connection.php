<?php
require_once("../logica/connectionDB.php");
?>

<html>
  <head>
    <meta charset="utf-8">
    <title>TDCodeVis - Connection</title>
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
      <h1 id="title">Connection Database</h1>
      <div class="conteudo-interno">
        <p>Configure your database</p>
        <p>Status: <?php echo "$status";?></p>
        <form action="connection.php" method="POST">
          <label>Host</label><input type="text" name="host" placeholder="ex: localhost" <?php if($status=="Connected") echo "value=\"$host\";"?> >
          <label>Port</label><input type="text" name="port" placeholder="ex: 5432" <?php if($status=="Connected") echo "value=\"$port\";"?> >
          <label>Database</label><input type="text" name="db" placeholder="ex: tdr-software" <?php if($status=="Connected") echo "value=\"$db\";"?> >
          <label>User</label><input type="text" name="user" placeholder="ex: Admin" <?php if($status=="Connected") echo "value=\"$user\";"?> >
          <label>Password</label><input type="password" name="password" placeholder="ex: Password" <?php if($status=="Connected") echo "value=\"$pass\";"?> >
          <input id="btn" type="submit" name="connectBTN" value="Connect">
        </form>
      </div>
    </section>

    <script>
          document.querySelector('#btn').addEventListener('click',
            function(){
              document.querySelector('.modal-page-load').style.display = 'flex';
            });
    </script>

    <section class="modal-page-load" style="<?php echo "$connectClick"?>">
      <section class="modal-box-load">
          <div class="loader"></div>
          <p>Connecting to the database, please wait!</p>
      </section>
    </section>

  </body>
  
</html>