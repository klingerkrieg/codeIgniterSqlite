<?php


class Disciplina_model extends AbstractModel {

		#nomes de tabelas sempre no plural
		#evite os caracteres _ - ou letras maiusculas
		public $table = "disciplinas";

		#campos nao podem ter - nem espaço
		#use sempre pascal_case (Sem letras maiusculas)
		public $fields = ["nome", "optativa", "carga_horaria"];
		
		
		#Várias disciplinas podem ser de apenas um professor
		public $manyToOne = [["table"=>"professores", 
							  "key"=>"professores_id"],
							
							
		#VÁRIAS disciplinas podem ter UMA disciplina como requisito 
		#(busca avançada de autorelacionamento não implementada)
							  ["table"=>"disciplinas",
							  "key"=>"requisito_id",
							  "field"=>"requisito"]
							];


		#VÁRIAS Disciplinas podem ter VÁRIOS Alunos
		public $manyToMany = [["table"=>"alunos", 
							  "key"=>"alunos_id",
							  "assocTable"=>"alunosdisciplinas"]];


		public $opcoesCargaHoraria = [10=>"10 h/a", 
									  30=>"30 h/a", 
									  45=>"45 h/a", 
									  60=>"60 h/a", 
									  80=>"80 h/a"];

		public $optativaArr = ["Não","Sim"];

		public function removerRequisito($id){
			$obj = R::load($this->table,$id);
			$obj->requisito = null;
			R::Store($obj);
		}

		#Recebe a id na tabela alunosdisciplinas e um array com a nota que foi editada
		public function lancar($idAux, $arr){
			#busca a linha na tabela
			$obj = R::load("alunosdisciplinas", $idAux);
			#verifica se o array contém todos os campos necessários e se são numéricos
			if (isset($arr["etapa"]) && isset($arr["nota"]) && is_numeric($arr["nota"])){
				#verifica qual foi a etapa e atualiza a nota no objeto
				if ($arr["etapa"] == "nota1"){
					$obj->nota1 = $arr["nota"];
				} else 
				if ($arr["etapa"] == "nota2"){
					$obj->nota2 = $arr["nota"];
				}
				#salva o objeto
				R::Store($obj);
				return true;
			}
			#se algo der errado...
			return false;
		}

		

}