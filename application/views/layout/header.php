<html>
<head>
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/semantic/semantic.css" />
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/estilo.css" />

  <script src="<?=base_url()?>static/jquery-3.4.1.min.js"></script>
  <script src="<?=base_url()?>static/jquery.mask.min.js"></script>
  <script src="<?=base_url()?>static/semantic/semantic.js"></script>
  <script src="<?=base_url()?>static/delete.js"></script>
  <script src="<?=base_url()?>static/mask.js"></script>
  <script src="<?=base_url()?>static/checkboxes.js"></script>

  <title>Sistema de exemplo</title>
</head>
<body>

<?php if (isset($_SESSION['email'])): ?>

<div class="ui top fixed menu">
  <div class="item">
    <img src="<?=base_url()?>/static/logo.png">
  </div>
  
  <?php if (Seguranca::temPermissao("Admin")) : ?>
    <a href="<?=site_url()?>/usuarios/" class="item">Usuários</a>
  <?php endif; ?>

  <a href="<?=site_url()?>/alunos/" class="item">Alunos</a>
  <a href="<?=site_url()?>/professores/" class="item">Professores</a>
  <a href="<?=site_url()?>/coordenacoes/" class="item">Coordenações</a>
  <a href="<?=site_url()?>/disciplinas/" class="item">Disciplinas</a>
  
  <a href="<?=site_url()?>/login/logout/" class="item"><?=$_SESSION['email']?> | Logout</a>

</div>

<?php endif; ?>

<div class="ui text container">
