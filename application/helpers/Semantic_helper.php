<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!isset($semantic_sizes)){
    global $semantic_sizes;
    $semantic_sizes = ["","one","two","three","four","five","six","seven","eight","nine",
            "ten","eleven","twelve","thirteen","fourteen","fifteen","sixteen"];
}




if (!function_exists("clearOptions")){
    function clearOptions($options){
        unset($options["required"]);
        unset($options["disabled"]);
        unset($options["readonly"]);
        unset($options["placeholder"]);
        $keys = array_keys($options);
        foreach($keys as $key){
            if (is_numeric($key) || $options[$key] == ""){
                unset($options[$key]);
            }
        }
        return $options;
    }
}

if (!function_exists("parseToAttributes")){
    function parseToAttributes($options){
        $options = clearOptions($options);
        $attributes = [];
        foreach ($options as $attr=>$val ){
            array_push($attributes,"$attr='$val'");
        }
        return implode(" ",$attributes);
    }
}


if (!function_exists("optionsInterpreter")){
    function optionsInterpreter($options){
        global $semantic_sizes;

        $opts["placeholder"] = "";
        $opts["readonly"] = "";
        $opts["required"] = "";
        $opts["disabled"] = "";
        $opts["id"] = "";
        $opts["size"] = "";
        $opts["class"] = "";

        if (is_array($options)){

            $opts = array_merge($opts,$options);

            if (isset($options["required"]) || in_array("required",$options)){
                $opts["required"] = "<span class='red'>*</span>";
            }
            if (isset($options["disabled"]) || in_array("disabled",$options)){
                $opts["disabled"] = "disabled";
            }
            if (isset($options["size"])){
                if (isset($semantic_sizes[$options["size"]]))
                    $opts["size"] = $semantic_sizes[$options["size"]]. " wide";
                else 
                    $opts["size"] = $semantic_sizes[8]. " wide";
            }

        } else {
            if (strstr($options,"required")){
                $opts["required"] = "<span class='red'>*</span>";
            }
            if (strstr($options,"disabled")){
                $opts["disabled"] = "disabled";
            }
        }

        return $opts;
    }
}

if (!function_exists("input")){
    /**
     * $name = Name do campo
     * $label = Label
     * $data = [name=>'João']
     * $options = [placeholder='nome'|hidden|readonly|required|disabled|class|id|size=1-16]
     */
    function input($name,$label="",$data=[],$options="text"){
        $opt = optionsInterpreter($options);

        $placeholder = "";
        if (is_array($options)){
            if (isset($options["placeholder"])){
                $opt["placeholder"] = $options["placeholder"];
            }
            if (isset($options["hidden"]) || in_array("hidden",$options)){
                $hidden = true;
            }
            if (isset($options["readonly"]) || in_array("readonly",$options)){
                $opt["readonly"] = "readonly";
            }
        } else {
            if (strstr($options,"hidden")){
                $hidden = true;
            }
            if (strstr($options,"readonly")){
                $opt["readonly"] = "readonly";
            }
        }
       

        if (isset($hidden)){
            return "<input ".parseToAttributes($opt)."
                    type='hidden' name='$name' value='" . val($data,$name) . "' >";
        } else {
            
            $html = "<div class='{$opt['size']} field'> ";

            $input = "<input ".parseToAttributes($opt)." type='text' 
                        name='$name' value='" . val($data,$name) ."' {$opt['disabled']} 
                        {$opt['readonly']} placeholder='{$opt['placeholder']}'>
                        " . error($name);

            if ($label != "") {
                $html .= "<label>$label {$opt['required']} $input</label>";
            } else {
                $html .= $input;
            }
            
            $html .= "</div>";

            return $html;

        }
        
    }
}


if (!function_exists("upload")){
    /**
     * $name = Name do campo
     * $label = Label
     * $type = file/image
     * $data = [name=>'arquivo.txt']
     * $path = "./uploads"
     * $options = [required|disabled|class|id|size=1-16]
     */
    function upload($name,$label="",$type="file",$data=[],$path="./uploads",$options=""){

        $options = optionsInterpreter($options);

        $html = "<div class='field {$options['disabled']} {$options['size']}'>
                    <label>$label {$options['required']}
                        <input ".parseToAttributes($options)." type='file' name='$name'>
                        ".error('foto')."
                    </label>";

        if (val($data,$name) != ""){
            if ($type == "file"){
                $html .= "<a target='_BLANK' href='".base_url()."$path/{$data[$name]}'>{$data[$name]}</a>";
            } else
            if ($type == "image"){
                $html .= "<img class='ui tiny circular image' src='".base_url()."$path/{$data[$name]}' />";
            }
        }
        
        return $html."</div>";
    }
}


if (!function_exists("select")){
    /**
     * $name = Name do campo
     * $label = Label
     * $values = ["Opção 1", "Opção2"]
     * $data = [name=>1]
     * $options = [required|disabled|class|id|size=1-16]
     */
    function select($name,$label,$values,$data=[],$options=""){
        
        $options = optionsInterpreter($options);

        $val = $data;
        if (is_array($data) || is_object($data)){
            $val = val($data,$name);
            if ($val == "" && strstr($name,"_id")){
                list($key1, $key2) = explode("_",$name);
                $val = val($data, $key1, $key2);
            }
        }
        
        
        $html = "<div class='field {$options['disabled']} {$options['size']}'>"
            ."<label>$label {$options['required']}"
            . form_dropdown($name, $values, $val, parseToAttributes($options))
            . error($name)
            . "</label></div>";

        return $html;
    }
}



