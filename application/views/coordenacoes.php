<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um relacionamento Um para UM (Professores x Coordenacoes)</p>
</div>

<?=formStart("/coordenacoes/salvar");?>

<?=flashMessage()?>


<?=new HTMLInput("id",["hidden","value"=>$dados])?>

<?=new HTMLInput("nome",["label"=>"Nome","required","value"=>$dados])?>

<?=new HTMLSelect("professores_id",["label"=>"Professor", "options"=>$professores, "value"=>$dados])?>

<?php
$btn1 = new HTMLButton("Salvar");
$btn2 = new HTMLButton("Novo",["href"=>"/coordenacoes"]);
print new HTMLGroup($btn1,$btn2);
?>


<?=formEnd()?>



<?php
print formStart("/coordenacoes", "GET");
	$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","value"=>$_GET,"size"=>6]);
	$btn = new HTMLButton("Pesquisar");
	print new HTMLGroup($inp, $btn);
print formEnd();
?>



<?php
print tableHeader("Coordenações",
				"Editar",
				"Nome",
				"Professor",
				"Deletar");

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/coordenacoes/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	@print "<td>{$ln->professores->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/coordenacoes/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>