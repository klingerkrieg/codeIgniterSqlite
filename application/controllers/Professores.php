<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professores extends CI_Controller {

	
	public function __construct(){
		parent::__construct();
		
		//Carrega o Model
		$this->load->model("Professor_model");

		#verifica se o usuário está logado
		$this->seguranca->verificaLogado();

		#verifica se o usuário tem permissão de no mínimo Convidado
		$this->seguranca->permitir("Convidado");
	}
	
	
	//página principal
	public function index($id=null) {

		
		#lista paginada
		$listaPaginada = $this->Professor_model->pagination(val($_GET,"busca"));
		
		#busca o registro que tem aquela id
		$dados = $this->Professor_model->get($id);


		$this->load->model("Coordenacao_model");
		$coordenacoes = $this->Coordenacao_model->options("nome");


		$this->load->model("Disciplina_model");
		$grupos = $this->Disciplina_model->options("nome", "carga_horaria");


		#recupera os tipos de vínculo para professor
		$tiposVinculo = $this->Professor_model->tiposVinculo;

		
		include view('professores');
		
	}

	
	public function salvar(){


		#UPLOAD de foto com validacao
		$campoDeUpload = "foto";
		if (isset($_FILES[$campoDeUpload]) && $_FILES[$campoDeUpload]["name"] != ""){
			#faz o upload com a funcao uploadFile e já retorna o nome do arquivo
			$_POST[$campoDeUpload] = $this->uploadFile($_FILES, $campoDeUpload, "./uploads/", "jpg|jpeg|png");
			#caso nao consiga, ele retornará false e a validacao irá falhar
			$this->form_validation->set_rules($campoDeUpload, 'Foto', 'required', 
							["required"=>"Verifique o tamanho e o formato do arquivo."]);
		}




		#---------VALIDACOES------------

		$this->form_validation->set_rules('nome', 'Nome', 'required');
		$this->form_validation->set_rules('vinculo', 'Vínculo', 'required');



		#verifica se o formulário está validado
		if ($this->form_validation->run() == FALSE) {

			#se nao estiver
			$this->session->set_flashdata("error","<div class='ui red message'>O formulário não foi preenchido corretamente.</div>");
			$this->index();
		
		
		} else {
			#se estiver tudo certo, manda salvar
			$id = $this->Professor_model->save();

			#mensagem de confirmação
			if ($id == ""){
				$this->session->set_flashdata("error","<div class='ui red message'>Falha ao salvar.</div>");
			} else {
				$this->session->set_flashdata("success","<div class='ui green message'>Salvo com sucesso.</div>");
			}

			redirecionar("professores/index/" . $id);
		}
	}
	
	
	
	
	public function deletar($id){

		$this->Professor_model->delete($id);
		$this->session->set_flashdata("warning","<div class='ui yellow message'>Registro deletado.</div>");

		redirecionar("professores/index");
	}


	public function remover_disciplina($this_id, $assoc_id){
		$this->Professor_model->removerDisciplinas($this_id,$assoc_id);
		redirecionar("professores/index/" . $this_id );
	}


	
	public function buscaAvancada(){
		

		#busca todos os registros para a listagem
		$listaPaginada = $this->Usuario_model->pagination($_GET);
		

		$this->load->model("Coordenacao_model");
		$setores = $this->Coordenacao_model->options("nome");

		$this->load->model("Disciplina_model");
		$grupos = $this->Disciplina_model->options("nome", "cargaHoraria");


		#recupera os tipos de vínculo para professor
		$tiposVinculo = $this->Professor_model->tiposVinculo;

		
		include 'views/busca_professores.php';
	}
}
