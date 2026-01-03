(function (blocks, element) {
    const el = element.createElement;

    blocks.registerBlockType('mt-tickets/topbar-left', {
        edit: function () {
            // Editor preview placeholder (real content is rendered in PHP on the front end).
            return el(
                'div',
                { className: 'mt-topbar-left' },
                [
                    el('span', { className: 'mt-topbar-left__icon', 'aria-hidden': true }, '☎'),
                    el('span', { className: 'mt-topbar-left__text' }, 'Top bar contact (configured in Appearance → MT Tickets)')
                ]
            );
        },
        save: function () {
            return null; // dynamic (PHP) render
        }
    });
})(window.wp.blocks, window.wp.element);
