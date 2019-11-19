<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testes extends CI_Controller {
	
	private $html = "<table><tr><th>Teste</th><th>Recebido</th><th>Esperado</th><th>Resultado</th><th>Diferença</th></tr>";

	public function __construct(){
		parent::__construct();
	}

	private function test($val, $expected, $nome){

		$expected_str = $expected;
		$val_str = $val;
		
		if (is_string($expected))
			$expected_str = "\"$expected_str\"";
		if (is_string($val))
			$val_str = "\"$val_str\"";
		if (is_object($expected))
			$expected_str = "Object";
		if (is_object($val))
			$val_str = "Object";
		if (is_array($expected))
			$expected_str = "Array";	
		if (is_array($val))
			$val_str = "Array";

		#se for uma classe do redbean, vai comparar só pelas propriedades
		if (is_object($val) && is_object($expected) && get_class($val) == "RedBeanPHP\OODBBean" && get_class($expected) == "RedBeanPHP\OODBBean"){
			$val = json_decode(json_encode($val),true);
			$expected = json_decode(json_encode($expected),true);
		}
		

		$this->html .= "<tr><td>$nome</td><td>$val_str</td><td>$expected_str</td>";
		if ($val != $expected){
			$this->html .= "<td style='color:red;'>Falha</td><td>";
			
			if (is_array($val) || is_array($expected) || is_object($val) || is_object($expected)){
				$val = json_decode(json_encode($val),true);
				$expected = json_decode(json_encode($expected),true);
				$this->html .= "<div><pre><b>Recebido:</b>". print_r(array_diff_assoc($val, $expected),true) ."</pre></div>";
				$this->html .= "<div><pre><b>Esperado:</b>". print_r(array_diff_assoc($expected, $val),true). "</pre></div>";
			}
			$this->html .= "</td></tr>";
		} else {
			$this->html .= "<td style='color:green;'>Sucesso</td></tr>";
		}

	}

	private function report(){
		return $this->html . "</table>";
	}
	
	public function index() {

		
		$arr = ["item"=>1,"arr"=>["item2"=>2]];
		$this->test(val($arr,"item"), 1, "val(...)");
		$this->test(val($arr,"arr","item2"), 2, "val(...)");
		$this->test(val($arr,"item3"), "", "val(...)");
		$this->test(val($arr,"arr","item3"), "", "val(...)");


		$fields = ["nome","data","setores_id"];
		$post = ["nome"=>"joao","idade"=>21, "carros_id"=>1];
		$this->test(prepare_fields($fields,$post), ["nome"=>"joao", "setores_id"=>null], 'prepare_fields($fields,$post)');
		$fields = ["nome","data","setores_id"];
		$post = ["nome"=>"joao","data"=>"19/09/2000", "setores_id"=>1];
		$this->test(prepare_fields($fields,$post), ["nome"=>"joao","data"=>"19/09/2000", "setores_id"=>1], 'prepare_fields($fields,$post)');

		$arr = ["ativo"=>1];
		$this->test(checked(1, $arr, "ativo"), 'checked', 'checked($val, $arr, $field)');
		$this->test(checked(0, $arr, "ativo"), '', 'checked($val, $arr, $field)');
		$this->test(checked(1, ["linguagens"=>[1,2]], "linguagens"), 'checked', 'checked($val, $arr, $field)');
		$this->test(checked(3, ["linguagens"=>[1,2]], "linguagens"), '', 'checked($val, $arr, $field)');
		
		$arr = [["id"=>1,"nome"=>"joao"],["id"=>2,"nome"=>"maria"],["id"=>3,"nome"=>"ze"]];
		$resp = [1=>"joao",2=>"maria",3=>"ze"];
		$this->test(toOptions($arr), $resp, 'toOptions($arr)');
		$arr = [["id"=>1,"email"=>"joao@gmail.com"],["id"=>2,"email"=>"maria@gmail.com"],["id"=>3,"email"=>"ze@gmail.com"]];
		$resp = [1=>"joao@gmail.com",2=>"maria@gmail.com",3=>"ze@gmail.com"];
		$this->test(toOptions($arr), $resp, 'toOptions($arr)');


		#AbstractModel
		$this->load->model("Usuario_model");
		$this->load->model("Setor_model");
		$this->load->model("Grupo_model");
		$fields = [0=>"nome", 1=>"email",2=>"senha",3=>"tipo",4=>"foto",5=>"area",6=>"nivel"];
		$this->test($this->Usuario_model->fields, $fields, 'Usuarios_model->fields');

		
		$fields = [0=>"nome",1=>"email",2=>"tipo",3=>"area",4=>"nivel",5=>"setores_id",6=>"grupos_id"];
		$this->test($this->Usuario_model->searchFields, $fields, 'Usuarios_model->searchFields');

		$arr = [0=>["table"=>"usuarios", "key"=>"usuarios_id"]];
		$this->test($this->Setor_model->oneToMany, $arr, 'Setor_model->manyToOne');
		
		$arr = [0=>["table"=>"setores", "key"=>"setores_id"]];
		$this->test($this->Usuario_model->manyToOne, $arr, 'Usuarios_model->manyToOne');

		$arr = [0=>["table"=>"grupos", "key"=>"grupos_id", "assocTable"=>"gruposusuarios"]];
		$this->test($this->Usuario_model->manyToMany, $arr, 'Usuarios_model->manyToMany');

		
		$this->test($this->Usuario_model->secureField, "nivel", 'Usuarios_model->secureField');
		$this->test($this->Usuario_model->secureLevels, [1=>"Admin",2=>"Comum",3=>"Convidado"], 'Usuarios_model->secureLevels');
		$this->test($this->Usuario_model->arrayFields, ["area"], 'Usuarios_model->arrayFields');
		

		#salvando

		$dados_setor = ["nome"=>"Setor teste"];
		$id_setor = $this->Setor_model->save($dados_setor);

		$dados_grupo = ["nome"=>"Grupo teste"];
		$id_grupo = $this->Grupo_model->save($dados_grupo);


		$qtd_setores = R::count("setores");
		$obj = $this->Setor_model->getOrSave($dados_setor);
		$qtd_setores2 = R::count("setores");
		$this->test($qtd_setores, $qtd_setores2, 'Usuarios_model->getOrSave (get)');
		$obj = $this->Setor_model->getOrSave(["nome"=>"Setor teste 2"]);
		$qtd_setores3 = R::count("setores");
		$this->test($qtd_setores2+1, $qtd_setores3, 'Usuarios_model->getOrSave (save)');


		$dados = ["nome"=>"Test user",
				 "email"=>"test@gmail.com",
				 "senha"=>"123456",
				 "senhaConfirm"=>"123456",
				 "setores_id"=>$id_setor,
				 "area"=>[1,2],
				 "tipo"=>1,
				 "nivel"=>3];
		$id = $this->Usuario_model->save($dados);


		#update
		$dados_update = $dados;
		$dados_update["id"] = $id;
		$dados_update["grupos_id"] = $id_grupo;
		$id = $this->Usuario_model->save($dados_update);


		$this->test(is_numeric($id), true, 'Usuarios_model->save (return)');
		$loaded = R::load("usuarios",$id);
		$loaded_arr = ["nome"=>$loaded->nome,
						"email"=>$loaded->email,
						"senha"=>$loaded->senha,
						"setores_id"=>$loaded->setores->id,
						"area"=>$loaded->area,
						"tipo"=>$loaded->tipo,
						"nivel"=>$loaded->nivel];
		$post_arr = $dados;
		$post_arr["senha"] = sha1($post_arr["senha"]);
		$post_arr["area"] = implode(";",$post_arr["area"]).";";
		unset($post_arr["senhaConfirm"]);

		$this->test($post_arr, $loaded_arr, 'Usuarios_model->save (object)');


		#relacionamento many to many
		$ln = array_shift($loaded->ownGruposusuariosList);
		#print_r(["grupos_id"=>$ln->grupos_id,"usuarios_id"=>$ln->usuarios_id]);
		#print_r(["grupos_id"=>$id_grupo,"usuarios_id"=>$id]);
		$this->test(["grupos_id"=>$ln->grupos_id,"usuarios_id"=>$ln->usuarios_id],
					["grupos_id"=>$id_grupo,"usuarios_id"=>$id],
					'Usuarios_model->save (manyToMany)');


		
		#recuperando
		#get
		$obj = $this->Usuario_model->get($id);
		$loaded = R::load("usuarios",$id);
		$setores = $loaded->fetchAs( "setores" )->setores;
		$loaded = $this->Usuario_model->parseArrayFields($loaded);
		$this->test($obj, $loaded, 'Usuarios_model->get');

		#get (erro do formulario)
		$_POST = ["id"=>"","nome"=>"joao"];
		$post = $this->Usuario_model->get(null);
		$this->test($post, $_POST, 'Usuarios_model->get (form error)');

		#get (erro do formulario, porem o registro ja existe)
		#traz todas as informacoes do banco, com a alteração do post
		#basta ter a id no $_POST
		$_POST = ["id"=>$id,"nome"=>"joao"];
		$arrExpec = $obj;
		$arrExpec["nome"] = "joao";
		$post = $this->Usuario_model->get(null);
		$this->test($post, $arrExpec, 'Usuarios_model->get (form error exists)');
		$_POST = [];

		
		#findOne
		$obj = $this->Usuario_model->findOne($post_arr);
		$this->test($obj != false, true, 'Usuarios_model->findOne');
		

		#paginacao
		$perPage = 10;
		$pages = $this->Usuario_model->pagination(10, 1, "");
		$this->test(count($pages["data"]), $perPage, 'Usuarios_model->pagination (data)');
		$this->test($pages["per_page"], $perPage, 'Usuarios_model->pagination (per_page)');
		$this->test($pages["page_max"], ceil($pages["total_rows"]/$perPage), 'Usuarios_model->pagination (page_max)');

		$pages = $this->Usuario_model->pagination(10, 1, "Test user");
		$ln = array_shift($pages["data"]);
		$this->test($ln->nome, "Test user", 'Usuarios_model->pagination (user->nome)');
		$this->test($ln->setores->nome, "Setor teste", 'Usuarios_model->pagination (user->setor)');

		$pages = $this->Usuario_model->pagination(10, 1, $loaded_arr);
		$this->test(count($pages["data"]) > 0, true, 'Usuarios_model->pagination (filtros)');


		#options
		$options = $this->Usuario_model->options("nome");
		$key = array_key_first($options);
		$first = array_shift($options);
		$this->test($key.$first, "0", 'Usuarios_model->options (first blank)');
		$key = array_key_first($options);
		$first = array_shift($options);
		$this->test(is_numeric($key), true, 'Usuarios_model->options (second id)');
		$this->test(is_numeric($first), false, 'Usuarios_model->options (second name)');

		#all
		$qtd = count($this->Usuario_model->all());
		$this->test($qtd > 0, true, 'Usuarios_model->all');


		#delete
		$this->Setor_model->delete($id_setor);
		$setor = $this->Setor_model->get($id_setor);
		$this->test($setor, [], 'Setor_model->delete');
		$loaded = $this->Usuario_model->get($id);
		$this->test($loaded->id, $id, 'Setor_model->delete (usuario->id)');
		$this->test($loaded->email, $dados["email"], 'Setor_model->delete (usuario->email)');
		$this->test($loaded->setores, null, 'Setor_model->delete (usuario->setores)');

		$this->Usuario_model->delete($id);
		$loaded = $this->Usuario_model->get($id);
		$this->test($loaded, [], 'Usuario_model->delete');


		$grupo = $this->Grupo_model->get($id_grupo);
		$found = false;
		foreach($grupo->ownGruposusuariosList as $ln){
			if ($ln->usuarios->id == $id){
				$found = true;
			}
		}
		$this->test($found, false, 'Usuario_model->delete (grupos->ownGruposusuariosList)');


		$this->Grupo_model->delete($id_grupo);
		$grupo = $this->Usuario_model->get($id);
		$this->test($grupo, [], 'Grupo_model->delete');
		

		#limpa os testes
		$testes = R::findAll("usuarios","nome like ? ",[$dados["nome"]."%"]);
		foreach($testes as $t){
			R::trash($t);
		}

		$testes = R::findAll("setores","nome like ? ",[$dados_setor["nome"]."%"]);
		foreach($testes as $t){
			R::trash($t);
		}

		$testes = R::findAll("grupos","nome like ? ",[$dados_grupo["nome"]."%"]);
		foreach($testes as $t){
			R::trash($t);
		}

		$report = $this->report();

		$this->load->view("testes",["report"=>$report]);
		
		
	}
	
	
}

