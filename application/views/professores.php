<?php include 'layout/header.php' ?>

<div class="ui grid">


<form		action="<?=site_url()?>/professores/salvar"
	class="ui form column stackable grid" 
	method="post" enctype="multipart/form-data">


<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>

<?=input("matricula","Matrícula", $dados, "disabled")?>

<?=radio("vinculo","Vínculo", $tiposVinculo, $dados, 4, "required")?>

<?=select("coordenacoes_id","Coordenação", $coordenacoes, $dados)?>



<?=upload("foto","Foto","image",$dados,"uploads")?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/professores">Novo</a>
</div>


</form>
</div>


<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios" method="GET">
	<div class="fields">
		<input name="busca" placeholder="Pesquisar."  value="<?=val($_GET,"busca")?>" />
		<button  class="ui blue button" type="submit">Pesquisar</button>
		<a class="ui button" href="<?=site_url()?>/professores/buscaAvancada">Busca avançada</a>
	</div>
</form>



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