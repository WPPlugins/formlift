/**
 * Created by TBP6 on 8/12/2016.
 */

var INPUT_ERROR = "input_error";
var NAME_ERROR = "name_error";
var PHONE_ERROR = "phone_error";
var EMAIL_ERROR = "email_error";
var POSTAL_ERROR = "postal_error";

var ERROR_CLASSNAME_OFF = "web-form-error-off";
var ERROR_CLASSNAME = "web-form-error";
var ERROR_TEXT_CLASSNAME = "error-text";
var BUZZ_WORDS = ["BEST SEO", "SEO", "GOOD", "PORTFOLIO", "SATISFACTION", " ", "AFFORDABLE", "$", "FIRST PAGE", "WEBSITE TRAFFIC", "PROPOSAL", "QUOTE", "BILLING", "WANT MORE", "CLIENTS", "CUSTOMERS"];
var SPAM_MESSAGE = "This seams like spam to me... so I won't allow you to submit this form in it's current state. Sorry for any inconvenience.";

/**
 * Once someone submits the form, this function is called. It runs through all the input elements
 * in the form and passes them off through to validation functions. Returns false if data isn't valid, submits the form
 * otherwise.
 *
 * @param FormObj: An object that contains information about the FORM being Processed
 * @returns {boolean} : null
 */
function flpCheckForm( FormObj ) {
    var errors = [];
    var elements = flpGetFormElements(FormObj.form);
    for ( var i=0; i<elements.length;i++ ) {

        flpRemoveError(elements[i]);

        var result = flpCheckInput(FormObj, elements[i]);
        if (result != null){
            flpShowError(result.element);
            if (!flpIN(result,errors)){
                errors.push(result);
            }
        }
    }
    if (flpFormHasCaptcha(FormObj) && grecaptcha.getResponse() == "") {
        errors.push({element:'', message:'Please verify that you are not a robot.'});
    }
    if (errors.length > 0){
        flpCreateError(errors, FormObj);
    } else {
        var e = flpGetFormElements(FormObj.form);
        for ( i=0;i<e.length;i++ ){
            if (flpCheckIsRadio(e[i])){
                if (e[i].checked == true){
                    flpCookieFields(e[i].name, e[i].value);
                }
            } else {
                flpCookieFields(e[i].name, e[i].value);
            }
        }
        jQuery.when( flpPostSubmission(FormObj.id) ).done(
            function () {
                FormObj.form.submit();
            }
        );
    }
}

/**
 *
 * @param element: LABEL
 */
function flpShowError(element){
    var label = flpGetLabel(element);
    if (label != null){
        label.style.color = '#FF0000';
    } else {
        element.style.borderColor = '#FF0000';
    }
}

function flpRemoveError(element){
    var label = flpGetLabel(element);
    if (label != null){
        label.removeAttribute('style');
    } else {
        element.removeAttribute('style');
    }
}

/**
 * The algorithm requires html elements only to be present in the data being processed #text (are they really
 * elements?) have a nasty habit of showing up when you least expect it. This removes those elements.
 * @param list: a list of HTML elements
 * @returns {Array}: a list of HTML elements
 */
function flpCleanList(list){
    var temp_list = [];
    for ( var i=0; i<list.length;i++ ){
        if (  list[i].tagName ){
            temp_list.push( list[i] );
        }
    }
    return temp_list;
}

function flpGetMessage(element){
    if (flpGetLabel(element)){
        return flpGetLabel(element).innerHTML;
    } else if (flpCheckIsSelect(element)){
        return element.children[0].innerHTML;
    } else if (flpCheckIsTextInput(element)) {
        return element.placeholder;
    } else {
        return element.name;
    }

}
/**
 * creates an error message below the form when an error occurs. iterates through the errors found and displays them
 * in an unordered ist
 * @param message_list
 * @param FormObj
 */
