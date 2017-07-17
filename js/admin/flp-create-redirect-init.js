var FLP_RADIO_STANDARD_CONDITIONS = [
    {label:"Is Equal To",value:"Is Equal To"},
    {label:"Is NOT Equal To",value:"Is NOT Equal To"}
    ];
var FLP_OTHER_STANDARD_CONDITIONS = [
    {label:"Is Equal To",value:"Is Equal To"},
    {label:"Is NOT Equal To",value:"Is NOT Equal To"},
    {label:"Contains",value:"Contains"},
    {label:"Does NOT Contain",value:"Does NOT Contain"},
    {label:"Starts With", value:"Starts With"},
    {label:"Ends With", value:"Ends With"}
    ];

function flpArrayHas(a, array){
    for (var i=0;i<array.length;i++ ){
        if (array[i] == a){
            return true;
        }
    }
    return false;
}

function flpInitRedirectArea(){
    for (var i=0;i<REDIRECT_OPTIONS.length;i++){
        var option = flpCreateRow(REDIRECT_OPTIONS[i]);

    }
}

function flpCreateRow(option_dict) {

    var row = document.createElement("LI");
    row.className = 'flp-redirect-row';
    var field_names = flpGetFields();

    if (option_dict == false){
        option_dict = {
            field: field_names[0].value,
            value: null,
            condition: "Is Equal To",
            url: ""
        }
    }

    if (document.getElementsByName(option_dict.field).length > 0){
        var field_select = flpCreateDropDownField(field_names, option_dict.field, "flp_fields[]", true);
    } else {
        field_select = flpCreateTextField(option_dict.field, "flp_fields[]");
    }
    if (flpCheckIsRadio(document.getElementsByName(option_dict.field)[0]) || flpCheckIsSelect(document.getElementsByName(option_dict.field))){
        var conditional = flpCreateDropDownField(FLP_RADIO_STANDARD_CONDITIONS, option_dict.condition, "flp_conditions[]");
    } else {
        conditional = flpCreateDropDownField(FLP_OTHER_STANDARD_CONDITIONS, option_dict.condition, "flp_conditions[]");
    }
    if (flpCheckIsRadio(document.getElementsByName(option_dict.field)[0]) || flpCheckIsSelect(document.getElementsByName(option_dict.field))){
        var possible_values = flpGetOptions(option_dict.field);
        var values = flpCreateDropDownField(possible_values,option_dict.value, "flp_values[]");
    } else {
        values = flpCreateTextField(option_dict.value, "flp_values[]");
    }
    var url = flpCreateTextField(option_dict.url, "flp_urls[]");
    var button = flpCreateDeleteButton();
    row.appendChild(flpWrapCell("IF"));
    row.appendChild(field_select);
    row.appendChild(conditional);
    row.appendChild(values);
    row.appendChild(flpWrapCell("Then Go To"));
    row.appendChild(url);
    row.appendChild(button);

    var table = document.getElementById("formlift-redirect-mb-table");
    table.appendChild(row);

}

function flpGetFields() {
    var form = flpGetForm();
    var elements = flpGetFormElements(form);
    var field_names = []; // [{label:"",value:""}]
    var seen_names = [];
    for (var i=0;i<elements.length;i++){
        if (!flpArrayHas(elements[i].name, seen_names)){
            var label = flpGetMessage(elements[i]);
            field_names.push({label:label, value:elements[i].name});
            seen_names.push(elements[i].name);
        }
    }
    return field_names;
}

function flpGetForm() {
    var forms = document.getElementsByClassName('infusion-form-'+FORM_ID);
    return forms[0];
}

