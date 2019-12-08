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
							  ["table"=>"disciplinas",
							  "key"=>"requisito_id",
							  "field"=>"requisito"]
							];


		#VÁRIAS Disciplinas podem ter VÁRIOS Alunos
		public $manyToMany = [["table"=>"alunos", 
							  "key"=>"alunos_id",
							  "assocTable"=>"alunosdisciplinas"]];


		public $opcoesCargaHoraria = [0=>"",
									  10=>"10 h/a", 
									  30=>"30 h/a", 
									  45=>"45 h/a", 
									  60=>"60 h/a", 
									  80=>"80 h/a"];

		public $optativaArr = ["Não","Sim"];

		public function removerRequisito($id_assoc){
			$obj = R::load($this->table,$id_assoc);
			$obj->requisito = null;
			R::Store($obj);
		}

		

}