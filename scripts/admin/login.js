$("#login").submit(function (e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');
    var type = form.attr('method');
    $.ajax({
        type: type,
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function (data) {
            window.location.url = 'admin';
        }
         });


});