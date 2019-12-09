<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projetos extends CI_Controller {

	
	public function __construct(){
		parent::__construct();
		
		//Carrega o Model
		$this->load->model("Projeto_model");

		#verifica se o usuário está logado
		$this->seguranca->verificaLogado();

		#verifica se o usuário tem permissão de no mínimo Convidado
		$this->seguranca->permitir("Convidado");
	}
	
	
	//página principal
	public function index($id=null) {

		
		#lista paginada
		$listaPaginada = $this->Projeto_model->pagination(val($_GET,"busca"));
		
		#busca o registro que tem aquela id
		$dados = $this->Projeto_model->get($id);


		$projetos = $this->Projeto_model->options("nome");


		include view('projetos');
		
	}

	
	public function salvar(){


		#---------VALIDACOES------------
		$this->form_validation->set_rules('nome', 'Nome', 'required');



		#verifica se o formulário está validado
		if ($this->form_validation->run() == FALSE) {

			#se nao estiver
			$this->session->set_flashdata("error","<div class='ui red message'>O formulário não foi preenchido corretamente.</div>");
			$this->index();
		
		
		} else {
			#se estiver tudo certo, manda salvar
			$id = $this->Projeto_model->save();

			#mensagem de confirmação
			if ($id == ""){
				$this->session->set_flashdata("error","<div class='ui red message'>Falha ao salvar.</div>");
			} else {
				$this->session->set_flashdata("success","<div class='ui green message'>Salvo com sucesso.</div>");
			}

			redirecionar("projetos/index/" . $id);
		}
	}
	
	
	
	
	public function deletar($id){

		$this->Projeto_model->delete($id);
		$this->session->set_flashdata("warning","<div class='ui yellow message'>Registro deletado.</div>");

		redirecionar("projetos/index");
	}



	public function remover_relacao($this_id, $tab_auxiliar_id){
		$this->Projeto_model->removerProjetos($tab_auxiliar_id);
		redirecionar("projetos/index/" . $this_id );
	}


}
