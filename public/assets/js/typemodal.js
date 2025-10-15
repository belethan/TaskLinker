$(document).ready(function() {

    // Sélection de la modale
    const $modal = $('#confirmArchiveModal');
    const $modalTitle = $modal.find('.modal-title');
    const $modalBody = $modal.find('.modal-body p');
    const $confirmBtn = $modal.find('#confirmArchiveBtn');

    // Quand la modale s'ouvre
    $modal.on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // bouton cliqué
        const projetNom = button.data('projet-nom');
        const confirmUrl = button.data('confirm-url');

        // Mise à jour du titre
        $modalTitle.text("Confirmer l'archivage");

        // Mise à jour du message
        $modalBody.html(
            'Voulez-vous vraiment archiver le projet "' + projetNom + '" ?' +
            '<p style="text-align:center; margin-top:8px;"><strong>Ce projet ne sera plus accessible.</strong></p>'
        );


        // Mise à jour du lien de confirmation
        $confirmBtn.attr('href', confirmUrl);
    });
});
