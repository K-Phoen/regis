(function($) {
    function initRemoveMembersButtons() {
        var form = $('#team-remove-repository');

        $('#team-repo-list').on('click', '.remove-repo', function() {
            $(this).prop('disabled', true);
            $('input[name=repository_id]', form).val($(this).data('identifier'));
            form.submit();
        });
    }

    $(function() {
        initRemoveMembersButtons();
    });
})(jQuery);