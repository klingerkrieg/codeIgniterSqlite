<?php include 'layout/header.php' ?>


<div class="ui message">
  <p>Aqui você encontrará o básico para a criação de usuários e permissões.</p>
</div>


<?=formStart("/usuarios/salvar");?>


<?=flashMessage()?>

<?=new HTMLInput("id",["hidden","value"=>$dados])?>

<?=new HTMLInput("nome",["label"=>"Nome","required","value"=>$dados])?>

<?=new HTMLInput("email",["label"=>"E-mail","required","value"=>$dados])?>

<?=new HTMLInput("senha",["label"=>"Senha","required","type"=>"password"])?>

<?=new HTMLInput("senhaConfirm",["label"=>"Confirmação da senha","required","type"=>"password"])?>

<?php
#verifica se tem permissao para escolher o nível do usuário
if (Seguranca::temPermissao("Admin")):
	print new HTMLSelect("nivel",["label"=>"Nível de acesso", "options"=>$niveis, "value"=>$dados]);
endif;
?>


<?php
$btn1 = new HTMLButton("Salvar");
$btn2 = new HTMLButton("Novo",["href"=>"/usuarios"]);
print new HTMLGroup($btn1,$btn2);
?>

<?=formEnd()?>



<?php
print formStart("/usuarios", "GET");

	$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","value"=>$_GET,"size"=>6]);
	$btn = new HTMLButton("Pesquisar");
	$btn2 = new HTMLButton("Busca avançada",["href"=>"/usuarios/buscaAvancada"]);
	print new HTMLGroup($inp, $btn,$btn2);

print formEnd();
?>


<?php
print tableHeader("Usuários",
				"Editar",
				"Nome",
				"E-mail",
				"Deletar");

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/usuarios/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->email}</td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/usuarios/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>