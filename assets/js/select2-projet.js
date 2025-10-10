// public/js/select2-projet.js
$(document).ready(function() {
    console.log('Select2 init:', $('select.select2-ajax').length);
    $('select.select2-ajax').each(function() {
        const $select = $(this);
        const ajaxUrl = $select.data('ajax-url');
        const projetId = $select.data('projet-id');

        $select.select2({
            placeholder: 'Sélectionnez des employés',
            allowClear: true,
            width: '100%',
            tags: true,
            ajax: {
                url: ajaxUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        projetId: projetId
                    };
                },
                processResults: function (data) {
                    return {
                       results: data.results || []
                    };
                },
            },
        });

        // Pré-sélection des employés existants
        /*const selectedOptions = $select.find('option:selected');
        selectedOptions.each(function() {
            const val = $(this).val();
            const text = $(this).text();
            if(val) {
                const option = new Option(text, val, true, true);
                $select.append(option);
            }
        });*/
        $select.trigger('change.select2');
    });
});
