(function($) {
    var resultsLlist,
        previousSearch;

    function displayRepositoriesList(repositories) {
        resultsLlist.empty();

        $(repositories).each(function(i, repo) {
            resultsLlist.append(
                '<li>' +
                    repo.identifier +
                    '<button type="button" data-identifier="'+repo.identifier+'" class="add-repository btn btn-primary btn-xs">Add</button>' +
                '</li>'
            );
        });
    }

    function initAddRepositoryButtons() {
        var form = $('#team-add-repository');

        $('#repositories-list').on('click', '.add-repository', function() {
            $(this).prop('disabled', true);
            $('input[name=new_repository_id]', form).val($(this).data('identifier'));
            form.submit();
        });
    }

    function search(terms) {
        previousSearch && previousSearch.abort();

        resultsLlist.html('<li>Loading...</li>');
        previousSearch = $.ajax(Routing.generate('teams_repository_search', {'q': terms}))
            .done(function(result) {
                displayRepositoriesList(result.repositories);
            });
    }

    function initSearch() {
        var searchField = $('#repository-search');

        searchField.parents('form').on('submit', function(e) {
            e.preventDefault();
        });

        searchField.on('keyup', function() {
            var searchTerms = searchField.val();

            if (searchTerms.length === 0) {
                displayRepositoriesList([]);
                return;
            }

            search(searchTerms);
        });
    }

    $(function() {
        resultsLlist = $('#repositories-list');

        initAddRepositoryButtons();
        initSearch();
    });
})(jQuery);