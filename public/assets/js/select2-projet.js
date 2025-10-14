// public/js/select2-projet.js
$(document).ready(function() {

    $('select.select2-ajax').each(function() {
        const $select = $(this);
        const ajaxUrl = $select.data('ajax-url');
        const projetId = $select.data('projet-id');
        //const selectedIds = ($select.data('selected') || '').split(',').filter(Boolean);

        // Récupère les IDs déjà sélectionnés pour éviter doublons dans l'AJAX
        let selectedIds = [];
        $select.find('option:selected').each(function() {
            if ($(this).val()) {
                selectedIds.push($(this).val());
            }
        });

        // Ajouter explicitement les options pré-sélectionnées (éviter doublons)
        selectedIds.forEach(id => {
            const text = $select.find(`option[value='${id}']`).text();
            if ($select.find(`option[value='${id}']`).length === 0) {
                const option = new Option(text, id, true, true);
                $select.append(option);
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
                delay: 150,
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
                },

            },
        });

        // --- Déclencher rendu initial ---
        $select.trigger('change.select2');

        // --- Quand un tag est supprimé, il devient à nouveau sélectionnable ---
        $select.on('select2:unselect', function(e) {
            const removedId = e.params.data.id;
            // Retirer l’ID supprimé de selectedIds pour qu’il soit à nouveau disponible
            selectedIds = selectedIds.filter(id => id != removedId);
            $(this).trigger('change.select2');
        });
    });
 });
