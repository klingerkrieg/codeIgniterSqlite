<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	
	public function __construct(){
		parent::__construct();
		
		//Carrega o Model de usuario
		$this->load->model("Usuario_model");

		#verifica se o usuário fez o login corretamente
		if (!isset($_SESSION["email"])){
			redirect("login/index/");
		}

		$this->seguranca->permitir("Comum");
	}
	
	
	//página principal
	public function index($id=null) {

		#lista paginada
		$listaPaginada = $this->Usuario_model->pagination(val($_GET,"busca"));
		
		#busca o registro que tem aquela id
		$dados = $this->Usuario_model->get($id);

		$niveis = $this->Usuario_model->secureLevels;

		
		include view("usuarios");
		
	}

	

	
	public function salvar(){

		#-----------PERMISSAO de SALVAR
		#So permite usuarios com nivel Admin
		$this->seguranca->permitir("Admin");



		#---------VALIDACOES------------
		#a senha so sera validada se for num novo registro
		#ou se enviado algo diferente de branco
		#pq quando ela nao é enviada, significa que nao é pra alterar
		if (val($_POST,"senha") != "" || val($_POST,"id") == ""){
			$this->form_validation->set_rules('senha', 'Senha', 'required|min_length[6]',
					array('min_length' => 'Defina uma senha com no mínimo 6 dígitos.')
			);
			$this->form_validation->set_rules('senhaConfirm', 'Confirmação da senha', 'required|matches[senha]');
		}
		$this->form_validation->set_rules('nome', 'Nome', 'required');
		$this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
		$this->form_validation->set_rules('nivel', 'Nível de acesso', 'required');

		




		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata("error","Corrija os erros no formulário.");
			$this->index();
		} else {

			$id = $this->Usuario_model->save();

			#mensagem de confirmação
			if ($id == ""){
				$this->session->set_flashdata("error","Falha ao salvar.");
			} else {
				$this->session->set_flashdata("success","Salvo com sucesso.");
			}

			redirect("usuarios/index/" . $id );
		}
	}
	
	
	
	
	public function deletar($id){
		#So permite usuarios com nivel Admin
		$this->seguranca->permitir("Admin");

		$this->Usuario_model->delete($id);
		$this->session->set_flashdata("warning","<div class='ui yellow message'>Registro deletado.</div>");

		if (isset($_GET["page"])){
			$actPage = "?page={$_GET["page"]}";
		}

		redirect("usuarios/index$actPage");
	}


	public function remover_grupo($this_id, $assoc_id){
		#So permite usuarios com nivel Admin
		$this->seguranca->permitir("Admin");

		$this->load->model("Grupo_model");
		$this->Grupo_model->remove_usuario($assoc_id);
		redirect("usuarios/index/" . $this_id );
	}

	public function remover_permissao($this_id, $assoc_id){
		#So permite usuarios com nivel Admin
		$this->seguranca->permitir("Admin");
		
		$this->load->model("Permissao_model");
		$this->Permissao_model->remove_usuario($assoc_id);
		redirect("usuarios/index/" . $this_id );
	}
}
