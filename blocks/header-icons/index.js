wp.domReady(function () {
    const el = wp.element.createElement;

    function userSvg() {
        return el('svg', { width: 20, height: 20, viewBox: '0 0 24 24', fill: 'none', 'aria-hidden': true, xmlns: 'http://www.w3.org/2000/svg' }, [
            el('path', { d: 'M20 21a8 8 0 0 0-16 0', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round' }),
            el('circle', { cx: 12, cy: 8, r: 4, stroke: 'currentColor', 'stroke-width': 2 }),
        ]);
    }

    function cartSvg() {
        return el('svg', { width: 20, height: 20, viewBox: '0 0 24 24', fill: 'none', 'aria-hidden': true, xmlns: 'http://www.w3.org/2000/svg' }, [
            el('path', { d: 'M6 6h15l-2 9H7L6 6Z', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linejoin': 'round' }),
            el('path', { d: 'M6 6 5 3H2', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round' }),
            el('circle', { cx: 9, cy: 20, r: 1.5, fill: 'currentColor' }),
            el('circle', { cx: 18, cy: 20, r: 1.5, fill: 'currentColor' }),
        ]);
    }

    wp.blocks.registerBlockType('mt-tickets/header-icons', {
        edit: function () {
            return el('div', { className: 'mt-header-icons' }, [
                el('button', { className: 'mt-header-icon-btn', type: 'button' }, userSvg()),
                el('button', { className: 'mt-header-icon-btn', type: 'button' }, [
                    cartSvg(),
                    // Preview badge placeholder
                    el('span', { className: 'mt-cart-badge' }, '2')
                ])
            ]);
        },
        save: function () { return null; }
    });
});
