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

print new HTMLInput("reqdes",["label"=>"Requerido e desativado", "value"=>["reqdes"=>"João"],"required", "readonly", "attributes"=>['test'=>'requerido[name=reqdes][disabled]']]);

print new HTMLSelect("tipo",["label"=>"Select","options"=>["tipo1","tipo2"],"value"=>["tipo"=>1],"required","id"=>"sel","class"=>"teste", "attributes"=>['test'=>'requerido#sel.teste']]);

print new HTMLCheckbox("tipo",["label"=>"Checkbox","options"=>["tipo1","tipo2"],"value"=>["tipo"=>1], "required","id"=>"chk","class"=>"teste"]);

print new HTMLRadio("tipo",["label"=>"Radio","options"=>["tipo1","tipo2"],"value"=>["tipo"=>1], "required","id"=>"rad","class"=>"teste"]);

#Values sem ser array
print '<h4>Sizes and group</h4>';
$sel = new HTMLSelect("tipo",["label"=>"Select","options"=>["tipo1","tipo2"],"value"=>1,"size"=>4]);
$inp = new HTMLInput("nome",["label"=>"Nome","value"=>"Maria","size"=>4]);
$inp2 = new HTMLInput("nome",["label"=>"CPF","size"=>2,"class"=>"cpf","id"=>"cpf"]);
$rad = new HTMLRadio("sexo",["label"=>"Sexo","options"=>["m"=>"M","f"=>"F"],"value"=>"f", "required"]);
print new HTMLGroup($sel, $inp, $inp2, $rad);


print '<h4>Upload</h4>';
print new HTMLUpload("foto",["label"=>"Foto","fileType"=>"image","value"=>["foto"=>"Capturar_PNG.PNG"],"path"=>"uploads","required"]);

print new HTMLUpload("arquivo",["label"=>"Arquivo","fileType"=>"file","value"=>"teste.rtf","path"=>"uploads","required"]);

print new HTMLUpload("arquivo",["label"=>"Desativado","value"=>["arquivo"=>"teste.rtf"],"path"=>"uploads","disabled","id"=>"id_teste","class"=>"teste"]);

print '<h4>Buttons</h4>';
print new HTMLButton("Tamanho 6",["size"=>6]);
print new HTMLButton("Link Tamanho 6",["href"=>"/testes/semantic","size"=>6,"color"=>"yellow"]);
#ele sempre substitui qualquer aspas duplas por aspas simples nos atributos
print new HTMLButton("Onclick",["onClick"=>'alert("teste");']);

$btn1 = new HTMLButton("Tam 3 desativado",["size"=>3, "disabled"=>true]);
$btn2 = new HTMLButton("Link tam 3 desativado",["size"=>3, "href"=>"/testes/semantic","disabled"=>true,"color"=>"yellow"]);
print new HTMLGroup($btn1, $btn2);


$inp = new HTMLInput("busca",["placeholder"=>"Pesquisar","size"=>6]);
$btn = new HTMLButton("Pesquisar");
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