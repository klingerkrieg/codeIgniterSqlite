<?php
include 'header.php';

print "<h3>Testes do semantic_helper</h3>";

print formStart("#");

print '<h4>Inputs</h4>';

#Values como Arrays
print new HTMLInput("nome1",["label"=>"Normal", "value"=>["nome"=>"João"], "attributes"=>['test'=>'[name=nome1]']]);

print new HTMLInput("nome",["label"=>"Desativado", "value"=>["nome"=>"João"],"disabled", "attributes"=>['test'=>'[name=nome][disabled]']]);

print new HTMLInput("nome",["label"=>"Requerido", "value"=>["nome"=>"João"],"required", "attributes"=>['test'=>'requerido']]);

print new HTMLInput("readon",["label"=>"Somente leitura", "value"=>["readon"=>"João"],"readonly", "attributes"=>['test'=>'[name=readon][readonly]']]);

print new HTMLInput("reqdes",["label"=>"Readonly e desativado", "value"=>["reqdes"=>"João"],"disabled", "readonly", "attributes"=>['test'=>'[name=reqdes][readonly][disabled]']]);

print new HTMLSelect("sel",["label"=>"Select First not blank","options"=>[1=>"tipo1",2=>"tipo2"], "blank"=>false, "attributes"=>['test'=>"js:$('[name=sel]').val() == 1"]]);

print new HTMLSelect("tipo",["label"=>"Select Tipo 2 selected","options"=>["tipo1","tipo2"],"value"=>["tipo"=>1],"required","id"=>"sel","class"=>"teste", "attributes"=>['test'=>'requerido#sel.teste']]);

print new HTMLCheckbox("chkbox",["label"=>"Checkbox Tipo nada marcado","options"=>["tipo1","tipo2"],"value"=>["chkbox"=>null], "required","id"=>"chk","class"=>"teste", "attributes"=>["test"=>"js:$('[name=chkbox\\\\[\\\\]][type=checkbox]').prop('checked') == false"]]);

print new HTMLCheckbox("chkbox2",["label"=>"Varios marcados","options"=>["tipo1","tipo2","tipo3"],"value"=>["chkbox2"=>[0,2]], "class"=>"chk2", "attributes"=>["test"=>"js:$('.chk2[type=checkbox]').eq(0).prop('checked') == true && $('.chk2[type=checkbox]').eq(1).prop('checked') == false && $('.chk2[type=checkbox]').eq(2).prop('checked') == true"]]);

print new HTMLCheckbox("chkbox3",["label"=>"Checkbox value simples","options"=>["tipo1","tipo2"],"value"=>1, "class"=>"chk3", "attributes"=>["test"=>"js:$('.chk3').eq(0).prop('checked') == false && $('.chk3').eq(1).prop('checked') == true"]]);

print new HTMLRadio("radio",["label"=>"Radio Tipo 1 checked","options"=>["tipo1","tipo2"],"value"=>["radio"=>0], "required","id"=>"rad","class"=>"teste", "attributes"=>["test"=>"js:$('[name=radio][type=radio]').eq(0).prop('checked')"]]);

print new HTMLRadio("radio",["label"=>"Radio nada marcado","options"=>["tipo1","tipo2"],"value"=>[], "id"=>"rad2", "attributes"=>["test"=>"js:$('[name=radio][type=radio]').eq(0).prop('checked')"]]);

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

print new HTMLUpload("arquivo2",["label"=>"Desativado","value"=>["arquivo"=>"teste.rtf"],"path"=>"uploads","disabled","id"=>"id_teste","class"=>"teste", "attributes"=>['test'=>'[name=arquivo2][disabled][type=file]']]);

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



include 'layout/bottom.php';
?>

<style>
.ui.text.container{
	max-width:90% !important;
}
table {
	width:100%;
}
</style>