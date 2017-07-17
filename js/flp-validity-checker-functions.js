/**
 * Created by Adrian on 2016-12-28.
 */

function flpCheckEmail(input) {
    if (/Email/.test(input.name)){
        return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(input.value);
    } else {
        return true;
    }
}

function flpCheckIsFilled(input, requiredIds){
    if ((input.type == 'text' || input.tagName == 'TEXTAREA' || input.tagName == 'SELECT') && input.name in requiredIds){
        return !(input.value == null || input.value == "");
    } else {
        //pass
        return true;
    }
}

function flpCheckIsSpam(input){
    if (input.type == 'text' || input.tagName == 'TEXTAREA'){
        var count = 0;
        for (var i = 0;i<BUZZ_WORDS.length;i++){
            var regex = new RegExp(BUZZ_WORDS[i],"i");
            if(regex.test(input.value)){
                count+=1;
            }
        }
        return (count <= 3);
    } else {
        //pass
        return true;
    }
}

function flpCheckIsValidName(input) {
    if (/FirstName/.test(input.name) || /LastName/.test(input.name) ){
        return !/[`~!@#$%^&*()_=+\[\]{}\\|':"<>,.?\/]/.test(input.value);
    } else {
        //pass
        return true;
    }
}

function flpCheckIsPhoneValid(input){
    if (/Phone/.test(input.name) ){
        return /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im.test(input.value);
    } else {
        //pass
        return true;
    }
}

function flpIsChecked(input, requiredIds) {
    if (input.type == "checkbox" && input.name in requiredIds){
        return input.checked;
    } else {
        //pass
        return true;
    }
}

function flpCheckRadio( input, requiredIds ) {
    if (input.type == 'radio' && input.name in requiredIds){
        var options = input.parentNode.parentNode.childNodes;
        options = flpCleanList( options ) ;
        var result;
        for ( var i = 0; i < options.length; i++ ) {
            var options_new = ( options[i].childNodes ) ;
            for ( var k=0; k < options_new.length;k++ ) {
                if ( options_new[k].tagName && options_new[k].tagName == "INPUT" && options_new[k].type == "radio" && options_new[k].checked ) {
                    return true;
                }
            }
        }
        return false;
    } else {
        //pass
        return true;
    }
}

function flpCheckIsTextInput(element){
    return (element.tagName == "INPUT" || element.tagName == "TEXTAREA") &&
        (element.type != 'hidden' && element.type != 'radio' && element.type != 'checkbox');
}

function flpCheckIsHidden(element){
    return (element.tagName == "INPUT" && element.type == "hidden");
}

function flpCheckIsCheckbox(element){
    return (element.tagName == "INPUT" && element.type == 'checkbox');
}

function flpCheckIsRadio(element){
    return (element.tagName == "INPUT" && element.type == 'radio');
}

function flpCheckIsSelect(element) {
    return element.tagName == "SELECT";
}