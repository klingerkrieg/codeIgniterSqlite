</div> <!-- ui text container -->
<div id="rodape"></div>

<?php if (ENVIRONMENT == "development"): ?>
  <div class="ui yellow message debugger">
    <i class="close icon" onclick="$(this).closest('.message').transition('fade');"></i>
    <div class="header">
      Debugger
    </div>
  <?php

  #mostra os erros do PHP
  foreach($_SESSION["php_errors"] as $err){
    print "<p class='ui red message'>$err</p>";
  }


  #mostra as validações que não foram mostradas no formulário
  $errors = errors();
  foreach($errors as $val=>$err){
    print "<p class='ui red message'>
    O campo \"<b>$val</b>\" não existe no formulário, a validação do <b>controller</b> não está permitindo que o formulário seja salvo sem esse campo.</p>";
  }
  clear_errors();



  $logs = val($_SESSION,'logs');
  if ($logs != ""){
    foreach($logs as $log){
      print "<p>$log</p>";
    }
    $_SESSION["logs"] = [];
  }
  $logs = R::getDatabaseAdapter()->getDatabase()->getLogger()->getLogs();
  foreach($logs as $log){
    print "<p>$log</p>";
  }
  ?>
  <script>
  divDebugger = $("div.debugger");
  divDebugger.find('p:contains("CREATE TABLE"),p:contains("select"),p:contains("PRAGMA")').addClass("ui blue message").next().addClass("ui blue message");
  divDebugger.find('p:contains("ALTER TABLE"),p:contains("select"),p:contains("PRAGMA")').addClass("ui blue message").next().addClass("ui blue message");
  divDebugger.find('p:contains("DROP TABLE"),p:contains("select"),p:contains("PRAGMA")').addClass("ui blue message").next().addClass("ui blue message");
  divDebugger.find('p:contains("CREATE TEMPORARY"),p:contains("select"),p:contains("PRAGMA")').addClass("ui blue message").next().addClass("ui blue message");
  divDebugger.find('p:contains("CREATE INDEX"),p:contains("select"),p:contains("PRAGMA")').addClass("ui blue message").next().addClass("ui blue message");
  divDebugger.find('p:contains("SELECT"),p:contains("select"),p:contains("PRAGMA")').addClass("ui blue message").next().addClass("ui blue message");
  divDebugger.find('p:contains("UPDATE"),p:contains("update")').addClass("ui green message").next().addClass("ui green message");
  divDebugger.find('p:contains("INSERT"),p:contains("insert")').addClass("ui green message").next().addClass("ui green message");
  divDebugger.find('p:contains("DELETE"),p:contains("delete")').addClass("ui green message").next().addClass("ui green message");
  divDebugger.find('p:contains("SQLSTATE")').removeClass("blue green").addClass("ui red message").prev().addClass("ui red message");
  </script>
  </div>
<?php endif; ?>

</body>
</html>