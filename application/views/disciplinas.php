<?php include 'layout/header.php' ?>

<div class="ui grid">


<form		action="<?=site_url()?>/disciplinas/salvar"
	class="ui form column stackable grid" 
	method="post" enctype="multipart/form-data">


<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>

<?=radio("optativa","Optativa", $optativaArr, $dados, 2, "required")?>

<?=select("carga_horaria","Carga Horária", $opcoesCargaHoraria, $dados, "required")?>

<?=select("professores_id","Professor", $professores, $dados)?>

<?=select("requisito_id","Pré-requisito", $disciplinas, $dados)?>





<?php if (val($dados,'id') != ""): ?>
<div class="field">
<?=tableHeader("Disciplinas dependentes",
				"Nome",
				"Optativa",
				"CH",
				"Professor",
				"Remover")?>
	
<?php
$listagem = $dados->ownRequisitoList;
foreach($listagem as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/disciplinas/index/{$ln->id}'> {$ln->nome} </a></td>";
	print "<td>{$optativaArr[$ln->optativa]}</td>";
	print "<td>{$ln->cargaHoraria} h/a</td>";
	@print "<td>{$ln->professores->nome}</td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/disciplinas/remover_requisito/{$dados['id']}/{$ln->id}\")' > Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>

<?=tableBottom($listagem); ?>

</div>
<?php endif; ?>






<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/disciplinas">Novo</a>
</div>


</form>
</div>


<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios" method="GET">
	<div class="fields">
		<input name="busca" placeholder="Pesquisar."  value="<?=val($_GET,"busca")?>" />
		<button  class="ui blue button" type="submit">Pesquisar</button>
	</div>
</form>



<?=tableHeader("Disciplinas",
				"Editar",
				"Nome",
				"Optativa",
				"CH",
				"Professor",
				"Pré-requisito",
				"Deletar")?>

<tbody>	
<?php

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/disciplinas/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$optativaArr[$ln->optativa]}</td>";
	print "<td>{$ln->cargaHoraria} h/a</td>";
	@print "<td>{$ln->professores->nome}</td>";
	@print "<td>{$ln->requisito->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/disciplinas/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>