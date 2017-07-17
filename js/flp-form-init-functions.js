var FLP_FORM_LIST = [];
var FLP_REGISTERED_FORMS = 0;
var flpForm, flpForms, flpFormObj;

var flpOnloadCallback = function () {
  var captchas = document.getElementsByClassName('flp-g-recaptcha');
  for (var i=0;i<captchas.length;i++){
      grecaptcha.render(
          captchas[i].id,
          {
              'sitekey':flp_google_site_key.key
          }
      )
  }
};

function flpFillUrl(e)
{
    if (flpCheckIsSelect(e)){
        var val = flpCheckUrl(e.name);
        var options = jQuery('option[value="'+val+'"]');
        //console.log(options);
        if (options.length > 0){
            options[options.length-1].selected = "true";
        }
    } else if (flpCheckIsRadio(e) || flpCheckIsCheckbox(e)){
        if (e.value == flpCheckUrl(e.name)){
            e.checked = "true";
        }
    } else if (flpCheckIsTextInput(e) || flpCheckIsHidden(e)) {
        e.value = flpCheckUrl(e.name);
    }
}

function flpFillCookie(e){
    if (flpCheckIsSelect(e)){
        var val = flpCheckCookie(e.name);
        var options = jQuery('option[value="'+val+'"]');
        //console.log(options);
        if (options.length > 0){
            options[options.length-1].selected = "true";
        }
    } else if (flpCheckIsRadio(e) || flpCheckIsCheckbox(e)){
        if (e.value == flpCheckCookie(e.name)){
            e.checked = "true";
        }
    } else if (flpCheckIsTextInput(e) || flpCheckIsHidden(e)){
        e.value = flpCheckCookie(e.name);
    }

}

function flpCheckAutoFill(e, auto, replace){

    if (auto == 3){
        // do nothing
    } else if (auto == 1){
        if (flpCheckUrl(e.name) != false && replace == 1) {
            flpFillUrl(e);
        } else if (flpCheckUrl(e.name) != false && replace == 2){
            if (e.value == "" || e.value == "null" || e.value == null || flpCheckIsRadio(e) || flpCheckIsCheckbox(e)){
                flpFillUrl(e);
            }
        }
    } else if (auto == 2){
        if (flpCheckCookie(e.name) != false && replace == 1){
            flpFillCookie(e);
        } else if (flpCheckCookie(e.name) != false && replace == 2){
            if (e.value == "" || e.value == "null" || e.value == null || flpCheckIsRadio(e) || flpCheckIsCheckbox(e)){
                flpFillCookie(e);
            }
        }
    }
}

function flpAutoFillForm(FormObj) {
    var elements = flpGetFormElements(FormObj.form);
    for(var i=0;i<elements.length;i++){
        flpCheckAutoFill(elements[i], FormObj.autoFill, FormObj.overWrite);
    }
}

/**
 * If Infusionsoft sends some data to the site for example, it will automatically cookie said data for potential later
 * use.
 */
function flpProcessUser() {
    if (location.search) {
        var URI = location.search;
        var parameter = URI.slice(1);
        var var_dict = {};
        var parameters = parameter.split("&");
        for (var i = 0; i < parameters.length; i++) {
            var temp = parameters[i].split("=");
            var_dict[temp[0]] = decodeURIComponent(decodeURIComponent(temp[1]));
            if (typeof var_dict[temp[0]] == 'string' && var_dict[temp[0]] != "undefined"){
                flpCookieFields(temp[0],var_dict[temp[0]]);
            }
        }
    }
}

try {
    flpProcessUser();
} catch (e) {
    console.log(e);
}


/**
 * Check to see if a Variable string is in the URL params
 *
 * @param e , a string
 * @returns {*}
 */
function flpCheckUrl(e) {
    if (location.search) {
        var URI = location.search;
        var parameter = URI.slice(1);
        var parameters = parameter.split("&");
        for (var i = 0; i < parameters.length; i++) {
            var temp = parameters[i].split("=");
            if (temp[0] == e && typeof temp[1] == 'string' && temp[1] != "undefined") {
                return decodeURIComponent(decodeURIComponent(temp[1]));
            }
        }
        return false;
    } else {
        return false;
    }
}

function flpRegisterForm(FormObj) {
    var elements = flpGetFormElements(FormObj.form);
    var labels = FormObj.form.getElementsByTagName("LABEL");

    var captcha = flpGetCaptcha(FormObj);

    captcha.id += "-"+FLP_REGISTERED_FORMS;

    for (var i=0; i<elements.length;i++){
        elements[i].id += "-"+FLP_REGISTERED_FORMS;
    }
    for (i=0;i<labels.length;i++){
        labels[i].setAttribute('for', labels[i].getAttribute('for')+"-"+FLP_REGISTERED_FORMS);
    }
}

function flpGetFormElements(parent){
    var list_of_elements = [];
    function flpFindElements(root){
        if (root.tagName && (root.tagName == "INPUT" || root.tagName == "SELECT" || root.tagName == "TEXTAREA" || root.tagName == 'BUTTON')){
            list_of_elements.push(root)
        } else if (root.childNodes) {
            for (var i=0;i<root.childNodes.length;i++){
                flpFindElements(root.childNodes[i])
            }
        }
    }
    flpFindElements(parent);
    return list_of_elements;

}
/**
 * turns the submit button into a regular button that activates the validation algorithm rather than
 * submitting the form.
 * @param FormObj
 */
