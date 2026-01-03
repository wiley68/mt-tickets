wp.domReady(function () {
    const el = wp.element.createElement;
    const useState = wp.element.useState;
    const useEffect = wp.element.useEffect;

    // Shared fetch (used by both blocks if both scripts run)
    if (!window.mtTicketsTopbarDataPromise) {
        window.mtTicketsTopbarDataPromise = wp.apiFetch({ path: '/mt-tickets/v1/topbar' })
            .catch(function () { return null; });
    }

    function iconSvg(iconKey) {
        // Same icons as render.php (simple inline SVG)
        const common = { width: 18, height: 18, viewBox: '0 0 24 24', fill: 'none', xmlns: 'http://www.w3.org/2000/svg' };
        const strokeProps = { stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' };

        if (iconKey === 'email') {
            return el('svg', common, [
                el('path', Object.assign({ d: 'M4 6h16v12H4z' }, strokeProps)),
                el('path', Object.assign({ d: 'M4 7l8 6 8-6' }, strokeProps)),
            ]);
        }

        if (iconKey === 'info') {
            return el('svg', common, [
                el('circle', Object.assign({ cx: 12, cy: 12, r: 10 }, strokeProps)),
                el('path', Object.assign({ d: 'M12 10v6' }, strokeProps)),
                el('path', Object.assign({ d: 'M12 7h.01' }, strokeProps)),
            ]);
        }

        // phone default
        return el('svg', common, [
            el('path', Object.assign({
                d: 'M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.86.31 1.7.57 2.5a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.58-1.09a2 2 0 0 1 2.11-.45c.8.26 1.64.45 2.5.57A2 2 0 0 1 22 16.92z'
            }, strokeProps))
        ]);
    }

    wp.blocks.registerBlockType('mt-tickets/topbar-left', {
        edit: function () {
            const [data, setData] = useState(null);

            useEffect(function () {
                window.mtTicketsTopbarDataPromise.then(function (d) {
                    if (d) setData(d);
                });
            }, []);

            const iconKey = (data && data.icon) ? data.icon : 'phone';
            const text = (data && data.text) ? data.text : 'For contact: 555 555 555';

            return el('div', { className: 'mt-topbar-left' }, [
                el('span', { className: 'mt-topbar-left__icon', 'aria-hidden': true }, iconSvg(iconKey)),
                el('span', { className: 'mt-topbar-left__text' }, text)
            ]);
        },
        save: function () { return null; }
    });
});
