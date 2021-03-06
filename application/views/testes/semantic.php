<?php
include 'header.php';

print "<h3>Testes do semantic_helper</h3>";

print formStart("#");

print '<h4>Inputs</h4>';

#Values como Arrays
print new HTMLInput("nome1",["label"=>"Normal", "value"=>["nome"=>"João"], "attributes"=>['test'=>'[name=nome1]']]);

print new HTMLInput("nome",["label"=>"Desativado", "value"=>["nome"=>"João"],"disabled", "attributes"=>['test'=>'[name=nome][disabled]']]);

print new HTMLInput("nomeReq",["label"=>"Requerido", "value"=>["nome"=>"João"],"required", "attributes"=>['test'=>'js:$("#nomeReq").parents(".field").find("span.red").length == 1']]);

print new HTMLInput("user",["id"=>"user", "icon"=>"user", "attributes"=>['test'=>"js:$('#user').prev('i.icon').length"]]);

print new HTMLInput("readon",["label"=>"Somente leitura", "value"=>["readon"=>"João"],"readonly", "attributes"=>['test'=>'[name=readon][readonly]']]);

print new HTMLInput("reqdes",["label"=>"Readonly e desativado", "value"=>["reqdes"=>"João"],"disabled", "readonly", "attributes"=>['test'=>'[name=reqdes][readonly][disabled]']]);

print new HTMLSearch("ajax",["label"=>"Ajax test","value"=>["ajax"=>2], "display_value"=>"maria", "url"=>"testes/ajax/?q={query}", "attributes"=>['test'=>"js:$('#ajax').val() == 2 && $('#ajax_prompt').val() == 'maria'"]]);

print new HTMLSearch("ajax2",["label"=>"Ajax display embutido","value"=>["ajax2"=>1, "ajax2_prompt"=>"pedro"], "url"=>"testes/ajax/?q={query}", "attributes"=>['test'=>"js:$('#ajax2').val() == 1 && $('#ajax2_prompt').val() == 'pedro'"]]);

print new HTMLSelect("ajax3",["label"=>"Ajax Select em branco", "url"=>"testes/ajax/?q={query}" ,"attributes"=>['test'=>"js:$('[name=ajax3]').val() == ''"]]);

print new HTMLSelect("ajax4",["label"=>"Ajax Select com value", "url"=>"testes/ajax/?q={query}", "value"=>["ajax4"=>2], "attributes"=>['test'=>"js:$('[name=ajax4]').val() == 2"]]);

print new HTMLSelect("sel",["label"=>"Natural Select First not blank","options"=>[1=>"tipo1",2=>"tipo2"], "blank"=>false, "natural"=>true, "attributes"=>['test'=>"js:$('[name=sel]').val() == 1"]]);

print new HTMLSelect("sel2",["label"=>"Natural Select First blank","options"=>[1=>"tipo1",2=>"tipo2"], "natural"=>true, "attributes"=>['test'=>"js:$('[name=sel2]').val() == ''"]]);

print new HTMLSelect("notsearch",["label"=>"Not searchable","options"=>[1=>"tipo1",2=>"tipo2"], "search"=>false ,"attributes"=>['test'=>"js:$('[name=sel]').val() == 1"]]);

print new HTMLSelect("selectReq",["label"=>"Requerido Select Tipo 2 selected ID modificada","options"=>["tipo1","tipo2"],"value"=>["selectReq"=>1],"required","id"=>"modSelReq","class"=>"teste", "attributes"=>['test'=>'js:$("#modSelReq").parents(".field").find("span.red").length == 1 && $("[name=selectReq]").val() == 1']]);

print new HTMLCheckbox("chkbox",["label"=>"Checkbox Tipo nada marcado","options"=>["tipo1","tipo2"],"value"=>["chkbox"=>null], "required","id"=>"chk","class"=>"teste", "attributes"=>["test"=>"js:$('[name=chkbox\\\\[\\\\]][type=checkbox]').prop('checked') == false"]]);

print new HTMLCheckbox("chkbox2",["label"=>"Varios marcados","options"=>["tipo1","tipo2","tipo3"],"value"=>["chkbox2"=>[0,2]], "class"=>"chk2", "attributes"=>["test"=>"js:$('#chkbox2_0').prop('checked') == true && $('#chkbox2_1').prop('checked') == false && $('#chkbox2_2').prop('checked') == true"]]);

print new HTMLCheckbox("chkbox3",["label"=>"Checkbox value simples","options"=>["tipo1","tipo2"],"value"=>1, "class"=>"chk3", "attributes"=>["test"=>"js:$('.chk3').eq(0).prop('checked') == false && $('.chk3').eq(1).prop('checked') == true"]]);

print new HTMLRadio("radio1",["label"=>"Radio Tipo 1 checked","options"=>["tipo1","tipo2"],"value"=>["radio1"=>0], "required","id"=>"rad","class"=>"teste", "attributes"=>["test"=>"js:$('[name=radio1][type=radio]').eq(0).prop('checked')"]]);

