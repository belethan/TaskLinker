$(document).ready(function () {
    const $modal = $('#genericConfirmModal');
    const $modalTitle = $modal.find('.modal-title');
    const $modalBody = $modal.find('.modal-body');
    const $confirmBtn = $modal.find('#confirmActionBtn');

    $modal.on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);

        const title = button.data('modal-title') || 'Confirmation';
        const body = button.data('modal-body') || 'Voulez-vous vraiment effectuer cette action ?';
        const confirmUrl = button.data('confirm-url') || '#';
        const confirmColor = button.data('confirm-color') || 'btn-danger';

        $modalTitle.text(title);
        $modalBody.html(body);
        $confirmBtn.attr('href', confirmUrl);

        // Appliquer la bonne couleur
        $confirmBtn.removeClass('btn-danger btn-warning btn-primary').addClass(confirmColor);
    });
});
