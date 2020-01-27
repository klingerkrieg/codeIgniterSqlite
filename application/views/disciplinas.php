<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um <b>autorelacionamento</b> Muios para UM (Disciplinas x Disciplinas), uma disciplina pode ser dependente de outra</p>
<p>Um exemplo de um relacionamento Muitos para UM (Disciplinas x Professores)</p>
<p>E um exemplo de radio</p>
</div>

<?=formStart("/disciplinas/salvar");?>

<?=flashMessage()?>

<?=new HTMLInput("id",["hidden","value"=>$dados])?>

<?=new HTMLInput("nome",["label"=>"Nome","required","value"=>$dados])?>

<?=new HTMLRadio("optativa",["label"=>"Optativa","options"=>$optativaArr,"required","value"=>$dados])?>

<?=new HTMLSelect("carga_horaria",["label"=>"Carga Horária","options"=>$opcoesCargaHoraria,"required","value"=>$dados])?>

<?=new HTMLSelect("professores_id",["label"=>"Professor","options"=>$professores,"value"=>$dados])?>

<?=new HTMLSelect("requisito_id",["label"=>"Pré-requisito","options"=>$disciplinas,"value"=>$dados])?>


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






<?php
$btn1 = new HTMLButton("Salvar");
$btn2 = new HTMLButton("Novo",["href"=>"/disciplinas"]);
print new HTMLGroup($btn1,$btn2);
?>

<?=formEnd()?>

<?php
print formStart("/disciplinas", "GET");

	$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","value"=>$_GET,"size"=>6]);
	$btn = new HTMLButton("Pesquisar");
	print new HTMLGroup($inp, $btn);

print formEnd();
?>

<?php
print tableHeader("Disciplinas",
				"Editar",
				"Nome",
				"Optativa",
				"CH",
				"Professor",
				"Pré-requisito",
				"Deletar");

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