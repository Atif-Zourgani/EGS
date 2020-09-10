const $ = require('jquery');

$(document).ready(function () {

    var searchRequest = null;

    // can't submit form
    $('.search-bar').submit(function (e) {
        e.preventDefault();
    });

    // start function with a delay of 150ms
    var timeoutId = 0;
    $('.search-bar input').focus().keyup(function () {
        clearTimeout(timeoutId);
        setTimeout(searchEntity.bind(this), 150);
        $('#loader').show();
    });


    function searchEntity() {
        //var that = this;
        var value = $(this).val(); // value into the input
        var $card = $('.card'); // item to show/hide
        var url = $('.search-bar').attr("action"); // form's action
        //var entityNoResult = $("#entitiesNav").html('');
        var entityNoResult = $("#no-result").html('');

        /*var minlength = 2;  // if 2 letters, replaced by timeout
        if (value.length < minlength) {
            $card.show().css({'display': 'flex'});
        }*/

        // stop the request if it was canceled by user (ex: user types 'leo' and changes his mind for 'steph'
        // -> the first request 'Leo' is aborted
        if (searchRequest != null) {
            searchRequest.abort();
        }

        searchRequest = $.ajax({
            type: "GET",
            url: url,
            data: {'searchText' : value},
            dataType: "text",

            success: function(response){

                /*if (value !== $(that).val()) {
                    return;
                }*/

                var result = JSON.parse(response); // translate to js

                if (!result.entities) { // delay an error because of the server
                    // TODO handle server error
                    return;
                }

                // error's message if no result
                if (result.entities.error) {
                    entityNoResult.append(result.entities.error).addClass('active');
                    $('#loader').hide();
                    return;
                }

                $card.hide();

                $('.cards-container').hide();

                $.each(result.entities, function(entityId, entityData) {
                    $card.each(function () {
                        if (($(this).attr('data-id')) === entityId ) {
                            $(this).show().css({'display': 'flex'});
                            $(this).closest('.cards-container').show();
                        }
                    });
                    $('#loader').hide();
                    entityNoResult.removeClass('active');
                });
                $('#load-more').hide();

            }
        });

    }


    // SEARCH ALL STUDENTS WITH LABEL "ELITE"
    $('.gamer-label-search--elite').on('click', function() {
        var url = $('.gamer-label-search--elite').attr("action"); // form's action
        var $card = $('.card'); // item to show/hide
        var entityNoResult = $("#no-result").html('');

        $.ajax({url: url, success: function(result){

                if (result.entities.error) {
                    entityNoResult.append(result.entities.error).addClass('active');
                    $('#loader').hide();
                    return;
                }

                $card.hide();

                $('.cards-container').hide();

                $.each(result.entities, function(entityId, entityData) {
                    $card.each(function () {
                        if (($(this).attr('data-id')) === entityId ) {
                            $(this).show().css({'display': 'flex'});
                            $(this).closest('.cards-container').show();
                        }
                    });
                    $('#loader').hide();
                    entityNoResult.removeClass('active');
                });
                $('#load-more').hide();
            }});

    });

    // SEARCH ALL STUDENTS WITH LABEL "CHALLENGER"
    $('.gamer-label-search--challenger').on('click', function() {
        var url = $('.gamer-label-search--challenger').attr("action"); // form's action
        var $card = $('.card'); // item to show/hide
        var entityNoResult = $("#no-result").html('');

        $.ajax({url: url, success: function(result){

                if (result.entities.error) {
                    entityNoResult.append(result.entities.error).addClass('active');
                    $('#loader').hide();
                    return;
                }

                $card.hide();

                $('.cards-container').hide();

                $.each(result.entities, function(entityId, entityData) {
                    $card.each(function () {
                        if (($(this).attr('data-id')) === entityId ) {
                            $(this).show().css({'display': 'flex'});
                            $(this).closest('.cards-container').show();
                        }
                    });
                    $('#loader').hide();
                    entityNoResult.removeClass('active');
                });
                $('#load-more').hide();
            }});

    });

    // SEARCH ALL STUDENTS WITH LABEL "GAMER"
    $('.gamer-label-search--gamer').on('click', function() {
        var url = $('.gamer-label-search--gamer').attr("action"); // form's action
        var $card = $('.card'); // item to show/hide
        var entityNoResult = $("#no-result").html('');

        $.ajax({url: url, success: function(result){

                if (result.entities.error) {
                    entityNoResult.append(result.entities.error).addClass('active');
                    $('#loader').hide();
                    return;
                }

                $card.hide();

                $('.cards-container').hide();

                $.each(result.entities, function(entityId, entityData) {
                    $card.each(function () {
                        if (($(this).attr('data-id')) === entityId ) {
                            $(this).show().css({'display': 'flex'});
                            $(this).closest('.cards-container').show();
                        }
                    });
                    $('#loader').hide();
                    entityNoResult.removeClass('active');
                });
                $('#load-more').hide();
            }});

    });

});
