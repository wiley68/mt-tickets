wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsTopbarDataPromise) {
        window.mtTicketsTopbarDataPromise = wp.apiFetch({ path: '/mt-tickets/v1/topbar' })
            .catch(function () { return null; });
    }

    wp.blocks.registerBlockType('mt-tickets/topbar-menu', {
        edit: function () {
            const [data, setData] = useState(null);

            useEffect(function () {
                window.mtTicketsTopbarDataPromise.then(function (d) {
                    if (d) setData(d);
                });
            }, []);

            const assigned = !!(data && data.menu && data.menu.assigned);
            const items = (data && data.menu && Array.isArray(data.menu.items)) ? data.menu.items : [];

            // If assigned and has items: show them; else show assigned label + generic items;
            let listItems;
            if (assigned) {
                if (items.length) {
                    listItems = items.map(function (it, idx) {
                        return el('li', { key: idx }, el('a', { href: '#', onClick: function (e) { e.preventDefault(); } }, it.title));
                    });
                } else {
                    listItems = [
                        el('li', { key: 1 }, el('span', null, 'Top Bar Menu (assigned)'))
                    ];
                }
            } else {
                listItems = [
                    el('li', { key: 1 }, el('a', { href: '#', onClick: function (e) { e.preventDefault(); } }, 'Help')),
                    el('li', { key: 2 }, el('a', { href: '#', onClick: function (e) { e.preventDefault(); } }, 'FAQ')),
                    el('li', { key: 3 }, el('a', { href: '#', onClick: function (e) { e.preventDefault(); } }, 'Contacts'))
                ];
            }

            return el('nav', { className: 'mt-topbar-menu', 'aria-label': 'Top bar menu (preview)' },
                el('ul', { className: 'menu' }, listItems)
            );
        },
        save: function () { return null; }
    });
});
