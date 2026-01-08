wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsFooterColumn3Promise) {
        window.mtTicketsFooterColumn3Promise = wp.apiFetch({ path: '/mt-tickets/v1/footer-column3' })
            .catch(function () { return null; });
    }

    function labelStyle() {
        return {
            display: 'inline-flex',
            alignItems: 'center',
            padding: '8px 10px',
            border: '1px dashed #cbd5e1',
            borderRadius: '10px',
            fontSize: '13px',
            lineHeight: '1',
            opacity: 0.9,
            maxWidth: '100%'
        };
    }

    wp.blocks.registerBlockType('mt-tickets/footer-column3', {
        edit: function () {
            const [data, setData] = useState(null);

            useEffect(function () {
                window.mtTicketsFooterColumn3Promise.then(function (d) {
                    if (d) setData(d);
                });
            }, []);

            const assigned = !!(data && data.menu && data.menu.assigned);
            const name = (data && data.menu && data.menu.name) ? data.menu.name : '';

            const text = data
                ? (assigned ? ('Menu: ' + (name || 'Footer Column 3 Menu')) : 'No menu assigned (Footer Column 3 Menu)')
                : 'Menu: loading…';

            return el('div', { className: 'mt-footer-column3-menu' },
                el('div', { style: labelStyle() }, text)
            );
        },
        save: function () { return null; }
    });
});
