var formatCreated = function(date, inSeparator, outSeparator){
    var spaceSplit = date.split(" ");
    var dateHyphenSplit = spaceSplit[0].split(inSeparator);

    return dateHyphenSplit[2] + outSeparator + dateHyphenSplit[1] + outSeparator + dateHyphenSplit[0];
};

var emptyTemplate = '<div class="alert alert-warning">Nous n\'avons pas trouvé de résultats.</div>';
var statsTemplate = '{{ nbHits }} résultats trouvés en {{ processingTimeMS }}ms';
var itemTemplate = function(data){
    var created = formatCreated(data._highlightResult.created.date.value, "-", "/");

    $template = '<a href="' + Routing.generate('front_article_view', {'slug': data.slug}) + '" class="list-group-item">' +
                '<h4 class="list-group-item-heading">[' + created + '] ' + data._highlightResult.title.value + '</h4>' +
                '<p class="list-group-item-text">' + data._highlightResult.description.value + '</p>' +
                '</a>';

    return $template;
};

var search = instantsearch({
    appId: 'LRBWDHZ4PS',
    apiKey: 'da6f7c0f97c73aa9fc38de90e8d1b429',
    indexName: 'Post',
    searchFunction: function(helper){
        var searchBox = $('#search-box');
        var hitsContainer = $('#hits-container');
        var paginationContainer = $('#pagination-container');
        var statsContainer = $('#stats-container');

        if('' != searchBox.val()){
            helper.search();
        } else {
            hitsContainer.html('');
            paginationContainer.html('');
            statsContainer.html('');
        }
    }
});

search.addWidget(
    instantsearch.widgets.searchBox({
        container: '#search-box',
        placeholder: 'Rechercher...',
        poweredBy: true,
        autofocus: true
    })
);

search.addWidget(
    instantsearch.widgets.hits({
        container: '#hits-container',
        templates: {
            item: itemTemplate,
            empty: emptyTemplate
        },
    })
);

search.addWidget(
    instantsearch.widgets.pagination({
        container: '#pagination-container',
        labels: {
            previous: 'Précédent',
            next: 'Suivant'
        },
        cssClasses: {
            root: 'pagination',
            active: 'active',
            disabled: 'disabled'
        },
        showFirstLast: false
    })
);

search.addWidget(
    instantsearch.widgets.stats({
        container: '#stats-container',
        templates: {
            body: statsTemplate
        }
    })
);

search.start();