function flpCreateError( message_list, FormObj ){
    /**
     * creates an error.
     */
    var area = FormObj.form.lastElementChild;
    area.className = ERROR_CLASSNAME;
    area.innerHTML = "<span class='" + ERROR_TEXT_CLASSNAME + "'>There appears to be an issue:</span>";
    var errors = document.createElement("UL");
    for (var i=0;i<message_list.length;i++){
        var message = message_list[i].message;
        if (typeof message_list[i].element == 'string'){
            var question = message_list[i].element;
        } else {
            question = flpGetMessage(message_list[i].element);
        }
        errors.innerHTML += "<li><span class='" + ERROR_TEXT_CLASSNAME + "'>" + question +" " + message + "</span></li>";
    }
    area.appendChild(errors);
}

/**
 * checks to see if an input field's text is valid according to data extrapolated from the field's id.
 * ie. if "NAME" is found in the field it will validate the data according to alphabetical characters only
 * "PHONE" means Numbers only etc...
 * returns an object with an error message and whether the selection is valid or not.
 * @param FormObj
 * @param input
 * @returns {{message: "message", element:*}}, true
 */
function flpCheckInput( FormObj, input ) {
    var error = {message:null, element:input, name:input.name};
    if (!flpCheckIsFilled(input, FormObj.requiredIds)){
        error.message = FormObj.errors[INPUT_ERROR];
    } if (!flpCheckRadio(input, FormObj.requiredIds)){
        error.message = FormObj.errors[INPUT_ERROR];
    } if (!flpIsChecked(input, FormObj.requiredIds)) {
        error.message = FormObj.errors[INPUT_ERROR];
    } if (!flpCheckEmail(input) && input.value != ""){
        error.message = FormObj.errors[EMAIL_ERROR];
    } if (!flpCheckIsPhoneValid(input) && input.value != ""){
        error.message = FormObj.errors[PHONE_ERROR];
    } if (!flpCheckIsValidName(input) && input.value != ""){
        error.message = FormObj.errors[NAME_ERROR];
    } if (!flpCheckIsSpam(input)) {
        error.message = SPAM_MESSAGE;
    }
    if (error.message != null){
        return error;
    } else {
        return null;
    }
}

function flpPostSubmission(id) {
    if (typeof flp_ajax_object !== 'undefined') {
        return jQuery.ajax({
            type: 'post',
            dataType: 'text',
            url: flp_ajax_object.ajax_url,
            data: {action: 'post_submission', id: id},
            success: function (e) {
                //console.log(e);
            }
        });//jQuery.ajax
    }
}

function flpPostImpression(id) {
    if (typeof flp_ajax_object !== 'undefined') {
        jQuery.ajax({
            type: 'post',
            dataType: 'text',
            url: flp_ajax_object.ajax_url,
            data: {action: 'post_impression', id: id},
            success: function (e) {
                //console.log(e);
            }
        });//jQuery.ajax
    }
}

function flpCookieFields(field, value) {
    if (/inf_field_/.test(field) || /inf_custom_/.test(field) || /inf_option_/.test(field)){
        document.cookie = String(field)+"="+String(value)+";path=/";
    }
}

/** get the cookied data.
 *
 * @param cname String
 * @return {string}
 */
function flpGetCookie(cname) {
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        c = c.trim();
        var ci = c.split('=');
        //var reg = new RegExp(/^'+ci[0]+'$/, 'i');
        // console.log(reg, cname);
        if (cname == ci[0] && ci[1]) {
            try {
                return ci[1].replace(/\+/g, ' ');
            } catch (e){
                console.log(e);
            }
        }
    }
    return "";
}

function flpCheckCookie(cname) {
    var value = flpGetCookie(cname);
    if (value != "" ) {
        return value;
    } else {
        return false;
    }
}


function flpIN(e, array){
    for (var i=0;i<array.length;i++ ){
        if (array[i].message == e.message && array[i].name == e.name){
            return true;
        }
    }
    return false;
}

function flpFormHasCaptcha(FormObj) {
    var divs = FormObj.form.getElementsByTagName('DIV');
    for (var i=0; i<divs.length; i++){
        if (divs[i].className == "flp-g-recaptcha"){
            return true;
        }
    }
    return false;
}


function flpGetCaptcha(FormObj) {
    var divs = FormObj.form.getElementsByTagName('DIV');
    for (var i=0; i<divs.length; i++){
        if (divs[i].className == "flp-g-recaptcha"){
            return divs[i];
        }
    }
    return false;
}