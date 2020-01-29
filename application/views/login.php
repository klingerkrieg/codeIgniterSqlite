<?php include 'layout/header.php' ?>

<div class="ui middle aligned center aligned grid">
  <div class="column">
    <h2 class="ui teal image header">
      <img src="<?=base_url()?>/static/logo.png" class="image">
      <div class="content">
        Login do sistema
      </div>
    </h2>

  
  
  <?=formStart("/login/login","POST",["grid"=>false])?>
  
  <?=flashMessage()?>

  <?=new HTMLInput("email",["icon"=>"user","placeholder"=>"Digite seu e-mail"])?>

  <?=new HTMLInput("senha",["icon"=>"lock","type"=>"password","placeholder"=>"Digite sua senha"])?>

  <?=new HTMLButton("Entrar",["class"=>"fluid large", "color"=>"teal"])?>

  <?=formEnd(false)?>

    <div class="ui message">
      Novo usuário? <a href="<?=site_url()?>/login/cria_usuario/">Cadastre-se</a>
    </div>
  </div>
</div>




<?php include 'layout/bottom.php' ?>