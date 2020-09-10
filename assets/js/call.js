const $ = require('jquery');

$(document).ready(function () {

    // DATEPICKER (SELECT A CALL)
    if ($('body.select-call').length > 0) {
        $.datepicker.setDefaults($.datepicker.regional['fr']);

        $('#datepicker').datepicker({
            dateFormat: 'yy/mm/dd',
            maxDate: 0
        }).datepicker("setDate", "0");

        $('#datepicker-teacher').datepicker({
            dateFormat: 'yy/mm/dd',
            minDate: 0,
            maxDate: 0
        }).datepicker("setDate", "0");
    }

    // DATEPICKER (CHANGE ALL STUDENTS POINTS)
    if ($('body.usefull').length > 0) {
        $.datepicker.setDefaults($.datepicker.regional['fr']);

        $('#datepicker').datepicker({
            dateFormat: 'yy/mm/dd',
            maxDate: 0
        }).datepicker("setDate", "0");
    }



    // CHANGE INPUT'S DESIGN (FORM CALL)
    $('.input-status input:nth-of-type(1)').addClass('far fa-check-circle');
    $('.input-status input:nth-of-type(2)').addClass('far fa-clock');
    $('.input-status input:nth-of-type(3)').addClass('far fa-times-circle');
    $('.input-status input:nth-of-type(4)').addClass('far fa-smile');

    $('.input-status-restricted input:nth-of-type(4)').hide();


    // DISPLAY CALL'S DETAILS
    $('.js-see-call').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.block-call').find('.call-details').fadeToggle();
    });

    // DISPLAY CALLS WITH FILTERS (PAGE DISPLAY ALL CALLS)
    $('.call-filters').on('change', function() {

        $('.call-details').hide();

        var section = $('.call-filter-section').val();
        var date = $('.call-filter-date').val();

        $('.block-calls .list-call').each(function () {

            if ( ($(this).attr('data-section') === section || section === 'all' ) && ($(this).attr('data-date') === date || date === 'all')) {
                $(this).show();
            } else {
                $(this).hide();
            }

            if($('.block-calls .list-call:visible').length === 0) {
                $('.no-result').show()
            } else {
                $('.no-result').hide()
            }
        });
    });

});