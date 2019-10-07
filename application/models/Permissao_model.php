<?php

class Permissao_model extends AbstractModel {
	
	public $table = "permissoes";
	public $fields = ["nome", "controles", "admin"];
	public $arrayFields = ["controles"];
	
	public $oneToMany = [["table"=>"usuarios", "key"=>"usuario_id"]];


	public function remove_usuario($id_permissao,$id_usuario){
		$obj = R::load($this->table, $id_permissao);
		unset($obj->ownUsuariosList[$id_usuario]);
		R::Store($obj);
	}
}
