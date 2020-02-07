<?php include 'layout/header.php' ?>


<div class="ui message">
<p>Aqui você encontrará como adicionar dados na tabela auxiliar de um 
	relacionamento de Muitos para Muitos (Aluno x Disciplinas)</p>
<p>Você também encontrará dois exemplos de Ajax (arquivo: static/notas.js).
	<ul>
		<li>O primeiro exemplo são campos de autocompletar, temos um do tipo Search e outro do tipo Select</li>
		<li>O segundo exemplo é uma demonstração de como fazer o ajax manualmente, no caso, para lançar as notas sem ter que submeter a página.</li>
	</ul>
</p>
</div>




<?php

// formulario de busca
print formStart("/notas", "GET");

	print flashMessage();

	print new HTMLRadio("input",["label"=>"Ajax tipo Search",
								"options"=>[0=>"Ativar Search"],"value"=>$_GET]);

	print new HTMLSearch("id",["label"=>"Disciplina",
								"url"=>"/notas/disciplinas/?q={query}",
								"value"=>$_GET]);

	print new HTMLRadio("input",["id"=>"input2", "label"=>"Ajax tipo Select",
								"options"=>[1=>"Ativar Select"],"value"=>$_GET]);

	print new HTMLSelect("id",["id"=>"disciplinaSearch",
								"label"=>"Disciplina",
								"url"=>"/notas/disciplinas/?q={query}",
								"value"=>$_GET]);
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
		#print "<td><div class='ui input'><input id='nota1' class='tableInput' value='{$tabAux->nota1}' alunosdisciplinas='{$tabAux->id}'></div></td>";
		#print "<td><div class='ui input'><input id='nota2' class='tableInput' value='{$tabAux->nota2}' alunosdisciplinas='{$tabAux->id}'></div></td>";

		print "<td>".new HTMLInput("nota1",["class"=>"tableInput","value"=>$tabAux->nota1,"attributes"=>["alunosdisciplinas"=>$tabAux->id]])."</td>";
		print "<td>".new HTMLInput("nota2",["class"=>"tableInput","value"=>$tabAux->nota2,"attributes"=>["alunosdisciplinas"=>$tabAux->id]])."</td>";

		print "<td> <span id='final{$tabAux->id}'>". (($tabAux->nota1 + $tabAux->nota2)/2) ."</span></td>";

		print "</tr>";
	}
	
	print tableBottom($listagem);
endif;
#fim da listagem dos alunos da disciplina

include 'layout/bottom.php';
?>