(function($) {
    var repositories = [];

    function displayRepositoriesList(repositories) {
        var list = $('#repositories-list');

        list.empty();

        $(repositories).each(function(i, repository) {
            list.append(
                '<li>' +
                    '<a class="name" href="'+ repository.publicUrl +'">' + repository.name + '</a>'+
                    '<button type="button" data-identifier="'+repository.identifier+'" class="add-repository btn btn-primary btn-xs">Add</button>' +
                '</li>'
            );
        });
    }
    
    function loadRemoteRepositoriesList() {
        $.ajax(Routing.generate('repositories_remote_list'))
            .done(function(result) {
                repositories = result.repositories;

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

    function search(terms, repositories) {
        var matches = [];

        terms = terms.toLowerCase();

        $(repositories).each(function (i, repo) {
            var repoIdentifier = repo.identifier.toLowerCase();

            if (repoIdentifier.indexOf(terms) !== -1) {
                matches.push(repo);
            }
        });

        displayRepositoriesList(matches);
    }

    function initSearch() {
        var searchField = $('#repository-search');

        searchField.parents('form').on('submit', function(e) {
            e.preventDefault();
        });

        searchField.on('keyup', function() {
            var searchTerms = searchField.val();

            if (searchTerms.length === 0) {
                displayRepositoriesList(repositories);
            } else {
                search(searchTerms, repositories);
            }
        });
    }

    $(function() {
        initAddRepositoryButtons();
        initSearch();
        loadRemoteRepositoriesList();
    });
})(jQuery);
