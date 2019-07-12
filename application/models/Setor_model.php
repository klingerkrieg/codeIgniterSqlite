<?php

class Setor_model extends AbstractModel {

		public $table = "setores";
        public $fields = ["nome"];
		


        public function preSave($obj, $data) {

			#salva a pessoa no setor
			if (val($data,'pessoa_id') != "" ){
				$usu = R::load("usuarios", $data['pessoa_id']);
				#para relacionar um para muitos utilize essa notacao
				#own+NomeDaTabela+List
				#essa propriedade sera um array com varios daquele model que voce adicionar
				$obj->ownUsuariosList[] = $usu;

				#tambem seria possivel fazer
				#$usu->setores = $obj;
				#R::store($usu);
			}
			
			return $obj;
		}
		
		public function remove_usuario($id_setor,$id_usuario){
			$obj = R::load($this->table, $id_setor);
			unset($obj->ownUsuariosList[$id_usuario]);
			R::Store($obj);

		}


}