<?php

class AbstractModel extends CI_Model {

	#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
	public $table = "";
	public $fields = [""];
	public $searchFields = [];

	#para fazer associacoes entre tabelas
	#defina uma matriz com os relacionamentos [['table'=>'usuarios', 'key'=>'usuario_id'], ...]
	public $oneToMany = false;
	public $manyToOne = false;
	#a matriz do manyToMany requer 3 itens
	#[['table'=>'usuarios', 'key'=>'usuario_id', 'assocTable'=>'gruposusuarios'], ...]
	public $manyToMany = false;

	public $secureField = false;
	public $secureLevels = false;

	public $arrayFields = false;

	public $saveLog = true;

	public function __construct(){
		parent::__construct();

		if ($this->arrayFields == false)
			$this->arrayFields = [];
			
		foreach($this->fields as $key=>$field){
			if (!is_numeric($key)){
				if ($field == "array"){
					array_push($this->arrayFields,$key);
				} else
				if ($field == "secure"){
					$this->secureField = $key;
				}
			}
		}
		$arr = array_merge($this->arrayFields);
		if ($this->secureField != false){
			array_push($arr, $this->secureField);
		}
		foreach($arr as $field){
			unset($this->fields[$field]);
			array_push($this->fields, $field);
		}
		if (count($this->arrayFields) == 0)
			$this->arrayFields = false;
		
		

		#se tiver campo de seguranca
		if ($this->secureField != false){
			$_SESSION["model_seguranca"] = get_class($this);
		}
	}


	function decamelize($string) {
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
	}

	public function parseArrayFields($obj){
		if ($this->arrayFields != false){
			foreach($this->arrayFields as $field){
				if (isset($obj->$field) && !is_array($obj->$field))
					$obj->$field = explode(";",$obj->$field);
			}
		}
		return $obj;
	}
	
