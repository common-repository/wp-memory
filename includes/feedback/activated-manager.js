jQuery(document).ready(function ($) {
    //console.log('js activated...'); 
    // jQuery('#adminmenumain').css('opacity', '.1');
    // jQuery('.wrap').css('opacity', '.1');
    // jQuery('.wp-pointer').css('opacity', '0');
    jQuery('.bill-activate-modal-wpmemory').slideDown();
    // jQuery('.bill-activate-modal-wpmemory').css('opacity', '1');
    jQuery('#imagewait').hide();


    // jQuery('#bill_imagewait').show();
    // install plugin wptools
    jQuery('.bill-install-wpt-plugin-now').click(function (e) {
        // alert('ccc');
        e.preventDefault();
        //jQuery('.bill-install-wpt-plugin-now').prop('disabled', true);
        jQuery('.bill-install-wpt-plugin-now').hide();
        jQuery('#bill_imagewait').show();
        var nonce = jQuery('#nonce').val();
        // alert(nonce);
        var slug = jQuery('#slug').val();

        //  console.log(slug);

        //   'action': 'wpmemory_install_plugin',
        // action: 'wpmemory_bill_install_plugin',
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wpmemory_install_plugin',
                slug: 'wptools',
                nonce: nonce
            },
            success: function (response) {
                console.log(response);
                var main_slug = jQuery('#main_slug').val();
                var slug = 'wptools';
                //if (response.trim() === 'OK') {
                if (response.includes('OK')) {
                    jQuery('#bill_imagewait').hide();
                    alert("Plugin WPtools Installed Successfully. Go to Plugin's page and activate it!");
                    //$('body').showToast('WPtools Installed Successfully!', 5000, 'ok');


                    // create cookie...
                    //var BILLCLASS = "ACTIVATED_" + slug;
                    var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
                    var d = new Date();
                    var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
                    d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
                    var expires = "expires=" + d.toUTCString();

                    //console.log(BILLCLASS);
                    //console.log(expires);

                    document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";





                }
            },
            error: function (xhr, status, error) {
                console.error('Error while installing the plugin.:', error);
                alert('An error occurred while installing the plugin. Please try again later.');
            },
            complete: function () {
                console.log('Complete');
                jQuery('#bill_imagewait').hide();
                jQuery('#loading-spinner').prop('disabled', true);
                jQuery('#loading-spinner').text('Installed');





            }
        });
    });
    // Close
    jQuery('#wpmemory-activate-close-up-dialog').on('click', function () {

        var slug = jQuery('#slug').val();

        // console.log(slug);


        var BILLCLASS = "ACTIVATED_" + slug.toUpperCase();
        var d = new Date();
        var DayInSeconds = 24 * 60 * 60; // 10 dias * 24 horas * 60 minutos * 60 segundos
        d.setTime(d.getTime() + (DayInSeconds * 1000)); // Convertendo para milissegundos
        var expires = "expires=" + d.toUTCString();

        //console.log(BILLCLASS);
        //console.log(expires);

        document.cookie = BILLCLASS + "=" + Date.now() + "; " + expires + "; path=/";



        alert('Thank you for installing our plugin!');
        jQuery('.bill-activate-modal-wpmemory').hide();

    });
});  