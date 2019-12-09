$(function(){

    $(".tableInput").blur(function(){

        idAux = $(this).attr("alunosdisciplinas");
        notaFinal = $('#final'+$(this).attr("alunosdisciplinas"));

        dados = {
            nota:$(this).val(), //valor da nota
            etapa:$(this).attr("id") //nota1 ou nota2
        }

        $.ajax(site_url+"/notas/lancar/"+idAux,{
            data:dados,
            element:$(this),
            notaFinal:notaFinal,
            idAux:idAux,
            success:function(resp){
                if (resp == 1){
                    this.element.addClass("salvo");
                } else {
                    this.element.addClass("falha");
                }

                nota = 0;
                $("[alunosdisciplinas="+this.idAux+"]").each(function(){
                    nota += parseInt($(this).val());
                });


                this.notaFinal.html(nota/2);
                
            }
        });
    });

});