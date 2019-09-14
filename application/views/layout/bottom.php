</div><!-- ui text container -->


<?php if (ENVIRONMENT == "development"): ?>
  <div class="ui yellow message debugger">
    <i class="close icon" onclick="$(this).closest('.message').transition('fade');"></i>
    <div class="header">
      Debugger
    </div>
  <?php

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
  $('p:contains("SELECT"),p:contains("select")').addClass("ui blue message").next().addClass("ui blue message");
  $('p:contains("UPDATE"),p:contains("update")').addClass("ui green message").next().addClass("ui green message");
  $('p:contains("INSERT"),p:contains("insert")').addClass("ui green message").next().addClass("ui green message");
  $('p:contains("DELETE"),p:contains("delete")').addClass("ui green message").next().addClass("ui green message");
  $('p:contains("SQLSTATE")').removeClass("blue green").addClass("ui red message").prev().addClass("ui red message");
  </script>
  </div>
<?php endif; ?>

</body>
</html>