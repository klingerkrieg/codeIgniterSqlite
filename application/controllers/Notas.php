<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notas extends CI_Controller {

	
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
	public function index() {

		#busca o registro que tem aquela id
		$dados = $this->Disciplina_model->get(val($_GET,"id",null));

		if (!isset($_GET["input"])){
			$_GET["input"] = 0;
		}
		
		include view('notas');
		
	}

	
	public function disciplinas(){
		$query = $_GET["q"];
		$disciplinas = $this->Disciplina_model->findAll(["nome like"=>$query, "limit"=>5]);
		$json = ["success"=>true, "results"=>[]];

		foreach($disciplinas as $ln){
			array_push($json["results"], ["name"=>$ln["nome"], "value"=>$ln["id"]]);
		}
		
		print json_encode($json);
	}

	
	public function lancar($idAux){
		print $this->Disciplina_model->lancar($idAux, $_GET);
	}

	
}
