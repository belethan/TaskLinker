// public/js/select2-projet.js
$(document).ready(function() {

    $('select.select2-ajax').each(function() {
        const $select = $(this);
        const ajaxUrl = $select.data('ajax-url');
        const projetId = $select.data('projet-id');

        // Récupère les IDs déjà sélectionnés pour éviter doublons dans l'AJAX
        let selectedIds = [];
        $select.find('option:selected').each(function() {
            if ($(this).val()) {
                selectedIds.push($(this).val());
            }
        });

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
                        projetId: projetId,
                        exclude_ids: selectedIds // <- on envoie les IDs déjà sélectionnés
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || []
                    };
                }
            },
        });

        // Pré-sélectionner les employés existants (déjà liés au projet)
        $select.find('option:selected').each(function() {
            const val = $(this).val();
            const text = $(this).text();
            if (val) {
                // Crée une option pour Select2 si elle n'existe pas encore
                if ($select.find(`option[value='${val}']`).length === 0) {
                    const option = new Option(text, val, true, true);
                    $select.append(option);
                }
            }
        });

        // Déclenche le rendu pour Select2
        $select.trigger('change.select2');
    });
});
