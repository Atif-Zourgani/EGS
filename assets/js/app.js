require('../css/app.scss');

const $ = require('jquery');
require('../../node_modules/select2/dist/js/select2.min');
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

$(document).ready(function () {

    $('.js-flash-message').delay(3000).fadeOut(500);


    $('.user-links a').on('click', function (e) {
        e.preventDefault();
    });

    $('.burger-menu').on('click', function () {
        $('.nav-menu').toggleClass('open');
    });

    $('.active-user-short').on('click', function() {
        $('.logout').toggleClass('displayed');
    });


    // LOGOUT (IF USER IS STUDENT)
    $('.js-logout').on('click', function() {
        $('.logout').fadeToggle(50);
    });

    // OPEN/CLOSE MODAL ALL BADGES (PAGE STUDENT)
    $('#js-badges').on('click', function() {
        $('.modal-badges').fadeIn();
    });
    $('.js-close-modal').on('click', function() {
        $('.modal-badges, .modal-student-attendance').fadeOut();
    });


    // OPEN/CLOSE MODAL ALL ABSENCES AND DELAYS (PAGE STUDENT)
    $('.js-student-attendance').on('click', function() {
        $('.modal-student-attendance').fadeIn();
    });

    // DISPLAY MORE COMMENTS (PAGE STUDENT)
    if ($('body.student').length > 0) {
        $('.js-teacher-comment').hide();
        $('.js-teacher-comment').slice(0,4).show();

        $('#load-more').on('click', function() {
            $('.js-teacher-comment:hidden').slice(0,4).slideDown();
            if($('.js-teacher-comment:hidden').length === 0) {
                $('#load-more').fadeOut();
            }
        });
    }

    // STUDENT FORM FEEDS (PAGE STUDENT)

    $('.input-student-feed').select2();

    ClassicEditor
        .create( document.querySelector( '.ckeditor' ) )
        .then( editor => {
        window.editor = editor
        });

    $('.display-feed').on('click', function() {
       $('.feed-overlay').css({'right': 0});
       $('.main-scrollable').css({'overflow-y': 'hidden'});
       $('.blur').show();
    });

    $('.close-feed').on('click', function() {
        $('.feed-overlay').css({'right': -1000});
        $('.main-scrollable').css({'overflow-y': 'scroll'});
        $('.blur').hide();
    });


    $('#feed-cat').on('change',function() {

        var cat = $(this).val();

        $('.feed').each(function () {

            if (($(this).attr('data-cat') === cat) || (cat === 'all')) {
                $(this).show();
            } else {
                $(this).hide();
            }

        });
    });


    // GAMER LABEL CHECKBOX (PAGE STUDENT)
    var gamer = $('.gamer-checkbox');
    var elite = $('.gamer-checkbox--elite');
    var challenger = $('.gamer-checkbox--challenger');

    var eliteBool = elite.attr('data-elite');
    var challengerBool = challenger.attr('data-challenger');

    gamer.change(function() {
        $(this).closest("form").submit();
    });

    if (eliteBool == 1) {
        elite.prop('checked', true);
        challenger.prop('checked', false);
    } else {
        elite.prop('checked', false);
    }

    if (challengerBool == 1) {
        challenger.prop('checked', true);
        elite.prop('checked', false);
    } else {
        challenger.prop('checked', false);
    }

    if (challengerBool == 0 && eliteBool == 0) {
        gamer.prop('checked', true);
        elite.prop('checked', false);
        challenger.prop('checked', false);
    }


    // DISPLAY DISCIPLINE WITH FILTERS (PAGE STUDENT / SECTION SKILLS)
    $('select[name="skills"]').on('change',function() {
        var category = $(this).val();
        $('.js-all-disciplines').hide();

        $('.category-name, .js-student-discipline').each(function () { // for each skill
            if ($(this).attr('data-category') === category ) {
                $(this).show(); // show skill
                $(this).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
            } else if (category === 'all') {
                $('.js-all-disciplines').show().addClass('open');
                $(this).hide();
                $('.js-all-disciplines span').text('Fermer toutes les catégories');
                $('.category-name i').removeClass('fa-caret-right').addClass('fa-caret-down');
                $('.category-name').show();
                $('.student-disciplines .active').removeClass('active');
                $('.student-disciplines').addClass('active').css({'margin-bottom': '20px'});
                $('.js-student-discipline').show();
            } else {
                $(this).hide(); // hide skill
            }
        });
    });

    // OPEN FIRST CATEGORY (PAGE STUDENT / SECTION SKILLS)
    $('.student-disciplines').first().addClass('active').find('.category-name i').removeClass('fa-caret-right').addClass('fa-caret-down');

    // SHOW OR HIDE STUDENT'S DISCIPLINES ON CLICK ON CATEGORY (PAGE STUDENT / SECTION SKILLS)
    $('.student-disciplines .category-name').on('click', function() {

        $(this).parent('.student-disciplines').find('.js-student-discipline').toggle();

        if ($(this).parent('.student-disciplines').hasClass('active')) {
            $(this).parent('.student-disciplines').removeClass('active').css({'margin-bottom': '0'});
            $(this).find('i').removeClass('fa-caret-down').addClass('fa-caret-right');
        } else {
            $(this).parent('.student-disciplines').addClass('active').css({'margin-bottom': '20px'});
            $(this).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
        }

        if ($('.student-disciplines.active').length === $('.student-disciplines').length) {
            $('.js-all-disciplines span').text('Fermer toutes les catégories');
            $('.js-all-disciplines i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $('.js-all-disciplines').addClass('open');
        } else if ($('.student-disciplines.active').length === 0) {
            $('.js-all-disciplines span').text('Déplier toutes les catégories');
            $('.js-all-disciplines i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $('.js-all-disciplines').removeClass('open');
        }
    });

    $('.js-all-disciplines').on('click', function() {
        $(this).toggleClass('open');
        if ($('.js-all-disciplines').hasClass('open')) {
            $('.student-disciplines.active').removeClass('active');
            $('.js-all-disciplines button span').text('Fermer toutes les catégories');
            $('.student-disciplines.student-disciplines').addClass('active').css({'margin-bottom': '20px'});
            $('.js-student-discipline').show();
            $('.category-name i').removeClass('fa-caret-right').addClass('fa-caret-down');
            $(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up')
        } else {
            $('.student-disciplines.active').removeClass('active').css({'margin-bottom': '0'});
            $('.js-all-disciplines button span').text('Déplier toutes les catégories');
            $('.js-student-discipline').hide();
            $('.category-name i').removeClass('fa-caret-down').addClass('fa-caret-right');
            $(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
    });


    // DISPLAY COMMENT WITH FILTERS (PAGE STUDENT / SECTION COMMENTS)
    $('.select-teacher, .select-discipline').on('change',function() {

        var teacher = $('.select-teacher').val();
        var discipline = $('.select-discipline').val();

        $('.js-teacher-comment').each(function () { // for each skill

            $('#load-more').hide();

            if (($(this).attr('data-teacher') === teacher || teacher === 'all' ) && ($(this).attr('data-discipline') === discipline || discipline === 'all')) { // if skill's level = active level
                $(this).show(); // show skill
            } else {
                $(this).hide(); // hide skill
            }

            if ($('.js-teacher-comment:visible').length === 0) {
                $('.no-result').show()
            } else {
                $('.no-result').hide()
            }

        });
    });

    // CHECK INCIDENT ON CLICK (PAGE STUDENT'S INCIDENTS)
    $('.student-reliability .incident').not(".student-reliability .incident-disable").on('click', function() {
        $(this).closest('.incident').not(".student-reliability .incident-disable").toggleClass('active');
        if($(this).hasClass('active')) {
            $(this).find('input').attr('checked', true);
        } else {
            $(this).find('input').attr('checked', false);
        }
    });

    // PREVENT SUBMIT EMPTY FORM INCIDENT
    $('.submit-incident').on('click', function() {
        if($('.incident.active').length == 0) {
            $('.incident-error').show();
            return false;
        }
    });

    // DISPLAY BEHAVIOUR'S RULES MODAL
    $('.behaviour-rules').on('click', function() {
        $('.rules-modal').fadeIn();
    });
    $('.close-rules-modal').on('click', function() {
        $('.rules-modal').fadeOut();
    });


    // DISPLAY MORE CARDS
    if ($('body.section').length === 0) {
        if ($('.cards-container').length > 0) {
            $('.card.card--user, .card.card--section').slice(0,50).show().css({'display': 'flex'});

            $('.card.card--discipline').show().css({'display': 'flex'});

            $('#load-more').on('click', function() {
                $('.card:hidden').slice(0,20).slideDown().css({'display': 'flex'});
                if($('.card:hidden').length === 0) {
                    $('#load-more').fadeOut();
                }
            });
        }
    } else {
        $('.card.card--user').show().css({'display': 'flex'});
    }

    // PROGRESS BAR ON STUDENT'S CAREER'S PAGES
    moveProgressBar();
    // on browser resize...
    $(window).resize(function() {
        moveProgressBar();
    });

    // SIGNATURE PROGRESS
    function moveProgressBar() {
        var getPercent = ($('.progress-wrap').data('progress-percent') / 100);
        var getProgressWrapWidth = $('.progress-wrap').width();
        var progressTotal = getPercent * getProgressWrapWidth;
        var animationLength = 1500;

        // on page load, animate percentage bar to data percentage length
        // .stop() used to prevent animation queueing
        $('.progress-wrap .progress-bar').stop().animate({
            left: progressTotal
        }, animationLength);
    }

});
