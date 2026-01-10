wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsHeaderHeroPromise) {
        window.mtTicketsHeaderHeroPromise = wp.apiFetch({ path: '/mt-tickets/v1/header-hero' }).catch(() => null);
    }

    wp.blocks.registerBlockType('mt-tickets/header-hero', {
        edit: function () {
            const [data, setData] = useState(null);
            useEffect(() => {
                window.mtTicketsHeaderHeroPromise.then(d => d && setData(d));
            }, []);

            const title = (data && data.title) ? data.title : 'Buy a bus ticket quickly and conveniently';
            const description = (data && data.description) ? data.description : 'Search by destination, date and carrier';
            const hasShortcode = (data && data.shortcode && data.shortcode.trim() !== '');

            return el('div', {
                style: {
                    padding: 'var(--wp--preset--spacing--xl)',
                    backgroundColor: 'var(--wp--preset--color--primary)',
                    borderRadius: '8px',
                    minHeight: '200px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: '#FFFFFF',
                    textAlign: 'center'
                }
            },
                el('p', {
                    style: {
                        margin: '0',
                        fontSize: '18px',
                        fontWeight: '600'
                    }
                }, 'Hero block')
            );
        },
        save: function () { return null; }
    });
});
