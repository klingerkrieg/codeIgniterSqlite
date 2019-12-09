$(function(){
    $('.dropdown').dropdown({
        clearable: true,
        placeholder: 'any',
        on:"hover"
    });
});



function confirmDelete(url){
    $('.ui.basic.modal').modal('show');
    $('#confirmDeleteBtn').click(function(){
        window.location = url;
    })
}