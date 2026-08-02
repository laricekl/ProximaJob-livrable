<div id="proxima-dialog" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-primary/55 px-4 backdrop-blur-sm" role="presentation">
  <section class="w-full max-w-md rounded-3xl border border-white/40 bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="proxima-dialog-title" aria-describedby="proxima-dialog-message">
    <div class="flex items-start gap-4">
      <div id="proxima-dialog-icon" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-secondary-container/10 text-secondary-container">
        <span class="material-symbols-outlined">help</span>
      </div>
      <div class="min-w-0 flex-1">
        <h2 id="proxima-dialog-title" class="font-serif text-xl font-bold text-primary">Confirmation</h2>
        <p id="proxima-dialog-message" class="mt-2 text-sm leading-6 text-on-surface-variant"></p>
      </div>
    </div>

    <div id="proxima-dialog-input-wrap" class="mt-5 hidden">
      <label id="proxima-dialog-input-label" for="proxima-dialog-input" class="mb-2 block text-sm font-semibold text-primary">Mot de passe</label>
      <input id="proxima-dialog-input" type="password" autocomplete="current-password" class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-sm text-primary outline-none transition focus:border-secondary-container focus:ring-2 focus:ring-secondary-container/20">
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
      <button id="proxima-dialog-cancel" type="button" class="rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-primary transition hover:bg-surface-container-low">Annuler</button>
      <button id="proxima-dialog-confirm" type="button" class="rounded-xl bg-primary-container px-5 py-2.5 text-sm font-bold text-white transition hover:opacity-90">Confirmer</button>
    </div>
  </section>
</div>

<div id="proxima-toast-region" class="pointer-events-none fixed right-4 top-24 z-[10001] flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-3" aria-live="polite" aria-atomic="true"></div>

<script>
  (() => {
    const overlay = document.getElementById('proxima-dialog');
    const title = document.getElementById('proxima-dialog-title');
    const message = document.getElementById('proxima-dialog-message');
    const iconWrap = document.getElementById('proxima-dialog-icon');
    const icon = iconWrap.querySelector('.material-symbols-outlined');
    const inputWrap = document.getElementById('proxima-dialog-input-wrap');
    const inputLabel = document.getElementById('proxima-dialog-input-label');
    const input = document.getElementById('proxima-dialog-input');
    const cancelButton = document.getElementById('proxima-dialog-cancel');
    const confirmButton = document.getElementById('proxima-dialog-confirm');
    const toastRegion = document.getElementById('proxima-toast-region');
    let resolveDialog = null;
    let dialogMode = 'confirm';
    let previousFocus = null;

    const closeDialog = (value) => {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.style.overflow = '';
      const resolver = resolveDialog;
      resolveDialog = null;
      previousFocus?.focus?.();
      resolver?.(value);
    };

    const openDialog = (mode, text, options = {}) => new Promise((resolve) => {
      if (resolveDialog) closeDialog(dialogMode === 'prompt' ? null : false);

      dialogMode = mode;
      resolveDialog = resolve;
      previousFocus = document.activeElement;
      title.textContent = options.title || (mode === 'prompt' ? 'Vérification requise' : 'Confirmation');
      message.textContent = text;
      cancelButton.textContent = options.cancelLabel || 'Annuler';
      confirmButton.textContent = options.confirmLabel || 'Confirmer';

      const destructive = options.tone === 'danger';
      icon.textContent = destructive ? 'warning' : (mode === 'prompt' ? 'lock' : 'help');
      iconWrap.className = `flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ${destructive ? 'bg-error-light text-error' : 'bg-secondary-container/10 text-secondary-container'}`;
      confirmButton.className = `rounded-xl px-5 py-2.5 text-sm font-bold text-white transition hover:opacity-90 ${destructive ? 'bg-error' : 'bg-primary-container'}`;

      inputWrap.classList.toggle('hidden', mode !== 'prompt');
      inputLabel.textContent = options.inputLabel || 'Mot de passe';
      input.type = options.inputType || 'password';
      input.value = '';
      input.placeholder = options.placeholder || '';

      overlay.classList.remove('hidden');
      overlay.classList.add('flex');
      document.body.style.overflow = 'hidden';
      window.setTimeout(() => (mode === 'prompt' ? input : confirmButton).focus(), 0);
    });

    cancelButton.addEventListener('click', () => closeDialog(dialogMode === 'prompt' ? null : false));
    confirmButton.addEventListener('click', () => closeDialog(dialogMode === 'prompt' ? input.value : true));
    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) closeDialog(dialogMode === 'prompt' ? null : false);
    });
    input.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        closeDialog(input.value);
      }
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && resolveDialog) closeDialog(dialogMode === 'prompt' ? null : false);
    });

    const notify = (text, tone = 'info') => {
      const toast = document.createElement('div');
      const toneClasses = tone === 'error'
        ? 'border-error/20 bg-error-light text-error-dark'
        : tone === 'success'
          ? 'border-success/20 bg-success-light text-success-dark'
          : 'border-primary/10 bg-white text-primary';
      toast.className = `pointer-events-auto flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-semibold shadow-xl ${toneClasses}`;

      const toastIcon = document.createElement('span');
      toastIcon.className = 'material-symbols-outlined text-lg';
      toastIcon.textContent = tone === 'error' ? 'error' : tone === 'success' ? 'check_circle' : 'info';
      const toastText = document.createElement('span');
      toastText.className = 'flex-1 leading-5';
      toastText.textContent = text;
      toast.append(toastIcon, toastText);
      toastRegion.appendChild(toast);
      window.setTimeout(() => toast.remove(), 5000);
    };

    window.ProximaUI = {
      confirm: (text, options = {}) => openDialog('confirm', text, options),
      prompt: (text, options = {}) => openDialog('prompt', text, options),
      notify,
    };

    document.addEventListener('submit', async (event) => {
      const form = event.target.closest('form[data-proxima-confirm]');
      if (!form) return;
      if (form.dataset.proximaConfirmed === 'true') {
        delete form.dataset.proximaConfirmed;
        return;
      }

      event.preventDefault();
      const approved = await window.ProximaUI.confirm(form.dataset.proximaConfirm, {
        title: form.dataset.proximaTitle || 'Confirmer cette action',
        confirmLabel: form.dataset.proximaConfirmLabel || 'Confirmer',
        tone: form.dataset.proximaTone || 'danger',
      });
      if (approved) {
        form.dataset.proximaConfirmed = 'true';
        form.requestSubmit(event.submitter || undefined);
      }
    }, true);
  })();
</script>
