(function($) {
    var users = [],
        resultsLlist,
        previousSearch;

    function displayUsersList(users) {
        resultsLlist.empty();

        $(users).each(function(i, user) {
            resultsLlist.append(
                '<li>' +
                    user.username + ' — ' + user.email +
                    '<button type="button" data-identifier="'+user.id+'" class="add-user btn btn-primary btn-xs">Add</button>' +
                '</li>'
            );
        });
    }

    function initAddUserButtons() {
        var form = $('#team-add-member');

        $('#users-list').on('click', '.add-user', function() {
            $(this).prop('disabled', true);
            $('input[name=new_member_id]', form).val($(this).data('identifier'));
            form.submit();
        });
    }

    function search(terms) {
        previousSearch && previousSearch.abort();

        resultsLlist.html('<li>Loading...</li>');
        previousSearch = $.ajax(Routing.generate('teams_user_search', {'q': terms}))
            .done(function(result) {
                displayUsersList(result.users);
            });
    }

    function initSearch() {
        var searchField = $('#user-search');

        searchField.parents('form').on('submit', function(e) {
            e.preventDefault();
        });

        searchField.on('keyup', function() {
            var searchTerms = searchField.val();

            if (searchTerms.length === 0) {
                displayUsersList([]);
                return;
            }

            search(searchTerms);
        });
    }

    $(function() {
        resultsLlist = $('#users-list');

        initAddUserButtons();
        initSearch();
    });
})(jQuery);