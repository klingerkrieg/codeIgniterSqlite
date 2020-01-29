<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller { //<----- O nome da classe, 
	// sempre deve ser igual ao nome do arquivo
	

	//Construtor da classe
	public function __construct(){
			parent::__construct(); //Sempre deverá ter essa linha,
			//para construir aos moldes do CodeIgniter
			
			//Sempre que iniciar essa classe, ele carregará o model de Usuario
			$this->load->model("Usuario_model");
	}
	
	
	#a função index sempre será a função principal do arquivo
	#ela é aquela função que será acessada se o usuário digitar somente o nome do controller
	#http://localhost/sqlite/index.php/usuarios/
	public function index($id=null) { //recebo a id do usuario via parâmetro
		
		#Utilizo o model para receber todos os registros do banco de dados
		$listaPaginada = $this->Usuario_model->pagination();
		
		#Se eu tiver enviado a id de algum individuo
		#O model usuario irá trazer os dados desse usuário
		#Para que ele seja exibido no formulário
		$dados = $this->Usuario_model->get($id);
		

		#Chamo a VIEW
		include view("usuarios");
	}
	
	
	
	public function salvar(){
		$id = $this->Usuario_model->save();
		redirect("usuarios/index/" . $id );
	}
	
	
	
	
	public function deletar($id){
		$this->Usuario_model->delete($id);
		redirect("usuarios/index");
	}
}
