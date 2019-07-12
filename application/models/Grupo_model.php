<?php

class Grupo_model extends AbstractModel {

		public $table = "grupos";
        public $fields = ["nome"];
		


        public function preSave($obj, $data) {

			#salva a pessoa no grupo
			if (val($data,'pessoa_id') != "" ){
				#esse relacionamento sera diferente do setor
				#porque esse permitira
				#muitos grupos pra um usuario
				#muitos usuarios pra um grupo


				$usu = R::load("usuarios", $data['pessoa_id']);

				#nomes de tabelas nao podem ter _ - ou letras maiusculas
				$assoc = R::dispense("gruposusuarios");

				#o nome das propriedades tem que ser igual ao nome das tabelas (plural)
				$assoc->usuarios = $usu;
				$assoc->grupos = $obj;

				R::Store($assoc);

			}
			
			return $obj;
		}
		
		public function remove_usuario($id_assoc){
			$obj = R::load("gruposusuarios", $id_assoc);
			R::Trash($obj);
		}



}