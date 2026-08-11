(() => {
  const sdkUrl = 'https://cdn.sender.net/accounts_resources/universal.js?explicit=true';
  let sdkPromise;

  const prepareQueue = (accountId) => {
    window.Sender = 'sender';
    window.sender = window.sender || function senderQueue() {
      (window.sender.q = window.sender.q || []).push(arguments);
    };
    window.sender.l = window.sender.l || Date.now();
    window.sender.on = window.sender.on || function senderListener(event, callback) {
      window.sender.listeners = window.sender.listeners || {};
      (window.sender.listeners[event] = window.sender.listeners[event] || []).push(callback);
    };
    window.sender(accountId);
  };

  const loadSdk = (accountId) => {
    prepareQueue(accountId);
    if (window.senderFormsLoaded && window.senderForms) return Promise.resolve();
    if (sdkPromise) return sdkPromise;

    sdkPromise = new Promise((resolve, reject) => {
      const timeout = window.setTimeout(() => reject(new Error('Sender SDK timeout')), 15000);
      const loaded = () => {
        if (window.senderFormsLoaded && window.senderForms) {
          window.clearTimeout(timeout);
          resolve();
        }
      };
      window.addEventListener('onSenderFormsLoaded', loaded, { once: true });

      const script = document.createElement('script');
      script.src = sdkUrl;
      script.async = true;
      script.addEventListener('load', loaded, { once: true });
      script.addEventListener('error', () => {
        window.clearTimeout(timeout);
        reject(new Error('Sender SDK unavailable'));
      }, { once: true });
      document.head.append(script);
    });
    const currentPromise = sdkPromise;
    currentPromise.catch(() => {
      if (sdkPromise === currentPromise) sdkPromise = undefined;
    });

    return sdkPromise;
  };

  document.querySelectorAll('[data-kerman-subscription]').forEach((module) => {
    const button = module.querySelector('[data-open-subscription]');
    const container = module.querySelector('[data-subscription-form-container]');
    const status = module.querySelector('[data-subscription-status]');
    const accountId = module.dataset.accountId;
    const formId = module.dataset.formId;
    const autoLoad = module.hasAttribute('data-auto-load');
    if (!button || !container || !status || !accountId || !formId) return;

    const showForm = async () => {
      module.classList.remove('has-load-error');
      button.disabled = true;
      button.setAttribute('aria-busy', 'true');
      button.hidden = true;
      status.textContent = status.dataset.loadingText || '';
      container.hidden = false;

      try {
        await loadSdk(accountId);
        await new Promise((resolve, reject) => {
          const timeout = window.setTimeout(() => {
            const formStatus = window.senderForms.getStatus(formId)?.[formId];
            reject(new Error(`Sender form ${formStatus || 'unavailable'}`));
          }, 12000);

          window.senderForms.render(formId, {
            initialStatus: 'enabled',
            onRender(renderedId) {
              if (renderedId !== formId) return;
              window.clearTimeout(timeout);
              resolve();
            },
          });
        });
        status.textContent = '';
        button.hidden = true;
        button.removeAttribute('aria-busy');
        if (!autoLoad) container.focus({ preventScroll: true });
      } catch (error) {
        module.classList.add('has-load-error');
        container.hidden = true;
        status.textContent = status.dataset.errorText || '';
        button.hidden = false;
        button.disabled = false;
        button.removeAttribute('aria-busy');
      }
    };

    button.addEventListener('click', showForm);
    if (autoLoad) showForm();
  });
})();
