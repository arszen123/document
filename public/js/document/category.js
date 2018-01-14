$(function () {
    //TODO clean the mess!!! LAST
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

    $('#files').jstree({ 'core' : {
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

    $('#changePermission').on('click',changePermission);

    $('#deleteCategory').on('click', deleteCategory);

    $('#categories').on('select_node.jstree',loadFiles);

    $('#uploadFile').on('click',uploadFileAjaxRequest);


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
        success: function(data,status,xhr) {
            var ct = xhr.getResponseHeader("content-type") || "";
            if (ct.indexOf('json') > -1) {
                if (data.status === 'success') {
                    successAction(data.message);
                }
            }else{
                modal.setTitle('Create Category').setContent(data);
                if (!modal.isOpen) {
                    modal.open();
                    $('#createCategoryForm').submit(createCategoryPost);
                }
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
        success: function(data,status,xhr) {
            var ct = xhr.getResponseHeader("content-type") || "";
            if (ct.indexOf('json') > -1) {
                if (data.status === 'success') {
                    successAction(data.message);
                }
            }else{
                modal.setTitle('Edit Category').setContent(data);
                if (!modal.isOpen) {
                    modal.open();
                    $('#name').val(this.text);
                    delete this.text;
                    $('#editCategoryForm').submit(editCategoryPost);
                }
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

    /**
     * Change permission
     */

    var changePermissionAjaxData = {
        success: function(data,status,xhr) {
            var ct = xhr.getResponseHeader("content-type") || "";
            if (ct.indexOf('json') > -1) {
                if (data.status === 'success') {
                    successAction(data.message);
                }
            }else{
                modal.setTitle('Edit Category').setContent(data);
                if (!modal.isOpen) {
                    modal.open();
                    $('#editPermissionForm').submit(changePermissionPost);
                }
            }
        }
    };

    function changePermission(){
        category = $("#categories").jstree(true).get_selected(true);
        if(category[0]) {
            changePermissionAjaxData.url = '/document/category/permission/'+category[0]['id'];
            changePermissionAjaxData.method = 'GET';
            $.ajax(changePermissionAjaxData);
            category = null;
        }
        if(category != null && !category[0]){
            wrongRequest('Select a category first!')
        }
    }

    function changePermissionPost(event){
        event.preventDefault();
        changePermissionAjaxData.data = $( this ).serialize();
        changePermissionAjaxData.method = 'POST';
        $.ajax(changePermissionAjaxData);
    }

    /**
     * Helpers
     */
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
                $("#categories").jstree(true).settings.core.data = data.data;
                $("#categories").jstree(true).refresh();
                noticeData.content = data.message;
                noticeData.color = 'green';
                new jBox('Notice', noticeData).open();
            }
        });
    }

    /**
     * Files
     */

    function loadFiles(){
        id = $("#categories").jstree(true).get_selected(true)[0]['id'];
        $.ajax({
            method:'GET',
            url:'/document/file/list/'+id,
            success: function(data,status,xhr) {
                var ct = xhr.getResponseHeader("content-type") || "";
                if (ct.indexOf('json') > -1) {
                    if(data.status == 'success') {
                        $("#files").jstree(true).settings.core.data = data.data;
                        $("#files").jstree(true).refresh();
                        noticeData.content = data.message;
                        noticeData.color = 'green';
                        new jBox('Notice', noticeData).open();
                    }
                    if(data.status == 'failed'){
                        wrongRequest(data.message);
                        $("#files").jstree(true).settings.core.data = [];
                        $("#files").jstree(true).refresh();
                    }
                }

            }
        });
    }

    /**
     * Download file
     */
    $('#files').on('select_node.jstree',function(event,node){
        url = node.node.a_attr.href;
        $.ajax({
            url:url,
            type: 'POST',
            success: function(data,status,xhr) {
                var ct = xhr.getResponseHeader("content-type") || "";
                if (ct.indexOf('json') > -1) {
                    wrongRequest(data.message)
                }else {
                    window.location = url;
                }
                $('#files').jstree(true).deselect_all(true);
            }
        });
    });

    /**
     * Upload file
     */
    var uploadFileAjaxData = {
        success: function(data,status,xhr) {
            var ct = xhr.getResponseHeader("content-type") || "";
            if (ct.indexOf('html') > -1) {
                modal.setTitle('Upload File').setContent(data);
                if (!modal.isOpen) {
                    modal.open();
                    $('#uploadFileForm').submit(uploadFileAjaxPost);
                }
            }
            console.log(ct);
            if (ct.indexOf('json') > -1) {
                console.log(data);
                noticeData.content = data.message;
                noticeData.color = 'green';
                new jBox('Notice', noticeData).open();
                loadFiles();
            }
        }
    };

    function uploadFileAjaxRequest(){
        category = $("#categories").jstree(true).get_selected(true);
        if(category[0]) {
            uploadFileAjaxData.url = '/document/file/upload/'+category[0]['id'];
            uploadFileAjaxData.method = 'GET';
            delete uploadFileAjaxData.data;
            delete uploadFileAjaxData.enctype;
            delete uploadFileAjaxData.cache;
            delete uploadFileAjaxData.contentType;
            delete uploadFileAjaxData.processData;
            $.ajax(uploadFileAjaxData);
            category = null;
        }
        if(category != null && !category[0]){
            wrongRequest('Select a category first!')
        }
    }

    function uploadFileAjaxPost(event){
        event.preventDefault();
        form_data = new FormData();
        form_data.append('fileToUpload',$('#fileToUpload').prop('files')[0]);
        form_data.append('fileName',$('#fileName').val());
        uploadFileAjaxData.data = new FormData(this);
        uploadFileAjaxData.method = 'POST';
        uploadFileAjaxData.enctype = 'multipart/form-data';
        uploadFileAjaxData.cache = false;
        uploadFileAjaxData.contentType = false;
        uploadFileAjaxData.processData = false;
        $.ajax(uploadFileAjaxData);
    }
});