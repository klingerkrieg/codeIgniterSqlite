<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um <b>autorelacionamento</b> MUITOS para MUITOS (Projetos x Projetos)</p>
</div>

<?=formStart(site_url()."/projetos/salvar");?>


<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>




<!-- Exemplo do Muitos para Muitos -->
<?php if (val($dados,"id") != ""): ?>
<div class="field">
<?=select("relacionado_id","Relacionar projeto", $projetos)?>

<?=tableHeader("Projetos relacionados",
				"Nome",
				"Remover")?>
	
<?php
$listagem = $dados->ownProjrelacionadosList;
foreach($listagem as $tabAuxiliar){

	$ln = $tabAuxiliar->relacionado;
	if ($ln->id == $dados["id"]){	
		$ln = $tabAuxiliar->projetos;
	}

	print "<tr>";
	print "<td><a href='".site_url()."/projetos/index/{$ln->id}'> {$ln->nome} </a></td>";
	print "<td><a onclick='confirmDelete(\"".site_url()."/projetos/remover_relacao/{$dados['id']}/{$tabAuxiliar->id}\")' > Remover </a></td>";
	print "</tr>";


}
?>
</tbody>
<?=tableBottom($listagem); ?>
</div>
<?php endif; ?>
<!-- Fim Muitos para Muitos -->



<?php
$btn1 = button("Salvar");
$btn2 = button("Novo",["href"=>site_url()."/projetos"]);
print group($btn1, $btn2);
?>


<?=formEnd()?>

<?php
print formStart(site_url()."/projetos", "GET");

	
	$inp = input("busca","",$_GET,"","Pesquisar");
	$btn = button("Pesquisar");
	print group($inp, $btn);

print formEnd();
?>




<!-- Listagem -->
<?=tableHeader("Coordenações",
				"Editar",
				"Nome",
				"Deletar")?>

<tbody>	
<?php

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/projetos/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/projetos/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>