<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um relacionamento Um para UM (Professores x Coordenacoes)</p>
<p>E um relacionamento Um para Muitos (Professores x Disciplinas)</p>
<p>Também encontrará como criar campos do tipo radio e upload.</p>
</div>


<?=formStart("/professores/salvar");?>

<?=flashMessage()?>


<?=new HTMLInput("id",["hidden","value"=>$dados])?>

<?=new HTMLInput("nome",["label"=>"Nome","required","value"=>$dados])?>

<?=new HTMLInput("matricula",["label"=>"Matrícula","value"=>$dados, "disabled"])?>

<!-- as opcoes sao definidas no model e o controller as recupera,
 o usuário só poderá escolher um -->
<?=new HTMLRadio("vinculo",["label"=>"Vínculo", "options"=>$tiposVinculo, "value"=>$dados, "required"])?>

<!-- as opcoes sao definidas no model e o controller as recupera,
  nesse caso o usuário poderá marcar mais de uma -->
<?=new HTMLCheckbox("especialidades",["label"=>"Especialidades", "options"=>$especialidades, "value"=>$dados])?>

<?=new HTMLSelect("coordenacoes_id",["label"=>"Coordenação", "options"=>$coordenacoes, "value"=>$dados])?>

<!-- o restante do código do upload encontra-se no controller -->
<?=new HTMLUpload("foto",["label"=>"Foto", "fileType"=>"image", "path"=>"uploads", "value"=>$dados])?>


<!-- Exemplo do UM para Muitos -->
<?php if (val($dados,"id") != ""): ?>
<div class="field">
	<?=new HTMLSelect("disciplinas_id",["label"=>"Leciona", "options"=>$disciplinas])?>

	<?=tableHeader("Disciplinas que leciona",
					"Nome",
					"Optativa",
					"CH",
					"Remover");

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
$btn1 = new HTMLButton("Salvar");
$btn2 = new HTMLButton("Novo",["href"=>"/professores"]);
print new HTMLGroup($btn1,$btn2);
?>


<?=formEnd()?>

<?php
print formStart("/professores", "GET");

$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","value"=>$_GET,"size"=>6]);
$btn = new HTMLButton("Pesquisar");
$btn2 = new HTMLButton("Busca avançada",["href"=>"/professores/buscaAvancada"]);
print new HTMLGroup($inp, $btn, $btn2);

print formEnd();
?>


<?php
print tableHeader("Professores",
				"Editar",
				"Nome",
				"Matrícula",
				"Qtd. de disciplinas",
				"CH",
				"Coordenação",
				"Deletar");
				

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