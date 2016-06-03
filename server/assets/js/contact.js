/**
 * Created by dev2 on 01/06/16.
 */

var translations,
    lang;

// load jQuery if it isn't already
if(!window.jQuery){
    var jq = document.createElement('script'); jq.type = 'text/javascript';
    q.src = 'server/assets/js/jquery.min.js';
    document.getElementsByTagName('head')[0].appendChild(jq);
}

// get translations for any text stuff in the script
jQuery.getJSON( 'server/translate/translations.json', function(json) {
    translations = json;
    lang = json.default_lang;
    window.console.log('translations:',json);
});



/**
 * Parse in variables to the translation string
 * @param  {string} str  string to insert vars into
 * @param  {array}  vars array of variables to insert
 * @return {string}      string with vars inserted
 */
function parseMessage (str, vars) {
    var i, pattern, re, n = vars.length;
    for (i = 0; i < n; i++) {
        pattern = "\\{\\$" + (i+1) + "\\}";
        re = new RegExp(pattern, "g");
        str = str.replace(re, vars[i]);
    }
    return str;
}


/**
 * Shows the error/success message on ajax request's return
 * @param  {object} $msg jQuery element object for the message div
 * @param  {string} html HTML string to insert into the message div
 */
function showMessageBox ($msg, html) {
    $msg.slideUp('slow', function() {
        $(this)
            .html(html)
            .slideDown('slow');
    });
}

/**
 * Get the error message HTML from array of errors for the message box
 * @param  {array}  errors  error messages
 * @return {string}         error messages HTML block
 */
function getErrorsHtml (errors) {
    var errors_string = '';

    errors_string += '<div class="errors">';
    errors_string +=     '<p>'+translations.form.error.title[lang]+'</p>';
    errors_string +=     '<ul>';

    // loop through errors
    for (var i = 0; i < errors.length; i++) {
        errors_string += '<li>'+errors[i]+'</li>';
    }

    errors_string +=     '</ul>';
    errors_string += '</div>';

    return errors_string;
}

/**
 * Get the success message for the message box
 * @param  {string} success Success message
 * @return {string}         Success HTML block
 */
function getSuccessHtml (success) {
    var success_string = '';

    success_string += '<div class="success">';
    success_string +=     '<p>'+success+'</p>';
    success_string += '</div>';

    return success_string;
}

/**
 * Validates a field
 * @param  {object} $field jQuery object for the field to be validated
 * @return {string}        Error message if failed
 */
function validateTheField ($field) {

    var type  = $field.prop('type'),
        name  = $field.prop('name'),
        val   = $field.val(),
        msg;

    // check if field is required and has content
    if ($field.prop('required') && $field.val() === '') {
        msg = parseMessage(translations.form.error.required[lang], [name]);
        return msg;
    }

    // check the type and validate against that
    switch ( type ) {

        // Check that the input data contains an @ sign and at least one dot (.).
        // Also, the @ must not be the first character of the email address,
        // the last dot must be present after the @ sign, and a minimum 2 characters before the end
        case 'email':
            var aPos = val.indexOf("@"),
                dPos = val.lastIndexOf(".");

            if (aPos < 1 || dPos < aPos+2 || dPos+2>=val.length ) {
                msg = parseMessage(translations.form.error.email[lang], [name]);
                return msg;
            }
            break;

        // checks if tel or number fields are numeric
        //case 'tel':
        //case 'number':
        //    if (!$.isNumeric(val)) {
        //        msg = parseMessage(translations.form.error.numeric[lang], [name]);
        //        return msg;
        //    }
        //    break;

        // NOTE:
        // We don't validate URLs because a full validation would require
        // a massive set of code.
        //
        // Here are a couple for you to consider if you really want to
        // validate URLs:
        //
        // https://gist.github.com/dperini/729294
        // https://github.com/garycourt/uri-js
    }

}

/**
 * Validate a form's fields
 * @param  {object} $form jQuery object of the form to be validated
 * @return {errors array} List of errors to display
 */
function validateTheForm ($form) {

    var errors = [],
        passed = true,
        $msg = $form.find('.message'),
        html;

    // loop through fields, check for errors
    $form
        .find('[required]')
        .each(function(i){
            var err = validateTheField($(this));

            if(err) { errors.push(err); }
        });

    // check if there are errors
    if (errors.length) {
        passed = false;
        html = getErrorsHtml(errors);
        showMessageBox($msg, html);
    }

    // return false if there's errors
    return passed;
}

/**
 * Adds a reset button to the form which clears the form and disappears
 */
function resetFormButton ($submit, submit_text) {
    jQuery('<button class="button secondary">'+translations.form.reset[lang]+'</button>')
        .insertAfter('.button.primary')
        .click(function(e){
            e.preventDefault();
            //grecaptcha.reset();
            jQuery('input.field, textarea.field').val('');
            jQuery('select.field').select2('val', '');
            jQuery(this).fadeOut('slow', function(){
                jQuery(this).remove();
                $submit
                    .text(submit_text)
                    .removeClass('complete')
                    .removeAttr('disabled');
            });
        });
}

/**
 * Prevent google reCaptcha's session timing out
 */
//function recaptchaCallback () {
//    console.log('resetting google recaptcha');
//    grecaptcha.reset();
//}

// when the page is ready...
jQuery(document).ready(function($) {


    // loop through each of the forms
    $('form').each(function(){

        var $form      = $(this),
            $msg       = $form.find('.message'),
            $submit    = $form.find('button[type=submit]'),
            submit_txt = $submit.text(),
            action     = $form.prop('action'),
            data;

        // hide message box by default
        $msg.fadeOut(0);

        // when the form is submitted
        $form.submit(function(e){

            // prevent the form being submitted before processing
            e.preventDefault();

            // if the javascript validation passes,
            // try sending the data to be processed
            if (validateTheForm($form)) {

                window.console.log('js validation complete, starting ajax request');

                // get data from fields
                data = new FormData($form[0]);

                // disabled the button to prevent double submission.
                $submit
                    .text(translations.form.button_text[lang])
                    .attr('disabled', 'disabled');

                // Send that data over to be processed
                $.ajax({
                        url: action,
                        type: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                    })

                    // when it's done, do stuff with the data
                    .done(function(data) {

                        if(data.substring(0, 1) !== "{"){
                            showMessageBox($msg, '<p>'+translations.form.error.title[lang]+'</p>');
                            window.console.log(data);
                            return;
                        }

                        // parse the returned data as JSON
                        data = $.parseJSON(data);

                        // if we have errors, let's show 'em
                        if (data.errors) {

                            showMessageBox($msg, getErrorsHtml(data.errors));

                            // reset the button text
                            $submit
                                .text(submit_txt)
                                .removeAttr('disabled');
                        }

                        // success messages
                        if(data.success) {
                            showMessageBox($msg, getSuccessHtml(translations.form.success.title[lang]));

                            $submit
                                .text(translations.form.success.button_text[lang])
                                .attr('disabled', 'disabled')
                                .addClass('complete');

                            resetFormButton($submit, submit_txt);
                        }

                        // if a redirect url is returned, do it
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    })

                    // if it failed, tell the user
                    .fail(function(data) {
                        showMessageBox($msg, '<p>'+translations.form.error.title[lang]+'</p>');

                        // reset the button text
                        $submit
                            .text(submit_txt)
                            .removeAttr('disabled');
                    });

            }

        });
    });

    // convert all select dropdowns into "Select2" dropdowns
    $('select').select2();


}); // DOM ready