print new HTMLRadio("radio2",["label"=>"Radio nada marcado","options"=>["tipo1","tipo2"],"value"=>[], "id"=>"rad2", "attributes"=>["test"=>"js:$('#rad2_0').prop('checked') == false"]]);

print new HTMLTextArea("texto",["label"=>"Textarea sem limite","value"=>["texto"=>"joao"], "attributes"=>["test"=>"textarea#texto"]]);

print new HTMLTextArea("texto2",["label"=>"Textarea com limite","value"=>["texto2"=>"123456789"], "maxlength"=>10, "attributes"=>["test"=>"textarea#texto2[maxlength=10]"]]);

#Values sem ser array
print '<h4>Sizes and group</h4>';
$sel = new HTMLSelect("tipo2",["label"=>"Select tipo 1 selected","options"=>["tipo1","tipo2"],"value"=>0,"size"=>4,"attributes"=>["test"=>"js:$('.four.wide.field [name=tipo2]').val()"]]); 
$inp = new HTMLInput("nome2",["label"=>"Nome","value"=>"Maria","size"=>4, "attributes"=>['test'=>'.four.wide.field [name=nome2]']]);
$inp2 = new HTMLInput("cpf",["label"=>"CPF","size"=>2,"class"=>"cpf","id"=>"cpf", "attributes"=>['test'=>'.two.wide.field [name=cpf].cpf#cpf']]);
$rad = new HTMLRadio("sexo",["label"=>"Sexo","options"=>["m"=>"M","f"=>"F"],"value"=>"f", "required", "attributes"=>["test"=>"js:$('[name=sexo][type=radio]').eq(1).prop('checked')"]]);
print new HTMLGroup($sel, $inp, $inp2, $rad);


print '<h4>Upload</h4>';
print new HTMLUpload("foto",["label"=>"Foto","fileType"=>"image","value"=>["foto"=>"Capturar_PNG.PNG"],"path"=>"uploads","required", "attributes"=>['test'=>'[name=foto][type=file]']]);

print new HTMLUpload("arquivo",["label"=>"Arquivo","fileType"=>"file","value"=>"teste.rtf","path"=>"uploads","required", "attributes"=>['test'=>'[name=arquivo][type=file]']]);

print new HTMLUpload("arquivo2",["label"=>"Desativado","value"=>["arquivo"=>"teste.rtf"],"path"=>"uploads","disabled", "attributes"=>['test'=>'[name=arquivo2][disabled][type=file]']]);

print '<h4>Buttons</h4>';
print new HTMLButton("Tamanho 6",["size"=>6, "id"=>"btn6", "attributes"=>['test'=>'.six.wide.field button#btn6.buttonFix.ui.blue.button']]);
print new HTMLButton("Link Tamanho 6",["href"=>"/testes/semantic", "id"=>"link6","size"=>6,"color"=>"yellow", "attributes"=>['test'=>'.six.wide.field a#link6.buttonFix.ui.yellow.button']]);
#ele sempre substitui qualquer aspas duplas por aspas simples nos atributos
print new HTMLButton("Onclick",["onclick"=>'alert("teste");', "attributes"=>['test'=>'.field button.ui.blue.button[onclick]']]);


$btn1 = new HTMLButton("Tam 3 desativado",["size"=>3, "disabled", "attributes"=>['test'=>'.fields .three.wide.field button.ui.blue.button.disabled[disabled]']]);
$btn2 = new HTMLButton("Link tam 3 desativado",["size"=>3, "href"=>"/testes/semantic","disabled"=>true,"color"=>"yellow", "attributes"=>['test'=>'.fields .three.wide.field a.ui.yellow.button.disabled[disabled][href]']]);
print new HTMLGroup($btn1, $btn2);


$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","size"=>6, "attributes"=>['test'=>'.fields .six.wide.field [name=busca][placeholder=Pesquisar]']]);
$btn = new HTMLButton("Pesquisar", ["id"=>"pesquisar","attributes"=>['test'=>'.fields .field button#pesquisar']]);
print new HTMLGroup($inp, $btn);

print formEnd();


$CI =& get_instance();
$CI->session->set_flashdata("error","Erro");
$CI->session->set_flashdata("warning","Alerta");
$CI->session->set_flashdata("success","Ok");
$CI->session->set_flashdata("message","Aviso");

print flashMessage();



print '<h4>Tables</h4>';
print tableHeader("Tabela 1","id","nome");
print tableBottom(["total_rows"=>0,"page_max"=>0]);

print tableHeader("Tabela 2","id","nome");
$dados = [];
for($i = 0; $i < 5; $i++){
	$nome = substr(sha1($i),0,20);
	array_push($dados, ["id"=>$i, "nome"=>$nome]);
	print "<tr><td>$i</td><td>$nome</td></tr>";
}
print tableBottom(["total_rows"=>15,"page_max"=>3]);



include 'bottom.php';
?>

<style>
.ui.text.container{
	max-width:90% !important;
}
table {
	width:100%;
}
</style>