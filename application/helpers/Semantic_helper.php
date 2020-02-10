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
    protected $tooltip;

    protected $printableAttributes = ["type","disabled","class","style","name","id"];

    function __construct($options){
        global $semantic_sizes;

        if (is_array($options)){
            $this->getFromOptions(["name","id", "type","class","tooltip"],$options);
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
                if ($val != "")
                    array_push($htmlAttributes,"$attr=\"$val\"");
            }
        }
        foreach($this->othersAttributes as $attr=>$val){
            $val = str_replace('"',"'",$val);
            if ($val != "")
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
    protected $readonly;
    protected $required;
    protected $hidden;
    protected $label;
    protected $value;
    protected $icon;
    protected $right_icon;
    protected $maxlength;
    protected $div_class = "";

    /**
     * $name = Name do input
     * $options [id, readonly, required, hidden, label, icon, right_icon, maxlength, div_class]
     */
    function __construct($name,$options=[]){
        $this->name = $name;
        $this->type = "text";
        parent::__construct($options);

        $this->addAttributes(["readonly","placeholder","value","maxlength"]);
        
        if (is_array($options)){
            $this->getFromOptions(["readonly","placeholder","label","icon","right_icon","maxlength","div_class"],$options);
            
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
                $this->readonly = "readonly";
            }
            if (strstr($options,"required")){
                $this->required = "<span class='red'>*</span>";
            }
        }


        #se a id nao for preenchida, será o name
        if ($this->id == null)
            $this->id = str_replace("[]","",$this->name);
    }

    function writeElement(){
        return "<input ".$this->attributes()." />";
    }

    public function __toString(){
        
        if ($this->hidden){
            return $this->writeElement();
        } else {
            
            $html = "<div class='{$this->size} field {$this->div_class}' ";
            
            if ($this->tooltip)
                $html .= " data-tooltip='{$this->tooltip}' data-inverted='' ";
            $html .= ">";

            if ($this->label != null) {
                $html .= "<label for='{$this->id}'>{$this->label} {$this->required}</label>";
            }

            if ($this->icon){
                $html .= "<div class='ui left icon input'>";
                $html .= "<i class='{$this->icon} icon'></i>";
            } else
            if ($this->right_icon){
                $html .= "<div class='ui right icon input'>";
                $html .= "<i class='{$this->icon} icon'></i>";
            } else {
                $html .= "<div class='ui input'>";
            }

            $html .= $this->writeElement();
            
            $html .= "</div>";

            $html .= error($this->name);

            $html .= "</div>";
            return $html;
        }
    }
}


class HTMLSearch extends HTMLInput {
    protected $url;
    protected $display_value;
    protected $prompt_id;

    /**
     * $name = Name do input
     * $options [id, readonly, required, hidden, label, icon, maxlength, div_class, url, display_value]
     */
    function __construct($name,$options=[]){
        $this->div_class = "ui search";
        parent::__construct($name,$options);
        $this->prompt_id = $this->id . "_prompt";
        if (is_array($options)){
            $this->getFromOptions(["url","display_value"],$options);
            if (isset($options["value"]) && 
                is_array($options["value"]) && 
                key_exists($this->prompt_id, $options["value"])){
                $this->display_value = $options["value"][$this->prompt_id];
            }
        }
    }

    function writeElement(){
        if ($this->url != "" && !stristr("http",$this->url))
            $this->url = site_url() . "/" . $this->url;
        $this->addAttributes(["url"]);
        $this->removeAttributes(["name","value","class", "id"]);
        return "<input name='{$this->name}' id='{$this->id}' type='hidden' value='{$this->value}'>"
                ."<input name='{$this->prompt_id}' id='{$this->prompt_id}' class='{$this->class} prompt' ".$this->attributes()." for='{$this->id}' value='{$this->display_value}' />";
    }
}


class HTMLSelect extends HTMLInput {

    protected $options = [];
    protected $blank = true;
    protected $natural = false;
    protected $search = true;
    protected $url;

    /**
     * $name = Name do input
     * $options [id, readonly, required, options, label, search=true, blank=true, natural=false, url]
     */
    function __construct($name,$options=[]){
        $this->placeholder = "Selecione uma opção";
        parent::__construct($name,$options);
        if (is_array($options)){
            $this->getFromOptions(["search","blank","natural","url"],$options);
            if (isset($options["options"])){
                $this->options = $options["options"];
            }
        }
    }

