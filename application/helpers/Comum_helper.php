<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists("convertToPaginate")){
    function convertToPaginate($list,$per_page=5){
        $qtd = count($list);
        return ["data"=>$list,
                "total_rows"=>$qtd,
                "per_page"=>$per_page,
                "page_max"=>ceil($qtd/$per_page)];

    }
}


if (!function_exists("view")){
    function view($view){
        return "application/views/$view.php";
    }
}

if (!function_exists("paginaAtual")){
    function paginaAtual(){
        $actPage = "";
        if (isset($_GET["page"])){
            $actPage = "?page={$_GET["page"]}";
        }
        return $actPage;
    }
}


if (!function_exists("flashMessage")){
    function flashMessage(){
        $CI =& get_instance();
        return $CI->session->flashdata('error') . $CI->session->flashdata('success') . $CI->session->flashdata('warning');
    }
}

if (!function_exists("redirecionar")){
	function redirecionar($url){
		if (isset($_GET["page"])){
            $actPage = "page={$_GET["page"]}";
            if (strstr($url,"?")){
                $url .= $actPage;
            } else {
                $url .= "?".$actPage;
            }
        }


        redirect($url);
	}	
}



if (!function_exists("array_key_first")){
	function array_key_first($array){
		foreach($array as $key=>$val){
			return $key;
		}
	}	
}


if (!function_exists("cleanString")){
    function uploadFile($UPLOAD, $name, $path, $types, $maxSize = 1000){
        #local onde salvará o arquivo sqlite/uploads/
        #a pasta uploads deve existir, caso contrário ele não irá funcionar
        $config['upload_path']          = $path;
        $config['allowed_types']        = $types;
        $config['max_size']             = 1000;
        #limpa os caracteres especiais do nome do arquivo
        $config['file_name']            = cleanString($_FILES[$name]["name"]);

        $CI =& get_instance();
        $CI->load->library('upload', $config);
        #faz o upload
        if ( ! $CI->upload->do_upload($name)){
            return false;
        } else {
            #retorna como ficou o nome do arquivo no servidor
            return $this->upload->data()["file_name"];
        }
    }
}


if (!function_exists("cleanString")){
    function cleanString($text) {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/\./'           =>   '_',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }
}

if (!function_exists("checked")){
    function checked($val, $arr, $field){
        if (isset($arr[$field])){
            if ($arr[$field] === "" || $arr[$field] === null){
                return "";
            } else
            if (is_array($arr[$field]) && in_array($val, $arr[$field])){
                return "checked";
            } else
            if ($arr[$field] == $val){
                return "checked";
            }
        } else {
            return "";
        }
    }



    function toOptions($arr){
        $arr2 = [];
        foreach($arr as $val){
            #encontra qual o campo que deve ser exibido para o usuario
            $temp = $val;
            unset($temp["id"]);
            
            $arr2[$val["id"]] = implode(" - ",$temp);
        }
        return $arr2;
    }

    function error($field){
        if (ENVIRONMENT == "development") {
            if (!isset($_SESSION["erros_apresendados"])){
                $_SESSION["erros_apresendados"] = [];
            } else {
                array_push($_SESSION["erros_apresendados"], $field);
            }
        }

        return form_error($field);
    }


    function validation_errors_array($prefix = '', $suffix = '') {
        if (FALSE === ($OBJ = & _get_validation_object())) {
        return '';
        }

        return $OBJ->error_array($prefix, $suffix);
    }

    function errors(){
        $arr = validation_errors_array();
        if (isset($_SESSION["erros_apresendados"])){
            foreach($_SESSION["erros_apresendados"] as $val){
                unset($arr[$val]);
            }
        }
        return $arr;
    }

    function clear_errors(){
        $_SESSION["erros_apresendados"] = [];
        $_SESSION["php_errors"] = [];
    }


    function errorManager($errNo, $errStr, $errFile, $errLine, $a){
        if (error_reporting() == 0) {
            return;
        }
        if (!isset($_SESSION["php_errors"])){
            $_SESSION["php_errors"] = [];
        }
        array_push($_SESSION["php_errors"], "Erro:$errStr <br/>Arquivo:$errFile<br/>Linha:$errLine");
    }

    set_error_handler("errorManager");

}