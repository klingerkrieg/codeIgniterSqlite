<?php


class Coordenacao_model extends AbstractModel {

		#nomes de tabelas sempre no plural
		#evite os caracteres _ - ou letras maiusculas
		public $table = "coordenacoes";

		#campos nao podem ter - nem espaço
		#use sempre pascal_case (Sem letras maiusculas)
		public $fields = ["nome"];
		
		#Uma coordenação pode ter apenas UM professor e vice versa
		public $oneToOne = [["table"=>"professores", 
							 "key"=>"professores_id",
							 "side"=>"coordenacoes"]];


}