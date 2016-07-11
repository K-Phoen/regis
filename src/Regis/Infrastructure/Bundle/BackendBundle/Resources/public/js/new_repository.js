(function($) {
    function displayRepositoriesList(repositories) {
        var list = $('#repositories-list');

        list.empty();

        $(repositories).each(function(i, repository) {
            list.append(
                '<li>' +
                    '<a href="'+ repository.publicUrl +'">' + repository.identifier + '</a>'+
                    ' – <button type="button" data-identifier="'+repository.identifier+'" class="add-repository btn btn-primary btn-xs">Add</button>' +
                '</li>'
            );
        });
    }
    
    function loadRemoteRepositoriesList() {
        $.ajax(Routing.generate('repositories_remote_list'))
            .done(function(result) {
                console.log(result.repositories.length);
                displayRepositoriesList(result.repositories);
            });
    }

    function initAddRepositoryButtons() {
        var form = $('#repository-create');

        $('#repositories-list').on('click', '.add-repository', function() {
            $(this).prop('disabled', true);
            $('input[name=identifier]', form).val($(this).data('identifier'));
            form.submit();
        });
    }

    $(function() {
        initAddRepositoryButtons();
        loadRemoteRepositoriesList();
    });
})(jQuery);