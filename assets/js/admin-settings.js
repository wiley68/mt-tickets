jQuery(function ($) {
    let headerFrame;
    let footerFrame;

    // Header logo functions
    function setHeaderPreview(url, id) {
        $('#mt_tickets_logo_preview').attr('src', url);
        $('#mt_tickets_logo_id').val(id || 0);
    }

    $('#mt_tickets_logo_select').on('click', function (e) {
        e.preventDefault();

        if (headerFrame) {
            headerFrame.open();
            return;
        }

        headerFrame = wp.media({
            title: 'Select Header Logo',
            button: { text: 'Use this logo' },
            multiple: false
        });

        headerFrame.on('select', function () {
            const att = headerFrame.state().get('selection').first().toJSON();
            setHeaderPreview(att.url, att.id);
        });

        headerFrame.open();
    });

    $('#mt_tickets_logo_remove').on('click', function (e) {
        e.preventDefault();
        setHeaderPreview(MT_TICKETS_ADMIN.placeholder, 0);
    });

    // Footer logo functions
    function setFooterPreview(url, id) {
        $('#mt_tickets_footer_logo_preview').attr('src', url);
        $('#mt_tickets_footer_logo_id').val(id || 0);
    }

    $('#mt_tickets_footer_logo_select').on('click', function (e) {
        e.preventDefault();

        if (footerFrame) {
            footerFrame.open();
            return;
        }

        footerFrame = wp.media({
            title: 'Select Footer Logo',
            button: { text: 'Use this logo' },
            multiple: false
        });

        footerFrame.on('select', function () {
            const att = footerFrame.state().get('selection').first().toJSON();
            setFooterPreview(att.url, att.id);
        });

        footerFrame.open();
    });

    $('#mt_tickets_footer_logo_remove').on('click', function (e) {
        e.preventDefault();
        setFooterPreview(MT_TICKETS_ADMIN.placeholder, 0);
    });
});
