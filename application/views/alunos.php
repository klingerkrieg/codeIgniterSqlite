<?php include 'layout/header.php' ?>


<div class="ui message">
<p>Aqui você encontrará o relacionamento Muitos para Muitos (Aluno x Disciplinas).</p>
<p>Como criar matrícula (Uma numeração única).</p>
<p>Como data de cadastro (Data do sistema).</p>
</div>

<?=formStart(site_url()."/alunos/salvar");?>


<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>

<?=input("turma","Turma", $dados)?>


<!-- tanto a matrícula como a data de cadastro são criadas no model -->
<?=input("matricula","Matrícula", $dados, "disabled")?>

<?=input("data_cadastro","Data de cadastro", $dados, "disabled")?>



<!-- Exemplo do Muitos para Muitos -->
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
<!-- Fim Muitos para Muitos -->




<?php
$btn1 = button("Salvar");
$btn2 = button("Novo",["href"=>site_url()."/alunos"]);
print group($btn1, $btn2);
?>


<?=formEnd()?>

<?php
print formStart(site_url()."/alunos", "GET");

	
	$inp = input("busca","",$_GET,"","Pesquisar");
	$btn = button("Pesquisar");
	print group($inp, $btn);

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