/**
 * Refresh unified profile status chips every 10s (same rules as Manage Members list).
 */
(function () {
    var INTERVAL_MS = 10000;

    function baseUrl() {
        var b = document.body && document.body.getAttribute('data-admin-base-url');
        if (b) {
            return b;
        }
        return '';
    }

    function collectIds() {
        var seen = {};
        var out = [];
        document.querySelectorAll('.admin-profile-status-chip[data-user-id]').forEach(function (el) {
            var id = parseInt(el.getAttribute('data-user-id'), 10);
            if (id > 0 && !seen[id]) {
                seen[id] = true;
                out.push(id);
            }
        });
        return out;
    }

    function applyItem(meta) {
        var id = meta.id;
        if (!id) {
            return;
        }
        var sel = '.admin-profile-status-chip[data-user-id="' + id + '"]';
        document.querySelectorAll(sel).forEach(function (el) {
            el.className = 'admin-profile-status-chip approved-badge status-' + (meta.variant || 'unapproved');
            el.setAttribute('title', meta.title || '');
            var icon = meta.icon || 'fa-info-circle';
            el.innerHTML = '<i class="fa ' + icon + '" aria-hidden="true"></i> ' + (meta.label || '');
            var card = el.closest('.searchable-card');
            if (card) {
                if (meta.profile_status) {
                    card.setAttribute('data-status', meta.profile_status);
                }
                if (meta.registration_queued !== undefined && meta.registration_queued !== null) {
                    card.setAttribute('data-registration-queued', String(meta.registration_queued));
                }
            }
        });
    }

    function poll() {
        var ids = collectIds();
        if (!ids.length) {
            return;
        }
        var root = baseUrl();
        var url = root + '/admin/users/profile-status-json?ids=' + encodeURIComponent(ids.join(','));
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) {
                return;
            }
            if (xhr.status < 200 || xhr.status >= 300) {
                return;
            }
            try {
                var data = JSON.parse(xhr.responseText);
                if (!data || !data.ok || !data.items) {
                    return;
                }
                data.items.forEach(applyItem);
            } catch (e) {
                return;
            }
        };
        xhr.send();
    }

    function start() {
        if (collectIds().length === 0) {
            return;
        }
        setInterval(poll, INTERVAL_MS);
        setTimeout(poll, 1500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
