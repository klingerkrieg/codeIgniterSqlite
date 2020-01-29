<?php


class Professor_model extends AbstractModel {

		#nomes de tabelas sempre no plural
		#evite os caracteres _ - ou letras maiusculas
		public $table = "professores";

		#campos nao podem ter - nem espaço
		#use sempre pascal_case (Sem letras maiusculas)
		public $fields = ["nome", "matricula", "vinculo", "foto", "especialidades"=>"array"];
		
		#o search fields só é utilizado se você quiser restringir as buscas
		#ex: o usuário só pode pesquisar em X campos
		public $searchFields = ["nome", "matricula", "vinculo", "especialidades", "coordenacoes_id", "disciplinas_id"];
		
		#um Professor pode ter várias Disciplinas
		public $oneToMany = [["table"=>"disciplinas", 
								"key"=>"disciplinas_id"]];


		#UM professor pode ter apenas UMA coordenação e vice versa
		public $oneToOne = [["table"=>"coordenacoes", 
								"key"=>"coordenacoes_id",
								"side"=>"coordenacoes"]];


		public $tiposVinculo = ["Efetivo", "Substituto", "Visitante"];

		public $especialidades = ["Banco de dados", "Web", "Eng. de Software", "Redes"];


		#sempre antes de salvar um registro a funcao preSave é chamada
		public function preSave($obj, $data){

			#a matricula é gerada pelo sistema
			#verifica se ela já está preenchida
			if ($obj->matricula == ""){
				#se nao estiver preenchida, gera uma
				$matricula = rand(1000,9999);
				#verifica se já existe alguém com essa matrícula
				while (R::count($this->table,"matricula = ?", [$matricula]) > 0){
					#se o count der > que 0, é porque já existe, então gera outra
					$matricula = rand(1000,9999);
				}
				#ao final, será gerada uma matrícula única
				$obj->matricula = $matricula;
			}

			#a função preSave sempre tem que retornar o objeto que será salvo
			return $obj;
		}


		
		public function removerDisciplinas($id, $idDisciplina){
			$obj = R::load($this->table, $id);
			unset($obj->ownDisciplinasList[$idDisciplina]);
			R::Store($obj);
		}


}