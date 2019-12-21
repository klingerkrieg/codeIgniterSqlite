<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um relacionamento Um para UM (Professores x Coordenacoes)</p>
</div>

<?=formStart(site_url()."/coordenacoes/salvar");?>

<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>

<?=select("professores_id","Professor", $professores, $dados)?>


<?php
$btn1 = button("Salvar");
$btn2 = button("Novo",["href"=>site_url()."/coordenacoes"]);
print group($btn1, $btn2);
?>


<?=formEnd()?>



<?php
print formStart(site_url()."/coordenacoes", "GET");

	
	$inp = input("busca","",$_GET,"","Pesquisar");
	$btn = button("Pesquisar");
	print group($inp, $btn);

print formEnd();
?>



<?=tableHeader("Coordenações",
				"Editar",
				"Nome",
				"Professor",
				"Deletar")?>

<tbody>	
<?php

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