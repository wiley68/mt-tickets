jQuery(function ($) {
    let frame;

    function setPreview(url, id) {
        $('#mt_tickets_logo_preview').attr('src', url);
        $('#mt_tickets_logo_id').val(id || 0);
    }

    $('#mt_tickets_logo_select').on('click', function (e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Header Logo',
            button: { text: 'Use this logo' },
            multiple: false
        });

        frame.on('select', function () {
            const att = frame.state().get('selection').first().toJSON();
            setPreview(att.url, att.id);
        });

        frame.open();
    });

    $('#mt_tickets_logo_remove').on('click', function (e) {
        e.preventDefault();
        setPreview(MT_TICKETS_ADMIN.placeholder, 0);
    });
});
