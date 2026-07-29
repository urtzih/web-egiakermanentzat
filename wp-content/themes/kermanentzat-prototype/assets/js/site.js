(() => {
  const body = document.body;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const tickerTracks = document.querySelectorAll('.campaign-ticker__track');
  const copyButtons = document.querySelectorAll('[data-copy-value]');
  const revealItems = !reduceMotion.matches && 'IntersectionObserver' in window
    ? [...document.querySelectorAll('[data-reveal]')]
    : [];

  if (!reduceMotion.matches) {
    window.requestAnimationFrame(() => body.classList.add('motion-ready'));
  }

  const mobileNav = document.querySelector('.mobile-nav');
  if (mobileNav) {
    const mobileNavSummary = mobileNav.querySelector('summary');
    const desktopNavigation = window.matchMedia('(min-width: 48rem)');
    const coveredRegions = document.querySelectorAll('main, .campaign-ticker, .site-footer');

    const syncMobileNav = () => {
      const mobileMenuIsOpen = mobileNav.open && !desktopNavigation.matches;
      body.classList.toggle('mobile-menu-open', mobileMenuIsOpen);
      coveredRegions.forEach((region) => region.toggleAttribute('inert', mobileMenuIsOpen));
    };

    mobileNav.addEventListener('toggle', syncMobileNav);
    mobileNav.querySelectorAll('nav a').forEach((link) => {
      link.addEventListener('click', () => {
        mobileNav.open = false;
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape' || !mobileNav.open) return;
      mobileNav.open = false;
      mobileNavSummary.focus();
    });

    desktopNavigation.addEventListener('change', () => {
      if (desktopNavigation.matches) mobileNav.open = false;
      syncMobileNav();
    });

    syncMobileNav();
  }

  copyButtons.forEach((button) => {
    button.addEventListener('click', async () => {
      const value = button.dataset.copyValue;
      const feedback = document.querySelector(button.dataset.feedbackTarget || '');
      try {
        await navigator.clipboard.writeText(value);
        if (feedback) feedback.textContent = button.dataset.successMessage || 'Copiado.';
        if (button.dataset.analyticsEvent && window.kermanentzatAnalytics) {
          window.kermanentzatAnalytics.track(button.dataset.analyticsEvent);
        }
      } catch {
        if (feedback) feedback.textContent = value;
      }
    });
  });

  if (revealItems.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('reveal-visible');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: .08 });

    revealItems.forEach((item) => {
      item.classList.add('reveal-ready');
      observer.observe(item);
    });
  }

  if (tickerTracks.length) {
    document.addEventListener('visibilitychange', () => {
      tickerTracks.forEach((track) => {
        track.style.animationPlayState = document.hidden ? 'paused' : 'running';
      });
    });
  }
})();
