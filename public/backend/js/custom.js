(function ($) {
    "use strict";
    $(document).ready(function () {
        tinymce.init({
            selector: ".summernote",
            plugins:
                "anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount ",
            toolbar:
                "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat",
            tinycomments_mode: "embedded",
            tinycomments_author: "Author name",
            mergetags_list: [
                {
                    value: "First.Name",
                    title: "First Name",
                },
                {
                    value: "Email",
                    title: "Email",
                },
            ],
        });
        $(".select2").select2();

        $(".tags").tagify();


        $(".datepicker").datepicker({
            format: "dd-mm-yyyy",
            startDate: -Infinity,
            todayHighlight: true,
            todayBtn: "linked"
        }).on('changeDate', function (e) {
            $(this).datepicker('hide');
        });
        $(".clockpicker").clockpicker();

    });

    $("#setLanguageHeader").on("change", function (e) {
        this.submit();
    });


    //======NICE SELECT=======
    $('select:not(.select2)').niceSelect();


    //======STICKY SIDEBAR=======
    $(".sticky_sidebar").stickit({
        // top: 90,
    })


})(jQuery);


// tostr options
const options = {
    "closeButton": true,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-bottom-center",
    "preventDuplicates": true,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}
