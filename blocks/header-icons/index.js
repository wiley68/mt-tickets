wp.domReady(function () {
    const el = wp.element.createElement;

    wp.blocks.registerBlockType('mt-tickets/header-icons', {
        edit: function () {
            return el('div', { className: 'mt-header-icons' }, [
                el('button', { className: 'mt-header-icon-btn', type: 'button' }, '👤'),
                el('button', { className: 'mt-header-icon-btn', type: 'button' }, '🛒')
            ]);
        },
        save: function () { return null; }
    });
});