function flpTurnSubmitToButton( FormObj ) {
    /**
     *Changes ths submit button
     */
    var elements = flpGetFormElements(FormObj.form);
    var oldButton = elements[elements.length-1];
    oldButton.type = 'button';
    oldButton.onclick = function () {flpCheckForm( FormObj )};
}

function flpAddErrorArea(FormObj){
    var area = document.createElement("DIV");
    FormObj.form.appendChild(area);
}

function flpGetLabel(element){
    if (flpCheckIsTextInput(element) || flpCheckIsCheckbox(element) || flpCheckIsSelect(element)){
        var labels = jQuery("label[for='"+element.id+"']");
        return labels[0];
    } else if (flpCheckIsRadio(element)){
        var index = element.id.lastIndexOf('-');
        var registration = element.id.substr(index);
        labels = jQuery("label[for*='"+element.name+registration+"']");
        return labels[0];
    }
}

/**
 * recursive function to find all the labels in a form when "Turn Labels To Placeholders" is checked
 * the initial element is the Form itself
 * @param FormObj: object
 */
function flpLabels( FormObj ) {
    /**
     * element: Form element
     */

    var elements = flpGetFormElements(FormObj.form);

    if (FormObj.labelsOn == 'yes'){
        for (var i = 0;i<elements.length;i++){
            if (flpCheckIsTextInput(elements[i])){
                var label = flpGetLabel(elements[i]);
                elements[i].setAttribute('placeholder', label.innerHTML);
                label.parentNode.removeChild( label );
            }
        }
    }

    if (FormObj.selectOn == 'yes'){
        for ( i = 0;i<elements.length;i++){
            if ( flpCheckIsSelect(elements[i])){
                label = flpGetLabel(elements[i]);
                if (elements[i].options[0].value == "" || elements[i].options[0].value == null){
                    elements[i].options[0].innerHTML = label.innerHTML;
                } else {
                    var newOption = document.createElement('OPTION');
                    newOption.innerHTML = label.innerHTML;
                    newOption.value = "";
                    //newOption.selected = "selected";
                    elements[i].insertBefore(newOption, elements[i].children[0]);
                }
                label.parentNode.removeChild( label ) ;
            }
        }
    }
}

function flpAddDatePicker(){
    var elements = document.querySelectorAll('[data-type="date"]');

    for (var i=0;i<elements.length;i++){

        var e = elements[i];

        var isConfiguredChangeMonth = e.hasAttribute('data-changeMonth');
        var isConfiguredChangeYear  = e.hasAttribute('data-changeYear');
        var isConfiguredMinDate = e.hasAttribute('data-minDate');
        var isConfiguredMaxDate = e.hasAttribute('data-maxDate');
        var isConfiguredYearRange = e.hasAttribute('data-yearRange');

        var maxDate, minDate, yearRange, changeYear, changeMonth;

        if (isConfiguredChangeMonth)
            changeMonth = e.getAttribute('data-changeMonth');
        if (isConfiguredChangeYear)
            changeYear = e.getAttribute('data-changeYear');
        if (isConfiguredYearRange)
            yearRange = e.getAttribute('data-yearRange');
        if (isConfiguredMinDate)
            minDate = e.getAttribute('data-minDate');
        if (isConfiguredMaxDate)
            maxDate = e.getAttribute('data-maxDate');

        if (isConfiguredYearRange){
            jQuery(elements[i]).datepicker({changeMonth: changeMonth, changeYear: changeYear, yearRange:yearRange});
        } else if (isConfiguredMaxDate){
            jQuery(elements[i]).datepicker({changeMonth: changeMonth, changeYear: changeYear, minDate:minDate, maxDate:maxDate});
        }
    }
}

function flpInit(FormObj) {
    flpCreateCaptcha(FormObj);
    flpGetTimezone(FormObj);
    flpRegisterForm(FormObj);
    FLP_REGISTERED_FORMS += 1;
    flpTurnSubmitToButton(FormObj);
    flpAddErrorArea(FormObj);
    flpLabels(FormObj);
    try {
        flpAutoFillForm(FormObj);
    } catch (e){
        console.log(e);
    }
    if (FormObj.tracking == 'page_load'){
        flpPostImpression(FormObj.id);
    } else if (FormObj.tracking == 'mouse_over'){
        FormObj.form.addEventListener('mouseenter', function(){flpPostImpression(FormObj.id)});
    }
}

function flpCreateCaptcha(FormObj){

    if (FormObj.captcha == '')
        return;

    var container = document.createElement("DIV");
    container.className = 'infusion-field';

    var captcha = document.createElement("DIV");
    captcha.id = 'flp-captcha';
    captcha.className = 'flp-g-recaptcha';
    captcha.setAttribute('data-sitekey', flp_google_site_key.key);

    container.appendChild(captcha);

    var elements = flpGetFormElements(FormObj.form);
    var oldButton = elements[elements.length-1];
    var ref = oldButton.parentNode;

    FormObj.form.insertBefore(container, ref);
}

function flpGetTimezone(FormObj) {
    var timezone = jstz.determine();
    var inputNode = document.createElement('INPUT');
    inputNode.setAttribute('type', 'hidden');
    inputNode.setAttribute('name', 'timeZone');
    inputNode.setAttribute('value', timezone.name());
    FormObj.form.insertBefore(inputNode, FormObj.form.childNodes[0]);
}

//init
jQuery(document).ready(function () {
    for (var v = 0;v<FLP_FORM_LIST.length;v++){
        flpInit(FLP_FORM_LIST[v]);
    }
    flpAddDatePicker();
});