<?php


class Projeto_model extends AbstractModel {

		#nomes de tabelas sempre no plural
		#evite os caracteres _ - ou letras maiusculas
		public $table = "projetos";

		#campos nao podem ter - nem espaço
		#use sempre pascal_case (Sem letras maiusculas)
		public $fields = ["nome"];
		
		
		#VÁRIOS projetos podem estar relacionados á VÁRIOS projetos
		public $manyToMany = [["table"=>"projetos", 
							  "key"=>"projetos_id",
							  "assocTable"=>"projrelacionados"]];


		
		public function removeProjetos($id){
			$obj = R::load("projrelacionados", $id);
			R::Trash($obj);
		}


}