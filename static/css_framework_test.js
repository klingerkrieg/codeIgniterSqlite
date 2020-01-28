$(function(){

    $("[test]").each(function(){

        teste = $(this).attr("test");

        if (teste.indexOf("js") == 0){
            teste = teste.replace("js:","");
            res = eval(teste);
            showTestResult(this,res);
            return;
        } else
        if (teste.indexOf("requerido") > -1){
            if ($(this).prev().hasClass("red")){
                $(this).after($("<img class='testeJS' src='"+base_url+"/static/ok.png' />"));
            } else {
                $(this).after($("<img class='testeJS' src='"+base_url+"/static/fail.png' />"));
            }
            teste = teste.replace("requerido","");
            if (teste == ""){
                return;
            }
        } 

        busca = $(teste);
        showTestResult(this,busca.length == 1);
    });
});

function showTestResult(element, res){
    if (res){
        $(element).after($("<img class='testeJS' src='"+base_url+"/static/ok.png' />"));
    } else {
        $(element).after($("<img class='testeJS' src='"+base_url+"/static/fail.png' />"));
    }
}