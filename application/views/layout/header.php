<html>
<head>
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/semantic/semantic.css" />
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/estilo.css" />

  <script src="<?=base_url()?>static/jquery-3.4.1.min.js"></script>
  <script src="<?=base_url()?>static/jquery.mask.min.js"></script>
  <script src="<?=base_url()?>static/semantic/semantic.js"></script>
  <script src="<?=base_url()?>static/delete.js"></script>
  <script src="<?=base_url()?>static/mask.js"></script>
</head>
<body>

<?php if (isset($_SESSION['email'])): ?>

<div class="ui top fixed menu">
  <div class="item">
    <img src="<?=base_url()?>/static/logo.png">
  </div>
  <a href="<?=site_url()?>/usuarios/" class="item">Usuários</a>
  <a href="<?=site_url()?>/setores/" class="item">Setores</a>
  <a href="<?=site_url()?>/grupos/" class="item">Grupos</a>
  <a href="<?=site_url()?>/login/logout/" class="item"><?=$_SESSION['email']?> | Logout</a>

</div>

<?php endif; ?>

<div class="ui text container">
