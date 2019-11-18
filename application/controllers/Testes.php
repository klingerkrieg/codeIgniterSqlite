<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testes extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		

	}
	
	public function index() {

		$this->load->library('unit_test');

		$this->unit->set_test_items(array('test_name', 'result'));
		$str = '<table border="0" cellpadding="4" cellspacing="1">
			{rows}<tr>
				<td><b>{item}</b></td>
				<td>{result}</td>
				</tr>
			{/rows}</table>';

		$this->unit->set_template($str);


		
		$arr = ["item"=>1,"arr"=>["item2"=>2]];
		$this->unit->run(val($arr,"item"), 1, "val(...)");
		$this->unit->run(val($arr,"arr","item2"), 2, "val(...)");
		$this->unit->run(val($arr,"item3"), "", "val(...)");
		$this->unit->run(val($arr,"arr","item3"), "", "val(...)");


		$fields = ["nome","data","setores_id"];
		$post = ["nome"=>"joao","idade"=>21, "carros_id"=>1];
		$this->unit->run(prepare_fields($fields,$post), ["nome"=>"joao", "setores_id"=>null], 'prepare_fields($fields,$post)');
		$fields = ["nome","data","setores_id"];
		$post = ["nome"=>"joao","data"=>"19/09/2000", "setores_id"=>1];
		$this->unit->run(prepare_fields($fields,$post), ["nome"=>"joao","data"=>"19/09/2000", "setores_id"=>1], 'prepare_fields($fields,$post)');

		$arr = ["ativo"=>1];
		$this->unit->run(checked(1, $arr, "ativo"), 'checked', 'checked($val, $arr, $field)');
		$this->unit->run(checked(0, $arr, "ativo"), '', 'checked($val, $arr, $field)');
		$this->unit->run(checked(1, ["linguagens"=>[1,2]], "linguagens"), 'checked', 'checked($val, $arr, $field)');
		$this->unit->run(checked(3, ["linguagens"=>[1,2]], "linguagens"), '', 'checked($val, $arr, $field)');
		
		$arr = [["id"=>1,"nome"=>"joao"],["id"=>2,"nome"=>"maria"],["id"=>3,"nome"=>"ze"]];
		$resp = [1=>"joao",2=>"maria",3=>"ze"];
		$this->unit->run(toOptions($arr), $resp, 'toOptions($arr)');
		$arr = [["id"=>1,"email"=>"joao@gmail.com"],["id"=>2,"email"=>"maria@gmail.com"],["id"=>3,"email"=>"ze@gmail.com"]];
		$resp = [1=>"joao@gmail.com",2=>"maria@gmail.com",3=>"ze@gmail.com"];
		$this->unit->run(toOptions($arr), $resp, 'toOptions($arr)');
		
		
		echo $this->unit->report();
		
	}
	
	
}
