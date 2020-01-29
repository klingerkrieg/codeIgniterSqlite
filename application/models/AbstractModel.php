<?php

class AbstractModel extends CI_Model {

	#nomes de tabelas e campos nao podem ter _ - ou letras maiusculas
	public $table = "";
	public $fields = [""];
	public $searchFields = [];

	#para fazer associacoes entre tabelas
	#defina uma matriz com os relacionamentos [['table'=>'usuarios', 'key'=>'usuario_id'], ...]
	public $oneToOne = false;
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
					$obj->$field = json_decode($obj->$field);
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
			//não é necessário que id tenha um valor
			//o fato de existir a id, já indica que foi enviado pelo formulário
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

			#se nao encontrar nada, retorna um array vazio
			if ($obj->id == 0){
				return [];
			}

			#busca os registros relacionados
			if ($this->manyToOne != false){
				foreach($this->manyToOne as $rel){
					$field = $rel["table"];
					if (isset($rel["field"]))
						$field = $rel["field"];

					#se for autorelacionamento é preciso compor o own__List
					#o redbean nao faz sozinho
					if ($rel["table"] == $this->table){
						$own = "own". ucfirst($field) . "List";
						$arr = R::find($this->table,"{$field}_id = ?",[$obj->id]);
						$obj->$own = $arr;
						$assoc = $obj->fetchAs( $rel["table"] )->$field;
					}
					
					
					$assoc = $obj->fetchAs( $rel["table"] )->$field;
				}
			}

			if ($this->oneToOne != false){
				foreach($this->oneToOne as $rel){
					#isso só é necessário quando se está
					#no lado que não tem a chave
					if ($this->table != $rel["side"]){
						$tblName = $rel["side"];
						$obj->$tblName = R::findOne($tblName," {$this->table}_id = ?", [$id]);
					}
				}
			}


			if ($this->manyToMany != false){
				foreach($this->manyToMany as $rel){
					#isso só é necessário quando em autorelacionamento
					if ($this->table == $rel["table"]){
						$field = $rel["field"];#field obrigatório
						$arr = R::findAll($rel["assocTable"]," {$field}_id = ? or {$this->table}_id = ?", [$id, $id]);

						#busca o restante das informacoes
						foreach($arr as $ln){
							$r2 = $ln->fetchAs($this->table)->$field;
							$tbl = $this->table;
							$r3 = $ln->fetchAs($this->table)->$tbl;
						}
					} else {
						$tbl1 = $rel["table"];
						if (isset($rel["field"]))
							$tbl1 = $rel["field"];
						$tbl2 = $this->table;
						$arr = R::findAll($rel["assocTable"]," {$this->table}_id = ?", [$id]);
						
						#busca o restante das informacoes
						foreach($arr as $ln){
							$r2 = $ln->fetchAs($tbl1)->$tbl1;
							$r3 = $ln->fetchAs($tbl2)->$tbl2;
						}
					}

					#add os dados
					$own = "own". ucfirst($rel["assocTable"]) ."List";
					$obj->$own = $arr;
				}
			}

