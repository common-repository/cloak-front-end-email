jQuery(document).ready(function($) {

        //Add new field
        $('#cfe_add').click(function(e){
            //Add to bottom of the wrapper
            e.preventDefault();
            //Set value to equal email minus the @domain.com
            $('div[id=wrap_cfe_emails]').append('<p><input type="email" class="cfe_additional_email" size="100" value="add@email.com" onclick="if(this.value == \'add@email.com\'){this.value=null};" /> <span class="button button-primary cfe-remove">- Remove</span></p>');
        });

        //Remove field
        $(document).on('click','.cfe-remove', function(){
            $(this).parent().remove();
        });

        //Remove field from db
        $('.cfe-delete').click(function(){
                delete_email = $(this).parent().find('input').attr('name');

                var confirm_remove = confirm('Sure you want to delete this email?');
                if (confirm_remove == true) {
                        $.ajax({
                                url: ajaxurl,
                                type: 'post',
                                data:{
                                        action: 'cfe_remove_email',
                                        option_name: delete_email,
                                        nouce:  jQuery('#secure_cloak').val()
                                }
                        });
                        $(this).parent().remove();
                }
        });

        //on blur set name value
        $(document).on('blur', '.cfe_additional_email', function(){
            email_name = ($(this).val().match(/(.*?)@/gmi));
            email_name = String(email_name).replace("@", "");
            email_name = String(email_name).replace(".", "-");
            $(this).attr('name', 'cfe-' + email_name);
        });

        //on submit get each value
        $('#submit').click(function(){
            cfe_values = [];
            $('.cfe_additional_email').each(function(index){
                  cfe_values.push($(this).attr('name'));
                  return cfe_values;
            });
            //add value when submitted
            $('input[name=page_options]').attr('value', cfe_values);
        });
});
