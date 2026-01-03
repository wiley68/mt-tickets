wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsHeaderbarPromise) {
        window.mtTicketsHeaderbarPromise = wp.apiFetch({ path: '/mt-tickets/v1/headerbar' }).catch(() => null);
    }

    wp.blocks.registerBlockType('mt-tickets/header-menu', {
        edit: function () {
            const [data, setData] = useState(null);
            useEffect(() => { window.mtTicketsHeaderbarPromise.then(d => d && setData(d)); }, []);

            const assigned = !!(data && data.menu && data.menu.assigned);
            const items = (data && data.menu && Array.isArray(data.menu.items)) ? data.menu.items : [];

            const list = assigned && items.length
                ? items.map((it, i) => el('li', { key: i }, el('a', { href: '#', onClick: (e) => e.preventDefault() }, it.title)))
                : [el('li', { key: 1 }, el('span', null, assigned ? 'Primary Menu (assigned)' : 'Primary Menu (not assigned)'))];

            return el('div', { className: 'mt-primary-menu' }, [
                el('button', { className: 'mt-hamburger', type: 'button' }, '☰'),
                el('ul', { className: 'menu' }, list),
            ]);
        },
        save: function () { return null; }
    });
});
