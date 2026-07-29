(() => {
  const banner = document.querySelector('[data-consent-banner]');
  const dialog = document.querySelector('[data-consent-dialog]');
  if (!banner || !dialog) return;

  const measurementId = banner.dataset.measurementId || '';
  const registryVersion = banner.dataset.registryVersion || '';
  const storageKey = banner.dataset.storageKey || '';
  const maxAgeMs = Number(banner.dataset.maxAgeDays || 183) * 24 * 60 * 60 * 1000;
  const analyticsInput = dialog.querySelector('[data-consent-analytics]');
  let analyticsLoaded = false;

  const readChoice = () => {
    try {
      const choice = JSON.parse(window.localStorage.getItem(storageKey) || 'null');
      const updatedAt = Date.parse(choice?.updatedAt || '');
      const isCurrent = choice?.version === registryVersion
        && typeof choice?.analytics === 'boolean'
        && Number.isFinite(updatedAt)
        && Date.now() - updatedAt <= maxAgeMs;
      if (isCurrent) return choice;
      window.localStorage.removeItem(storageKey);
      clearAnalyticsCookies();
    } catch {
      try {
        window.localStorage.removeItem(storageKey);
      } catch {
        // Consent remains unset when storage is unavailable.
      }
    }
    return null;
  };

  const writeChoice = (analytics) => {
    const choice = {
      version: registryVersion,
      analytics,
      updatedAt: new Date().toISOString(),
    };
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(choice));
    } catch {
      // Apply the current choice even when it cannot be remembered.
    }
    return choice;
  };

  const clearAnalyticsCookies = () => {
    document.cookie.split(';').forEach((cookie) => {
      const name = cookie.split('=', 1)[0].trim();
      if (name !== '_ga' && !name.startsWith('_ga_')) return;
      const expires = 'Thu, 01 Jan 1970 00:00:00 GMT';
      document.cookie = `${name}=; expires=${expires}; path=/; SameSite=Lax`;
      document.cookie = `${name}=; expires=${expires}; path=/; domain=.${window.location.hostname}; SameSite=Lax`;
    });
  };

  const loadAnalytics = () => {
    if (analyticsLoaded || !/^G-[A-Z0-9]{6,20}$/.test(measurementId)) return;
    analyticsLoaded = true;
    window[`ga-disable-${measurementId}`] = false;
    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function gtag() {
      window.dataLayer.push(arguments);
    };
    window.gtag('consent', 'default', {
      analytics_storage: 'denied',
      ad_storage: 'denied',
      ad_user_data: 'denied',
      ad_personalization: 'denied',
      functionality_storage: 'denied',
      personalization_storage: 'denied',
      security_storage: 'granted',
      wait_for_update: 500,
    });
    window.gtag('consent', 'update', {
      analytics_storage: 'granted',
    });
    window.gtag('js', new Date());
    window.gtag('config', measurementId, {
      allow_google_signals: false,
      allow_ad_personalization_signals: false,
      cookie_expires: 15811200,
      cookie_flags: 'SameSite=Lax;Secure',
      cookie_update: false,
      send_page_view: true,
    });

    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(measurementId)}`;
    document.head.append(script);
  };

  const disableAnalytics = () => {
    window[`ga-disable-${measurementId}`] = true;
    clearAnalyticsCookies();
  };

  const applyChoice = (choice) => {
    banner.hidden = true;
    analyticsInput.checked = choice.analytics;
    if (choice.analytics) loadAnalytics();
    else disableAnalytics();
  };

  const saveChoice = (analytics) => {
    const hadActiveAnalytics = analyticsLoaded;
    const choice = writeChoice(analytics);
    applyChoice(choice);
    if (!analytics && hadActiveAnalytics) window.location.reload();
  };

  const openDialog = () => {
    const choice = readChoice();
    analyticsInput.checked = choice?.analytics === true;
    if (typeof dialog.showModal === 'function') dialog.showModal();
    else dialog.setAttribute('open', '');
  };

  const closeDialog = () => {
    if (typeof dialog.close === 'function') dialog.close();
    else dialog.removeAttribute('open');
  };

  document.querySelectorAll('[data-consent-open]').forEach((button) => {
    button.addEventListener('click', openDialog);
  });
  banner.querySelector('[data-consent-accept]').addEventListener('click', () => saveChoice(true));
  banner.querySelector('[data-consent-reject]').addEventListener('click', () => saveChoice(false));
  banner.querySelector('[data-consent-configure]').addEventListener('click', openDialog);
  dialog.querySelector('[data-consent-dialog-reject]').addEventListener('click', () => {
    saveChoice(false);
    closeDialog();
  });
  dialog.querySelector('[data-consent-dialog-cancel]').addEventListener('click', closeDialog);
  dialog.querySelector('[data-consent-form]').addEventListener('submit', (event) => {
    event.preventDefault();
    saveChoice(analyticsInput.checked);
    closeDialog();
  });

  window.kermanentzatAnalytics = {
    track(eventName) {
      if (!analyticsLoaded || !['copy_iban', 'copy_bank_details'].includes(eventName)) return;
      window.gtag('event', eventName);
    },
  };

  const choice = readChoice();
  if (choice) applyChoice(choice);
  else banner.hidden = false;
})();
