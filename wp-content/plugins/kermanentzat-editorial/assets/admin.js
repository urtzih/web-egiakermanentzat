(() => {
  const select = document.querySelector('#kerman_update_type');
  const groups = document.querySelectorAll('[data-kerman-types]');
  if (!select || groups.length === 0) return;

  const update = () => {
    groups.forEach((group) => {
      const types = (group.dataset.kermanTypes || '').split(/\s+/);
      group.hidden = !types.includes(select.value);
    });
  };

  select.addEventListener('change', update);
  update();
})();
