// app.js

const $ = require('jquery');
require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover()
});

// Flash messages
(() => {
    const toastr = require('toastr')

    // Configuration
    toastr.options.progressBar = true
    toastr.options.timeOut = 10000
    toastr.options.extendedTimeOut = 5000

    if (typeof flashMessages !== 'undefined') {
        for (let [index, item] of flashMessages.entries()) {
            toastr[item.level](item.message)
        }
    }
})();

// Modal decision
(() => {
    $('#modal-decision').on('show.bs.modal',function(event){
        const $button = $(event.relatedTarget)
        const $wrapper = $('.datetime-picker-wrapper')
        const action = $button.data('form-action')
        const message = $button.data('message')
        const question = $button.data('question')?$button.data('question'):'Are you sure?'
        const icon = $button.data('icon')
        const cls = $button.data('cls')
        const datetime = $button.data('datetime')
        const datetimeDisabled = $button.data('datetime-disabled')

        // Update the modal's content.
        const $modal = $(this)
        $modal.find('form').attr('action', action)
        $modal.find('.modal-body label.question').html(question)
        $modal.find('.modal-footer button.option').attr('class', cls)
        $modal.find('.modal-footer button.option i').attr('class', icon)
        $modal.find('.modal-footer button.option span').html(message)

        // Init datetime picker
        if (datetime) {
            $wrapper.show()
            $wrapper.find('input').val(datetime)
            if (datetimeDisabled) {
                $wrapper.find('input').prop('disabled', true)
            } else {
                $wrapper.find('input').prop('disabled', false)
            }
        } else {
            $wrapper.hide()
            $wrapper.find('input').prop('disabled', false)
        }

        // Animation
        $modal.find('.modal-footer button.option').on('click', function(){
            $modal.find('.modal-footer button.option i').attr('class', 'fa fa-sync-alt fa-spin')
            $(this).attr('class', 'btn btn-warning disabled')
        })
    })
})()