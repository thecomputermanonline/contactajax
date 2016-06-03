
$(function() {
    $('.error').hide();
    $(".button").click(function() {
         //validate and process form here
        $('.error').hide();
        var name = $("input#name").val();
        if (name == "") {
            $("label#name_error").show();
            $("input#name").focus();
            return false;
        }
        var email = $("input#email").val();
        if (email == "") {
            $("label#email_error").show();
            $("input#email").focus();
            return false;
        }
        var phone = $("input#phone").val();
        if (phone == "") {
            $("label#phone_error").show();
            $("input#phone").focus();
            return false;
        }

        var sujet = $("input#sujet").val();
        if (sujet == "") {
            $("label#sujet_error").show();
            $("input#sujet").focus();
            return false;
        }

        var message = $("input#message").val();
        if (message == "") {
            $("label#message_error").show();
            $("input#message").focus();
            return false;
        }

        //var dataString = $("#contact_form form").serialize();
        //var data2 = json_encode(dataString);
        var dataString = '&phone=' + phone;

        //alert (dataString);return false;
        $.ajax({
            type: "POST",
            url: action,
           // url: "server/index.php",
            //data:   {json: {
            //    "Name":name,
            //    "Email":email,
            //    "Phone":phone
            //}} ,
            data: dataString,

            dataType: "json",


            success: function(content) {

                if(content.status == "success"){


                    $('#contact_form').html("<div id='message'></div>");
                    $('#message').html("<h2>Contact Form Submitted!</h2>")
                        .append("<p>We will be in touch soon.</p>")
                        .hide()
                        .fadeIn(1500, function() {
                            $('#message').append("<img id='checkmark' src='check.png' />");
                        });


                }
                else if(content.status == "error"){
                    alert("Error on query!");
                }




            }


            //success: function() {
            //    $('#contact_form').html("<div id='message'></div>");
            //    $('#message').html("<h2>Contact Form Submitted!</h2>")
            //        .append("<p>We will be in touch soon.</p>")
            //        .hide()
            //        .fadeIn(1500, function() {
            //            $('#message').append("<img id='checkmark' src='check.png' />");
            //        });
            //}
        });
        return false;

    });
});