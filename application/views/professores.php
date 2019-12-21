<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um relacionamento Um para UM (Professores x Coordenacoes)</p>
<p>E um relacionamento Um para Muitos (Professores x Disciplinas)</p>
<p>Também encontrará como criar campos do tipo radio e upload.</p>
</div>


<?=formStart(site_url()."/professores/salvar");?>


<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>

<?=input("matricula","Matrícula", $dados, "disabled")?>

<!-- as opcoes sao definidas no model e o controller as recupera,
 o usuário só poderá escolher um -->
<?=radio("vinculo","Vínculo", $tiposVinculo, $dados, 4, "required")?>

<!-- as opcoes sao definidas no model e o controller as recupera,
  nesse caso o usuário poderá marcar mais de uma -->
<?=checkbox("especialidades","Especialidades", $especialidades, $dados, 4)?>

<?=select("coordenacoes_id","Coordenação", $coordenacoes, $dados)?>

<!-- o restante do código do upload encontra-se no controller -->
<?=upload("foto","Foto","image",$dados,"uploads")?>





<!-- Exemplo do UM para Muitos -->
<?php if (val($dados,"id") != ""): ?>
<div class="field">
<?=select("disciplinas_id","Leciona", $disciplinas)?>

<?=tableHeader("Disciplinas que leciona",
				"Nome",
				"Optativa",
				"CH",
				"Remover")?>
	
<?php
$listagem = $dados->ownDisciplinasList;
foreach($listagem as $ln){

	print "<tr>";
	
	print "<td><a href='".site_url()."/disciplinas/index/{$ln->id}'> {$ln->nome} </a></td>";
	print "<td>{$optativaArr[$ln->optativa]}</td>";
	print "<td>{$ln->cargaHoraria} h/a</td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/professores/remover_disciplina/{$dados['id']}/{$ln->id}\")' > Remover </a></td>";
	print "</tr>";
}
?>
</tbody>
<?=tableBottom($listagem); ?>
</div>
<?php endif; ?>
<!-- Fim UM para Muitos -->





<?php
$btn1 = button("Salvar");
$btn2 = button("Novo",["href"=>site_url()."/professores"]);
print group($btn1, $btn2);
?>


<?=formEnd()?>

<?php
print formStart(site_url()."/professores", "GET");

	
	$inp = input("busca","",$_GET,"","Pesquisar");
	$btn = button("Pesquisar");
	$btn2 = button("Busca avançada",["href"=>site_url()."/professores/buscaAvancada"]);
	print group($inp, $btn, $btn2);

print formEnd();
?>


<?=tableHeader("Professores",
				"Editar",
				"Nome",
				"Matrícula",
				"Qtd. de disciplinas",
				"CH",
				"Coordenação",
				"Deletar")?>

<tbody>	
<?php

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/professores/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->matricula}</td>";

	$chTotal = 0;
	foreach($ln->ownDisciplinasList as $disc){
		$chTotal += $disc->carga_horaria;
	}

	print "<td>". count($ln->ownDisciplinasList) ."</td>";

	print "<td> {$chTotal} h/a</td>";
	

	#link para abrir diretamente a coordenação já em modo de edição
	@print "<td><a href='".site_url()."/coordenacoes/index/{$ln->coordenacoes->id}'> {$ln->coordenacoes->nome} </a> </td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/professores/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>