$(function(){
    $('.dropdown').dropdown({
        clearable: true,
        placeholder: 'Selecione uma opção',
        on:"hover"
    });


    $("input[url]").each(function(){
        el = $(this);
        el.parents("div.search").search({
            apiSettings: {
                url: el.attr("url")
            },
            onSelect:function(result, response){
                $("#"+el.attr("for")).val(result.value);            
            },
            fields: {
                title: 'name'
            },
            minCharacters : 2,
            searchDelay: 300
        });

    });


    $('.search[url]').each(function(){
        el = $(this);
        el.dropdown({
            apiSettings: {
                url: el.attr("url"),
                cache:false,
            },
            clearable: true,
            placeholder: 'Selecione uma opção',
            on:"hover"
        });
    });
    
    
});



function confirmDelete(url){
    $('.ui.basic.modal').modal('show');
    $('#confirmDeleteBtn').click(function(){
        window.location = url;
    })
}