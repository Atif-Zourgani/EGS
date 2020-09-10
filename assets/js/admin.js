require('../css/app.scss');

const $ = require('jquery');

$(document).ready(function () {

    // Display modals to avoid to delete datas by mistake when user download backup

    $('.close-flash-modal').on('click', function () {
        $('.flash-message--back-up').fadeOut(300);
    });

    $('.cancel-delete-datas').click(function () {
        $('.flash-message--back-up').fadeOut(500);
    });

    $('.js-delete-datas').click(function () {
        $('.modal-security-before-remove').fadeIn(500);
    });


    // SELECT ALL INPUT FOR STUDENTS WITHOU ACCOUNT
    $('#select-all').click(function () {
        if (this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function () {
                this.checked = true;
            });
        } else {
            $(':checkbox').each(function () {
                this.checked = false;
            });
        }
    });


    // AVOID TO PERSIST IF NO STUDENTS SELECTED
    var inputChecked = 0;
    $(".student-without-account").each(function() {
        $(this).click(function(){
            $('.student-without-account').checked = true;
            inputChecked ++
        });
    });

    $('.btn-new-users').click(function(e) {
        if (inputChecked === 0) {
            event.preventDefault(e);
            alert('Veuillez sélectionner au moins un étudiant.');
        }
    });


   // SEARCH STUDENT TO DISABLE WITH AUTOCOMPLETE
    $("#select-student-to-disabled").autocomplete({

        source: function (request, response) {
            response($.map(studentsToDisable, function (value) {

                var name = value.student.toUpperCase();

                if (name.indexOf(request.term.toUpperCase()) != -1) {
                    return {
                        label: value.student,
                        id: value.id
                    }
                } else {
                    return null;
                }
            }));
        }
    });


    $('#select-student-to-disabled').on('autocompleteselect', function (e, ui) {
        $(".list-students-to-disable").append('<div class="flex flex--align-center block-student-to-disable"><input type="checkbox" name="student" value="' + ui.item.id + '" checked ><label for="student" class="student-to-disable">' + ui.item.value +'</label></div>');
        $(this).reset();
    });

});
