<?php include 'layout/header.php' ?>


<div class="ui message">
<p>Aqui você encontrará o relacionamento Muitos para Muitos (Aluno x Disciplinas).</p>
<p>Como criar matrícula (Uma numeração única).</p>
<p>Como data de cadastro (Data do sistema).</p>
</div>

<?=formStart("/alunos/salvar");?>


<?=flashMessage()?>


<?=new HTMLInput("id",["hidden","value"=>$dados])?>

<?=new HTMLInput("nome",["label"=>"Nome","required","value"=>$dados])?>

<?=new HTMLInput("turma",["label"=>"Turma","value"=>$dados])?>

<!-- tanto a matrícula como a data de cadastro são criadas no model -->
<?=new HTMLInput("matricula",["label"=>"Matrícula","value"=>$dados, "disabled"])?>

<?=new HTMLInput("data_cadastro",["label"=>"Data de cadastro","value"=>$dados, "disabled"])?>



<!-- Exemplo do Muitos para Muitos -->
<?php if (val($dados,"id") != ""): ?>
<div class="field">
<?=new HTMLSelect("disciplinas_id",["label"=>"Matricular", "options"=>$disciplinas]);?>

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
<!-- Fim Muitos para Muitos -->




<?php
$btn1 = new HTMLButton("Salvar");
$btn2 = new HTMLButton("Novo",["href"=>"/alunos"]);
print new HTMLGroup($btn1,$btn2);
?>


<?=formEnd()?>

<?php
print formStart("/alunos", "GET");

	$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","value"=>$_GET,"size"=>6]);
	$btn = new HTMLButton("Pesquisar");
	print new HTMLGroup($inp, $btn);

print formEnd();
?>


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