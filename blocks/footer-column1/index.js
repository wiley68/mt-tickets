wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsFooterbarPromise) {
        window.mtTicketsFooterbarPromise = wp.apiFetch({ path: '/mt-tickets/v1/footerbar' }).catch(() => null);
    }

    wp.blocks.registerBlockType('mt-tickets/footer-column1', {
        edit: function () {
            const [data, setData] = useState(null);
            useEffect(() => {
                window.mtTicketsFooterbarPromise.then(d => d && setData(d));
            }, []);

            const logoUrl = (data && data.logo && data.logo.url) ? data.logo.url : '';

            return el('div', {
                style: {
                    padding: '16px',
                    border: '1px solid #e2e8f0',
                    borderRadius: '4px',
                    backgroundColor: '#fff'
                }
            },
                el('h4', { style: { marginTop: '0', marginBottom: '12px' } }, 'About the Platform'),
                logoUrl ? el('a', {
                    href: '#',
                    onClick: (e) => e.preventDefault(),
                    style: {
                        display: 'inline-block',
                        marginBottom: '12px'
                    }
                },
                    el('img', {
                        src: logoUrl,
                        style: {
                            height: '44px',
                            width: 'auto',
                            display: 'block'
                        },
                        alt: 'Footer Logo'
                    })
                ) : el('div', { style: { marginBottom: '12px', color: '#999' } }, 'Footer Logo'),
                el('p', { style: { marginBottom: '0' } }, 'Ticket sales for carriers, schedules and reservations. The theme is independent of the plugin.')
            );
        },
        save: function () { return null; }
    });
});

