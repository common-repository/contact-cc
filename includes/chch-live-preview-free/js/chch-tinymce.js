jQuery(document).ready(function($) {

  tinymce.PluginManager.add('chch_lp_keyup_event', function(editor, url) {
    editor.on('keyup', function(e) { 
      editorId = editor.id;
      get_ed_content = tinymce.activeEditor.getContent();
      chchLpCustomizeEditor(editorId, get_ed_content);
    });

    editor.on('change', function(e) {
      editorId = editor.id; 
      get_ed_content = tinymce.activeEditor.getContent();
      chchLpCustomizeEditor(editorId, get_ed_content);
    });
  });

  $('#content').on('keyup, change', function(e) {
    var editorId = tinymce.activeEditor.id;
    var get_ed_content = tinymce.activeEditor.getContent();
    chchLpCustomizeEditor(editorId, get_ed_content);
  }); 

  function chchLpCustomizeEditor(id, content) {
    var text = content;
    htmlFomat = $('#' + id + '_lp-editor').attr('data-html');
    
    if(htmlFomat == 'no') {
      text = text.replace(/<p>/g, '');
      text = text.replace(/<\/p>/g, '<br>'); 
    }
    if (typeof text === "undefined") {
      text = '';
    }
    $('#' + id + '_lp-editor').text(text).trigger('change'); 
  }
});