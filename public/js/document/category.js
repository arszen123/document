$(function () {
    var categoriesId = '#categories';
    var filesId = '#files';
    var categoryNameHelperId = '#categoryName';
    var helperId = '#helper';
    var fileInfoId = {visibleName:'#visibleName',filename:'#fileName',version:'#versionNumber',
        uploaded:'#uploadedTime',user:'#uploadedUser'};
    var modal = new jBox('Modal');
    var noticeData = {delayOpen: 1, delayClose: 1, attributes: {y: 'bottom'}};
    var confirm = new jBox('Confirm', {
        content: 'Do you really want to do this?',
        cancelButton: 'Nope',
        confirmButton: 'Sure do!',
        confirm: function () {
            $.ajax(deleteAjaxData);
        }
    });
    var deleteAjaxData = {
        method:'GET',
        success:function(data,status,xhr){
            if(isJson(xhr)) {
                if(isSuccess(data.status)){
                    loadCategories();
                    showSuccessMessage(data.message);
                }
                ifFailShowMessage(data);
            }
        }
    };
    /**
     * Init jsTree
     */
    $(categoriesId).jstree({ 'core' : {
            "multiple":false
        },"types" : {
            "root" : {
                "icon" : "glyphicon glyphicon-tree-deciduous"
            },
            "default" : {
                "icon" : "glyphicon glyphicon-folder-open"
            }
        },
        "plugins" : [
            "types", "wholerow"
        ]
    });

    $(filesId).jstree({ 'core' : {
            "multiple":false
        },
        "types" : {
            "default" : {
                "icon" : "glyphicon glyphicon-file"
            }
        },
        "plugins" : [
            "types", "wholerow"
        ]
    });

    loadCategories();

    $('#createSubCategory').on('click', createSubCategory);

    $('#createRootCategory').on('click', createRootCategory);

    $('#editCategory').on('click',editCategory);

    $('#changePermission').on('click',changePermission);

    $('#deleteCategory').on('click', deleteCategory);

    $(categoriesId).on('select_node.jstree',loadFiles);

    $('#uploadFile').on('click',uploadFileAjaxRequest);

    var lastDetailesUrl = null;
    $(filesId).on('hover_node.jstree',function (event,node) {
        url = node.node.a_attr.href;
        url = url.replace('download','detailes');
        if(lastDetailesUrl!== url){
            lastDetailesUrl = url;
            $.ajax({
                url:url,
                method:'GET',
                success: function(data,status,xhr) {
                    if (!isJson(xhr)) {
                        showFailMessage('Something went wrong!');
                        return;
                    }
                    if(isSuccess(data.status)){
                        setUpFileDetailes(data.data);
                    }
                }
            });
        }
    });

    function setUpFileDetailes(data){
        $(fileInfoId.visibleName).html(data.visibleName);
        $(fileInfoId.filename).html(data.filename);
        $(fileInfoId.version).html(data.version);
        $(fileInfoId.uploaded).html(data.uploaded);
        $(fileInfoId.user).html(data.user);
    }

    /**
     * Delete category
     */
    function deleteCategory() {
        category = $(categoriesId).jstree(true).get_selected(true);
        if(category[0]) {
            confirm.enable();
            confirm.setContent("Do you really want to delete <font color='red'>" + category[0]['text'] + "</font> category, and it's <font color='red'>sub</font> categories?")
            deleteAjaxData.url = '/document/category/delete/'+category[0]['id'];
            confirm.open();
            category = null;
        }
        if(category != null && !category[0]){
            confirm.close();
            confirm.disable();
        }
    }

    var ajaxRequestData = {
        success: function(data,status,xhr) {
            if (isJson(xhr)) {
                if(isSuccess(data.status))
                    loadCategories();
                showMessage(data);
                console.log(data);
                modal.close();
            }else{
                modal.setContent(data);
                if (!modal.isOpen) {
                    modal.open();
                    $('form').submit(makePostRequest);
                }
            }
        }
    };

    function makeGetRequest(url) {
        ajaxRequestData.url = url;
        delete ajaxRequestData.data;
        ajaxRequestData.method = 'GET';
        $.ajax(ajaxRequestData);
    }

    function makePostRequest(event) {
        ajaxRequestData.data = $(this).serialize();
        ajaxRequestData.method = 'POST';
        $.ajax(ajaxRequestData);
        event.preventDefault();
    }

    function createRootCategory(){
        modal.setTitle('Create Root category');
        makeGetRequest('/document/category/create');
    }

    function createSubCategory(){
            categoryShouldSelected('/document/category/create','Create sub category');
    }

    function editCategory(){
        categoryShouldSelected('/document/category/edit','Edit category');
    }

    function changePermission(){
        categoryShouldSelected('/document/category/permission','Edit category');
    }

    function categoryShouldSelected(url,title){
        data = $(categoriesId).jstree(true).get_selected(false);
        if(data>0) {
            modal.setTitle(title);
            makeGetRequest(url +'/'+data);
        }else{
            showFailMessage('Select a category first!')
        }
    }

    function loadCategories(){
        load({url:'/document/category/list',jsTreeId:categoriesId});
    }

    function loadFiles(){
        category = $(categoriesId).jstree(true).get_selected(true)[0];
        $(categoryNameHelperId).html('Category: <div class="categoryName">'+category['text']+'</div>');
        load({url:'/document/file/list/'+category['id'],jsTreeId:filesId});
    }

    function load(settings){
        $.ajax({
            method:'GET',
            url: settings.url,
            success: function(data,status,xhr) {
                if (isJson(xhr)) {
                    jstreeData = JSON.stringify($(settings.jsTreeId).jstree(true).settings.core.data);
                    if(isSuccess(data.status) && data.data && JSON.stringify(data.data) !== jstreeData) {
                        $(settings.jsTreeId).jstree(true).settings.core.data = data.data;
                        $(settings.jsTreeId).jstree(true).refresh();
                        $(helperId).empty();
                    }
                    if(isFailed(data.status)){
                        if(jstreeData !== '[]') {
                            $(settings.jsTreeId).jstree(true).settings.core.data = [];
                            $(settings.jsTreeId).jstree(true).refresh();
                            $(helperId).html('No files in this category!');
                        }
                    }
                }

            }
        });
    }

    /**
     * Download file
     */
    $(filesId).on('select_node.jstree',function(event,node){
        url = node.node.a_attr.href;
        $.ajax({
            url:url,
            type: 'POST',
            success: function(data,status,xhr) {
                if (isJson(xhr)) {
                    showMessage(data);
                }else {
                    window.location = url;
                }
                $(filesId).jstree(true).deselect_all(true);
            }
        });
    });

    /**
     * Upload file
     */
    var uploadFileAjaxData = {
        success: function(data,status,xhr) {
            var jsonCt = isJson(xhr);
            if (!jsonCt) {
                modal.setTitle('Upload File').setContent(data);
                if (!modal.isOpen) {
                    modal.open();
                    $('form').submit(uploadFileAjaxPost);
                }
            }
            if (jsonCt) {
                if(isSuccess(data.status))
                    loadFiles();
                showMessage(data);
                modal.close();
            }
        }
    };

    function uploadFileAjaxRequest(){
        category = $(categoriesId).jstree(true).get_selected(true);
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
            showFailMessage('Select a category first!');
        }
    }

    function uploadFileAjaxPost(event){
        event.preventDefault();
        uploadFileAjaxData.data = new FormData(this);
        uploadFileAjaxData.method = 'POST';
        uploadFileAjaxData.enctype = 'multipart/form-data';
        uploadFileAjaxData.cache = false;
        uploadFileAjaxData.contentType = false;
        uploadFileAjaxData.processData = false;
        $.ajax(uploadFileAjaxData);
    }


    /**
     * HELPERS
     */
    function isJson(xhr) {
        ct = xhr.getResponseHeader("content-type") || "";
        return (ct.indexOf('json') > -1);
    }

    function showMessage(data){
        ifSuccessShowMessage(data);
        ifFailShowMessage(data);
    }
    function ifSuccessShowMessage(data){
        if(isSuccess(data.status)){
            showSuccessMessage(data.message)
        }
    }
    function isSuccess(data){
        return data === 'success';
    }

    function showSuccessMessage(content){
        noticeData.content = content;
        noticeData.color = 'green';
        new jBox('Notice', noticeData).open();
    }

    function ifFailShowMessage(data){
        if(isFailed(data.status)){
            showFailMessage(data.message)
        }
    }
    function isFailed(data){
        return data === 'failed';
    }

    function showFailMessage(content){
        noticeData.content = content;
        noticeData.color = 'red';
        new jBox('Notice', noticeData).open();
    }
});