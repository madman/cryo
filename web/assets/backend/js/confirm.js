$(function () {

    var $overlay = null;

    $('.confirm').click(function () {

        var data = '<div class="modal fade" id="confirm">' +
            '<div class="modal-dialog modal-sm">' +
                '<div class="modal-content">' +
                    '<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel">{ question }</h4></div>' +
                    '<div class="modal-body">' +
                        '<button type="button" data-dismiss="modal" class="btn btn-danger" id="delete">Delete</button> &nbsp; ' +
                        '<button type="button" data-dismiss="modal" class="btn">Cancel</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '</div>';

        var self = $(this),
            question = self.data('question'),
            confirm = $('#confirm');

        data = data.replace("{ question }", question);

        if (null == $overlay){
            $overlay = $(data);
            $('body').append($overlay);
        } else {
            confirm.replaceWith(data);
        }

        $overlay.modal({ backdrop: 'static', keyboard: false, show: true })
            .one('click', '#delete', function () {
                $.ajax({
                    url: self.attr('href'),
                    dataType: 'json',
                    success: function (data) {
                        if (data.result == 'success') {
                            self.closest('tr').remove();
                        }
                    }
                });
            });

        return false;
    });

});