<?php

class AbstractModel extends CI_Model {

	public $table = "";
	public $fields = [""];
	
	//recebo a id do elemento que quero abrir
	public function get($id = null){
		if ($id == null){
			//se a id for nula, retorna um array vazio
			return [];
		} else {
			//caso contrário, busca no banco
			//aquele elemento da tabela que tem aquela id
			return R::load($this->table,$id);
		}
	}

	public function findOne($data){
		$where = [];
		$whereData = [];
		foreach($data as $key=>$value){
			array_push($where, "$key = ?");
			array_push($whereData, $value);
		}
		$where = implode(" and ", $where);

		return R::findOne($this->table,$where,$whereData);
	}

	//recebe o número da página que será exibida, inicia na 1
	public function pagination($page){
		
		//Quantidade de itens por pagina
		$max_items_per_page = 10;
		$loc = ($page-1) * $max_items_per_page;

		//Seleciona todos os dados, ordenando pelo primeiro campo da tabela
		$list = R::findAll($this->table , " ORDER BY " . $this->fields[0] 
						. " LIMIT $loc,$max_items_per_page " );
		
		//Recupera a quantidade total de itens na tabela
		$qtd = R::count($this->table);
		
		//Retorna um array com a list, registros na página,
		//e a quantidade total de itens.
		return ["list"=>$list,"qtd"=>$qtd];
	}
	
	
	public function all(){
		
		return R::findAll($this->table);
		
	}
	
	public function delete($id){
		
		$obj = R::load($this->table,$id);
		R::trash($obj);
		
	}


	public function save($data=null) {
		
		if ($data == null){
			$data = $_POST;
		}

		$fields = prepare_fields($this->fields, $data);
		
		$obj = R::load($this->table, val($data,'id'));

		
		foreach($fields as $key=>$val){
			$obj[$key] = $val;
		}
		
		
		return R::store($obj);
	}


}