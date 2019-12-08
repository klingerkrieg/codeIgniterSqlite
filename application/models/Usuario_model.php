<?php

class Usuario_model extends AbstractModel {

	#nomes de tabelas sempre no plural
	#evite os caracteres _ - ou letras maiusculas
	public $table = "usuarios";

	#campos nao podem ter - nem espaço
	#use sempre pascal_case (Sem letras maiusculas)
	public $fields = ["nome","email", "senha", "nivel"=>"secure"];
	

	#Seguranca
	#variável que contem os níveis
	public $secureLevels = [1=>"Admin", 2=>"Comum", 3=>"Convidado"];
	
	

	public function login($data){
		$data["senha"] = sha1($data["senha"]);
		return $this->findOne($data);
	}

	public function preSave($obj, $data) {

		#Caso alguem tente salvar como Admin, seta para a permissao padrao
		if (($obj["nivel"] == "Admin" || $obj["nivel"] == "") && !Seguranca::temPermissao("Admin")){
			$obj["nivel"] = 3;
		}

		
		if ($obj["senha"] != ""){
			//criptografa a senha
			$obj["senha"] = sha1($obj["senha"]);
		} else {
			//caso a senha venha em branco, nao vai modificar
			unset($obj["senha"]);
		}

		//A função preSave sempre deve retornar o $obj
		return $obj;
	}
	

	public function resetAdmin(){

		$email = "admin@admin.com";
		$senha = "123456";

		#deleta todos os admins
		$sql = "delete from {$this->table} where email = '$email'";
		R::exec($sql);
			

		#recria o admin
		$admin = R::dispense($this->table);
		$admin->nome = "admin";
		$admin->email = $email;
		$admin->senha = sha1($senha);
		$admin->nivel = 1;
		R::Store($admin);
		return ["id"=>$admin->id, "email"=>$email, "senha"=>$senha];
	}


}
