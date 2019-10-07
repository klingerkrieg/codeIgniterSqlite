<?php

class Usuario_model extends AbstractModel {

	#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
	public $table = "usuarios";
	public $fields = ["nome","email","senha", "tipo", "foto"];
	public $tiposUsuarios = [1=>"Professor", 2=>"Técnico", 3=>"Bolsista"];
	public $searchFields = ["nome","email"];

	#varios usuarios podem ter o mesmo setor
	#a key é o campo no formulario que contem a id do setor
	public $manyToOne = [["table"=>"setores", "key"=>"setores_id"],
							["table"=>"permissoes","key"=>"permissoes_id"]];
	public $manyToMany = [["table"=>"grupos","key"=>"grupo_id", "assocTable"=>"gruposusuarios"]];

	public $defaultPermission_id = 2;#Comum
	
	public function login($data){
		$data["senha"] = sha1($data["senha"]);
		return $this->findOne($data);
	}

	public function preSave($obj, $data) {

		#Seta a permissão padrao para quem não tiver o nivel
		#de acesso necessário
		if (Seguranca::temPermissao("permissoes")){
			$this->load->model("Permissao_model");
			$obj["permissoes_id"] = $this->$defaultPermission_id;
		}
		
		if ($obj["senha"] != ""){
			//criptografa a senha
			$obj["senha"] = sha1($obj["senha"]);
		} else {
			//caso a senha venha em branco, nao vai modificar
			unset($obj["senha"]);
		}

		//A função preSave sempre deve retornar o $obj
		return $obj;
	}
	

	public function findNotInSetor($idSetor){

		return R::findAll($this->table,"setores_id <> ? or setores_id is null ",[$idSetor]);			
	}

	public function findNotInGrupo($idGrupo){

		$sql = "select * from usuarios where 
				id not in (select usuarios_id from gruposusuarios where grupos_id = ? )";
		return R::getAll($sql,[$idGrupo]);
	}

	public function resetAdmin(){

		#deleta todos os admins
		$sql = "delete from {$this->table} where email = 'admin@admin.com'";
		R::exec($sql);
		
		#cria a permissao de admin
		$this->load->model("Permissao_model");
		$permissao_id = $this->Permissao_model->getOrSave(["nome"=>"Admin", "admin"=>1, "controles"=>""]);

		#recria o admin
		$data = ["nome"=>"admin","email"=>"admin@admin.com","senha"=>"123456","permissoes_id"=>$permissao_id];
		return $this->save($data);
	}


}
