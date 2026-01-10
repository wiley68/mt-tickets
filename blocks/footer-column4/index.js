wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    if (!window.mtTicketsFooterColumn4Promise) {
        window.mtTicketsFooterColumn4Promise = wp.apiFetch({ path: '/mt-tickets/v1/footer-column4' })
            .catch(function () { return null; });
    }

    wp.blocks.registerBlockType('mt-tickets/footer-column4', {
        edit: function () {
            const [data, setData] = useState(null);

            useEffect(function () {
                window.mtTicketsFooterColumn4Promise.then(function (d) {
                    if (d) setData(d);
                });
            }, []);

            const title = (data && data.title) ? data.title : 'For Contact';
            const description = (data && data.description) ? data.description : 'Address:\nPhone:\nEmail:\nOpening hours:';

            // Split description by newlines for display
            const descriptionLines = description.split('\n').filter(line => line.trim());

            return el('div', {
                style: {
                    padding: '16px',
                    border: '1px solid #e2e8f0',
                    borderRadius: '4px',
                    backgroundColor: '#fff'
                }
            },
                el('h4', { 
                    style: { 
                        marginTop: '0', 
                        marginBottom: '12px',
                        fontWeight: '600'
                    } 
                }, title),
                el('div', { style: { marginBottom: '0' } },
                    descriptionLines.map(function (line, index) {
                        return el('p', { 
                            key: index,
                            style: { 
                                marginBottom: '8px',
                                marginTop: '0'
                            } 
                        }, line.trim());
                    })
                )
            );
        },
        save: function () { return null; }
    });
});
