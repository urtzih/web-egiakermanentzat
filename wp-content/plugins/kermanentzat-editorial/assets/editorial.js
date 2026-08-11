(() => {
  const shareRoots = [...document.querySelectorAll('[data-share-root]')];
  if (!shareRoots.length) return;

  const controllers = new Map();

  const copyText = async (value) => {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(value);
      return;
    }

    const input = document.createElement('textarea');
    input.value = value;
    input.setAttribute('readonly', '');
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.append(input);
    input.select();
    const copied = document.execCommand('copy');
    input.remove();
    if (!copied) throw new Error('Copy failed');
  };

  const closeOthers = (currentRoot) => {
    controllers.forEach((controller, root) => {
      if (root !== currentRoot) controller.close();
    });
  };

  shareRoots.forEach((root) => {
    const trigger = root.querySelector('[data-share-trigger]');
    const menu = root.querySelector('[data-share-menu]');
    const copyButton = root.querySelector('[data-share-copy]');
    const actions = root.closest('.kerman-card__actions');
    const feedback = actions?.querySelector('[data-share-feedback]');
    if (!trigger || !menu || !copyButton) return;

    const open = () => {
      closeOthers(root);
      menu.hidden = false;
      trigger.setAttribute('aria-expanded', 'true');
    };

    const close = () => {
      menu.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
    };

    const toggle = () => {
      if (menu.hidden) open();
      else close();
    };

    controllers.set(root, { close, trigger });

    trigger.addEventListener('click', async () => {
      if (feedback) feedback.textContent = '';
      const title = root.dataset.shareTitle || document.title;
      const url = root.dataset.shareUrl || window.location.href;
      const nativeShareAvailable = window.matchMedia('(pointer: coarse)').matches
        && typeof navigator.share === 'function';

      if (!nativeShareAvailable) {
        toggle();
        return;
      }

      try {
        await navigator.share({ title, url });
      } catch (error) {
        if (error?.name !== 'AbortError') open();
      }
    });

    menu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        close();
        trigger.focus();
      });
    });

    copyButton.addEventListener('click', async () => {
      try {
        await copyText(root.dataset.shareUrl || window.location.href);
        close();
        trigger.focus();
        if (feedback) feedback.textContent = feedback.dataset.successMessage || '';
      } catch {
        if (feedback) feedback.textContent = root.dataset.shareUrl || window.location.href;
      }
    });
  });

  document.addEventListener('click', (event) => {
    controllers.forEach((controller, root) => {
      if (!root.contains(event.target)) controller.close();
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    controllers.forEach((controller, root) => {
      const menu = root.querySelector('[data-share-menu]');
      if (!menu?.hidden) {
        controller.close();
        controller.trigger.focus();
      }
    });
  });
})();
