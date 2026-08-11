(() => {
  const select = document.querySelector('#kerman_update_type');
  const groups = document.querySelectorAll('[data-kerman-types]');
  if (select && groups.length > 0) {
    const update = () => {
      groups.forEach((group) => {
        const types = (group.dataset.kermanTypes || '').split(/\s+/);
        group.hidden = !types.includes(select.value);
      });
    };

    select.addEventListener('change', update);
    update();
  }

  const language = document.querySelector('#_kerman_language');
  const translation = document.querySelector('#kerman_translation_post_id');
  if (language && translation) {
    const updateTranslations = () => {
      const requiredLanguage = language.value === 'es' ? 'eu' : 'es';
      Array.from(translation.options).forEach((option) => {
        if (!option.dataset.kermanLanguage) return;
        option.hidden = option.dataset.kermanLanguage !== requiredLanguage;
        option.disabled = option.hidden;
      });
      if (translation.selectedOptions[0]?.disabled) {
        translation.value = '0';
      }
    };

    language.addEventListener('change', updateTranslations);
    updateTranslations();
  }
})();
