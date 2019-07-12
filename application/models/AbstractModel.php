<?php

class AbstractModel extends CI_Model {

	public $table = "";
	public $fields = [""];
	public $searchFields = [];


	function decamelize($string) {
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
	}
	
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
	public function pagination($page, $busca = null){
		
		//Quantidade de itens por pagina
		$max_items_per_page = 10;
		$loc = ($page-1) * $max_items_per_page;

		$where = [];
		$values = [];
		if ($busca != null){
			#se o searchFields tiver sido preenchido, usa ele, se nao, usa o fields
			$fields = (count($this->searchFields) == 0) ? $this->fields : $this->searchFields;
			
			foreach($fields as $field ){
				array_push($where, $field." like ?");
				array_push($values,"%".$busca."%");
			}
		}
		$where = implode(" or ", $where);

		//Seleciona todos os dados, ordenando pelo primeiro campo da tabela
		$list = R::findAll($this->table , $where . " ORDER BY " . $this->decamelize($this->fields[0]) 
						. " LIMIT $loc,$max_items_per_page ", $values );
		
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


	public function preSave($obj, $data){
		return $obj;
	}

	public function posSave($obj, $data){
		
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

		$obj = $this->preSave($obj, $data);
		
		$id = R::store($obj);

		$this->posSave($obj, $data);

		return $id;
	}


}