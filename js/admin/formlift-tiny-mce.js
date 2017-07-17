/**
 * Created by Adrian on 2017-06-04.
 */
(function() {
    tinymce.PluginManager.add( 'formlift_form', function( editor, url ) {
        editor.addButton( 'formlift_form', {
            title:'Insert Infusionsoft Form',
            cmd: 'formlift_form',
            image: formlift_icon.url
        });
        editor.addCommand('formlift_form', function () {



        })
    });
})();

function flpGetForms()
{
    jQuery.ajax({
        url:ajaxurl,
        type:"POST",
        success:function(form_list){
            return form_list;
        },
        dataType:"json"
    });
}