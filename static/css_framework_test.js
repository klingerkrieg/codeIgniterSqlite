$(function(){

    $("[test]").each(function(){

        teste = $(this).attr("test");
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

        if (busca.length == 1){
            $(this).after($("<img class='testeJS' src='"+base_url+"/static/ok.png' />"));
        } else {
            $(this).after($("<img class='testeJS' src='"+base_url+"/static/fail.png' />"));
        }

    });
});