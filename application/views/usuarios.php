<?php include 'layout/header.php' ?>

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
		<?=form_error('nome')?>
	</label>
</div>


<div class="field">
	<label>E-mail
		<input type="text" name="email" value="<?=val($dados,'email')?>">
		<?=form_error('email')?>
	</label>
</div>

<div class="field">
	<label>Senha
		<input type="password" name="senha" value="">
		<?=form_error('senha')?>
	</label>
</div>

<div class="field">
	<label>Confirmação da senha
		<input type="password" name="senhaConfirm" value="">
		<?=form_error('senhaConfirm')?>
	</label>
</div>

<?php
#verifica se tem permissao para escolher o nível do usuário
if (Seguranca::temPermissao("permissoes")): ?>
<div class="field">
	<label>Nível de acesso
		<select name="permissoes_id">
			<option></option>
			<?php
			foreach($permissoes as $item){
				
				if ($dados['permissoes_id'] == $item['id']){
					$selected = "selected";
				} else {
					$selected = "";
				}

				print "<option $selected value='{$item['id']}'>{$item['nome']}</option>";
			}
			?>
		</select>
		<?=form_error('permissoes_id')?>
	</label>
</div>
<?php endif; ?>

<div class="field">
	<label>Setor
		<select name="setores_id">
			<option></option>
			<?php
			foreach($setores as $item){
				#para ja vir selecionado o setor do usuario, caso tenha sido escolhido
				if ($dados['setores_id'] == $item['id']){
					$selected = "selected";
				} else {
					$selected = "";
				}

				print "<option $selected value='{$item['id']}'>{$item['nome']}</option>";
			}
			?>
		</select>
		<?=form_error('setores_id')?>
	</label>
</div>

<?php if (val($dados,'id') != ""): ?>

<div class="field">
	<label>Adicionar grupo
		<select name="grupo_id">
			<option value=""></option>
			<?php
			foreach($grupos as $item){
				print "<option value='{$item['id']}'>{$item['nome']}</option>";
			}
			?>
		</select>
	</label>
</div>

<div class="field">
<table class="ui celled table">
	<thead>
		<tr>
			<th colspan="3">Grupos do usuário</th>
		</tr>
		<tr>
			<th>Nome</th>
			<th>Remover grupo</th>
		</tr>
	</thead>
	<tbody>
	
<?php


foreach($dados->ownGruposusuariosList as $gruposusuarios){
	
	$grupo = $gruposusuarios->grupos;

	print "<tr>";
	
	print "<td><a href='".site_url()."/grupos/index/{$grupo->id}'> {$grupo->nome} </a></td>";

	#para remover o usuario eu preciso passar a id_usuario
	#depois a id do registro da tabela gruposusuarios
	print "<td><a href='".site_url()."/usuarios/remover_grupo/{$dados['id']}/{$gruposusuarios->id}'> Remover </a></td>";
	
	print "</tr>";
}
?>
</tbody>
</table>
</div>


<?php endif; ?>

<div class="field">
	<label>Tipo de usuário</label>
	<div class="six fields">
		<?php
		foreach($tiposUsuarios as $key=>$tipo){
			if (val($dados,"tipo") == $key){
				$checked = "checked";
			} else {
				$checked = "";
			}
			print "<label class='field'><input type='radio' name='tipo' $checked value='$key'>$tipo</label>";
		}
		?>
		<?=form_error('tipo')?>
	</div>
</div>


<div class="field">
	<label>Foto
		<input type="file" name="foto">
		<?=form_error('foto')?>
	</label>
	<?php
	if (val($dados,"foto") != ""){
		print "<img class='ui tiny circular image' src='".base_url()."uploads/{$dados['foto']}' />";
	} ?>
</div>

<div class="field">
	<button  class="ui blue button" type="submit">Salvar</button>
	<a class="ui button" href="<?=site_url()?>/usuarios">Novo</a>
</div>


</form>
</div>


<form class="ui form column stackable grid" action="<?=site_url()?>/usuarios" method="GET">
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