(function($) {
    function initRemoveMembersButtons() {
        var form = $('#team-remove-member');

        $('#members-list').on('click', '.remove-user', function() {
            $(this).prop('disabled', true);
            $('input[name=member_id]', form).val($(this).data('identifier'));
            form.submit();
        });
    }

    $(function() {
        initRemoveMembersButtons();
    });
})(jQuery);