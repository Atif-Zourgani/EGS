const $ = require('jquery');

require('../css/jquery-ui.scss');

$(document).ready(function () {

    // KEEP SELECTED VALUES WHEN FORM IS SUBMITTED (PAGE EVALUATION)
    if ($('body.evaluation').length > 0) {

        $("#select-discipline").autocomplete({
            source: disciplinesA,
            change: function() {
                localStorage.setItem("discipline", $(this).val());
            }

        });

        var section = $('#select-section');
        section.on('change', function() {
            localStorage.setItem("section", $(this).val());
        });
        if (section.length && section.val() != "none") {
            section.val(localStorage.getItem("section"));
        }

        var discipline = $('#select-discipline');
        if (discipline.length && discipline.val() != "none") {
            discipline.val(localStorage.getItem("discipline"));
        }

        var student = $('#select-student');
        student.on('change', function() {
            localStorage.setItem("student", $(this).val());
        });
        if (student.length && student.val() != "none") {
            student.val(localStorage.getItem("student"));
        }

        if ($('body.evaluation-form, body.evaluation-second-step').length > 0) {
            discipline.css({'cursor': 'not-allowed'});
            discipline.closest('.select-designed').css({'cursor': 'not-allowed'});
            section.css({'cursor': 'not-allowed'});
            section.closest('.select-designed').css({'cursor': 'not-allowed'});
            section.on('mousedown', function(e) {
                e.preventDefault();
            });
            discipline.on('mousedown', function(e) {
                e.preventDefault();
            })
        }

        $('#reboot-evaluation').click(function() {
            localStorage.clear();
        });
    }

    // CHANGE CSS OF INPUT EVALUATION'S FORM

    $('li.skill input').on('click', function() {
        $(this).closest('.skill').toggleClass('checked');
        if ($('.checked').length > 0) {
            $(this).closest('.skill').find('.fa-minus-square').removeClass('fa-minus-square').addClass('fa-check-square');
        } else {
            $(this).closest('.skill').find('.far.fa-check-square').removeClass('fa-check-square').addClass('fa-minus-square');
        }
    });

    // CHANGE CSS OF INPUT RATING (EVALUATION'S FORM)
    if ($('.evaluation-form .rating').length > 0) {
        var input = $('.rating input');
        input.addClass('far fa-star');

        input.on('click', function() {
            $('.fa-star.fas').removeClass('fas').addClass('far');
            if ($(this).hasClass('far')) {
                $(this).removeClass('far').addClass('fas');
            } else {
                $(this).removeClass('fas').addClass('far');
            }
        });

        $('.rating input:nth-child(2)').on('click', function() {
            $('.rating input:nth-child(1)').addClass('fas').removeClass('far');
        });
        $('.rating input:nth-child(3)').on('click', function() {
            $('.rating input:nth-child(1), .rating input:nth-child(2)').addClass('fas').removeClass('far');
        });
        $('.rating input:nth-child(4)').on('click', function() {
            $('.rating input:nth-child(1), .rating input:nth-child(2), .rating input:nth-child(3)').addClass('fas').removeClass('far');
        });
        $('.rating input:nth-child(5)').on('click', function() {
            $('.rating input:nth-child(1), .rating input:nth-child(2), .rating input:nth-child(3), .rating input:nth-child(4)').addClass('fas').removeClass('far');
        });
    }

    $('.evaluation #select-student').on('change', function() {
        this.form.submit();
    });

    if ($('.all-skills-assigned').length > 0) {
        $('body.evaluation-form .main-scrollable').css({'height': 'calc(100vh - 70px)'})
    }

});