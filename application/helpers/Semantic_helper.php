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




class HTMLElement {

    protected $id;
    protected $name;
    protected $size;
    protected $type;
    protected $disabled;
    protected $class = "";
    protected $style;
    protected $value;
    protected $othersAttributes = [];

    protected $printableAttributes = ["type","disabled","class","style","name","id"];

    function __construct($options){
        global $semantic_sizes;

        if (is_array($options)){
            $this->getFromOptions(["name","id", "type","class","attributes","disabled"],$options);
            if (isset($options["size"])){
                if (isset($semantic_sizes[$options["size"]]))
                    $this->size = $semantic_sizes[$options["size"]]. " wide";
                else 
                    $this->size = $semantic_sizes[8]. " wide";
            }

        } else {
            if (strstr($options,"disabled")){
                $this->disabled = "disabled";
            }
        }
    }

    function getFromOptions($values, $options){
        foreach($values as $val){
            if (isset($options[$val])){
                $this->$val = $options[$val];
            } else 
            if (in_array($val, $options)){
                $this->$val = true;
            }
        }
    }

    function attributes(){
        
        $htmlAttributes = [];
        foreach ($this->printableAttributes as $attr ){
            if ( isset($this->$attr) && $this->$attr != null ){
                $val = str_replace('"',"'",$this->$attr);
                array_push($htmlAttributes,"$attr=\"$val\"");
            }
        }
        foreach($this->othersAttributes as $attr=>$val){
            $val = str_replace('"',"'",$val);
            array_push($htmlAttributes,"$attr=\"$val\"");
        }
        return implode(" ",$htmlAttributes);
    }

    function addAttributes($arr){
        $this->printableAttributes = array_merge($this->printableAttributes,$arr);
    }

}



class HTMLInput extends HTMLElement {
    
    protected $id;
    protected $readOnly;
    protected $required;
    protected $hidden;
    protected $label;

    function __construct($name,$options=[]){
        $this->name = $name;
        $this->type = "text";
        parent::__construct($options);

        $this->addAttributes(["readOnly","placeholder","value"]);
        
        if (is_array($options)){
            $this->getFromOptions(["readonly","placeholder","label"],$options);
            
            #ifs especiais
            if (isset($options["required"]) || in_array("required",$options)){
                $this->required = "<span class='red'>*</span>";
            }
            if (isset($options["hidden"]) || in_array("hidden",$options)){
                $this->type = "hidden";
                $this->hidden = true;
            }
            if (isset($options["value"])){
                if (is_array($options["value"]) || is_object($options["value"])){
                    $this->value = val($options["value"], $this->name);
                } else {
                    $this->value = $options["value"];
                }
            }
            if (isset($options["label"])){
                $this->label = $options["label"];
            }
        } else {
            if (strstr($options,"hidden")){
                $this->type = "hidden";
                $this->hidden = true;
            }
            if (strstr($options,"readonly")){
                $this->readOnly = "readonly";
            }
            if (strstr($options,"required")){
                $this->required = "<span class='red'>*</span>";
            }
        }
    }

    function writeElement(){
        return "<input ".$this->attributes()." />";
    }

    public function __toString(){
        
        if ($this->hidden){
            return $this->writeElement();
        } else {
            
            $html = "<div class='{$this->size} field'> ";

            $input = $this->writeElement();
            $input .= error($this->name);

            if ($this->label != null) {
                $html .= "<label>{$this->label} {$this->required} $input</label>";
            } else {
                $html .= $input;
            }

            $html .= "</div>";
            return $html;
        }
    }
}


class HTMLSelect extends HTMLInput {

    protected $options = [];

    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        if (is_array($options)){
            if (isset($options["options"])){
                $this->options = $options["options"];
            }
        }
    }

    function writeElement(){
        $html = "<select ".$this->attributes().">";
        foreach($this->options as $k=>$val){
            $selected = "";
            if ($this->value == $k){
                $selected = "selected";
            }
            $html .= "<option $selected value='$k'>$val</option>";
        }
        $html .= "</select>";
        return $html;
    }

}

class HTMLRadio extends HTMLSelect {

    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        $this->type = "radio";
    }

    function writeElement(){
        $html = "";
        
        foreach($this->options as $key=>$option){
            $html .= "<label class='field'>";
            $checked = "";
            if ($key === $this->value){
                $checked = "checked";
            }
            $html .= "<input type='$this->type' $checked ".$this->attributes()." />";
            $html .= "$option</label>";
        }
        return $html."</div>";
    }

    function __toString(){
        $html = "<div class='field {$this->disabled}'>
                    <label>$this->label {$this->required}</label>
                    <div class='{$this->size} fields'>";

        $html .= $this->writeElement();
        
        return $html . "</div>" . error($this->name);
    }
}

class HTMLCheckbox extends HTMLRadio {
    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        $this->type = "checkbox";
    }
}

class HTMLUpload extends HTMLInput {
    protected $path;
    protected $fileType;

    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        if (is_array($options)){
            $this->getFromOptions(["path","fileType"],$options);
        }
    }

    function writeElement(){
        $this->type = "file";
        $html = "<input ".$this->attributes()." />";

        if ($this->value != null){
            if ($this->fileType == "image"){
                $html .= "<img class='ui tiny circular image' src='".base_url()."{$this->path}/{$this->value}' />";
            } else {
                $html .= "<a target='_BLANK' href='".base_url()."{$this->path}/{$this->value}'>{$this->value}</a>";
            }
        }
        return $html;
    }
}

class HTMLButton extends HTMLElement {
    protected $text;
    protected $href;
    protected $color = "blue";
    protected $onClick;

    function __construct($text,$options=[]){
        $this->text = $text;
        $this->type = "submit";
        parent::__construct($options);

        $this->addAttributes(["href","onClick"]);

        if (is_array($options)){
            $this->getFromOptions(["href","color"],$options);
            #ifs especiais
            if (isset($options["onClick"])){
                $this->type = "button";
                $this->onClick = $options["onClick"];
            }
        }

        //Fix href
        if ($this->href && strstr($this->href,"/")){
            $this->href = site_url() . $this->href;
        }
    }

    public function __toString(){
        
        $html = "<div class='{$this->size} field'> ";
        $this->class .= " ui {$this->color} button {$this->disabled}";

        if ($this->href){
            $html .= "<a ".$this->attributes()." >{$this->text}</a>";
        } else {
            $html .= "<button ".$this->attributes()." >{$this->text}</button>";
        }

        $html .= "</div>";
        return $html;
        
    }
}


class HTMLGroup{
    protected $items;

    function __construct(){
        $this->items = func_get_args();
    }

    function __toString(){
        $html = "<div class='fields'>";
        foreach($this->items as $item){
            $html .= $item;
        }
        return $html."</div>";
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
        if (strstr($action,"/")){
            $action = site_url().$action;
        }
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
