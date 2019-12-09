<?php include 'layout/header.php' ?>


<div class="ui message">
  <p>Aqui você encontrará o básico para a criação de usuários e permissões.</p>
</div>


<div class="ui grid">


<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios/salvar"
	method="post" enctype="multipart/form-data">

<?=$this->session->flashdata('error')?>
<?=$this->session->flashdata('success')?>
<?=$this->session->flashdata('warning')?>

<input type="hidden" name="id" value="<?=val($dados,'id')?>">

<div class="field">
	<label>Nome
		<input type="text" name="nome" value="<?=val($dados,'nome')?>">
		<?=error('nome')?>
	</label>
</div>


<div class="field">
	<label>E-mail
		<input type="text" name="email" value="<?=val($dados,'email')?>">
		<?=error('email')?>
	</label>
</div>

<div class="field">
	<label>Senha
		<input type="password" name="senha" value="">
		<?=error('senha')?>
	</label>
</div>

<div class="field">
	<label>Confirmação da senha
		<input type="password" name="senhaConfirm" value="">
		<?=error('senhaConfirm')?>
	</label>
</div>

<?php
#verifica se tem permissao para escolher o nível do usuário
if (Seguranca::temPermissao("Admin")): ?>
<div class="field">
	<label>Nível de acesso
		<?=form_dropdown("nivel", $niveis, val($dados,"nivel"));?>
		<?=error('nivel')?>
	</label>
</div>
<?php endif; ?>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/usuarios">Novo</a>
</div>

</form>
</div>


<div class="ui grid">
<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios" method="GET">
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


<table class="ui celled table">
	<thead>
		<tr>
			<th>Editar</th>
			<th>Nome</th>
			<th>E-mail</th>
			<th>Deletar</th>
		</tr>
	</thead>
	<tbody>
	
<?php

$actPage = "";
if (isset($_GET["page"])){
	$actPage = "?page={$_GET["page"]}";
}

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/usuarios/index/{$ln->id}$actPage'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->email}</td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/usuarios/deletar/{$ln->id}$actPage\")'> Deletar </a></td>";
	
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