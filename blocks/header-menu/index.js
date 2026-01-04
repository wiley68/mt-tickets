wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsHeaderbarPromise) {
        window.mtTicketsHeaderbarPromise = wp.apiFetch({ path: '/mt-tickets/v1/headerbar' })
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
            opacity: 0.9
        };
    }

    wp.blocks.registerBlockType('mt-tickets/header-menu', {
        edit: function () {
            const [data, setData] = useState(null);

            useEffect(function () {
                window.mtTicketsHeaderbarPromise.then(function (d) {
                    if (d) setData(d);
                });
            }, []);

            const assigned = !!(data && data.menu && data.menu.assigned);
            const name = (data && data.menu && data.menu.name) ? data.menu.name : '';

            const text = data
                ? (assigned ? ('Menu: ' + (name || 'Primary Menu')) : 'No menu assigned (Primary Menu)')
                : 'Menu: loading…';

            return el('div', { className: 'mt-primary-menu', style: { display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '10px' } }, [
                el('button', { className: 'mt-hamburger', type: 'button', disabled: true, style: { opacity: 0.6 } }, '☰'),
                el('div', { style: labelStyle() }, text)
            ]);
        },
        save: function () { return null; }
    });
});
