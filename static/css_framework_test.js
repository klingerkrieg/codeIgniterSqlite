$(function(){

    $("[test]").each(function(){

        teste = $(this).attr("test");

        if (teste.indexOf("js") == 0){
            teste = teste.replace("js:","");
            res = eval(teste);
            showTestResult(this,res);
            return;
        }

        busca = $(teste);
        showTestResult(this,busca.length == 1);
    });
});

function showTestResult(element, res){
    if ($(element).hasClass("button"))
        element = $(element);
    else 
        element = $(element).parent();
    if (res){
        element.after($("<img class='testeJS' src='"+base_url+"/static/ok.png' />"));
    } else {
        element.after($("<img class='testeJS' src='"+base_url+"/static/fail.png' />"));
    }
}