function flpCreateDropDownField(values, selected, name, isPrime){
    var select = document.createElement("SELECT");
    select.name = name;
    select.className = 'flp-max-width';
    //this happens when a dropdown is created that needs added functionality to update the row with new options
    if (isPrime == true){
        select.onchange = function () {
            flpOptionsUpdate(select);
        }
    }
    for (var i=0; i<values.length;i++){
        var option = document.createElement("OPTION");
        option.innerHTML = values[i].label;
        option.value = values[i].value;

        if (values[i].value == selected ){
            option.selected = "true";
        }
        select.appendChild(option);
    }

    return flpWrapCell(select);
}

function flpCreateTextField(value, name) {
    var input = document.createElement("INPUT");
    if (name == "flp_urls[]"){
        input.placeholder = "http://example.com/";
    }
    input.type = "text";
    input.name = name;
    input.value = value;

    return flpWrapCell(input);
}

function flpGetOptions(name){
    var element = document.getElementsByName(name);
    var options = []; // [{label:"", value:""}]
    if (element.length > 1){
        for (var i=0;i<element.length;i++){
            var label = flpGetRadioLabel(element[i]).innerHTML;
            options.push({label:label, value:element[i].value});
        }
    } else {
        var elements = element[0].children;
        for (i=0;i<elements.length;i++){
            if (elements[i].value != ""){
                options.push({label:elements[i].innerHTML, value:elements[i].value});
            }
        }
    }
    return options;
}

function flpGetRadioLabel(e){
    var labels = jQuery("label[for='"+e.id+"']");
    return labels[0];
}

function flpWrapCell(html){
    var cell = document.createElement("DIV");
    cell.className = 'flp-redirect-cell';
    if (typeof html === 'object'){
        cell.appendChild(html);
    } else {
        cell.innerHTML = html;
        cell.className = "text-cell";
    }
    return cell;
}

function flpOptionsUpdate(element){
    var option_1 = flpGetAssociatedOption(element, 2);
    var option_2 = flpGetAssociatedOption(element, 3);

    if (flpCheckIsRadio(document.getElementsByName(element.value)[0]) || flpCheckIsSelect(document.getElementsByName(element.value)[0])){
        var conditions = flpCreateDropDownField(FLP_RADIO_STANDARD_CONDITIONS, FLP_RADIO_STANDARD_CONDITIONS[0], "flp_conditions[]");
        var possible_values = flpGetOptions(element.value);
        var values = flpCreateDropDownField(possible_values, possible_values[0], "flp_values[]");
    } else if (flpCheckIsCheckbox(document.getElementsByName(element.value)[0])) {
        conditions = flpCreateDropDownField(FLP_RADIO_STANDARD_CONDITIONS, FLP_RADIO_STANDARD_CONDITIONS[0], "flp_conditions[]");
        values = flpCreateTextField(document.getElementsByName(element.value)[0].value, "flp_values[]");
    } else {
        conditions = flpCreateDropDownField(FLP_OTHER_STANDARD_CONDITIONS, FLP_OTHER_STANDARD_CONDITIONS[0], "flp_conditions[]");
        values = flpCreateTextField('', "flp_values[]");
    }
    option_1.parentNode.replaceChild(conditions, option_1);
    option_2.parentNode.replaceChild(values, option_2);
}

function flpGetAssociatedOption(element, index) {
    var cell = element.parentNode;
    var row = cell.parentNode;
    return row.children[index];
}

/**
 * deletes the row where the given button is contained
 * @param element, a button
 */
function flpDeleteRow(element){
    var row = element.parentNode.parentNode;
    row.parentNode.removeChild(row);
}

function flpDoesFormHaveNecessaryFields() {
    var form = flpGetForm();
    var elements = flpGetFormElements(form);
    for (var i=0;i<elements.length;i++){
        if (elements[i].tagName == "SELECT" || elements[i].type == "radio"){
            return true
        }
    }
    return false;
}

function flpCreateDeleteButton(){
    var button = document.createElement("BUTTON");
    button.type = "button";
    button.innerHTML = "Delete";
    button.className = "flp-button flp-delete-button";
    button.onclick = function (){
        flpDeleteRow(button);
    };
    return flpWrapCell(button);
}
