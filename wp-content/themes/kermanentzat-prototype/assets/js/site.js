(() => {
  const body = document.body;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

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

  document.querySelectorAll('[data-copy-value]').forEach((button) => {
    button.addEventListener('click', async () => {
      const value = button.dataset.copyValue;
      const feedback = document.querySelector(button.dataset.feedbackTarget || '');
      try {
        await navigator.clipboard.writeText(value);
        if (feedback) feedback.textContent = button.dataset.successMessage || 'Copiado.';
      } catch {
        if (feedback) feedback.textContent = value;
      }
    });
  });

  if (!reduceMotion.matches && 'IntersectionObserver' in window) {
    const items = [...document.querySelectorAll('[data-reveal]')];
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('reveal-visible');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: .08 });

    items.forEach((item) => {
      item.classList.add('reveal-ready');
      observer.observe(item);
    });
  }

  document.addEventListener('visibilitychange', () => {
    document.querySelectorAll('.campaign-ticker__track').forEach((track) => {
      track.style.animationPlayState = document.hidden ? 'paused' : 'running';
    });
  });
})();
