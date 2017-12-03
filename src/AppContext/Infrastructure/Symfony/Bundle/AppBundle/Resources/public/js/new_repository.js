(function($) {
    var repositories = [];

    function displayRepositoriesList(repositories) {
        var list = $('#repositories-list');

        list.empty();

        $(repositories).each(function(i, repository) {
            list.append(
                '<div class="repository">' +
                    '<div class="name">' +
                        repository.name +
                    '</div>'+
                    '<div class="actions">' +
                        '<a data-type="'+repository.type+'" data-name="'+repository.name+'" data-identifier="'+repository.identifier+'" class="add-repository btn btn-success">Add</a>' +
                    '</div>' +
                '</div>'
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
            $('input[name=type]', form).val($(this).data('type'));
            $('input[name=identifier]', form).val($(this).data('identifier'));
            $('input[name=name]', form).val($(this).data('name'));
            form.submit();
        });
    }

    function search(terms, repositories) {
        var matches = [];

        terms = terms.toLowerCase();

        $(repositories).each(function (i, repo) {
            var repoName = repo.name.toLowerCase();

            if (repoName.indexOf(terms) !== -1) {
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
