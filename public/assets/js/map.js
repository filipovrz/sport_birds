function initBsMap(containerId, markers, options) {
    options = options || {};
    const el = document.getElementById(containerId);
    if (!el || typeof L === 'undefined') {
        return null;
    }

    const latInput = options.latInput ? document.getElementById(options.latInput) : null;
    const lngInput = options.lngInput ? document.getElementById(options.lngInput) : null;
    let initialLat = parseFloat(latInput?.value);
    let initialLng = parseFloat(lngInput?.value);
    if (isNaN(initialLat) || isNaN(initialLng)) {
        initialLat = null;
        initialLng = null;
    }

    const center = options.center
        || (initialLat !== null && initialLng !== null ? [initialLat, initialLng] : [42.6977, 23.3219]);
    const zoom = options.zoom || (initialLat !== null ? 14 : 8);

    const map = L.map(containerId, { scrollWheelZoom: true }).setView(center, zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    const markerColors = {
        loft: '#1e5f8a',
        gps: '#c9a227',
        competition: '#2d8a5e',
        event: '#7c3aed',
    };

    function bsMapIcon(type) {
        const color = markerColors[type] || '#3388ff';
        return L.divIcon({
            className: 'bs-map-pin',
            html: '<span class="bs-map-pin-dot" style="background:' + color + '"></span>',
            iconSize: [18, 18],
            iconAnchor: [9, 9],
            popupAnchor: [0, -10],
        });
    }

    const markerLayers = [];

    (markers || []).forEach(function (m) {
        const marker = L.marker([m.lat, m.lng], { icon: bsMapIcon(m.type) }).addTo(map);
        markerLayers.push(marker);
        let html = '<strong>' + escapeHtml(m.title) + '</strong>';
        if (m.desc) {
            html += '<br>' + escapeHtml(m.desc);
        }
        if (m.url) {
            html += '<br><a href="' + m.url + '">Детайли</a>';
        }
        marker.bindPopup(html);
    });

    if (options.pickable) {
        let pickLayer = null;

        function setPosition(lat, lng, pan) {
            if (latInput) {
                latInput.value = lat.toFixed(6);
            }
            if (lngInput) {
                lngInput.value = lng.toFixed(6);
            }
            if (pickLayer) {
                map.removeLayer(pickLayer);
            }
            pickLayer = L.marker([lat, lng], { draggable: true }).addTo(map);
            pickLayer.on('dragend', function (ev) {
                const p = ev.target.getLatLng();
                if (latInput) {
                    latInput.value = p.lat.toFixed(6);
                }
                if (lngInput) {
                    lngInput.value = p.lng.toFixed(6);
                }
                if (typeof options.onPick === 'function') {
                    options.onPick(p.lat, p.lng);
                }
            });
            if (pan) {
                map.setView([lat, lng], Math.max(map.getZoom(), 14));
            }
            if (typeof options.onPick === 'function') {
                options.onPick(lat, lng);
            }
        }

        map.on('click', function (e) {
            setPosition(e.latlng.lat, e.latlng.lng, true);
        });

        if (initialLat !== null && initialLng !== null) {
            setPosition(initialLat, initialLng, false);
        }
    } else if (markerLayers.length) {
        try {
            const fg = L.featureGroup(markerLayers);
            map.fitBounds(fg.getBounds().pad(0.2));
        } catch (e) {
            /* ignore */
        }
    }

    setTimeout(function () {
        map.invalidateSize();
    }, 150);
    window.addEventListener('resize', function () {
        map.invalidateSize();
    });

    return map;
}

function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
