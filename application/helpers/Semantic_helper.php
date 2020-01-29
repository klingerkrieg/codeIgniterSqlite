<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!isset($semantic_sizes)){
    global $semantic_sizes;
    $semantic_sizes = ["","one","two","three","four","five","six","seven","eight","nine",
            "ten","eleven","twelve","thirteen","fourteen","fifteen","sixteen"];
}



abstract class HTMLElement {

    protected $id;
    protected $name;
    protected $size;
    protected $type;
    protected $disabled;
    protected $class = "";
    protected $style;
    protected $othersAttributes = [];

    protected $printableAttributes = ["type","disabled","class","style","name","id"];

    function __construct($options){
        global $semantic_sizes;

        if (is_array($options)){
            $this->getFromOptions(["name","id", "type","class"],$options);
            if (isset($options["size"])){
                if (isset($semantic_sizes[$options["size"]]))
                    $this->size = $semantic_sizes[$options["size"]]. " wide";
                else 
                    $this->size = $semantic_sizes[8]. " wide";
            }
            if (isset($options["disabled"]) || in_array("disabled", $options, true)){
                $this->disabled = "disabled";
            }
            if (isset($options["attributes"])){
                $this->othersAttributes = $options["attributes"];
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
            if (in_array($val, $options, true)){
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

    function removeAttributes($arr){
        foreach($arr as $attr){
            unset($this->printableAttributes[array_search($attr,$this->printableAttributes)]);
        }
    }

}




class HTMLInput extends HTMLElement {
    
    protected $id;
    protected $readOnly;
    protected $required;
    protected $hidden;
    protected $label;
    protected $value;
    protected $icon;

    /**
     * $name = Name do input
     * $options [id, readonly, required, hidden, label, icon]
     */
    function __construct($name,$options=[]){
        $this->name = $name;
        $this->type = "text";
        parent::__construct($options);

        $this->addAttributes(["readonly","placeholder","value"]);
        
        if (is_array($options)){
            $this->getFromOptions(["readonly","placeholder","label","icon"],$options);
            
            #ifs especiais
            if (isset($options["required"]) || in_array("required",$options, true)){
                $this->required = "<span class='red'>*</span>";
            }
            if (isset($options["hidden"]) || in_array("hidden", $options, true)){
                $this->type = "hidden";
                $this->hidden = true;
            }
            if (isset($options["value"])){
                if (is_array($options["value"]) || is_object($options["value"])){
                    if (isset($options["value"][$this->name])){
                        $this->value = $options["value"][$this->name];
                    } else
                    if (isset($options["value"][str_replace("_id","",$this->name)])){
                        #se for uma chave estrangeira
                        $this->value = $options["value"][str_replace("_id","",$this->name)]['id'];
                    }

                } else {
                    $this->value = $options["value"];
                }
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

            if ($this->icon){
                $html .= "<div class='ui left icon input'>";
                $html .= "<i class='{$this->icon} icon'></i>";
            }

            $input = $this->writeElement();

            if ($this->label != null) {
                $html .= "<label>{$this->label} {$this->required} $input</label>";
            } else {
                $html .= $input;
            }

            if ($this->icon)
                $html .= "</div>";

            $html .= error($this->name);

            $html .= "</div>";
            return $html;
        }
    }
}


class HTMLSelect extends HTMLInput {

    protected $options = [];
    protected $blank = true;

    /**
     * $name = Name do input
     * $options [id, readonly, required, options, label, blank=true]
     */
    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        if (is_array($options)){
            $this->getFromOptions(["blank"],$options);
            if (isset($options["options"])){
                $this->options = $options["options"];
            }
        }
    }

    function writeElement(){
        if ($this->blank){
            $this->options = [""] + $this->options;
        }
        
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

    /**
     * $name = Name do input
     * $options [id, required, options, label]
     */
    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        $this->type = "radio";
        $this->blank = false;
        $this->removeAttributes(["value"]);
    }

    function writeElement(){
        $html = "";        
        foreach($this->options as $key=>$option){
            $html .= "<label class='field'>";
            $checked = "";
            if (is_array($this->value)){
                if (in_array($key, $this->value)){
                    $checked = "checked";
                }
            } else
            if (!is_null($this->value) && $key == $this->value){
                $checked = "checked";
            }
            $html .= "<input $checked ".$this->attributes()." value={$key} />";
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
    /**
     * $name = Name do input
     * $options [id, required, options, label]
     */
    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        $this->type = "checkbox";
        $this->blank = false;
        if (is_array($options)){

            if (isset($options["value"])){
                if (is_array($options["value"]) || is_object($options["value"])){
                    
                    if (isset($options["value"][$this->name]) && is_array($options["value"][$this->name])){
                        $this->value = $options["value"][$this->name];
                    }
                } else {
                    $this->value = [$options["value"]];
                }
            }
        }
        if (strstr($this->name,"[]") == false){
            $this->name = $this->name."[]";
        }
    }
}

class HTMLUpload extends HTMLInput {
    protected $path;
    protected $fileType;

    /**
     * $name = Name do input
     * $options [id, readonly, required, path, fileType, label]
     */
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
    protected $onclick;

    /**
     * $name = Name do input
     * $options [id, text, href, color, onclick]
     */
    function __construct($text,$options=[]){
        $this->text = $text;
        $this->type = "submit";
        parent::__construct($options);

        if ($this->size != null){
            $this->class .= " buttonFix";
        }

        $this->addAttributes(["href","onclick"]);

        if (is_array($options)){
            $this->getFromOptions(["href","color"],$options);
            #ifs especiais
            if (isset($options["onclick"])){
                $this->type = "button";
                $this->onclick = $options["onclick"];
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

    /**
     * $args.. [HTMLElement]
     */
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
     * $options=[grid=>true, class=>'']
     * Ao final do formulário use a função formEnd();
     */
    function formStart($action,$method="POST",$options = []){
        if (strstr($action,"/")){
            $action = site_url().$action;
        }
        $grid = true;
        $class = "";
        if (isset($options['grid'])) {
            $grid = $options['grid'];
        }
        if (isset($options['class'])) {
            $class = $options['class'];
        }

        $gridClass = "";
        if ($grid) {
            $grid = "<div class='ui grid'>";
            $gridClass = "grid";
        }
        return "$grid
                <form		action='$action'
                    class='ui form column stackable $gridClass $class' 
                    method='$method' enctype='multipart/form-data'>";
    }
}

if (!function_exists("formEnd")){
    /**
     * Grid true imprime um fechamento de div após o form
     * $grid=true
     */
    function formEnd($grid=true){
        if ($grid)
            $grid = "</div>";
        return "</form>$grid";
    }
}

if (!function_exists("flashMessage")){
    function flashMessage(){
        $CI =& get_instance();
        $html = "";
        if ($CI->session->flashdata('error'))
            $html .= "<div class='ui red message'>". $CI->session->flashdata('error') . "</div>";
        if ($CI->session->flashdata('success'))
            $html .= "<div class='ui green message'>". $CI->session->flashdata('success') . "</div>";
        if ($CI->session->flashdata('warning'))
            $html .= "<div class='ui yellow message'>". $CI->session->flashdata('warning') . "</div>"; 
        if ($CI->session->flashdata('message'))
            $html .= "<div class='ui message'>". $CI->session->flashdata('message') . "</div>";
        return $html;
    }
}