if (!function_exists("checkbox")){
    /**
     * $name = Name do campo
     * $label = Label
     * $values = ["Opção 1", "Opção2"]
     * $data = [name=>1]
     * $size = 1-16
     * $options = [required|disabled|class|id|size=1-16]
     */
    function checkbox($name,$label,$values,$data,$options=""){
        
        $options = optionsInterpreter($options);

        $html = "<div class='field {$options['disabled']}'>
            <label>$label {$options['required']}</label>
            <div class='{$options['size']} fields'>";

        $html .= "<input type='hidden' name='{$name}[]' value=''>";   
        
        foreach($values as $key=>$option){
            $html .= "<label class='field'>";
            $html .= form_checkbox($name."[]", $key, checked($key, $data, $name), parseToAttributes($options));
            $html .= "$option</label>";
        }
        
        $html .= "</div>".error($name)."</div>";



        return $html;
    }
}


if (!function_exists("radio")){
    /**
     * $name = Name do campo
     * $label = Label
     * $values = ["Opção 1", "Opção2"]
     * $data = [name=>1]
     * $size = 1-16
     * $options = [required|disabled|class|id|size=1-16]
     */
    function radio($name, $label, $values, $data=[], $options=""){
        
        $options = optionsInterpreter($options);

        $html = "<div class='field {$options['disabled']}'>
            <label>$label {$options['required']}</label>
            <div class='{$options['size']} fields'>";
                
        foreach($values as $key=>$option){
            $html .= "<label class='field'>";
            $html .= form_radio($name, $key, checked($key, $data, $name), parseToAttributes($options));
            $html .= "$option</label>";
        }
                
        $html .= "</div>".error($name)."</div>";



        return $html;
    }
}



if (!function_exists("tableHeader")){
    /**
     * O primeiro parâmetro será o cabeçalho da tabela, os seguintes serão as colunas.
     * tableHeader('Lista de usuários','id','nome','editar');
     */
    function tableHeader(){
        $data = func_get_args();

        $html = "<table class='ui celled table'><thead><tr><th colspan='50'>{$data[0]}</th></tr><tr>";

        unset($data[0]);
        foreach($data as $val){
            $html .= "<th>$val</th>";
        }

        $html .= "</tr></thead>";
        return $html;
    }
}



if (!function_exists("tableBottom")){
    /**
     * $listagem = Use a variável que foi criada no model->pagination();
     * $listagem = ['total_rows','page_max'];
     */
    function tableBottom($listagem){

        if (!isset($listagem["total_rows"])){
            $listagem = convertToPaginate($listagem);
        }

        $html = "";

        if ($listagem["total_rows"] == 0){
            $html .= "<tr><td colspan='50'><center>Nada encontrado</center></td></tr>";
        }
        
        $CI =& get_instance();
        
        #paginacao
        $CI->pagination->initialize($listagem);
        
        $html .= "<tfoot><tr><th colspan='50'><span class='ui label'>
			Total: {$listagem["total_rows"]}
        </span>";

		if ($listagem["page_max"] > 1){
			$html .= "<div class='ui right floated pagination menu'>"
				.$CI->pagination->create_links()."</div>";
        }

        $html .= "</th></tr></tfoot></tbody></table>";
        
        return $html;

    }
}

if (!function_exists("formStart")){
    /**
     * $action=Controller/método que o formulário irá submeter
     * $method=POST/GET
     * Ao final do formulário use a função formEnd();
     */
    function formStart($action,$method="POST"){
        return "<div class='ui grid'>
                <form		action='$action'
                    class='ui form column stackable grid' 
                    method='$method' enctype='multipart/form-data'>";
    }
}

if (!function_exists("formEnd")){
    function formEnd(){
        return "</form></div>";
    }
}

if (!function_exists("button")){
    /**
     * $text = O texto do botão
     * $options:
     * size=1-16
     * type=submit/button
     * disabled=false/true
     * color=blue
     * href=Um link
     */
    function button($text, $options=[]){
        global $semantic_sizes;

        $size = 0;
        if (isset($options["size"]))
            $size = $options["size"];

        $type = "submit";
        if (isset($options["type"]))
            $type = $options["type"];
        
        $disabled = "";
        if (isset($options["disabled"]))
            $disabled = "disabled";
        
    
        $color = "blue";
        if (isset($options["color"]))
            $color = $options["color"];
        
        $href = null;
        if (isset($options["href"]))
            $href = $options["href"];

        if ($size != 0)
            $size = "{$semantic_sizes[$size]} wide";

        if ($href){
            $html = "<div class='$size field'><a class='ui $color button $disabled' style='width:100%' href='$href' >$text</a></div>";
        } else {
            $html = "<div class='$size field'><button  class='ui $color button $disabled' style='width:100%' type='$type' >$text</button></div>";
        }

        return $html;
    }
}



if (!function_exists("group")){
    /**
     * Passe todos os elementos que desejar inserir na mesma linha:
     * $el1 = button('teste');
     * $el2 = input('teste');
     * print group($el1, $el2);
     */
    function group(){
        $items = func_get_args();
        $html = "<div class='fields'>";
        foreach($items as $item){
            $html .= $item;
        }
        return $html."</div>";
    }
}