<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grupos extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		
		//Carrega o Model de Grupo
		$this->load->model("Grupo_model");

		#verifica se o usuário fez o login corretamente
		if (!isset($_SESSION["email"])){
			redirect("login/index/");
		}

		$this->seguranca->permitir("Comum");
	}
	
	
	//página principal
	public function index($id=null) {

		if (isset($_GET['page'])){
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		//busca todos os registros para a listagem
		$listaPaginada = $this->Grupo_model->pagination($this->config->item("per_page"), $page, val($_GET,"busca"));

		//se for para abrir algum registro
		$dados = $this->Grupo_model->get($id);

		
		//Seleciona todas as pessoas que ainda nao estao naquele Grupo
		$this->load->model("Usuario_model");
		$pessoas = $this->Usuario_model->options("nome");
		
		$this->load->view('grupos', ["listaPaginada"=>$listaPaginada,
										"dados"=>$dados,
										"pessoas"=>$pessoas]);
		
	}
	
	
	
	public function salvar(){

		$this->form_validation->set_rules('nome', 'Nome', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata("error","Corrija os erros no formulário.");
			$this->index();
		} else {
			$obj = $this->Grupo_model->save();

			#mensagem de confirmação
			if ($obj == ""){
				$this->session->set_flashdata("error","Falha ao salvar.");
			} else {
				$this->session->set_flashdata("success","Salvo com sucesso.");
			}

			
			redirect("grupos/index/" . $obj );
		}
	}
	
	
	
	
	public function deletar($id){
		$this->Grupo_model->delete($id);

		$this->session->set_flashdata("warning","Registro deletado.");
		
		redirect("grupos/index");
		
	}

	public function remover_usuario($grupo_id, $assoc_id){
		$this->Grupo_model->remove_usuario($assoc_id);
		redirect("grupos/index/" . $grupo_id );
	}
}
