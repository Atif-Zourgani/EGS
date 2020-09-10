const $ = require('jquery');

$(document).ready(function () {

    function showSkills() {
        var level = $('.level.current').attr('data-value'); // active level

        $('.block-skills').each(function () { // for each skill
            if ($(this).attr('data-value') === level) { // if skill's level = active level
                $(this).show(); // show skill
            } else {
                $(this).hide(); // hide skill
            }
        });
    }

    // HIDE/SHOW SKILLS (PAGE DISCIPLINE)
    $('.discipline-levels .level:first-of-type').addClass('active').addClass('current');
    showSkills();

    // LEVELS ANIMATION (PAGE DISCIPLINE)
    if ($('body.discipline').length > 0 && $('body.discipline.mobile').length === 0) {
        var skillLevel = $('.discipline-levels .level');
        var progressBar = $('.progress-bar');
        var firstLevel = $('.levels li:first-of-type');
        var widthLevel = firstLevel.width() / 2;
        var secondLevel = $('.levels li:nth-of-type(2)');

        if ($('body.discipline.responsive').length > 0) {
            var left = firstLevel.offset().left + widthLevel;
        } else {
            var left = firstLevel.offset().left - 250 + widthLevel;
        }

        progressBar.css({'width': left + 'px'});

        firstLevel.addClass('current');

        if ($('.levels li').length > 1) {
            var widthBetweenLevels = (secondLevel.offset().left - firstLevel.offset().left);
            skillLevel.on('click', function() {
                $('.current').removeClass('current');
                $(this).addClass('current');
                var idLevel = $(this).attr('data-id');
                var distance = widthBetweenLevels * (idLevel - 1);
                progressBar.css({'width': left + distance + 'px'});
                $(this).prevAll().addClass('active');
                $(this).nextAll().removeClass('active');
            })
        }
    }

    // DISPLAY SKILLS FOR EACH LEVEL (PAGE DISCIPLINE)
    if ($('.discipline').hasClass('mobile')) {
        $('.discipline-levels .levels').on('change', function() {
            $('.level.active').removeClass('active').removeClass('current');
            $("select option:selected").addClass('active').addClass('current');
            showSkills();
        });
    } else {
        $('.discipline-levels .level').on('click', function() {
            //$('.active').removeClass('active');
            $(this).addClass('active');
            showSkills();
        });
    }

    $('.skills .skill.active').css({'color': '#35a98f'});

    $('.skills .skill.active i').removeClass('fa-minus-square').addClass('fa-check-square');

    if ($('.breadcrumbs').length === 0) {
        $('.main-scrollable').css({'height': '100vh'});
    }

    // DISPLAY SAME HEIGHT ON SKILLS FOR ALL LEVELS ON THE SAME PAGE (PAGE DISCIPLINE)
    /*if ($('body.discipline.mobile').length === 0) {
        var maxSkills = $('.skills').outerHeight();
        $('.skills').css({'height':maxSkills+'px'});
    }*/

    $('.discipline-infos-description p').each(function() {
        if ($(this).height() > 16) {
            $(this).css({'text-align': 'justify'});
        }
    });

});