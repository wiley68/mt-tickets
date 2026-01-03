(function (blocks, element) {
    const el = element.createElement;

    blocks.registerBlockType('mt-tickets/topbar-menu', {
        edit: function () {
            // Editor preview placeholder
            return el(
                'nav',
                { className: 'mt-topbar-menu', 'aria-label': 'Top bar menu (preview)' },
                el('ul', { className: 'menu' }, [
                    el('li', null, el('a', { href: '#', onClick: function (e) { e.preventDefault(); } }, 'Help')),
                    el('li', null, el('a', { href: '#', onClick: function (e) { e.preventDefault(); } }, 'FAQ')),
                    el('li', null, el('a', { href: '#', onClick: function (e) { e.preventDefault(); } }, 'Contacts'))
                ])
            );
        },
        save: function () {
            return null; // dynamic (PHP) render
        }
    });
})(window.wp.blocks, window.wp.element);
