$(function () {
    var modal = new jBox('Modal');
    var createCategoryUrl;
    $('#categories').jstree({ 'core' : {
            "check_callback" : true,
            "themes" : { "stripes" : true },
            "plugins" : [
                "contextmenu", "dnd", "search",
                "state", "types", "wholerow"
            ]
        } });

    loadCategories();

    $('#createSubCategory').on('click', function () {
        createSubCategory();
    });

    $('#createRootCategory').on('click', function () {
        createRootCategory();
    });

    confirm = new jBox('Confirm', {
        content: 'Do you really want to do this?',
        cancelButton: 'Nope',
        confirmButton: 'Sure do!',
        confirm: deteleCategory
    });
    confirm.disable();

    $('#deleteCategory').on('click', function () {
        category = $("#categories").jstree(true).get_selected(true);
        if(category[0]) {
            confirm.enable();
            confirm.setContent("Do you really want to delete <font color='red'>" + category[0]['text'] + "</font> category, and it's <font color='red'>sub</font> categories?")
            categoryToDelete = category[0];
            confirm.open();
        }
        if(!category[0]){
            confirm.disable();
            new jBox('Notice', {content: 'Select a category first!',delayOpen: 1, delayClose: 1, color: 'red', attributes: {y: 'bottom'}}).open();
        }
    });
    function deteleCategory(){
        $.ajax({
            method:'GET',
            url:'/document/category/delete/'+categoryToDelete['id'],
            success:function(){
                new jBox('Notice', {content: 'Categories deleted!',delayOpen: 1, delayClose: 1, color: 'green', attributes: {y: 'bottom'}}).open();
                loadCategories();
            }
        })
    }
    function createRootCategory(){
        createCategoryUrl = '/document/category/create';
        createCategory();
    }

    function createSubCategory(){
        data = $("#categories").jstree(true).get_selected(false);
        if(data) {
            createCategoryUrl = '/document/category/create/' + data;
            createCategory()
        }
    }
    function createCategory() {
        console.log(createCategoryUrl);
            $.ajax({
                method: 'GET',
                url: createCategoryUrl,
                success: function (data) {
                    if (data != 'saved') {
                        modal.setTitle('Create Category').setContent(data);
                        modal.open();

                        $('#createCategoryForm').submit(createCategoryPost);
                    }
                }
            });
    }

    function createCategoryPost(event) {
        $.ajax({
            method: 'POST',
            data: 'name='+$('#name').val(),
            url: createCategoryUrl,
            success: function(data){
                if(data != 'saved'){
                    modal.setContent(data);
                    //$('#createCategoryForm').submit(createCategoryPost);
                }
                if(data == 'saved') {
                    modal.close();
                    loadCategories();
                }
            }
        });
        event.preventDefault();
    }

    function loadCategories(){
        $.ajax({
            method:'GET',
            url:'/document/category/list',
            success: function(data){
                dataJson = JSON.parse(data);
                $("#categories").jstree(true).settings.core.data = dataJson;
                $("#categories").jstree(true).refresh();
            }
        });
    }
});