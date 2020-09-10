const $ = require('jquery');

$(document).ready(function () {

    // SLIDER FOR PERCENTAGE OF SECTION'S ATTENDANCE
    $(function () {
        setInterval(function () {
            var imgWidth = $('.slideshow .attendance-grade').width();
            $(".slideshow .slider").animate({marginLeft: -imgWidth}, 800, function () {
                $(this).css({marginLeft: 0}).find("li.attendance-grade:last").after($(this).find("li.attendance-grade:first"));
            })
        }, 10000);
    });


    // Presence percentage card for current week (Page % attendance)
    var ninthCurrentWeekPresenceCard = $(".attendance-grade.attendance-grade--current-week li:nth-child(13n)");
    ninthCurrentWeekPresenceCard.each(function () {
        var boundary = $(this);
        $('<li class="attendance-grade attendance-grade--current-week"><h1 class="slide-title">Semaine en cours</h1><ul class="flex flex--wrap flex--align-content-start">').insertAfter(boundary.parent().parent()).find('.flex').append(boundary.nextAll().addBack());
    });

    // Presence percentage card for last week (Page % attendance)
    var ninthLastWeekPresenceCard = $(".attendance-grade.attendance-grade--last-week li:nth-child(13n)");
    ninthLastWeekPresenceCard.each(function () {
        var boundary = $(this);
        $('<li class="attendance-grade attendance-grade--last-week"><h1 class="slide-title">Semaine passée</h1><ul class="flex flex--wrap flex--align-content-start">').insertAfter(boundary.parent().parent()).find('.flex').append(boundary.nextAll().addBack());
    });


    var windowHeight = $(window).height() - $('.reliability-title').outerHeight();
    var reliabilityList = $('.reliability-list');
    var reliabilityCardHeight = $('.reliability-student').outerHeight();
    var allReliabilityCards = reliabilityList.children('li').length;
    var invisibleItems = [];

    // Get max cards for window.height
    for (var i = 0; i < allReliabilityCards; i++) {

        if ((reliabilityList.children('li').eq(i).offset().top + reliabilityCardHeight) > (windowHeight)) {
            invisibleItems.push(i);
        }

    }

    var maxReliabilityCards = invisibleItems[0];
    var maxReliabilityCardsTag = $(reliabilityList).find("li:nth-child(" + maxReliabilityCards + "n)");

    maxReliabilityCardsTag.each(function () {
        var boundary = $(this);
        $('<ul class="flex flex--wrap flex--center flex--align-content-start reliability-list">').insertAfter(boundary.parent()).append(boundary.nextAll().addBack());
    });


    // SLIDER FOR PAGE STUDENTS POINTS
    $(function () {
        setInterval(function () {
            var imgWidth = $(window).width();
            $(".students-points .slider").animate({marginLeft: -imgWidth}, 800, function () {
                $(this).css({marginLeft: 0}).find(".reliability-list:last").after($(this).find(".reliability-list:first"));
            })
        }, 10000);
    });

});