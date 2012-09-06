jQuery(function($){
    $('select[name="cookie_preset"]').change(function(){
        if(this.value != 0)
        {
            jQuery.getJSON(this.value, function(data){
                $('input[name="settings[cookie_law][text]"]').val(data.text);
                $('textarea[name="settings[cookie_law][disclaimer]"]').val(data.disclaimer);
                $('input[name="settings[cookie_law][accept]"]').val(data.accept);
                $('input[name="settings[cookie_law][decline]"]').val(data.decline);
                $('textarea[name="settings[cookie_law][javascript]"]').val(data.javascript);
            });
        }
    });
});