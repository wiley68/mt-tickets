wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsFooterbarPromise) {
        window.mtTicketsFooterbarPromise = wp.apiFetch({ path: '/mt-tickets/v1/footerbar' }).catch(() => null);
    }

    wp.blocks.registerBlockType('mt-tickets/footer-logo', {
        edit: function () {
            const [data, setData] = useState(null);
            useEffect(() => { window.mtTicketsFooterbarPromise.then(d => d && setData(d)); }, []);
            const url = (data && data.logo && data.logo.url) ? data.logo.url : '';
            return el('a', { href: '#', onClick: (e) => e.preventDefault(), style: { display: 'inline-flex', alignItems: 'center' } },
                url ? el('img', { src: url, style: { height: '44px', width: 'auto' } }) : 'Footer Logo'
            );
        },
        save: function () { return null; }
    });
});

