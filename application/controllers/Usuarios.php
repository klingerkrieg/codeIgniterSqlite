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

		$this->seguranca->permitir("Convidado");
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

	public function uploadFile($UPLOAD, $name){
		#local onde salvará o arquivo sqlite/uploads/
		#a pasta uploads deve existir, caso contrário ele não irá funcionar
		$config['upload_path']          = "./uploads/";
		$config['allowed_types']        = 'jpg|jpeg|png';
		$config['max_size']             = 1000;
		#limpa os caracteres especiais do nome do arquivo
		$config['file_name']            = cleanString($_FILES[$name]["name"]);

		$this->load->library('upload', $config);
		#faz o upload
		if ( ! $this->upload->do_upload($name)){
			return false;
		} else {
			#retorna como ficou o nome do arquivo no servidor
			return $this->upload->data()["file_name"];
		}
	}

	
	public function salvar(){

		#-----------PERMISSAO de SALVAR
		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");



		#UPLOAD de foto com validacao
		$campoDeUpload = "foto";
		if (isset($_FILES[$campoDeUpload]) && $_FILES[$campoDeUpload]["name"] != ""){
			#faz o upload com a funcao uploadFile e já retorna o nome do arquivo
			$_POST[$campoDeUpload] = $this->uploadFile($_FILES,$campoDeUpload);
			#caso nao consiga, ele retornará false e a validacao irá falhar
			$this->form_validation->set_rules($campoDeUpload, 'Foto', 'required', 
							["required"=>"Verifique o tamanho e o formato do arquivo."]);
		}




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





		#------------PERMISSAO DE ALTERAR O TIPO DO USUARIO
		#A permissao só será obrigatória quando a pessoa que estiver cadastrando
		#tiver o nivel de acesso necessário
		if (Seguranca::temPermissao("Admin"))
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
		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");

		$this->Usuario_model->delete($id);
		$this->session->set_flashdata("warning","<div class='ui yellow message'>Registro deletado.</div>");

		if (isset($_GET["page"])){
			$actPage = "?page={$_GET["page"]}";
		}

		redirect("usuarios/index$actPage");
	}


	public function remover_grupo($this_id, $assoc_id){
		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");

		$this->load->model("Grupo_model");
		$this->Grupo_model->remove_usuario($assoc_id);
		redirect("usuarios/index/" . $this_id );
	}

	public function remover_permissao($this_id, $assoc_id){
		#So permite usuarios com nivel Comum ou Superior
		$this->seguranca->permitir("Comum");
		
		$this->load->model("Permissao_model");
		$this->Permissao_model->remove_usuario($assoc_id);
		redirect("usuarios/index/" . $this_id );
	}
}
