wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsFooterCopyrightPromise) {
        window.mtTicketsFooterCopyrightPromise = wp.apiFetch({ path: '/mt-tickets/v1/footer-copyright' })
            .catch(function () { return null; });
    }

    wp.blocks.registerBlockType('mt-tickets/footer-copyright', {
        edit: function () {
            const [data, setData] = useState(null);

            useEffect(function () {
                window.mtTicketsFooterCopyrightPromise.then(function (d) {
                    if (d) setData(d);
                });
            }, []);

            const copyright = (data && data.copyright) ? data.copyright : '© MT Tickets. All rights reserved.';
            const imageUrl = (data && data.image && data.image.url) ? data.image.url : '';

            return el('div', {
                style: {
                    padding: '16px',
                    border: '1px solid #e2e8f0',
                    borderRadius: '4px',
                    backgroundColor: '#fff',
                    minHeight: '70px',
                    display: 'flex',
                    alignItems: 'center'
                }
            },
                el('div', {
                    style: {
                        display: 'flex',
                        width: '100%',
                        justifyContent: 'space-between',
                        alignItems: 'center'
                    }
                },
                    el('div', {
                        style: {
                            flex: '1'
                        }
                    },
                        el('p', {
                            style: {
                                margin: '0',
                                color: 'var(--wp--preset--color--muted, #64748b)',
                                fontSize: '14px'
                            }
                        }, copyright)
                    ),
                    el('div', {
                        style: {
                            display: 'flex',
                            alignItems: 'center'
                        }
                    },
                        imageUrl
                            ? el('img', {
                                src: imageUrl,
                                alt: 'Payment methods',
                                style: {
                                    height: '24px',
                                    width: 'auto',
                                    display: 'block'
                                }
                            })
                            : el('div', {
                                style: {
                                    color: '#999',
                                    fontSize: '12px'
                                }
                            }, 'Payment Icons Image')
                    )
                )
            );
        },
        save: function () { return null; }
    });
});