			return $this->parseArrayFields($obj);
		}
	}

	public function findOne($data){
		$where = [];
		$whereData = [];
		foreach($data as $key=>$value){
			if (in_array($key,$this->fields)){
				array_push($where, "$key = ?");
				array_push($whereData, $value);
			}
		}
		$where = implode(" and ", $where);
		
		return R::findOne($this->table,$where,$whereData);
	}

	/**
	 * Passe a string que será buscada ou um array com os campos e os valores a serem buscados
	 * o array também pode conter:
	 * 'order_by'=>'nome do campo'
	 * 'per_page'=>10 (default)
	 * */
	public function pagination($arr=""){

		include(APPPATH.'/config/config.php');
		$busca = null;
		$order_by = null;
		$per_page = $config['per_page'];
		
		
		if (is_string($arr)){
			$busca = $arr;
		} else {
			$busca = $arr;
			$order_by = val($arr,"order_by", null);
			$per_page = val($arr,"per_page", null);
			if ($per_page == null)
				$per_page = $config['per_page'];
		}
		
		#página atual
		if (isset($_GET['page'])){
			$page = $_GET['page']-1;
		} else {
			$page = 0;
		}
		//Quantidade de itens por pagina
		$loc = $page * $per_page;

		$where = [];
		$values = [];
		$joins = [];

		#se o searchFields tiver sido preenchido, usa ele, se nao, usa o fields e as keys de todos os relacionamentos
		if (count($this->searchFields) > 0){
			$fields = $this->searchFields;
		} else {
			$fields = $this->fields;
			if ($this->manyToMany) foreach($this->manyToMany as $rel){
				array_push($fields,$rel["key"]);
			}
			if ($this->manyToOne) foreach($this->manyToOne as $rel){
				array_push($fields,$rel["key"]);
			}
			if ($this->oneToMany) foreach($this->oneToMany as $rel){
				array_push($fields,$rel["key"]);
			}
			if ($this->oneToOne) foreach($this->oneToOne as $rel){
				array_push($fields,$rel["key"]);
			}
		}
		
		#BUSCA AVANCADA
		if (is_array($busca)){

			foreach($fields as $field ){

				
				if (isset($busca[$field])){

					#se valor for igual a 0
					if ($busca[$field] == "0"){
						continue;
					}
					
					# se for campos de checkbox
					if (is_array($busca[$field]) && strstr($field,"_id") == false){

						#se estiver entre os campos de array
						#quer dizer que no banco estará salvo separado por ;
						if (in_array($field,$this->arrayFields)){

							$chk = [];
							$writer = R::getWriter();
							foreach($busca[$field] as $v){
								array_push($chk, "$field like '%".addslashes ($v)."%'");
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
							array_push($where, $this->table.".".$this->decamelize($field)." IN ($chk)");
						}
						continue;
					} else
					if (is_string($busca[$field]) && trim($busca[$field]) == ""){
						continue;
					}


					

					#JOINS
					$tbl = "";
					if (strstr($field,"_id")){
						$nextField = true;
						#constroi o nome da tabela
						$tbl = str_replace("_id","",$field);
						#IDS
						$ids = $busca[$field];
						if (!is_array($ids)){
							$ids = [$ids];
						}
						$ids = implode(", ",$ids);
						


						#procura nos relacionamentos muitos para muitos
						if ($this->manyToMany) foreach($this->manyToMany as $rel){
							#test:Busca *x* (Disc. side)
							#test:Busca *x* (Aluno side)
							if ($rel["table"] == $tbl){
								$joinStr = "inner join {$rel['assocTable']} on "
										." {$this->table}.id = {$rel['assocTable']}.{$this->table}_id  and "
										." {$rel['assocTable']}.{$tbl}_id IN ($ids)";
								array_push($joins, $joinStr);
								$nextField = false;
								break;
							}
						}
						if (!$nextField) continue;
						
						
						if ($this->oneToOne) foreach($this->oneToOne as $rel){
							if ($rel["table"] == $tbl){
								if ($rel["side"] == $this->table){
									#test:Busca 1x1 (Coord. side)
									array_push($where, $this->table.".".$this->decamelize($field)." IN ($ids)");
								} else {
									#test:Busca 1x1 (Prof. side)
									$joinStr = "inner join {$rel['table']} on "
										." {$this->table}.id = {$rel['table']}.{$this->table}_id and "
										." {$rel['table']}.id IN($ids) ";
									array_push($joins, $joinStr);
								}
								$nextField = false;
								break;
							}
						}
						if (!$nextField) continue;

						
						if ($this->oneToMany) foreach($this->oneToMany as $rel){
							if ($rel["table"] == $tbl){
								#test:Busca 1x* (Prof. side)
								$joinStr = "inner join {$rel['table']} on "
										." {$this->table}.id = {$rel['table']}.{$this->table}_id and "
										." {$rel['table']}.id IN($ids) ";
										
								array_push($joins, $joinStr);
								$nextField = false;
								break;
							}
						}
						if (!$nextField) continue;


						if ($this->manyToOne) foreach($this->manyToOne as $rel){
							if ($rel["table"] == $tbl){
								#test:Busca *x1 (Disc. side)
								array_push($where, $this->table.".".$this->decamelize($field)." IN ($ids)");
								$nextField = false;
								break;
							}
						}
						if (!$nextField) continue;
						
					}

					array_push($where, $this->table.".".$this->decamelize($field)." like ?");
					array_push($values,"%".$busca[$field]."%");
				}
			}

			$where = implode(" AND ", $where);
			if ($where != ""){
				$where = " WHERE " . $where;
			}

		} else {
			#BUSCA SIMPLES
			if ($busca != null){
				
				foreach($fields as $field ){

					#ignora busca em outras tabelas
					if (strstr($field,"_id")){
						continue;
					}

					array_push($where, $this->table.".".$this->decamelize($field)." like ?");
					array_push($values,"%".$busca."%");
				}
			}

			$where = implode(" or ", $where);
			if ($where != ""){
				$where = " WHERE " . $where;
			}
		}
		
		$joins = implode (" ", $joins);

		if ($order_by == null){
			$order_by = $this->decamelize($this->fields[0]);
		}
		
		//Seleciona todos os dados, ordenando pelo primeiro campo da tabela
		$list = R::findAll($this->table , $joins 
							. $where 
							. " ORDER BY " 
							. $order_by
							. " LIMIT $loc,$per_page ", $values );

		
		
		//Recupera a quantidade total de itens na tabela
		$qtd = R::count($this->table, $joins . $where, $values );

		
		foreach($list as $key=>$data){

			#em caso de 1 para 1, tem que recuperar os dados manualmente
			if ($this->oneToOne != false){
				foreach($this->oneToOne as $rel){
					#isso só é necessário quando se está
					#no lado que não tem a chave
					if ($this->table != $rel["side"]){
						$tblName = $rel["side"];
						$list[$key]->$tblName = R::findOne($tblName," {$this->table}_id = ?", [$data->id]);
					}
				}
			}

			#em caso de autorelacionamento, tem que dar o fetchAs
			if ($this->manyToOne != false){
				foreach($this->manyToOne as $rel){
					if ($rel["table"] == $this->table){
						$field = $rel["table"];
						if (isset($rel["field"])){
							$field = $rel["field"];
						}
						$assoc = $list[$key]->fetchAs( $this->table )->$field;
					}
				}
			}
		}
		
		
		
		//Retorna um array com os dados, total de registros,
		//qtd de itens por pagina e a quantidade máxima de páginas
		return ["data"=>$list,"total_rows"=>$qtd, "per_page"=>$per_page, "page_max"=>ceil($qtd/$per_page)];
	}
		
	public function options(){
		$fields = func_get_args();
		$fields = implode(",",$fields);
		$all = R::getAll("select id, $fields from {$this->table}");
		return toOptions($all);
	}
	
	public function all(){
		
		return R::findAll($this->table);
		
	}
	
	public function delete($id){

		if ($this->saveLog)
			$this->saveState($id);
		
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
				$var = "own".ucfirst($rel["table"])."List";
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
				#$obj[$key] = implode(";",$val).";";
				
				#remove o input hidden
				$val2 = [];
				foreach($val as $k=>$v){
					if ($v != ""){
						array_push($val2,$v);
					}
				}
				$obj[$key] = json_encode($val2);
			} else {
				$obj[$key] = trim($val);
			}
		}

		#verifica se existe relacionamentos
		if ($this->oneToOne){
			foreach($this->oneToOne as $rel){
				if (isset($data[$rel["key"]])){

					#em relacionamento 1 para 1, a chave deve ficar sempre de um lado só
					#o programador escolhe o lado
					if ($rel["side"] == $this->table){
						$field = $rel["table"];
						if (isset($rel["field"]))
							$field = $rel["field"];

						if ($data[$rel["key"]] != 0){
							#seta todos que estao relacionados com esse para NULL
							R::exec("UPDATE $this->table set {$field}_id = NULL WHERE {$field}_id = ?",[$data[$rel["key"]]]);

							#recupera o item da tabela
							$another = R::load($rel["table"], $data[$rel["key"]]);
							#relaciona no formato um para um
							$obj->$field = $another;
						} else {
							$obj->$field = null;
						}
					} else {
						#se eu estiver do outro lado
						$field = $this->table;
						if ($data[$rel["key"]] != 0){
							#recupera o item da tabela do lado correto
							$another = R::load($rel["side"], $data[$rel["key"]]);
							#relaciona no formato um para um
							$another->$field = $obj;
						} else {
							#apaga o relacionamento
							$another = R::findOne($rel["side"], "{$this->table}_id = ?", [$obj->id]);
							$another->$field = null;
						}
						R::Store($another);
					}
				}
			}
		}

		
		if ($this->oneToMany){
			foreach($this->oneToMany as $rel){
				if (isset($data[$rel["key"]]) && $data[$rel["key"]] != 0){
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
				if (isset($data[$rel["key"]]) && $data[$rel["key"]] != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"],$data[$rel["key"]]);
					$tblName = $rel["table"];
					if (isset($rel["field"]))
						$tblName = $rel["field"];
					
					#associa o item
					$obj->$tblName = $another;
				} else 
				#remove a associação
				if (isset($data[$rel["key"]]) && $data[$rel["key"]] == 0){
					$tblName = $rel["table"];
					if (isset($rel["field"]))
						$tblName = $rel["field"];
					$obj->$tblName = null;
				}
			}
		}


		if ($this->manyToMany){
			foreach($this->manyToMany as $rel){
				if (isset($data[$rel["key"]]) && $data[$rel["key"]] != 0){
					#recupera o item da tabela
					$another = R::load($rel["table"], $data[$rel["key"]]);
					#cria a tabela de associacao
					$assoc = R::dispense($rel["assocTable"]);

					#associa os itens
					$tbl1 = $rel["table"];
					if (isset($rel["field"]))
						$tbl1 = $rel["field"];
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

		if ($this->saveLog && $obj->id != 0)
			$this->saveState($obj->id);
		
		$id = R::store($obj);

		$this->posSave($obj, $data);

		if ($this->saveLog)
			$this->saveLog();

		return $id;
	}


	private $tmpState = "";
	public function saveState($id){
		$obj = R::load($this->table, $id);
		$this->tmpState = json_encode($obj);
	}


	public function saveLog(){
		if ($this->saveLog){

			$logs = R::getLogs();

			foreach($logs as $log){
				
				if (strstr(strtolower($log),"insert") 
					|| strstr(strtolower($log),"update") 
					|| strstr(strtolower($log),"delete")){
					
					$dblog = R::dispense("logs");
					$dblog->usuario_id = val($_SESSION,"user_id");
					$dblog->state = $this->tmpState;
					$dblog->sql = strip_tags($log);
					R::store($dblog);
				}
			}
		}
	}


}