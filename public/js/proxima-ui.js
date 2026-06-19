/* ── ProximaJob UI Behaviors ── */

function initProximaUi() {
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
        const duration = 2000;
        function update(now) {
          const p = Math.min((now - start) / duration, 1);
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
