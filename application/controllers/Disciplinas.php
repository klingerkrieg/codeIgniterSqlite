<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disciplinas extends CI_Controller {

	
	public function __construct(){
		parent::__construct();
		
		//Carrega o Model
		$this->load->model("Disciplina_model");

		#verifica se o usuário está logado
		$this->seguranca->verificaLogado();

		#verifica se o usuário tem permissão de no mínimo Convidado
		$this->seguranca->permitir("Convidado");
	}
	
	
	//página principal
	public function index($id=null) {

		
		#lista paginada
		$listaPaginada = $this->Disciplina_model->pagination(val($_GET,"busca"));
		
		#busca o registro que tem aquela id
		$dados = $this->Disciplina_model->get($id);


		$this->load->model("Professor_model");
		$professores = $this->Professor_model->options("matricula","nome");

		#autorelacionamento
		$disciplinas = $this->Disciplina_model->options("nome", "carga_horaria");

		$optativaArr = $this->Disciplina_model->optativaArr;
		$opcoesCargaHoraria = $this->Disciplina_model->opcoesCargaHoraria;



		#recupera os tipos de vínculo para professor
		$opcoesCargaHoraria = $this->Disciplina_model->opcoesCargaHoraria;

		
		include view('disciplinas');
		
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
			$id = $this->Disciplina_model->save();

			#mensagem de confirmação
			if ($id == ""){
				$this->session->set_flashdata("error","Falha ao salvar.");
			} else {
				$this->session->set_flashdata("success","Salvo com sucesso.");
			}

			redirecionar("disciplinas/index/" . $id);
		}
	}
	
	
	
	
	public function deletar($id){

		$this->Disciplina_model->delete($id);
		$this->session->set_flashdata("warning","Registro deletado.");

		redirecionar("disciplinas/index");
	}

	public function remover_requisito($id, $id_assoc){
		$this->Disciplina_model->removerRequisito($id_assoc);
		redirecionar("disciplinas/index/$id");
	}


	
}
