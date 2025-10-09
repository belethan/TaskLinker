/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/style.css';
import './bootstrap.js';
import './js/select.js';

$(document).ready(function() {
    $('.select2-ajax').each(function() {
        var $select = $(this);
        var ajaxUrl = $select.data('ajax-url');
        var projetId = $select.data('projet-id');

        $select.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Rechercher un employé...',
            allowClear: true,
            closeOnSelect: false,
            language: 'fr',
            ajax: {
                url: ajaxUrl,
                dataType: 'json',
                delay: 100, // Délai avant la recherche
                data: function(params) {
                    return {
                        q: params.term, // Terme de recherche
                        projet_id: projetId || null,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0, // 0 = afficher tous dès l'ouverture
            templateResult: formatEmploye,
            templateSelection: formatEmployeSelection,
            escapeMarkup: function(markup) {
                return markup;
            }
        });
    });

    // Format pour les résultats dans la liste déroulante
    function formatEmploye(employe) {
        if (employe.loading) {
            return 'Recherche...';
        }

        return '<div class="select2-result-employe">' +
            '<div class="select2-result-employe__title">' + employe.text + '</div>' +
            '</div>';
    }

    // Format pour l'élément sélectionné
    function formatEmployeSelection(employe) {
        return employe.text || employe.id;
    }

});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
