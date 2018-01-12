$(function () {
    var modal = new jBox('Modal');
    var categoryAjaxData;
    var noticeData = {delayOpen: 1, delayClose: 1, attributes: {y: 'bottom'}};
    var confirm = new jBox('Confirm', {
        content: 'Do you really want to do this?',
        cancelButton: 'Nope',
        confirmButton: 'Sure do!',
        confirm: deleteCategoryAjaxRequest
    });
    var deleteAjaxData = {
        method:'GET',
        success:function(){
            noticeData.content = 'Category deleted!';
            noticeData.color = 'green';
            new jBox('Notice', noticeData).open();
            loadCategories();
        }
    };
    /**
     * Init jsTree
     */
    $('#categories').jstree({ 'core' : {
            "check_callback" : true,
            "themes" : { "stripes" : true },
            "plugins" : [
                "contextmenu", "dnd", "search",
                "state", "types", "wholerow"
            ]
        } });

    loadCategories();

    $('#createSubCategory').on('click', createSubCategory);

    $('#createRootCategory').on('click', createRootCategory);

    $('#editCategory').on('click',editCategory);

    $('#deleteCategory').on('click', deleteCategory);

    /**
     * Delete category
     */
    function deleteCategory() {
        category = $("#categories").jstree(true).get_selected(true);
        if(category[0]) {
            confirm.enable();
            confirm.setContent("Do you really want to delete <font color='red'>" + category[0]['text'] + "</font> category, and it's <font color='red'>sub</font> categories?")
            deleteAjaxData.url = '/document/category/delete/'+category[0]['id'];
            confirm.open();
            category = null;
        }
        if(!category[0]){
            confirm.close();
            confirm.disable();
            wrongRequest('Select a category first!')
        }
    }

    function deleteCategoryAjaxRequest(){
        $.ajax(deleteAjaxData);
    }

    /**
     * Create category
     */

    function createRootCategory(){
        createCategoryForm('/document/category/create');
    }

    function createSubCategory(){
        data = $("#categories").jstree(true).get_selected(false);
        if(data) {
            createCategoryForm('/document/category/create/' + data);
        }
    }
    categoryAjaxData = {
        success: function (data) {
            if (data !== 'saved') {
                modal.setTitle('Create Category').setContent(data);
                if(!modal.isOpen) {
                    modal.open();
                    $('#createCategoryForm').submit(createCategoryPost);
                }
            }
            if(data === 'saved') {
                successAction('Categoty created');
            }
        }
    };
    function createCategoryForm(url) {
        categoryAjaxData.url = url;
        delete categoryAjaxData.data;
        categoryAjaxData.method = 'GET';
        $.ajax(categoryAjaxData);
    }

    function createCategoryPost(event) {
        categoryAjaxData.data = 'name='+$('#name').val();
        categoryAjaxData.method = 'POST';
        $.ajax(categoryAjaxData);
        event.preventDefault();
    }

    /**
     * Edit Category
     */

    var editCategoryAjaxData = {
        success: function(data) {
            if (data !== 'edited') {
                modal.setTitle('Edit Category').setContent(data);
                if (!modal.isOpen) {
                    modal.open();
                    $('#name').val(this.text);
                    delete this.text;
                    $('#editCategoryForm').submit(editCategoryPost);
                }
            }
            if (data === 'edited') {
                successAction('Category edited');
            }
        }
    };

    function editCategory(){
        category = $("#categories").jstree(true).get_selected(true);
        if(category[0]) {
            editCategoryAjaxData.url = '/document/category/edit/'+category[0]['id'];
            editCategoryAjaxData.method = 'GET';
            editCategoryAjaxData.text = category[0]['text'];
            $.ajax(editCategoryAjaxData);
            category = null;
        }
        if(category != null && !category[0]){
            wrongRequest('Select a category first!')
        }
    }

    function editCategoryPost(event){
        event.preventDefault();
        editCategoryAjaxData.data = 'name='+$('#name').val();
        editCategoryAjaxData.method = 'POST';
        $.ajax(editCategoryAjaxData);
    }

    function successAction(content){
        noticeData.content = content;
        noticeData.color = 'green';
        new jBox('Notice', noticeData).open();
        modal.setContent('');
        modal.close();
        loadCategories();
    }

    function wrongRequest(content){
        noticeData.content = content;
        noticeData.color = 'red';
        new jBox('Notice', noticeData).open();
    }
    /**
     * Load categories
     */
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