    function writeElement(){
        if ($this->natural){
            $html = "<select ".$this->attributes().">";

            if ($this->blank){
                $html .= "<option value=''>{$this->label}</option>";
            }

            foreach($this->options as $k=>$val){
                $selected = "";
                if ($this->value == $k){
                    $selected = "selected";
                }
                $html .= "<option $selected value='$k'>$val</option>";
            }
            $html .= "</select>";
        } else {
            #se tem url não terá options
            if ($this->url != ""){
                $this->url = site_url() . "/" . $this->url;
                $this->options = [];
                $this->addAttributes(["url"]);
                $this->search = true;
            }
            $this->removeAttributes(["type","name","placeholder"]);
            
            $this->class .= " ui fluid selection dropdown block";
            if ($this->search)
                $this->class .= " search";
            
            #Versão mais elegante do select com semantic
            $html = "<div ".$this->attributes()." >";
            $html .= "<input type='hidden' name='{$this->name}' value='{$this->value}' >";
            $html .= "<i class='dropdown icon'></i>";

            $html .= "<div class='default text'>{$this->placeholder}</div>";
            
            $html .= "<div class='menu'>";
            foreach($this->options as $k=>$val){
                $html .= "<div class='item' data-value='$k' >$val</div>";
            }
            $html .= "</div></div>";
        }
        return $html;

    }

}

class HTMLRadio extends HTMLSelect {

    protected $column = false;
    protected $append = "";

    /**
     * $name = Name do input
     * $options [id(Value/Array), required, options, label, column=false]
     */
    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        $this->type = "radio";
        $this->blank = false;
        $this->removeAttributes(["value","id","placeholder"]);
        if (is_array($options)){
            $this->getFromOptions(["column", "id"],$options);
        }
    }

    public function appendHTML($obj){
        if (get_class($obj) == "HTMLRadio" || get_class($obj) == "HTMLCheckbox"){
            $this->append .= $obj->writeElement();
        }
        return $this;
    }

    function writeElement(){
        $html = "";
        if ($this->type == "checkbox") {
            $html .= "<input type='hidden' name='$this->name' value=''>";
        }

        
        $id_num = 0;


        foreach($this->options as $key=>$option){

            $checked = "";
            if (is_array($this->value)){
                if (in_array($key, $this->value)){
                    $checked = "checked";
                }
            } else
            if (!is_null($this->value) && $key == $this->value){
                $checked = "checked";
            }
            
            $typeClass = "";
            if ($this->type == "radio"){
                $typeClass = "radio";
            }

            if (is_array($this->id)){
                $id = $this->id[$id_num];
            } else {
                $id = $this->id ."_". $id_num;
            }

            $html .= "<div class='ui field $typeClass checkbox' ";
            if ($this->tooltip){
                if (is_array($this->tooltip)){
                    $tooltip = $this->tooltip[$id_num];
                } else {
                    $tooltip = $this->tooltip;
                }
                $html .= " data-tooltip='{$tooltip}' data-inverted='' ";
            }
            $html .= ">";
            $html .= "<input id='$id' ".$this->attributes()." $checked value='$key' />";
            $html .= "<label for='$id'>$option</label>";
            $html .= "</div>";

            $id_num++;
        }

        return $html. $this->append;
    }

    function __toString(){
        
        #horizontal ou vertical
        if ($this->column){
            $classFields = "columnFields";
        } else {
            $classFields = "fields";
        }

        $html = "<div class='field {$this->disabled}'>";
        if ($this->label)
            $html .= "<label>$this->label {$this->required}</label>";
        
        $html .= "<div class='{$this->size} $classFields'>";

        $html .= $this->writeElement();

        return $html . "</div></div>" . error($this->name);
    }
}

class HTMLCheckbox extends HTMLRadio {
    protected $is_array = true;
    /**
     * $name = Name do input
     * $options [id, required, options, label, is_array]
     */
    function __construct($name,$options=[]){
        parent::__construct($name,$options);
        $this->type = "checkbox";
        $this->blank = false;
        if (is_array($options)){
            $this->getFromOptions(["is_array"],$options);

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
        if (strstr($this->name,"[]") == false && $this->is_array){
            $this->name = $this->name."[]";
        }
    }
}

class HTMLUpload extends HTMLInput {
    protected $path;
    protected $fileType = "file";

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
        $html = "<input ".$this->attributes()." /></div><div>";

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

class HTMLTextArea extends HTMLInput {
    
    /**
     * $name = Name do input
     * $options [id, readonly, required, label]
     */
    function __construct($name,$options=[]){
        parent::__construct($name,$options);
    }

    function writeElement(){
        $this->removeAttributes(["type","value"]);
        $html = "<textarea ".$this->attributes()." >{$this->value}</textarea>";
        return $html;
    }
}

class HTMLButton extends HTMLElement {
    protected $text;
    protected $href;
    protected $color;
    protected $onclick;

    /**
     * $name = Name do input
     * $options [id, text, href, color, onclick]
     */
    function __construct($text,$options=[]){
        $this->text = $text;
        $this->type = "submit";
        include(APPPATH.'/config/config.php');
        $this->color = $config["theme_color"];

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
        if (!strstr($action,"http")){
            $action = site_url()."/".$action;
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

        unset($_SESSION['error']);
        unset($_SESSION['success']);
        unset($_SESSION['warning']);
        unset($_SESSION['message']);
        return $html;
    }
}