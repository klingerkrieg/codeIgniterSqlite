<?php

class Usuario_model extends AbstractModel {

		public $table = "usuarios";
        public $fields = ["nome","email","senha"];
		
		
        public function save($data=null) {

			if ($data == null){
				$data = $_POST;
			}
			
			$fields = prepare_fields($this->fields, $data);
			
			$obj = R::load($this->table, val($data,'id'));
			
			
			foreach($fields as $key=>$val){
				$obj[$key] = $val;
			}
			
			if ($obj["senha"] != ""){
				//criptografa a senha
				$obj["senha"] = sha1($obj["senha"]);
			} else {
				//caso a senha venha em branco, nao vai modificar
				unset($obj["senha"]);
			}
			
			return R::store($obj);
        }


}