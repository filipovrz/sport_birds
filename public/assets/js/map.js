function initBsMap(containerId, markers, options) {
    options = options || {};
    const el = document.getElementById(containerId);
    if (!el || typeof L === 'undefined') return null;

    const map = L.map(containerId).setView(
        options.center || [42.6977, 23.3219],
        options.zoom || 7
    );
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    const colors = { loft: '#1e5f8a', gps: '#e8a838', event: '#2d8a5e' };
    (markers || []).forEach(function (m) {
        const marker = L.marker([m.lat, m.lng]).addTo(map);
        let html = '<strong>' + escapeHtml(m.title) + '</strong>';
        if (m.desc) html += '<br>' + escapeHtml(m.desc);
        if (m.url) html += '<br><a href="' + m.url + '">Детайли</a>';
        marker.bindPopup(html);
    });

    if (options.pickable) {
        map.on('click', function (e) {
            if (options.latInput) document.getElementById(options.latInput).value = e.latlng.lat.toFixed(6);
            if (options.lngInput) document.getElementById(options.lngInput).value = e.latlng.lng.toFixed(6);
            if (options.pickMarker) {
                if (options._pickLayer) map.removeLayer(options._pickLayer);
                options._pickLayer = L.marker(e.latlng).addTo(map);
            }
        });
        const lat = parseFloat(document.getElementById(options.latInput)?.value);
        const lng = parseFloat(document.getElementById(options.lngInput)?.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            options._pickLayer = L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 14);
        }
    } else if (markers.length) {
        const group = L.featureGroup(map._layers);
        try {
            const fg = L.featureGroup(Object.values(map._layers).filter(l => l.getLatLng));
            if (fg.getLayers().length) map.fitBounds(fg.getBounds().pad(0.2));
        } catch (e) {}
    }

    return map;
}

function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
