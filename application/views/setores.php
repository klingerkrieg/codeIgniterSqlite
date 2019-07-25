<?php include 'layout/header.php' ?>

<div class="ui grid">

<form class="ui form column stackable grid" action="<?=site_url()?>/setores/salvar" method="post">


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


<?php if (val($dados,'id') != ""): ?>

<div class="field">
	<label>Adicionar pessoa
		
		<select name="pessoa_id">
			<option value=""></option>
			<?php
			foreach($pessoas as $p){
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
			<th colspan="3">Usuários no setor</th>
		</tr>
		<tr>
			<th>Nome</th>
			<th>E-mail</th>
			<th>Remover usuário</th>
		</tr>
	</thead>
	<tbody>
	
<?php



foreach($dados->ownUsuariosList as $ln){
	
	print "<tr>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->email}</td>";
	
	#para remover o usuario eu preciso passar a id_setor e depois id_usuario
	print "<td><a href='".site_url()."/setores/remover_usuario/{$dados['id']}/{$ln->id}'> Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>
</table>
</div>

<?php endif; ?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/setores">Novo</a>
</div>

</form>


</div>




<form class="ui form column stackable grid" action="<?=site_url()?>/setores" method="GET">
	<div class="fields">
		<input name="busca" placeholder="Pesquisar."  value="<?=val($_GET,"busca")?>" />
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

foreach($list as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/setores/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/setores/deletar/{$ln->id}\")'> Deletar </a></td>";
	
	print "</tr>";
}

#paginacao, se houver mais de uma página a paginação aparecerá
$page_max = ceil($qtd/10);
if ($page_max > 1):
	$config['total_rows'] = $qtd;
	$this->pagination->initialize($config);
?>
	<tfoot>
	<tr>
		<th colspan="5">
		<div class="ui right floated pagination menu">
			<?=$this->pagination->create_links()?>
		</div>
		</th>
	</tr>
	</tfoot>
<?php endif; ?>
</tbody>
</table>

<?php
include 'layout/bottom.php';
?>