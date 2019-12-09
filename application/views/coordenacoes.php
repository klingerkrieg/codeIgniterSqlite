<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um relacionamento Um para UM (Professores x Coordenacoes)</p>
</div>

<div class="ui grid">
<form		action="<?=site_url()?>/coordenacoes/salvar"
	class="ui form column stackable grid" 
	method="post" enctype="multipart/form-data">


<?=flashMessage()?>

<?=input("id","", $dados, "hidden")?>

<?=input("nome","Nome", $dados, "required")?>

<?=select("professores_id","Professor", $professores, $dados)?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/coordenacoes">Novo</a>
</div>

</form>
</div>

<div class="ui grid">
<form  action="<?=site_url()?>/usuarios"
	class="ui form column stackable grid" method="GET">
	<div class="fields">
		<div class="twelve wide field">
			<input name="busca" placeholder="Pesquisar."  value="<?=val($_GET,"busca")?>" />
		</div>
		<div class="four wide field">
			<button  class="ui blue button field" type="submit">Pesquisar</button>
		</div>
	</div>
</form>
</div>


<?=tableHeader("Coordenações",
				"Editar",
				"Nome",
				"Professor",
				"Deletar")?>

<tbody>	
<?php

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/coordenacoes/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	@print "<td>{$ln->professores->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/coordenacoes/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>