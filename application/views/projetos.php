<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um <b>autorelacionamento</b> MUITOS para MUITOS (Projetos x Projetos)</p>
</div>

<div class="ui grid">
<form		action="<?=site_url()?>/projetos/salvar"
	class="ui form column stackable grid" 
	method="post" enctype="multipart/form-data">


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



<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/projetos">Novo</a>
</div>
</form>
</div>



<!-- Formulário para busca -->
<div class="ui grid">
<form  action="<?=site_url()?>/usuarios"
	class="ui form column stackable grid" method="GET">
	<div class="fields">
		<div class="twelve wide field">
			<input name="busca" placeholder="Pesquisar."  value="<?=val($_GET,"busca")?>" />
		</div>
		<div class="four wide field">
			<button  class="ui blue button field" type="submit">Pesquisar</button>
		</div>
	</div>
</form>
</div>




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