<?php include 'layout/header.php' ?>

<div class="ui grid">

<form class="ui form column stackable grid" action="<?=site_url()?>/grupos/salvar" method="post">


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
			<th colspan="3">Usuários no grupo</th>
		</tr>
		<tr>
			<th>Nome</th>
			<th>E-mail</th>
			<th>Remover usuário</th>
		</tr>
	</thead>
	<tbody>
	
<?php


foreach($dados->ownGruposusuariosList as $groupList){
	
	$user = $groupList->usuarios;

	print "<tr>";
	
	print "<td>{$user->nome}</td>";
	print "<td>{$user->email}</td>";
	
	#para remover o usuario eu preciso passar a id_setor
	#depois a id do registro da tabela gruposusuarios
	print "<td><a href='".site_url()."/grupos/remover_usuario/{$dados['id']}/{$groupList->id}'> Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>
</table>
</div>

<?php endif; ?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/grupos">Novo</a>
</div>

</form>


</div>


<form class="ui form column stackable grid" action="<?=site_url()?>/grupos" method="GET">
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

foreach($list as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/grupos/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";

	print "<td><a onclick='confirmDelete(\"".site_url()."/grupos/deletar/{$ln->id}\")'> Deletar </a></td>";
	
	print "</tr>";
}

#paginacao
$page_max = ceil($qtd/10);

$config['total_rows'] = $qtd;
$this->pagination->initialize($config);
?>
	<tfoot>
	<tr>
		<th colspan="5">
		<span class="ui label">
			Total: <?=$qtd?>
		</span>
		<?php if ($qtd > $config['total_rows']): ?>
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