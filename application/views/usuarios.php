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

foreach($list as $ln){
	
	print "<tr>";
	
	print "<td><a href='".site_url()."/usuarios/index/{$ln->id}'> Editar </a></td>";
	
	print "<td>{$ln->nome}</td>";
	print "<td>{$ln->email}</td>";
	
	print "<td><a href='".site_url()."/usuarios/deletar/{$ln->id}'> Deletar </a></td>";
	
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