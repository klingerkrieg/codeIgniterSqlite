<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists("input")){
    function input($name,$label,$data,$options="text"){
        if ($options == "hidden"){
            return "<input type='hidden' name='$name' value='" . val($data,$name) . "'>" . error($name);
        } else {

            $disabled = "";
            if (strstr($options,"disabled")){
                $disabled = "disabled";
            }
            $required = "";
            if (strstr($options,"required")){
                $required = "<span class='red'>*</span>";
            }

            $html = "<div class='field'>
                        <label>$label $required
                            <input type='text' name='$name' value='" . val($data,$name) ."' $disabled>
                            " . error($name) . "
                        </label>
                    </div>";

            return $html;

        }
        
    }
}

if (!isset($sizes)){
    $sizes = ["","one","two","three","four","five","six","seven","eight","nine",
            "ten","eleven","twelve","thirteen","fourteen","fifteen","sixteen"];
}

if (!function_exists("select")){
    function select($name,$label,$values,$data=[],$options=""){
        $required = "";
        if (strstr($options,"required")){
            $required = "<span class='red'>*</span>";
        }

        $val = $data;
        if (is_array($data) || is_object($data)){
            $val = val($data,$name);
            if ($val == "" && strstr($name,"_id")){
                list($key1, $key2) = explode("_",$name);
                $val = val($data, $key1, $key2);
            }
        }
        

        $html = "<div class='field'><label>$label $required"
            . form_dropdown($name, $values, $val) 
            . error($name)
            . "</label></div>";

        return $html;
    }
}



if (!function_exists("checkbox")){
    function checkbox($name,$label,$values,$data,$size=4,$options=""){
        global $sizes;

        $required = "";
        if (strstr($options,"required")){
            $required = "<span class='red'>*</span>";
        }

        $size = $sizes[$size];

        $html = "<div class='field'>
            <label>$label $required</label>
            <div class='$size fields'>";

        $html .= "<input type='hidden' name='{$name}[]' value=''>";   
        
        foreach($values as $key=>$option){
            $html .= "<label class='field'>";
            $html .= form_checkbox($name."[]", $key, checked($key, $data, $name) );
            $html .= "$option</label>";
        }
        
        $html .= "</div>".error($name)."</div>";



        return $html;
    }
}


if (!function_exists("radio")){
    function radio($name,$label,$values,$data,$size=4,$options=""){
        global $sizes;

        $required = "";
        if (strstr($options,"required")){
            $required = "<span class='red'>*</span>";
        }

        $size = $sizes[$size];

        $html = "<div class='field'>
            <label>$label $required</label>
            <div class='$size fields'>";
                
        foreach($values as $key=>$option){
            $html .= "<label class='field'>";
            $html .= form_radio($name, $key, checked($key, $data, $name) );
            $html .= "$option</label>";
        }
                
        $html .= "</div>".error($name)."</div>";



        return $html;
    }
}

if (!function_exists("upload")){
    function upload($name,$label,$type,$data,$path,$options=""){
        $required = "";
        if (strstr($options,"required")){
            $required = "<span class='red'>*</span>";
        }

        $html = "<div class='field'>
                    <label>$label $required
                        <input type='file' name='$name'>
                        ".error('foto')."
                    </label>";

        if (val($data,$name) != ""){
            if ($type == "file"){
                $html .= "<a target='_BLANK' href='".base_url()."$path/{$data[$name]}' />";
            } else
            if ($type == "image"){
                $html .= "<img class='ui tiny circular image' src='".base_url()."$path/{$data[$name]}' />";
            }
        }
        
        return $html."</div>";
    }
}



if (!function_exists("tableHeader")){
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

