$(function(){

    $('#disciplinaSearch').dropdown({
        apiSettings: {
            url: site_url + '/notas/disciplinas/{query}',
            cache:false
        },
    });


    //blur, quando sair do campo
    $(".tableInput").blur(function(){

        //recupera a chave primária na tabela alunosdisciplinas
        idAux = $(this).attr("alunosdisciplinas");
        //recupera o span que contém a nota final desse aluno
        notaFinal = $('#final'+$(this).attr("alunosdisciplinas"));

        //reune os dados que serão enviados para o PHP
        dados = {
            nota:$(this).val(), //valor da nota
            etapa:$(this).attr("id") //nota1 ou nota2
        }

        //faz a requisição ajax
        $.ajax(site_url+"/notas/lancar/"+idAux,{
            data:dados,
            element:$(this),
            notaFinal:notaFinal,
            idAux:idAux,
            success:function(resp){
                //indica se foi salvo com sucesso
                if (resp == 1){
                    this.element.addClass("salvo");
                } else {
                    this.element.addClass("falha");
                }

                nota = 0;
                //recalcula a nota final
                $("[alunosdisciplinas="+this.idAux+"]").each(function(){
                    nota += parseInt($(this).val());
                });
                this.notaFinal.html(nota/2);
            }
        });
    });

});