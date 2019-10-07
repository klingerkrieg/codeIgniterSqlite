<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Seguranca {
    private $CI = null;

    public function __construct(){
        $this->CI =& get_instance();
    }

    public function arvore($controles,$tabelas=null){
        if ($tabelas == null){
            $tabelas = $controles;
        }
        $arr = $this->controles($controles);
        $arr = array_merge($arr,$this->tabelas($tabelas));
        
        return $arr;
    }

    public function controles($controles){
        $arr = ["controles"=>[]];
        foreach($controles as $controle){
            $arr["controles"][$controle] = $this->controle($controle);
        }
        return $arr;
    }

    public function controle($controle){
        require_once "application/controllers/$controle.php";

        $methods = get_class_methods($controle);
        $default = ["__construct","get_instance"];
        $methods = array_diff($methods,$default);
        foreach($methods as $k=>$val){
            $methods[$k] = "$controle.$val";
        }

        return $methods;
    }

    public function tabelas($tabelas){
        $arr = ["tabelas"=>[]];
        foreach($tabelas as $tab){
            $arr["tabelas"][$tab] = ["read_$tab","update_$tab","delete_$tab","insert_$tab"];
        }
        return $arr;
    }


    public function check($permissao=null){

        if ($permissao == null){
            $permissao = $this->CI->uri->segment(1);
            $p2 = $this->CI->uri->segment(2);
            if ($p2 != null){
                $permissao.".".$p2;
            }
        }

        if (!$this->temPermissao($permissao)){
            include(APPPATH.'/config/routes.php');
            #redirect($route['default_controller']);
            print $this->CI->load->view('sem_permissao', '', true);
            die();
        }
    }

    public static function temPermissao($permissao=null){
        $sql = "select permissoes.nome from usuarios
            inner join permissoes on
            permissoes.id = usuarios.permissoes_id
            where
                usuarios.id = {$_SESSION['user_id']}
            and (permissoes.controles like ? or permissoes.admin = 1)";

        $row = R::getRow($sql,["%$permissao%"]);
        if ($row == null){
            return false;
        } else {
            return true;
        }
    }



}