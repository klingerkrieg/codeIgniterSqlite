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
	
	
	//a função index sempre será a função principal do arquivo
	//ela é aquela função que será acessada se o usuário digitar somente o nome do controller
	//http://localhost/sqlite/index.php/usuarios/
	public function index($id=null) { //recebo a id do usuario via parâmetro

		//verifico se foi passada alguma página para
		//ser exibida, caso não tenha sido passada
		//irá para a página 1
		//isso porque a tabela só mostra 10 registros
		//de cada vez, sendo assim, podem existir várias páginas
		//a primeira página, mostra os primeiros 10, a segunda página mostra os próximos
		if (isset($_GET['page'])){
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		//Utilizo o model para receber todos os registros do banco de dados
		//porém de forma paginada, traga somente os registros da pagina X
		$listaPaginada = $this->Usuario_model->pagination($this->config->item("per_page"), $page);
		
		//Se eu tiver enviado a id de algum individuo
		//O model usuario irá trazer os dados desse usuário
		//Para que ele seja exibido no formulário
		$dados = $this->Usuario_model->get($id);
		

		//Chamo a VIEW
		$this->load->view('usuarios', ["listaPaginada"=>$listaPaginada,
										"page"=>$page,
										"dados"=>$dados]);
		
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
