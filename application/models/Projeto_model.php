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
							  "key"=>"relacionado_id",
							  "assocTable"=>"projrelacionados",
							  "field"=>"relacionado"]];


		
		public function removerProjetos($tab_auxiliar_id){
			$obj = R::load("projrelacionados", $tab_auxiliar_id);
			R::Trash($obj);
		}


}