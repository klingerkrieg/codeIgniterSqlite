<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coordenacoes extends CI_Controller {

	
	public function __construct(){
		parent::__construct();
		
		//Carrega o Model
		$this->load->model("Coordenacao_model");

		#verifica se o usuário está logado
		$this->seguranca->verificaLogado();

		#verifica se o usuário tem permissão de no mínimo Convidado
		$this->seguranca->permitir("Convidado");
	}
	
	
	//página principal
	public function index($id=null) {

		
		#lista paginada
		$listaPaginada = $this->Coordenacao_model->pagination(val($_GET,"busca"));
		
		#busca o registro que tem aquela id
		$dados = $this->Coordenacao_model->get($id);


		$this->load->model("Professor_model");
		$professores = $this->Professor_model->options("matricula","nome");


		include view('coordenacoes');
		
	}

	
	public function salvar(){


		#---------VALIDACOES------------
		$this->form_validation->set_rules('nome', 'Nome', 'required');



		#verifica se o formulário está validado
		if ($this->form_validation->run() == FALSE) {

			#se nao estiver
			$this->session->set_flashdata("error","O formulário não foi preenchido corretamente.");
			$this->index();
		
		
		} else {
			#se estiver tudo certo, manda salvar
			$id = $this->Coordenacao_model->save();

			#mensagem de confirmação
			if ($id == ""){
				$this->session->set_flashdata("error","Falha ao salvar.");
			} else {
				$this->session->set_flashdata("success","Salvo com sucesso.");
			}

			redirecionar("coordenacoes/index/" . $id);
		}
	}
	
	
	
	
	public function deletar($id){

		$this->Coordenacao_model->delete($id);
		$this->session->set_flashdata("warning","Registro deletado.");

		redirecionar("coordenacoes/index");
	}


}
