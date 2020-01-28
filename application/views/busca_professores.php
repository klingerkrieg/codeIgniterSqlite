<?php include 'layout/header.php' ?>

<?=formStart("/professores/buscaAvancada","GET");?>

<?=flashMessage()?>

<?=new HTMLInput("nome",["label"=>"Nome","value"=>$_GET]);?>

<?=new HTMLInput("matricula",["label"=>"Matrícula","value"=>$_GET]);?>

<?=new HTMLCheckbox("vinculo",["label"=>"Vínculo","options"=>$tiposVinculo,"value"=>$_GET])?>

<?=new HTMLCheckbox("especialidades",["label"=>"Especialidades","options"=>$especialidades,"value"=>$_GET])?>

<?=new HTMLSelect("coordenacoes_id",["label"=>"Coordenação","options"=>$coordenacoes,"value"=>$_GET])?>

<?=new HTMLCheckbox("disciplinas_id",["label"=>"Disciplina","options"=>$disciplinas,"value"=>$_GET])?>



<?php
$btn1 = new HTMLButton("Buscar");
$btn2 = new HTMLButton("Nova busca",["href"=>"/professores/buscaAvancada"]);
print new HTMLGroup($btn1,$btn2);
?>

<?=formEnd();?>


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