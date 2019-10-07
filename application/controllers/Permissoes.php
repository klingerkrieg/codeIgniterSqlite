<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissoes extends CI_Controller {

	public function __construct(){
		parent::__construct();
		
		$this->load->model("Permissao_model");

		#verifica se o usuário fez o login corretamente
		if (!isset($_SESSION["email"])){
			redirect("login/index/");
		}

		$this->seguranca->check();
	}
	
	
	//página principal
	public function index($id=null) {

		$arvore = $this->seguranca->arvore(["usuarios","permissoes","setores","grupos"]);
		
		if (isset($_GET['page'])){
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		//busca todos os registros para a listagem
		$listaPaginada = $this->Permissao_model->pagination($this->config->item("per_page"), $page, val($_GET,"busca"));
		
		//se for para abrir algum registro
		//se tiver dado erro de validacao, o Model automaticamente pega os
		//dados que foram enviados via POST
		$dados = $this->Permissao_model->get($id);


		//recupera todos os setores para o select de setor
		$this->load->model("Usuario_model");
		$usuarios = $this->Usuario_model->all();
		
		$this->load->view('permissoes', ["listaPaginada"=>$listaPaginada,
										"dados"=>$dados,
										"usuarios"=>$usuarios,
										"arvore"=>$arvore]);
		
	}

	
	
	public function salvar(){

		$this->form_validation->set_rules('nome', 'Nome', 'required');
		
		if ($this->form_validation->run() == FALSE) {
			$this->index();
		} else {
			
			$obj = $this->Permissao_model->save();

			#mensagem de confirmação
			if ($obj == ""){
				$this->session->set_flashdata("error","<div class='ui red message'>Falha ao salvar.</div>");
			} else {
				$this->session->set_flashdata("success","<div class='ui green message'>Salvo com sucesso.</div>");
			}

			redirect("permissoes/index/" . $obj );
		}
	}
	
	
	public function deletar($id){
		$this->Permissao_model->delete($id);
		$this->session->set_flashdata("warning","<div class='ui yellow message'>Registro deletado.</div>");
		redirect("permissoes/index");
	}


	public function remover_usuario($id_permissao, $id_usuario){
		$this->Permissao_model->remove_usuario($id_permissao,$id_usuario);
		redirect("permissoes/index/" . $id_permissao );
	}
}
