jQuery(document).ready(function() {

    //Check for Phantomjs, Slimerjs, Selenium agent
    if(/PhantomJS/.test(window.navigator.userAgent) || /SlimerJS/.test(window.navigator.userAgent) || window.document.documentElement.getAttribute("webdriver") || navigator.webdriver) {
        console.log("Headless environment detected.");
    }else{
        //get all cfe-emails on page except dashboard
        cfeemails = [];
        jQuery('.cfe-wrapper span').each(function(i,elem) {
            if (jQuery(elem).attr('class') != 'cfe-jsemail-cfe-dashboard') {
                cfeemails.push(jQuery(elem).attr('class').replace('cfe-jsemail-', ''));
            }
        });

        //if only dashboard
        if(jQuery('.cfe-jsemail-cfe-dashboard').length > 0) {
           jQuery.ajax({
                type : "post",
                dataType : "text",
                url : cfe_object.ajaxurl,
                data : {
                    action: "cfe_js_admin_email",
                    nouce:  jQuery('.secure-cloak').val()
                },
                success: function(data) {
                    var s = '';
                    var a = data;
                    var subject_line = jQuery('.cfe-jsemail-cfe-dashboard').data('subject');
                    if(subject_line) {
                        s = '?subject=' + subject_line;
                    }
                    var h = 'mailto:' + a + s;
                    jQuery('.cfe-jsemail-cfe-dashboard').html('<a href="'+ h +'">'+ a +'</a>');
                },
                error: function() {
                    console.log('Ajax Error occured');
                }
            });
        }

        //if other emails are on page
        if(cfeemails.length > 0) {
                jQuery.ajax({
                    type : "post",
                    dataType : "json",
                    url : cfe_object.ajaxurl,
                    data : {
                        action: "cfe_get_all_emails",
                        emails: cfeemails,
                        nouce:  jQuery('.secure-cloak').val()
                    },
                    success: function(data) {
                        i = 0; //set iterator
                        //loop through emails and replace enable javascript
                        jQuery.each(data, function(i,elem){
                            var s = '';
                            var a = data[i].option_value;
                            var subject_line = jQuery('.cfe-jsemail-' + data[i].option_name).data('subject');
                            if(subject_line) {
                                s = '?subject=' + subject_line;
                            }
                            var h = 'mailto:' + a + s;
                            jQuery('.cfe-jsemail-' + data[i].option_name).html('<a href="'+ h +'">'+ a +'</a>');
                            i++;
                        });
                    },
                    error: function() {
                        console.log('Ajax Error occured');
                    }
                });
        }

    }//end of else

}); //end of jQuery
