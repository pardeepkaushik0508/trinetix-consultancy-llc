(() => {
  function bindMediaFields() {
    document.querySelectorAll('.trinetix-media-field').forEach((field) => {
      if (field.dataset.bound) return;
      field.dataset.bound = '1';
      const input = field.querySelector('.trinetix-media-id');
      const preview = field.querySelector('.trinetix-media-preview');
      const type = field.getAttribute('data-media-type') || 'image';
      const selectBtn = field.querySelector('.trinetix-media-select');
      const clearBtn = field.querySelector('.trinetix-media-clear');
      if (!selectBtn || !input) return;

      selectBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const frame = wp.media({
          title: 'Select media',
          button: { text: 'Use this media' },
          library: { type: type === 'video' ? 'video' : 'image' },
          multiple: false,
        });
        frame.on('select', () => {
          const attachment = frame.state().get('selection').first().toJSON();
          input.value = attachment.id;
          if (preview) {
            if (type === 'image' && attachment.url) {
              preview.innerHTML = '<img src="' + attachment.url + '" alt="" style="max-width:220px;height:auto;">';
            } else {
              preview.innerHTML = '<code>' + (attachment.filename || attachment.url) + '</code>';
            }
          }
        });
        frame.open();
      });

      if (clearBtn) {
        clearBtn.addEventListener('click', (e) => {
          e.preventDefault();
          input.value = '';
          if (preview) preview.innerHTML = '';
        });
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindMediaFields);
  } else {
    bindMediaFields();
  }
})();
