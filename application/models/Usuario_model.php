<?php

class Usuario_model extends AbstractModel {

		#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
		public $table = "usuarios";
        public $fields = ["nome","email","senha", "tipo", "foto"];
		public $tiposUsuarios = [1=>"Professor", 2=>"Técnico", 3=>"Bolsista"];
		public $searchFields = ["nome","email"];

		#varios usuarios podem ter o mesmo setor
		#a key é o campo no formulario que contem a id do setor
		public $manyToOne = [["table"=>"setores", "key"=>"setores_id"]];
		public $manyToMany = [["table"=>"grupos","key"=>"grupo_id", "assocTable"=>"gruposusuarios"]];

		
		public function login($data){
			$data["senha"] = sha1($data["senha"]);
			return $this->findOne($data);
		}

        public function preSave($obj, $data) {
			
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


}
