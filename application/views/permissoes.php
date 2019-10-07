<?php include 'layout/header.php' ?>

<div class="ui grid">

<form class="ui form column stackable grid" action="<?=site_url()?>/permissoes/salvar" method="post">


<?=$this->session->flashdata('error')?>
<?=$this->session->flashdata('success')?>
<?=$this->session->flashdata('warning')?>

<input type="hidden" name="id" value="<?=val($dados,'id')?>">

<div class="field">
	<label>Nome
		<input type="text" name="nome" value="<?=val($dados,'nome')?>">
		<?=form_error('nome')?>
	</label>
</div>


<div class="field">
	<?php
	if (val($dados,"admin") == "1"){
		$checked = "checked";
	} else {
		$checked = "";
	}
	?>
	<input type="hidden" name="admin" value="0">
	<label><input type="checkbox" name="admin" value="1" <?=$checked?> >Admin</label>
</div>



<div class="field" check_from="controles" >
	<label><input type="checkbox" check_to="controles" >Marcar todos</label>
	<br/>

	<div class="ui stackable three column grid rem_maxwidth">
	<input type="hidden" name="controles[]" value="">
	
	<?php foreach($arvore["controles"] as $c=>$methods):

		$checked = "";
		if ( isset($dados["controles"]) && in_array($c,$dados["controles"]) ){
			$checked = "checked";
		}
		?>
		<div class="column">
		<label class='field flex bold'>
			<input type="checkbox" check_to="t<?=$c?>" >
			<?=$c?>
		</label>

		<?php foreach($methods as $m):
		
			$checked = "";
			if ( isset($dados["controles"]) && in_array($m,$dados["controles"]) ){
				$checked = "checked";
			}
			?>
			<label class='field flex'>
				<input type="checkbox" name="controles[]" value="<?=$m?>" <?=$checked?> check_from="t<?=$c?>" >
				<?=$m?>
			</label>
		<?php endforeach; ?>

		</div>
	<?php endforeach; ?>
	<?=form_error('controles')?>
	</div>
</div>



<?php if (val($dados,'id') != ""): ?>

<div class="field">
	<label>Adicionar usuário
		
		<select name="usuario_id">
			<option value=""></option>
			<?php
			foreach($usuarios as $p){
				print "<option value='{$p['id']}'>{$p['nome']}</option>";
			}
			?>
		</select>
	</label>
</div>

<div class="field">
<table class="ui celled table">
	<thead>
		<tr>
			<th colspan="3">Usuários com essa permissão</th>
		</tr>
		<tr>
			<th>Nome</th>
			<th>E-mail</th>
			<th>Remover usuário</th>
		</tr>
	</thead>
	<tbody>
	
<?php


foreach($dados->ownUsuariosList as $user){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/usuarios/index/{$user->id}'> {$user->nome} </a></td>";
	print "<td>{$user->email}</td>";
	print "<td><a href='".site_url()."/permissoes/remover_usuario/{$dados['id']}/{$user->id}'> Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>
</table>
</div>

<?php endif; ?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/permissoes">Novo</a>
</div>

</form>


</div>


<form class="ui form column stackable grid" action="<?=site_url()?>/permissoes" method="GET">
	<div class="fields">
		<input name="busca" placeholder="Pesquisar." value="<?=val($_GET,"busca")?>"/>
		<button  class="ui blue button" type="submit">Pesquisar</button>
	</div>
</form>

<table class="ui celled table">
	<thead>
		<tr>
			<th>Editar</th>
			<th>Nome</th>
			<th>Deletar</th>
		</tr>
	</thead>
	<tbody>
	
<?php

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/permissoes/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/permissoes/deletar/{$ln->id}\")'> Deletar </a></td>";
	
	print "</tr>";
}

#paginacao
$this->pagination->initialize($listaPaginada);
?>
<tfoot>
<tr>
	<th colspan="5">
	<span class="ui label">
		Total: <?=$listaPaginada["total_rows"]?>
	</span>
	<?php if ($listaPaginada["page_max"] > 1): ?>
		<div class="ui right floated pagination menu">
			<?=$this->pagination->create_links()?>
		</div>
	<?php endif; ?>
	</th>
</tr>
</tfoot>
</tbody>
</table>

<?php
include 'layout/bottom.php';
?>