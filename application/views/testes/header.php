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
  <script src="<?=base_url()?>static/css_framework_test.js"></script>

  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/semantic/semantic.css" />
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/estilo.css" />

  <title>Sistema de exemplo</title>
</head>
<body>

<div class="ui top fixed menu">
  <div class="item">
    <img src="<?=base_url()?>/static/logo.png">
  </div>
  

  
  <!-- Exemplo de menu que só aparece se o usuário tiver permissão de Admin -->
  <a href="<?=site_url()?>/usuarios/" class="item">Sistema</a>
  <a href="<?=site_url()?>/testes/" class="item">Testes de unidade</a>
  <a href="<?=site_url()?>/testes/semantic" class="item">Semantic</a>

</div>


<div class="ui text container">
