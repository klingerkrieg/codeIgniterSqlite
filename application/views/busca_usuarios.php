<?php include 'layout/header.php' ?>

<div class="ui grid">


<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios/busca"
	method="get">

<?=$this->session->flashdata('error')?>
<?=$this->session->flashdata('success')?>
<?=$this->session->flashdata('warning')?>

<div class="field">
	<label>Nome
		<input type="text" name="nome" value="<?=val($_GET,'nome')?>">
	</label>
</div>


<div class="field">
	<label>E-mail
		<input type="text" name="email" value="<?=val($_GET,'email')?>">
	</label>
</div>

<?php
#verifica se tem permissao para escolher o nível do usuário
if (Seguranca::temPermissao("Admin")): ?>
<div class="field">
	<label>Nível de acesso
		<?=form_dropdown("nivel", $niveis, val($_GET,"nivel"));?>
	</label>
</div>
<?php endif; ?>

<div class="field">
	<label>Setor
	<?=form_dropdown("setore_id", $setores, val($_GET,"setore_id"));?>
	</label>
</div>


<div class="field">
	<label>Grupo
	<?=form_dropdown("grupo_id", $grupos, val($_GET,"grupo_id"));?>
	</label>
</div>


<div class="field">
	<label>Tipo de usuário</label>
	<div class="six fields">
		<?php
		foreach($tiposUsuarios as $key=>$tipo){
			
			print "<label class='field'>";
			print form_checkbox("tipo[]",$key, checked($key, $_GET,"tipo") );
			print "$tipo</label>";
		}
		?>
		<?=error('tipo')?>
	</div>
</div>


<div class="field">
	<label>Área de atuação</label>
	<div class="five fields">
		<?php
		foreach($areasUsuarios as $key=>$tipo){			
			print "<label class='field'>";
			print form_checkbox("area[]",$key, checked($key, $_GET,"area"));
			print "$tipo</label>";
		}
		?>
		<?=error('area[]')?>
	</div>
</div>


<div class="field">
	<button  class="ui blue button" type="submit">Buscar</button>
	<a class="ui button" href="<?=site_url()?>/usuarios/buscar">Nova busca</a>
</div>


</form>
</div>


<table class="ui celled table">
	<thead>
		<tr>
			<th>Editar</th>
			<th>Nome</th>
			<th>E-mail</th>
			<th>Setor</th>
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
	#link para abrir diretamente o setor já em modo de edição
	@print "<td><a href='".site_url()."/setores/index/{$ln->setores->id}'> {$ln->setores->nome} </a> </td>";
	
	print "<td><a onclick='confirmDelete(\"".site_url()."/usuarios/deletar/{$ln->id}\")'> Deletar </a></td>";
	
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