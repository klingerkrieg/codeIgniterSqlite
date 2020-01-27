<?php include 'layout/header.php' ?>


<div class="ui message">
<p>Aqui você encontrará como adicionar dados na tabela auxiliar de um relacionamento de Muitos para Muitos (Aluno x Disciplinas)</p>
<p>Você também encontrará um exemplo de Ajax (arquivo: static/notas.js).</p>
</div>

<?php
// formulario de busca
print formStart("/usuarios", "GET");

	print flashMessage();

	print new HTMLSelect("id",["label"=>"Disciplina","options"=>$disciplinas, "value"=>$_GET]);
	print new HTMLButton("Pesquisar");

print formEnd();



#listagem dos alunos da disciplina
if (isset($dados["id"])):
	
	print tableHeader("Alunos",
					"Aluno",
					"Nota 1",
					"Nota 2",
					"Nota final");

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