<?php


class Aluno_model extends AbstractModel {

		#nomes de tabelas sempre no plural
		#evite os caracteres _ - ou letras maiusculas
		public $table = "alunos";

		#campos nao podem ter - nem espaço
		#use sempre pascal_case (Sem letras maiusculas)
		public $fields = ["nome", "matricula", "turma", "data_cadastro"];
		
		
		#VÁRIOS Alunos podem estar em VÁRIAS Disciplinas
		public $manyToMany = [["table"=>"disciplinas", 
							  "key"=>"disciplinas_id",
							  "assocTable"=>"alunosdisciplinas"]];


		
		public function removerDisciplinas($id){
			$obj = R::load("alunosdisciplinas", $id);
			R::Trash($obj);
		}



		
		#sempre antes de salvar um registro a funcao preSave é chamada
		public function preSave($obj, $data){


			#se o objeto nao tem data de cadastro
			if ($obj->data_cadastro == ""){
				$obj->data_cadastro = Date("d/m/Y H:i:s");
			}



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



}