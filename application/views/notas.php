<?php include 'layout/header.php' ?>


<div class="ui message">
<p>Aqui você encontrará como adicionar dados na tabela auxiliar de um relacionamento de Muitos para Muitos (Aluno x Disciplinas)</p>
<p>Você também encontrará um exemplo de Ajax (arquivo: static/notas.js).</p>
</div>


<?=flashMessage()?>

<!-- formulario de busca -->
<div class="ui grid">
<form   action="<?=site_url()?>/notas"
		class="ui form column stackable grid" method="GET">
		
	<?=select("id","Disciplina", $disciplinas, $_GET)?>

	<div class="four wide field">
		<button  class="ui blue button field" type="submit">Pesquisar</button>
	</div>

</form>
</div>

<?=tableHeader("Alunos",
				"Aluno",
				"Nota 1",
				"Nota 2",
				"Nota final")?>

<tbody class='ui form'>	
<?php

#listagem dos alunos da disciplina
if (isset($dados["id"])):

$actPage = paginaAtual();
$listagem = $dados->ownAlunosdisciplinasList;
foreach($dados->ownAlunosdisciplinasList as $tabAux){

	print "<tr>";
	
	print "<td>{$tabAux->alunos->nome}</td>";
	print "<td><input id='nota1' class='tableInput' value='{$tabAux->nota1}' alunosdisciplinas='{$tabAux->id}'></td>";
	print "<td><input id='nota2' class='tableInput' value='{$tabAux->nota2}' alunosdisciplinas='{$tabAux->id}'></td>";

	print "<td> <span id='final{$tabAux->id}'>". (($tabAux->nota1 + $tabAux->nota2)/2) ."</span></td>";

	print "</tr>";
}

print tableBottom($listagem);
endif;
#fim da listagem dos alunos da disciplina

include 'layout/bottom.php';
?>