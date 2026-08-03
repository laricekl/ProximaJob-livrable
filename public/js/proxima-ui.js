/* ── ProximaJob UI Behaviors ── */

function initProximaUi() {
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Page navigation progress */
  const navigationProgress = document.createElement('div');
  navigationProgress.className = 'proxima-navigation-progress';
  navigationProgress.setAttribute('role', 'progressbar');
  navigationProgress.setAttribute('aria-label', 'Chargement de la page');
  navigationProgress.setAttribute('aria-hidden', 'true');
  document.body.appendChild(navigationProgress);

  const startNavigationProgress = () => {
    if (reducedMotion) return;
    navigationProgress.classList.remove('is-complete');
    navigationProgress.classList.add('is-active');
    navigationProgress.setAttribute('aria-hidden', 'false');
  };

  const completeNavigationProgress = () => {
    navigationProgress.classList.remove('is-active');
    navigationProgress.classList.add('is-complete');
    navigationProgress.setAttribute('aria-hidden', 'true');
    window.setTimeout(() => navigationProgress.classList.remove('is-complete'), 300);
  };

  const resetBusyForms = () => {
    document.querySelectorAll('form[data-proxima-busy="true"]').forEach((form) => {
      delete form.dataset.proximaBusy;
      form.removeAttribute('aria-busy');
      form.querySelectorAll('.proxima-is-busy').forEach((submitter) => {
        submitter.classList.remove('proxima-is-busy');
        submitter.removeAttribute('aria-disabled');
        submitter.querySelectorAll('.proxima-button-spinner').forEach((spinner) => spinner.remove());
      });
    });
  };

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    if (link.target && link.target !== '_self') return;
    if (link.hasAttribute('download') || link.dataset.noProgress !== undefined) return;

    const url = new URL(link.href, window.location.href);
    if (!['http:', 'https:'].includes(url.protocol) || url.origin !== window.location.origin) return;
    if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
    startNavigationProgress();
  });

  window.addEventListener('pageshow', () => {
    completeNavigationProgress();
    resetBusyForms();
  });

  /* Generic form busy state */
  document.addEventListener('submit', (event) => {
    const form = event.target.closest('form');
    if (!form || form.dataset.noBusy !== undefined) return;

    if (form.dataset.proximaBusy === 'true') {
      event.preventDefault();
      return;
    }

    const submitter = event.submitter;
    window.queueMicrotask(() => {
      if (event.defaultPrevented || form.dataset.proximaBusy === 'true') return;
      form.dataset.proximaBusy = 'true';
      form.setAttribute('aria-busy', 'true');

      if (submitter) {
        submitter.classList.add('proxima-is-busy');
        submitter.setAttribute('aria-disabled', 'true');
        const spinner = document.createElement('span');
        spinner.className = 'proxima-button-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        submitter.appendChild(spinner);
      }

      startNavigationProgress();
    });
  });

  /* Scroll Reveal */
  const revealElements = document.querySelectorAll('.reveal');

  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          revealObserver.unobserve(e.target);
        }
      });
    }, { threshold: 0.01, rootMargin: '120px 0px -10% 0px' });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('visible'));
  }

  /* Animated Counters */
  const counterElements = document.querySelectorAll('.counter');

  if ('IntersectionObserver' in window) {
    const counterObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = parseInt(el.dataset.target);
        const start = performance.now();
        const duration = reducedMotion ? 0 : 1200;
        function update(now) {
          const p = duration === 0 ? 1 : Math.min((now - start) / duration, 1);
          el.textContent = Math.floor((1 - Math.pow(1 - p, 3)) * target).toLocaleString('fr-FR');
          if (p < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
        counterObserver.unobserve(el);
      });
    }, { threshold: 0.4 });

    counterElements.forEach(el => counterObserver.observe(el));
  }

  /* Ripple Effect */
  document.querySelectorAll('.ripple').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const r = document.createElement('span');
      r.className = 'ripple-effect';
      const rect = this.getBoundingClientRect();
      const s = Math.max(rect.width, rect.height);
      Object.assign(r.style, { width: s+'px', height: s+'px', left: (e.clientX-rect.left-s/2)+'px', top: (e.clientY-rect.top-s/2)+'px' });
      this.appendChild(r);
      r.addEventListener('animationend', () => r.remove());
    });
  });

  /* Sparklines */
  document.querySelectorAll('.sparkline').forEach(el => {
    const vals = el.dataset.values.split(',').map(Number);
    const max = Math.max(...vals);
    vals.forEach(v => {
      const bar = document.createElement('div');
      bar.className = 'sparkline-bar flex-1 rounded-t-sm bg-secondary-container/60 hover:bg-secondary-container transition-colors cursor-pointer relative group';
      bar.style.height = (v/max*100)+'%';
      bar.innerHTML = `<span class="opacity-0 group-hover:opacity-100 absolute -top-5 left-1/2 -translate-x-1/2 text-[10px] font-bold text-primary whitespace-nowrap">${v}</span>`;
      el.appendChild(bar);
    });
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initProximaUi, { once: true });
} else {
  initProximaUi();
}

/* Toast Notifications */
function showToast(type, msg) {
  const c = document.getElementById('toastContainer');
  if (!c) return;
  const t = document.createElement('div');
  const isInfo = type === 'info';
  const color = isInfo ? '#2462B7' : '#EB843C';
  const icon = isInfo ? 'info' : 'check_circle';
  const border = isInfo ? 'border-l-blue-600' : 'border-l-secondary-container';
  t.className = `toast bg-white rounded-xl shadow-lg border border-outline-variant/10 pr-4 pl-1 py-1 flex items-center gap-2 min-w-[300px] ${border} border-l-4`;
  t.innerHTML = `<span class="material-symbols-outlined text-sm" style="color:${color}">${icon}</span><span class="text-sm text-primary flex-1">${msg}</span><button class="text-outline hover:text-primary transition-colors" onclick="this.closest('.toast').remove()"><span class="material-symbols-outlined text-sm">close</span></button><div class="toast-progress absolute bottom-0 left-0 h-[3px] rounded-r-full" style="width:100%;background:${color}"></div>`;
  c.appendChild(t);
  requestAnimationFrame(() => t.classList.add('show'));
  setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3800);
}
