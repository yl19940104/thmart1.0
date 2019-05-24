/** 
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved. 
 * For licensing, see LICENSE.md or http://ckeditor.com/license 
 */  
  
CKEDITOR.editorConfig = function( config ) {  
    // Define changes to default configuration here.  
    // For complete reference see:  
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config  
  
    // The toolbar groups arrangement, optimized for two toolbar rows.  
    config.toolbarGroups = [  
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },  
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },  
        { name: 'links' },  
        { name: 'insert' },  
        { name: 'forms' },  
        { name: 'tools' },  
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },  
        { name: 'others' },  
        '/',  
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },  
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },  
        { name: 'styles' },  
        { name: 'colors' },  
        { name: 'about' }  
    ];  
  
    // Remove some buttons provided by the standard plugins, which are  
    // not needed in the Standard(s) toolbar.  
    config.removeButtons = 'Underline,Subscript,Superscript';  
  
    // Set the most common block elements.  
    config.format_tags = 'p;h1;h2;h3;pre';  
  
    // Simplify the dialog windows.  
    config.removeDialogTabs = 'image:advanced;link:advanced';  
    config.extraAllowedContent = 'iframe[*]';  
    /*config.filebrowserImageBrowseUrl = 'http://proj7.thatsmags.com/Public/ckfinder/ckfinder.html?Type=Images';*/
    config.filebrowserImageBrowseUrl = 'http://api.com/Public/plug/ckfinder/ckfinder.html?Type=Images';  
    /*config.filebrowserFlashBrowseUrl = 'http://proj7.thatsmags.com/Public/ckfinder/ckfinder.html?Type=Flash';*/
    config.filebrowserFlashBrowseUrl = 'http://api.com/Public/plug/ckfinder/ckfinder.html?Type=Flash';
    config.filebrowserUploadUrl = '../ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
    config.filebrowserImageUploadUrl = '../ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
    config.filebrowserFlashUploadUrl = '../ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';  
    config.filebrowserWindowWidth = '800';  //“浏览服务器”弹出框的size设置  
    config.filebrowserWindowHeight = '500';  
    config.allowedContent = true;  
    config.height = 600;
};  