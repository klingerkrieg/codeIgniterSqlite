<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Testes extends CI_Controller {
	
	private $html = "<table><tr><th>Teste</th><th>Recebido</th><th>Esperado</th><th>Resultado</th><th>Diferença</th></tr>";

	public function __construct(){
		parent::__construct();
		$this->ini = time();
	}

	private function addSeparator($separator){
		$this->html .= "<tr><td colspan='2'><b>$separator</b></td></tr>";
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
		if (is_object($val) && get_class($val) == "RedBeanPHP\OODBBean"){
			$val = json_decode(json_encode($val),true);
		}
		if (is_object($expected) && get_class($expected) == "RedBeanPHP\OODBBean"){
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
		$this->end = time();
		return $this->html . "</table> Tempo: " .($this->end - $this->ini). "s";
	}
	
	public function index() {

		$dbname = "application/db/test.db";
		R::addDatabase('teste', "sqlite:$dbname");
		R::selectDatabase( 'teste' );
		R::fancyDebug();
		R::getDatabaseAdapter()->getDatabase()->getLogger()->setMode(1);
		#o salvamento do log deve ser desativado, pois impacta no tempo dos testes


		
		$this->addSeparator("Funções do framework");
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
		
		$arr = [["id"=>1,"nome"=>"joao"],["id"=>2,"nome"=>"maria"],["id"=>3,"nome"=>"ze"]];
		$resp = [1=>"joao",2=>"maria",3=>"ze"];
		$this->test(toOptions($arr), $resp, 'toOptions($arr)');
		$arr = [["id"=>1,"email"=>"joao@gmail.com"],["id"=>2,"email"=>"maria@gmail.com"],["id"=>3,"email"=>"ze@gmail.com"]];
		$resp = [1=>"joao@gmail.com",2=>"maria@gmail.com",3=>"ze@gmail.com"];
		$this->test(toOptions($arr), $resp, 'toOptions($arr)');


		$this->addSeparator("AbstractModel");
		#AbstractModel
		$this->load->model("Usuario_model");
		$this->Usuario_model->saveLog = false;
		$fields = [0=>"nome", 1=>"email", 2=>"senha", 3=>"nivel"];
		$this->test($this->Usuario_model->fields, $fields, 'Usuarios_model->fields');

		
		$this->load->model("Professor_model");
		$this->Professor_model->saveLog = false;
		$fields = ["nome", "matricula", "vinculo", "especialidades", "coordenacoes_id", "disciplinas_id"];
		$this->test($this->Professor_model->searchFields, $fields, 'Professor_model->searchFields');

		$arr = [0=>["table"=>"coordenacoes", "key"=>"coordenacoes_id", "side"=>"coordenacoes"]];
		$this->test($this->Professor_model->oneToOne, $arr, 'Professor_model->oneToOne');

		$arr = [0=>["table"=>"disciplinas", "key"=>"disciplinas_id"]];
		$this->test($this->Professor_model->oneToMany, $arr, 'Professor_model->manyToOne');

		$this->load->model("Disciplina_model");
		$this->Disciplina_model->saveLog = false;
		$arr = [0=>["table"=>"professores", "key"=>"professores_id"], 1=>["table"=>"disciplinas", "key"=>"requisito_id", "field"=>"requisito"]];
		$this->test($this->Disciplina_model->manyToOne, $arr, 'Usuarios_model->manyToOne');

		$arr = [0=>["table"=>"alunos", "key"=>"alunos_id", "assocTable"=>"alunosdisciplinas"]];
		$this->test($this->Disciplina_model->manyToMany, $arr, 'Disciplina_model->manyToMany');

		
		$this->test($this->Usuario_model->secureField, "nivel", 'Usuarios_model->secureField');
		$this->test($this->Usuario_model->secureLevels, [1=>"Admin",2=>"Comum",3=>"Convidado"], 'Usuarios_model->secureLevels');
		$this->test($this->Professor_model->arrayFields, ["especialidades"], 'Professor_model->arrayFields');
		

		
		#testando getOrSave
		$this->load->model("Coordenacao_model");
		$this->Coordenacao_model->saveLog = false;
		$coordteste_id = $this->Coordenacao_model->save(["nome"=>"Teste"]);
		$profteste_id = $this->Professor_model->save(["nome"=>"Prof Teste"]);
		$this->Coordenacao_model->save(["id"=>$coordteste_id, "professores_id"=>$profteste_id]);

		$qtd_coords = R::count("coordenacoes");
		$this->Coordenacao_model->getOrSave(["nome"=>"Teste"]);
		$qtd_coords2 = R::count("coordenacoes");
		$this->test($qtd_coords, $qtd_coords2, 'Coordenacao_model->getOrSave (get)');
		$coordteste2_id = $this->Coordenacao_model->getOrSave(["nome"=>"Teste 2"]);
		$qtd_coords3 = R::count("coordenacoes");
		$this->test($qtd_coords2+1, $qtd_coords3, 'Coordenacao_model->getOrSave (save)');


		#get (erro do formulario)
		$_POST = ["id"=>"","nome"=>"teste 3"];
		$post = $this->Coordenacao_model->get(null);
		$this->test($post, $_POST, 'form error');

		#get (erro do formulario, porem o registro ja existe)
		#traz todas as informacoes do banco, com a alteração do post
		#basta ter a id no $_POST
		$_POST = ["id"=>$coordteste2_id,"nome"=>"teste 3"];
		$arrExpec = ["id"=>$coordteste2_id, "nome"=>"teste 3", "professores_id"=>null];
		$rec = $this->Coordenacao_model->get(null);
		$this->test($rec, $arrExpec, 'form error exists');
		$_POST = [];

		

		#paginacao
		$per_page = 10;
		$pages = $this->Coordenacao_model->pagination(["per_page"=>10]);
		$this->test(count($pages["data"]) <= $per_page, true, 'Coordenacao_model->pagination (data)');
		$this->test($pages["per_page"], $per_page, 'Coordenacao_model->pagination (per_page)');
		$this->test($pages["page_max"], ceil($pages["total_rows"]/$per_page), 'Coordenacao_model->pagination (page_max)');

		$pages = $this->Coordenacao_model->pagination("Teste");
		$ln = array_shift($pages["data"]);
		$this->test($ln->nome, "Teste", 'Coordenacao_model->pagination (coord->nome)');
		$this->test($ln->professores->nome, "Prof Teste", 'Coordenacao_model->pagination (coord->prof)');

		$pages = $this->Coordenacao_model->pagination(["nome"=>"Teste"]);
		$this->test(count($pages["data"]) > 0, true, 'Coordenacao_model->pagination (filtros)');


		#options
		$options = $this->Coordenacao_model->options("nome", "professores_id");
		$key = array_key_first($options);
		$first = array_shift($options);
		$this->test(is_numeric($key), true, 'Coordenacao_model->options (second id)');
		$this->test($first, "Teste - $profteste_id", 'Coordenacao_model->options (second name)');

		#all
		$qtd = count($this->Coordenacao_model->all());
		$this->test($qtd > 0, true, 'Coordenacao_model->all');

		
		#findOne
		$obj = $this->Coordenacao_model->findOne(["nome"=>"Teste"]);
		$this->test($obj != false, true, 'Coordenacao_model->findOne');
		

		#findAll
		$qtd = count($this->Coordenacao_model->findAll(["nome like"=>"Teste"]));
		$this->test($qtd > 0, true, 'Coordenacao_model->findAll (like)');
		
		#coordenacao
		$this->addSeparator("Um para Um");
		$post = ["nome"=>"INFO"];
		$coordenacao_id = $this->Coordenacao_model->save($post);
		$this->test(is_numeric($coordenacao_id), true, 'Salva coordenação');


		#professor x coordenacao
		$post = ["nome"=>"Joao Alberto", "vinculo"=>0, "especialidades"=>[1,2], "coordenacoes_id"=>$coordenacao_id];
		$professor_id = $this->Professor_model->save($post);
		$this->test(is_numeric($professor_id), true, 'Salva professor');
		$prof = $this->Professor_model->get($professor_id);
		$this->test($prof->especialidades, [1,2], 'Especialidades do professor (Campo array)');
		$this->test($prof->vinculo, 0, 'Vínculo do professor');
		$this->test(null == $prof->matricula, false, 'Matrícula automática');
		$this->test($prof->coordenacoes->id, $coordenacao_id, 'Rel. do professor com a coord. (1x1)');

		$coord = $this->Coordenacao_model->get($coordenacao_id);
		$this->test($coord->professores->id, $professor_id, 'Rel. da coor. com professor (1x1)');
		#mudando o coordenador
		$post = ["nome"=>"Joao Maria"];
		$professor2_id = $this->Professor_model->save($post);

		$post = ["id"=>$coordenacao_id ,"nome"=>"INFO", "professores_id"=>$professor2_id];
		$this->Coordenacao_model->save($post);
		$coord = $this->Coordenacao_model->get($coordenacao_id);
		$this->test($coord->professores->id, $professor2_id, 'Altera coordenador (1x1)');
		$prof = $this->Professor_model->get($professor_id);
		$this->test($prof->coordenacoes, null, 'Rel. do antigo coordenador (1x1)');
		#removendo coordenacao
		$post = ["id"=>$coordenacao_id , "professores_id"=>0];
		$this->Coordenacao_model->save($post);
		$coord = $this->Coordenacao_model->get($coordenacao_id);
		$this->test($coord->professores, null, 'Remove coordenador (1x1) (Coord. side) [pelo form da coord.]');
		$prof2 = $this->Professor_model->get($professor2_id);
		$this->test($prof2->coordenacoes, null, 'Remove coordenacao (1x1) (Prof. side) [pelo form da coord.]');
		#remove coordenacao pelo lado do prof
		$post = ["id"=>$coordenacao_id , "professores_id"=>$professor2_id];
		$this->Coordenacao_model->save($post);
		$coord = $this->Coordenacao_model->get($coordenacao_id);
		$this->test($coord->professores->id, $professor2_id, 'Reinsere coordenador (1x1)');
		$post = ["id"=>$professor2_id , "coordenacoes_id"=>0];
		$this->Professor_model->save($post);
		$coord = $this->Coordenacao_model->get($coordenacao_id);
		$this->test($coord->professores, null, 'Remove coordenador (1x1) (Coord. side) [pelo form do prof.]');
		$prof2 = $this->Professor_model->get($professor2_id);
		$this->test($prof2->coordenacoes, null, 'Remove coordenacao (1x1) (Prof. side) [pelo form do prof.]');
		
		$this->addSeparator("Um para Muitos");
		#professor x disciplinas
		$post = ["nome"=>"Lógica", "optativa"=>0, "carga_horaria"=>30, "professores_id"=>$professor_id, "requisito_id"=>0];
		$this->load->model("Disciplina_model");
		$this->Disciplina_model->saveLog = false;
		$disciplina_id = $this->Disciplina_model->save($post);
		$disc = $this->Disciplina_model->get($disciplina_id);
		$this->test($disc->requisito, null, 'Disc. sem requisito');
		$this->test($disc->professores->id, $professor_id, 'Rel. disciplina x professor (1x*)');
		$prof = $this->Professor_model->get($professor_id);
		$this->test(count($disc->professores->ownDisciplinasList), 1, 'Rel. disciplina x professor (*x1) (Disc. side)');

		
		$post = ["nome"=>"PEOO", "optativa"=>0, "carga_horaria"=>30, "requisito_id"=>$disciplina_id];
		$disciplina2_id = $this->Disciplina_model->save($post);
		$post = ["id"=>$professor_id, "disciplinas_id"=>$disciplina2_id];
		$this->Professor_model->save($post);
		$prof = $this->Professor_model->get($professor_id);
		$this->test(count($prof->ownDisciplinasList), 2, 'Rel. professor x disciplina (1x*) (Prof. side)');
		#removendo disciplina
		$this->Professor_model->removerDisciplinas($professor_id, $disciplina_id);
		$prof = $this->Professor_model->get($professor_id);
		$this->test(count($prof->ownDisciplinasList), 1, 'Rel. professor x disciplina (1x*) (Prof. side) [removendo disciplina]');
		#pelo lado da disciplina
		$post = ["id"=>$disciplina2_id, "professores_id"=>0];
		$this->Disciplina_model->save($post);
		$disc2 = $this->Disciplina_model->get($disciplina2_id);
		$this->test($disc2->professores, null, 'Rel. disciplina x professor (*x1) (Disc. side) [removendo disciplina]');

		$this->addSeparator("Um para Muitos (Autorelacionamento)");
		#disciplinas x disciplinas
		$this->test($disc2->requisito->id, $disciplina_id, 'Rel. disciplina x disciplina (*x1)');
		$disc = $this->Disciplina_model->get($disciplina_id);
		$this->test(count($disc->ownRequisitoList), 1, 'Rel. disciplina x disciplina (1x*)');
		#removendo requisito
		$this->Disciplina_model->removerRequisito($disciplina2_id);
		$disc2 = $this->Disciplina_model->get($disciplina2_id);
		$this->test($disc2->requisito, null, 'Rel. disciplina x disciplina (*x1) [removendo requisito]');
		$disc = $this->Disciplina_model->get($disciplina_id);
		$this->test(count($disc->ownRequisitoList), 0, 'Rel. disciplina x disciplina (1x*) [removendo requisito]');
		#removendo pelo select
		$post = ["id"=>$disciplina2_id, "requisito_id"=>$disciplina_id];
		$this->Disciplina_model->save($post);
		$disc2 = $this->Disciplina_model->get($disciplina2_id);
		$this->test($disc2->requisito != null, true, 'Rel. disciplina x disciplina (1x*) [readicionando requisito]');
		$post = ["id"=>$disciplina2_id, "requisito_id"=>0];
		$this->Disciplina_model->save($post);
		$disc2 = $this->Disciplina_model->get($disciplina2_id);
		$this->test($disc2->requisito, null, 'Rel. disciplina x disciplina (1x*) [removendo requisito, pelo select]');
		

		$this->addSeparator("Muitos para Muitos");
		#alunos x disciplinas
		$post = ["nome"=>"Pedro", "turma"=>"1A"];
		$this->load->model("Aluno_model");
		$this->Aluno_model->saveLog = false;
		$aluno_id = $this->Aluno_model->save($post);
		$post = ["id"=>$aluno_id, "disciplinas_id"=>$disciplina_id];
		$this->Aluno_model->save($post);
		$aluno = $this->Aluno_model->get($aluno_id);
		$disc = $this->Disciplina_model->get($disciplina_id);
		$this->test($aluno->matricula != null, true, 'Matrícula do aluno');
		$this->test($aluno->data_cadastro != null, true, 'Data de cadastro do aluno');
		$this->test(count($aluno->ownAlunosdisciplinasList), 1, 'Rel. aluno x disciplina (*x*) (aluno side) [pelo form alunos]');
		$this->test(count($disc->ownAlunosdisciplinasList), 1, 'Rel. aluno x disciplina (*x*) (disc. side) [pelo form alunos]');

		#add disciplina aluno (disciplina side)
		$post = ["id"=>$disciplina2_id, "alunos_id"=>$aluno_id];
		$this->Disciplina_model->save($post);
		$aluno = $this->Aluno_model->get($aluno_id);
		$disc2 = $this->Disciplina_model->get($disciplina2_id);
		$this->test(count($aluno->ownAlunosdisciplinasList), 2, 'Rel. disciplina x aluno (*x*) (aluno side) [pelo form disciplinas]');
		$this->test(count($disc2->ownAlunosdisciplinasList), 1, 'Rel. disciplina x aluno (*x*) (disc. side) [pelo form disciplinas]');

		#removendo disciplina do aluno
		$id_assoc = array_pop($aluno->ownAlunosdisciplinasList)->id;
		$this->Aluno_model->removerDisciplinas($id_assoc);
		$aluno = $this->Aluno_model->get($aluno_id);
		$this->test(count($aluno->ownAlunosdisciplinasList), 1, 'Rel. aluno x disciplina (*x*) [removendo disciplina]');

		$this->addSeparator("Muitos para Muitos (Autorelacionamento)");
		#projetos x projetos auto relacionamento muitos x muitos
		$post = ["nome"=>"Projeto 1"];
		$this->load->model("Projeto_model");
		$this->Projeto_model->saveLog = false;
		$projeto_id = $this->Projeto_model->save($post);
		$post = ["nome"=>"Projeto 2"];
		$projeto2_id = $this->Projeto_model->save($post);
		$post = ["id"=>$projeto2_id, "relacionado_id"=>$projeto_id];
		$this->Projeto_model->save($post);
		$proj = $this->Projeto_model->get($projeto_id);
		$proj2 = $this->Projeto_model->get($projeto2_id);
		$this->test(count($proj->ownProjrelacionadosList), 1, 'Rel. proj. x proj. (*x*) [Proj 1]');
		$this->test(count($proj2->ownProjrelacionadosList), 1, 'Rel. proj. x proj. (*x*) [Proj 2]');

		#removendo projeto
		$id_assoc = array_pop($proj->ownProjrelacionadosList)->id;
		$this->Projeto_model->removerProjetos($id_assoc);
		$proj = $this->Projeto_model->get($projeto_id);
		$proj2 = $this->Projeto_model->get($projeto2_id);
		$this->test(count($proj->ownProjrelacionadosList), 0, 'Rel. proj. x proj. (*x*) [Proj 1 removendo proj]');
		$this->test(count($proj2->ownProjrelacionadosList), 0, 'Rel. proj. x proj. (*x*) [Proj 2 removendo proj]');

		
		$this->addSeparator("Buscas");
		$this->Coordenacao_model->save(["id"=>$coordenacao_id, "professores_id"=>$professor_id]);
		$pages = $this->Professor_model->pagination(["coordenacoes_id"=>[0, $coordenacao_id]]);
		$this->test($pages["total_rows"], 1, 'Busca 1x1 (Prof. side)');
		$pages = $this->Coordenacao_model->pagination(["professores_id"=>[0, $professor_id]]);
		$this->test($pages["total_rows"], 1, 'Busca 1x1 (Coord. side)');
		$this->Disciplina_model->save(["id"=>$disciplina_id, "professores_id"=>$professor_id, "alunos_id"=>$aluno_id]);
		$pages = $this->Professor_model->pagination(["disciplinas_id"=>[0, $disciplina_id]]);
		$this->test($pages["total_rows"], 1, 'Busca 1x* (Prof. side)');
		$pages = $this->Disciplina_model->pagination(["professores_id"=>[0, $professor_id]]);
		$this->test($pages["total_rows"], 1, 'Busca *x1 (Disc. side)');
		$pages = $this->Disciplina_model->pagination(["alunos_id"=>[0, $aluno_id]]);
		$this->test($pages["total_rows"], 1, 'Busca *x* (Disc. side)');
		$pages = $this->Aluno_model->pagination(["disciplinas_id"=>[0, $disciplina_id]]);
		$this->test($pages["total_rows"], 1, 'Busca *x* (Aluno side)');
		
		

		$this->addSeparator("Deletando");
		$this->Coordenacao_model->delete($coordenacao_id);
		$coord = $this->Coordenacao_model->get($coordenacao_id);
		$this->test($coord, [], 'Coordenacao_model->delete');
		$loaded = $this->Professor_model->get($professor_id);
		$this->test($loaded->id, $professor_id, 'Coordenacao_model->delete [professor ainda existe?]');		

		$this->Professor_model->delete($professor_id);
		$loaded = $this->Professor_model->get($professor_id);
		$this->test($loaded, [], 'Professor_model->delete');

		$this->Disciplina_model->delete($disciplina_id);
		$loaded = $this->Disciplina_model->get($disciplina_id);
		$this->test($loaded, [], 'Disciplina_model->delete');

		$this->Aluno_model->delete($aluno_id);
		$loaded = $this->Aluno_model->get($aluno_id);
		$this->test($loaded, [], 'Aluno_model->delete');

		$this->Projeto_model->delete($projeto_id);
		$loaded = $this->Projeto_model->get($projeto_id);
		$this->test($loaded, [], 'Projeto_model->delete');



		$this->addSeparator("Logs");
		$_SESSION["user_id"] = 22;
		$this->Coordenacao_model->saveLog = true;
		$this->Coordenacao_model->delete($coordteste_id);
		
		#log
		$row = R::getRow('SELECT * FROM logs ORDER BY id DESC LIMIT 1 ');
		$this->test($row['usuario_id'], 22, 'logs->usuario_id');
		$json = json_decode($row['state']);
		$this->test($json->id, $coordteste_id, 'logs->coordenacao_id');

		#limpa o banco de dados
		R::close();
		unlink($dbname);



		$report = $this->report();

		include view('testes/unidade');
	}

	public function semantic(){
		include view('testes/semantic');
	}


	public function ajax(){
		$query = $_GET["q"];
		$lista = [["nome"=>"joao","id"=>1],
						["nome"=>"maria","id"=>2],
						["nome"=>"pedro","id"=>3],
						["nome"=>"andre","id"=>4],
						["nome"=>"agnaldo","id"=>5]];
		$qtd = count($lista);
		if ($query != ""){
			for($i = 0; $i < $qtd; $i++){
				if (!stristr($lista[$i]["nome"], $query)){
					unset($lista[$i]);
				}
			}
		}

		$json = ["success"=>true, "results"=>[]];

		foreach($lista as $ln){
			array_push($json["results"], ["name"=>$ln["nome"], "value"=>$ln["id"]]);
		}
		
		print json_encode($json);
	}
	
	
}

