/**
 * Created by adria on 2016-10-20.
 */
jQuery(document).ready(function($){
    $('.flp-color').wpColorPicker();
});

function copy_shortcode(id){
    var short_code_input = jQuery(id);
    short_code_input.select();

    try {
        var successful = document.execCommand('copy');
    } catch (err) {
        console.log('unable to copy');
    }
}

function resetStats(post_id){
    jQuery.ajax({
        type : "post",
        dataType : "text",
        url : ajaxurl,
        data : {action: "reset_track_stats", id:post_id},
        success: function(response){
            if ("success" == response){
                alert(response);
                location.reload();
            } else {
                alert("Something went wrong: "+response);
            }

        }
    });//jQuery.ajax
}

function resetAllStats(){
    jQuery.ajax({
        type : "post",
        dataType : "text",
        url : ajaxurl,
        data : {action: "reset_all_track_stats"},
        success: function(response){
            if ('success' != response){
                alert("Something went wrong: "+response);
            } else {
                alert(response);
            }
        }
    });//jQuery.ajax
}

function flpGetThankYouPageUri(id, post_id){
    jQuery.ajax({
        type : "post",
        dataType : "text",
        url : ajaxurl,
        data : {action: "flp_get_permalink", ID:id , post_id: post_id},
        success: function(response){
            document.getElementById('flp-redirect-uri').value = response;
        }
    });//jQuery.ajax
}

function flpOpenSection(evt, animName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("flp-section");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("flp-tab");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" flp-active", "");
    }
    document.getElementById("_"+animName).style.display = "block";
    document.getElementById("flp_active_tab").value = animName;
    evt.currentTarget.className += " flp-active";
    flpScrollTo("#flp-custom-settings");
}

function flpGetPreviewForm(id) {
    jQuery.ajax({
        type : "post",
        dataType : "text",
        url : ajaxurl,
        data : {action: "flp_get_form_via_ajax", ID:id },
        success: function(response){
            //alert(response);
            document.getElementById('flp-preview-box').innerHTML = response;
        }
    });//jQuery.ajax
}

function flpScrollTo(hash){
    jQuery('html, body').animate({
        scrollTop: jQuery(hash).offset().top - 60}, 200);
}