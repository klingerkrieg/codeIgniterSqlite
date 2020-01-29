<?php

class Usuario_model extends AbstractModel {

		#nomes de tabelas sempre no plural
		#evite os caracteres _ - ou letras maiusculas
		public $table = "usuarios";
		
		#campos nao podem ter - nem espaço
		#use sempre pascal_case (Sem letras maiusculas), _ é permitido
        public $fields = ["nome","email","senha"];
		
		
		/**
		 * A função preSave sempre é chamada antes de salvar no banco
		 * aqui você pode modificar os dados antes de serem salvos
		 */
        public function preSave($obj,$data) {

			if ($obj["senha"] != ""){
				#criptografa a senha
				$obj["senha"] = sha1($obj["senha"]);
			} else {
				#caso a senha venha em branco, nao vai modificar
				unset($obj["senha"]);
			}
			#A função preSave sempre deve retornar o $obj modificado
			return $obj;
        }


}