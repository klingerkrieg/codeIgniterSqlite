<html>
<head>

<script>
site_url = '<?=site_url();?>';
base_url = '<?=base_url();?>';
</script>
  
  <script src="<?=base_url()?>static/jquery-3.4.1.min.js"></script>
  <script src="<?=base_url()?>static/jquery.mask.min.js"></script>
  <script src="<?=base_url()?>static/semantic/semantic.js"></script>
  <script src="<?=base_url()?>static/delete.js"></script>
  <script src="<?=base_url()?>static/mask.js"></script>
  <script src="<?=base_url()?>static/checkboxes.js"></script>

  <!-- script para salvar as notas -->
  <script src="<?=base_url()?>static/notas.js"></script>

  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/semantic/semantic.css" />
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/estilo.css" />

  <title>Sistema de exemplo</title>
</head>
<body>

<?php if (isset($_SESSION['email'])): ?>

<div class="ui top fixed menu">
  <div class="item">
    <img src="<?=base_url()?>/static/logo.png">
  </div>
  

  
  <!-- Exemplo de menu que só aparece se o usuário tiver permissão de Comum ou superior -->
  <?php if (Seguranca::temPermissao("Comum")) : ?>
    <a href="<?=site_url()?>/usuarios/" class="item">Usuários</a>
  <?php endif; ?>

  <!-- Exemplo de menu que abre ao passar o mouse -->
  <div href="#" class="ui dropdown item">
    Alunos
    <i class="dropdown icon"></i>
    <div class="menu">
      <a href="<?=site_url()?>/alunos/" class="item">Alunos</a>
      <a href="<?=site_url()?>/notas/" class="item">Lançar notas</a>
    </div>
  </div>

  <!-- Menu simples -->
  <a href="<?=site_url()?>/professores/" class="item">Professores</a>
  <a href="<?=site_url()?>/coordenacoes/" class="item">Coordenações</a>
  <a href="<?=site_url()?>/disciplinas/" class="item">Disciplinas</a>
  <a href="<?=site_url()?>/projetos/" class="item">Projetos</a>

  <a href="<?=site_url()?>/login/logout/" class="item"><?=$_SESSION['email']?> | Logout</a>

</div>

<?php endif; ?>

<div class="ui text container">
