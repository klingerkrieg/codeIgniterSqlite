<?php include 'layout/header.php' ?>

<div class="ui grid">


<form		action="<?=site_url()?>/alunos/salvar"
	class="ui form column stackable grid" 
	method="post" enctype="multipart/form-data">


<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>

<?=input("turma","Turma", $dados)?>

<?=input("matricula","Matrícula", $dados, "disabled")?>

<?=input("data_cadastro","Data de cadastro", $dados, "disabled")?>




<?php if (val($dados,"id") != ""): ?>
<div class="field">
<?=select("disciplinas_id","Matricular", $disciplinas)?>

<?=tableHeader("Disciplinas matriculadas",
				"Nome",
				"Optativa",
				"CH",
				"Professor",
				"Remover")?>
	
<?php
$listagem = $dados->ownAlunosdisciplinasList;
foreach($listagem as $tabAuxiliar){

	$ln = $tabAuxiliar->disciplinas;
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/disciplinas/index/{$ln->id}'> {$ln->nome} </a></td>";
	print "<td>{$optativaArr[$ln->optativa]}</td>";
	print "<td>{$ln->cargaHoraria} h/a</td>";
	@print "<td>{$ln->professores->nome}</td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/alunos/remover_disciplina/{$dados['id']}/{$ln->id}\")' > Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>

<?=tableBottom($listagem); ?>

</div>
<?php endif; ?>





<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/alunos">Novo</a>
</div>


</form>
</div>


<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios" method="GET">
	<div class="fields">
		<input name="busca" placeholder="Pesquisar."  value="<?=val($_GET,"busca")?>" />
		<button  class="ui blue button" type="submit">Pesquisar</button>
	</div>
</form>



<?=tableHeader("Alunos",
				"Editar",
				"Nome",
				"Matrícula",
				"Qtd. de disciplinas",
				"Deletar")?>

<tbody>	
<?php

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/alunos/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->matricula}</td>";

	print "<td>". count($ln->ownAlunosdisciplinasList) ."</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/alunos/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>