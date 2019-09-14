<?php include 'layout/header.php' ?>

<div class="ui grid">

<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios/salvar" method="post">

<input type="hidden" name="id" value="<?=val($dados,'id')?>">

<div class="field">
	<label>Nome
		<input type="text" name="nome" value="<?=val($dados,'nome')?>">
	</label>
</div>


<div class="field">
	<label>E-mail
		<input type="text" name="email" value="<?=val($dados,'email')?>">
	</label>
</div>

<div class="field">
	<label>Senha
		<input type="password" name="senha" value="">
	</label>
</div>


<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a  class="ui button" type="button" href="<?=site_url()?>/usuarios">Novo</a>
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

foreach($listaPaginada["data"] as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/usuarios/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->email}</td>";
	
	print "<td><a href='".site_url()."/usuarios/deletar/{$ln->id}'> Deletar </a></td>";
	
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