	//recebo a id do elemento que quero abrir
	public function get($id = null){
		if ($id == null){
			//se a id for nula, retorna um array vazio

			//se houver id no POST, provavelmente é porque houve erro de validacao
			//retorna os dados que foram submetidos pelo formulário
			if (isset($_POST['id'])){
				$data = $_POST;
				if ($_POST['id'] != ""){
					$state = $this->get($_POST['id']);
		
					foreach($_POST as $key=>$val){
						if ( isset($state[$key]) ){
							$state[$key] = $_POST[$key];
						}
					}
					
					$data = $state;
				}
				return $this->parseArrayFields($data);
			}

			return [];
		} else {
			//caso contrário, busca no banco
			//aquele elemento da tabela que tem aquela id
			$obj = R::load($this->table,$id);

			#busca os registros relacionados
			if ($this->manyToOne != false){
				foreach($this->manyToOne as $rel){
					$name = $rel["table"];
					$disc = $obj->fetchAs( $rel["table"] )->$name;
				}
			}

			return $this->parseArrayFields($obj);
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
	public function pagination($per_page, $page, $busca = null, $orderBy = null){

		//Quantidade de itens por pagina
		$loc = ($page-1) * $per_page;

		$where = [];
		$values = [];
		$joins = [];

		#se o searchFields tiver sido preenchido, usa ele, se nao, usa o fields
		$fields = (count($this->searchFields) == 0) ? $this->fields : $this->searchFields;

		if (is_array($busca)){

			foreach($fields as $field ){

				
				if (isset($busca[$field])){

					#se valor for igual a 0
					if ($busca[$field] == "0"){
						continue;
					}

					# se for campos de checkbox
					if (is_array($busca[$field])){

						#se estiver entre os campos de array
						#quer dizer que no banco estará salvo separado por ;
						if (in_array($field,$this->arrayFields)){

							$chk = [];
							$writer = R::getWriter();
							foreach($busca[$field] as $v){
								array_push($chk, "$field like '%;".addslashes ($v).";%'");
							}
							$chk = "(" . implode(" or ", $chk) . ")";
							#gera algo como: (field like '%;1;%' or field like '%;2;%')
							array_push($where, $chk);

						} else {

							$chk = [];
							$writer = R::getWriter();
							foreach($busca[$field] as $v){
								array_push($chk, addslashes ($v));
							}
							$chk = implode(",",$chk);
							#gera algo como: field IN (1,2)
							array_push($where, $this->decamelize($field)." IN ($chk)");
						}
						continue;
					} else
					if (trim($busca[$field]) == ""){
						continue;
					}


					
					$tbl = "";
					if (strstr($field,"_id")){
						#constroi o nome da tabela
						$tbl = str_replace("_id","",$field);
						$joinField = "";
						#procura nos relacionamentos muitos para muitos
						foreach($this->manyToMany as $rel){
							
							if ($rel["table"] == $tbl){

								$joinField = $rel["assocTable"].".".$field;

								$joinStr = "inner join {$rel['assocTable']} on "
										." {$this->table}.id = {$rel['assocTable']}.{$this->table}_id ";


								array_push($joins, $joinStr);
							}
						}
						
					}

					array_push($where, $this->decamelize($field)." like ?");
					array_push($values,"%".$busca[$field]."%");
				}
			}

			$where = implode(" AND ", $where);
			if ($where != ""){
				$where = " WHERE " . $where;
			}

		} else {
			if ($busca != null){
				
				foreach($fields as $field ){

					#ignora busca em outras tabelas
					if (strstr($field,"_id")){
						continue;
					}

					array_push($where, $this->decamelize($field)." like ?");
					array_push($values,"%".$busca."%");
				}
			}

			$where = implode(" or ", $where);
			if ($where != ""){
				$where = " WHERE " . $where;
			}
		}
		
		$joins = implode (" ", $joins);

		if ($orderBy == null){
			$orderBy = $this->decamelize($this->fields[0]);
		}
		
		//Seleciona todos os dados, ordenando pelo primeiro campo da tabela
		$list = R::findAll($this->table , $joins 
							. $where 
							. " ORDER BY " 
							. $orderBy
							. " LIMIT $loc,$per_page ", $values );
		
		//Recupera a quantidade total de itens na tabela
		$qtd = R::count($this->table, $joins . $where, $values );
		
		//Retorna um array com os dados, total de registros,
		//qtd de itens por pagina e a quantidade máxima de páginas
		return ["data"=>$list,"total_rows"=>$qtd, "per_page"=>$per_page, "page_max"=>ceil($qtd/$per_page)];
	}
		
	public function options($field){
		$all = R::getAll("select id, {$field} from {$this->table}");
		return [""] + toOptions($all);
	}
	
	public function all(){
		
		return R::findAll($this->table);
		
	}
	
	public function delete($id){
		
		$obj = R::load($this->table,$id);
		
		#remove os registros associados
		if ($this->manyToMany)
			foreach($this->manyToMany as $rel){
				$var = "own".ucfirst($rel["assocTable"])."List";
				foreach ($obj->$var as $assoc ){
					R::trash($assoc);
				}
			}
		#desfaz as associacoes
		if ($this->oneToMany)
			foreach($this->oneToMany as $rel){
				$var = "own".ucfirst($rel["assocTable"])."List";
				foreach ($obj->$var as $assoc ){
					$tbl = $this->table;
					$assoc->$tbl = null;
					R::store($assoc);
				}
			}		


		R::trash($obj);
		$this->saveLog();
		
	}


	public function preSave($obj, $data){
		return $obj;
	}

	public function posSave($obj, $data){
		
	}


	public function getOrSave($data){
		$obj = $this->findOne($data);
		if ($obj == null){
			return $this->save($data);
		} else {
			return $obj;
		}
	}


	public function save($data=null) {
		
		if ($data == null){
			$data = $_POST;
		}

		$fields = prepare_fields($this->fields, $data);
		
		$obj = R::load($this->table, val($data,'id'));

		
		foreach($fields as $key=>$val){
			if (is_array($val)){
				$obj[$key] = implode(";",$val).";";
			} else {
				$obj[$key] = trim($val);
			}
		}

		#verifica se existe relacionamentos
		if ($this->oneToMany){
			foreach($this->oneToMany as $rel){
				if (val($data,$rel["key"]) != "" && val($data,$rel["key"]) != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"], $data[$rel["key"]]);
					#para relacionar um para muitos utilize essa notacao
					#own+NomeDaTabela+List
					#essa propriedade sera um array com varios daquele model que voce adicionar
					$tblName = "own" . ucfirst($rel["table"]) . "List";
					array_push($obj->$tblName, $another);
				}
			}
		}

		if ($this->manyToOne){
			foreach($this->manyToOne as $rel){
				if (val($data,$rel["key"]) != "" && val($data,$rel["key"]) != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"],$data[$rel["key"]]);
					$tblName = $rel["table"];
					#associa o item
					$obj->$tblName = $another;
				}
			}
		}

		if ($this->manyToMany){
			foreach($this->manyToMany as $rel){
				if (val($data,$rel["key"]) != "" && val($data,$rel["key"]) != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"], $data[$rel["key"]]);
					#cria a tabela de associacao
					$assoc = R::dispense($rel["assocTable"]);

					#associa os itens
					$tbl1 = $rel["table"];
					$tbl2 = $this->table;

					$found = R::find($rel["assocTable"], "{$tbl1}_id = ? and {$tbl2}_id = ?", [$another->id, $obj->id]);
					
					#para evitar que cadastre duas vezes
					#antes de salvar, verifica se ja foram relacionados
					if ($found == null){
						$assoc->$tbl1 = $another;
						$assoc->$tbl2 = $obj;
						R::Store($assoc);
					}
				}
			}
		}

		$obj = $this->preSave($obj, $data);
		
		$id = R::store($obj);

		$this->posSave($obj, $data);

		$this->saveLog();

		return $id;
	}


	public function saveLog(){
		if ($this->saveLog){

			$logs = R::getLogs();

			foreach($logs as $log){
				if (strstr(strtolower($log),"insert") ||
					strstr(strtolower($log),"update") || 
					strstr(strtolower($log),"delete")){

						$dblog = R::dispense("logs");
						$dblog->usuario_id = val($_SESSION,"user_id");
						$dblog->sql = strip_tags($log);
						R::store($dblog);
				}
			}
		}
	}


}