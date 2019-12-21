<?php
include 'header.php';

print "<h3>Testes do semantic_helper</h3>";

print formStart("#");

print '<h4>Inputs</h4>';
print input("nome1","Normal",["nome"=>"João"], ["test"=>"[name=nome1]"]);

print input("nome","Desativado",["nome"=>"João"], ["disabled", "test"=>"[name=nome][disabled]"]);

print input("nome","Requerido",["nome"=>"João"], ["required", "test"=>"requerido"]);

print input("readon","Somente leitura",["readon"=>"João"], ["readonly", "test"=>"[name=readon][readonly]"]);

print input("reqdes","Requerido e desativado",["reqdes"=>"João"], ["required","disabled","test"=>"requerido[name=reqdes][disabled]"]);

print select("tipo","Select",["tipo1","tipo2"],["tipo"=>1],["required","id"=>"sel","class"=>"teste", "test"=>"requerido#sel.teste"]);

print checkbox("tipo","Checkbox",["tipo1","tipo2"],["tipo"=>1], ["required","id"=>"chk","class"=>"teste"]);

print radio("tipo","Radio",["tipo1","tipo2"],["tipo"=>1], ["required","id"=>"rad","class"=>"teste"]);


print '<h4>Sizes and group</h4>';
$sel = select("tipo","Select",["tipo1","tipo2"],["tipo"=>1],["size"=>4]);
$inp = input("nome","Nome",["nome"=>"Maria"],["size"=>4]);
$inp2 = input("nome","CPF",[],["size"=>4,"class"=>"cpf","id"=>"cpf"]);
$rad = radio("sexo","Sexo",["m"=>"M","f"=>"F"],["sexo"=>"f"], "required");
print group($sel, $inp, $inp2, $rad);


print '<h4>Upload</h4>';
print upload("foto","Foto","image",["foto"=>"Capturar_PNG.PNG"],"uploads","required");

print upload("arquivo","Arquivo","file",["arquivo"=>"teste.rtf"],"uploads","required");

print upload("arquivo","Desativado","file",["arquivo"=>"teste.rtf"],"uploads",["disabled"=>true,"id"=>"id_teste","class"=>"teste"]);

print '<h4>Buttons</h4>';
print button("Tamanho 6",["size"=>6]);
print button("Link Tamanho 6",["href"=>"./testes","size"=>6,"color"=>"yellow"]);

$btn1 = button("Tam 3 desativado",["size"=>3, "disabled"=>true]);
$btn2 = button("Link tam 3 desativado",["size"=>3, "href"=>"./testes","disabled"=>true,"color"=>"yellow"]);
print group($btn1, $btn2);


$inp = input("busca","",[],["placeholder"=>"Pesquisar","size"=>6]);
$btn = button("Pesquisar");
print group($inp, $btn);

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