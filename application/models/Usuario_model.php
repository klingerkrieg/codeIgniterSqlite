<?php

class Usuario_model extends AbstractModel {

		public $table = "usuarios";
        public $fields = ["nome","email","senha"];
		
		
        public function preSave($obj,$data) {

			if ($obj["senha"] != ""){
				//criptografa a senha
				$obj["senha"] = sha1($obj["senha"]);
			} else {
				//caso a senha venha em branco, nao vai modificar
				unset($obj["senha"]);
			}
			
			return $obj;
        }


}