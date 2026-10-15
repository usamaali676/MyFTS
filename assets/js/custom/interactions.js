/*
 * Shared portal micro-interactions: animated count-up numbers,
 * a reusable empty-state HTML builder for DataTables, and toastr defaults.
 * Purely additive helpers - nothing here runs unless a page opts in.
 */
(function () {
  function formatNumber(value, decimals) {
    return value.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  function countUp(el) {
    var raw = (el.dataset.countup || '').replace(/,/g, '');
    var target = parseFloat(raw);
    if (isNaN(target)) return;

    var prefix = el.dataset.countupPrefix || '';
    var suffix = el.dataset.countupSuffix || '';
    var decimals = el.dataset.countupDecimals ? parseInt(el.dataset.countupDecimals, 10) : 0;
    var duration = 900;
    var start = null;

    function step(ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = prefix + formatNumber(eased * target, decimals) + suffix;
      if (p < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = prefix + formatNumber(target, decimals) + suffix;
      }
    }
    requestAnimationFrame(step);
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-countup]').forEach(function (el, i) {
      setTimeout(function () { countUp(el); }, i * 100);
    });

    if (window.toastr) {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 4000,
        newestOnTop: true
      };
    }
  });

  // Reused by DataTables `language.emptyTable` across list pages.
  window.rsEmptyStateHTML = function (noun) {
    noun = noun || 'records';
    return '<div class="rs-empty-state">' +
      '<div class="rs-empty-bot"><div class="rs-head"><div class="rs-face"><div class="rs-eye"></div><div class="rs-eye"></div></div></div></div>' +
      '<h6>No ' + noun + ' yet</h6>' +
      '<p>New ' + noun + ' will show up here once they come in.</p>' +
      '</div>';
  };

  // Shared click behaviour for notification items: mark as read (if unread),
  // then jump straight to the related lead - used by both the nav dropdown
  // and the full "view all notifications" page.
  window.rsInitNotifications = function (options) {
    var selector = options.selector || '.dropdown-notifications-item[data-notification-id]';
    var readUrlTemplate = options.readUrlTemplate;
    var leadEditUrlTemplate = options.leadEditUrlTemplate;
    var leadIndexUrl = options.leadIndexUrl;
    var onRead = options.onRead || function () {};
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    document.querySelectorAll(selector).forEach(function (item) {
      item.addEventListener('click', function () {
        var id = item.dataset.notificationId;
        var leadId = item.dataset.leadId;
        var leadName = item.dataset.leadName;

        function navigate() {
          if (leadId) {
            window.location.href = leadEditUrlTemplate.replace('LEAD_ID', leadId);
          } else if (leadName) {
            window.location.href = leadIndexUrl + '?search=' + encodeURIComponent(leadName);
          }
        }

        if (item.classList.contains('notif-unread') && readUrlTemplate && id) {
          fetch(readUrlTemplate.replace('NOTIF_ID', id), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
          })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            item.classList.remove('notif-unread');
            onRead(data.unread_count);
            navigate();
          })
          .catch(navigate);
        } else {
          navigate();
        }
      });
    });
  };
})();
