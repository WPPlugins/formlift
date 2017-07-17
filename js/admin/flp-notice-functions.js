/**
 * Created by Adrian on 2017-06-01.
 */

function flp_dismiss_notice(e)
{
    if (typeof flp_ajax_object !== 'undefined') {
        jQuery.ajax({
            type: 'post',
            dataType: 'text',
            url: ajaxurl,
            data: {action: 'flp_do_dismiss', name: e.name, time: e.getAttribute('data-time')},
            success: function (m) {
                if (m == 'Success'){
                    document.getElementById(e.name).style.display = 'none';
                } else {
                    console.log(m);
                }
            }
        });//jQuery.ajax
    }
}