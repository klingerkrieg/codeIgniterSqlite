<?php include 'layout/header.php' ?>

<div class="ui message">
<p>Aqui você encontrará um <b>autorelacionamento</b> MUITOS para MUITOS (Projetos x Projetos)</p>
</div>

<?=formStart("/projetos/salvar");?>


<?=flashMessage()?>

<?=new HTMLInput("id",["hidden","value"=>$dados])?>

<?=new HTMLInput("nome",["label"=>"Nome","required","value"=>$dados])?>


<!-- Exemplo do Muitos para Muitos -->
<?php if (val($dados,"id") != ""): ?>
<div class="field">
	<?=new HTMLSelect("relacionado_id",["label"=>"Relacionar projeto","options"=>$projetos])?>

	<?=tableHeader("Projetos relacionados",
					"Nome",
					"Remover")?>
		
	<?php
	$listagem = $dados->ownProjrelacionadosList;
	foreach($listagem as $tabAuxiliar){

		$ln = $tabAuxiliar->relacionado;
		if ($ln->id == $dados["id"]){	
			$ln = $tabAuxiliar->projetos;
		}

		print "<tr>";
		print "<td><a href='".site_url()."/projetos/index/{$ln->id}'> {$ln->nome} </a></td>";
		print "<td><a onclick='confirmDelete(\"".site_url()."/projetos/remover_relacao/{$dados['id']}/{$tabAuxiliar->id}\")' > Remover </a></td>";
		print "</tr>";


	}
	?>
	</tbody>
	<?=tableBottom($listagem); ?>
</div>
<?php endif; ?>
<!-- Fim Muitos para Muitos -->


<?php
$btn1 = new HTMLButton("Salvar");
$btn2 = new HTMLButton("Novo",["href"=>"/projetos"]);
print new HTMLGroup($btn1,$btn2);
?>


<?=formEnd()?>

<?php
print formStart("/projetos", "GET");

	$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","value"=>$_GET,"size"=>6]);
	$btn = new HTMLButton("Pesquisar");
	print new HTMLGroup($inp, $btn);

print formEnd();
?>




<!-- Listagem -->
<?php
print tableHeader("Coordenações",
				"Editar",
				"Nome",
				"Deletar");

$actPage = paginaAtual();

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/projetos/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/projetos/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
	print "</tr>";
}

print tableBottom($listaPaginada);

include 'layout/bottom.php';
?>