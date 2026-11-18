document.addEventListener('DOMContentLoaded', () => {
  const themeButton = document.getElementById('theme-switch-btn');
  const themeIcon = document.getElementById('theme-switch-icon');
  const updateTheme = () => {
    const dark = document.documentElement.getAttribute('data-theme') === 'rebirth';
    if (themeIcon) themeIcon.className = dark ? 'fa-regular fa-sun' : 'fa-regular fa-moon';
    themeButton?.setAttribute('aria-pressed', String(dark));
  };
  themeButton?.addEventListener('click', () => {
    const dark = document.documentElement.getAttribute('data-theme') !== 'rebirth';
    if (dark) document.documentElement.setAttribute('data-theme', 'rebirth');
    else document.documentElement.removeAttribute('data-theme');
    try { localStorage.setItem('jobconnect_theme', dark ? 'rebirth' : 'default'); } catch (_) {}
    updateTheme();
  });
  updateTheme();

  document.querySelectorAll('input[type="password"]').forEach(input => {
    const wrapper = document.createElement('span');
    wrapper.className = 'password-field';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'password-toggle';
    toggle.setAttribute('aria-label', 'Hiện mật khẩu');
    toggle.setAttribute('aria-pressed', 'false');
    toggle.innerHTML = '<i class="fa-regular fa-eye" aria-hidden="true"></i>';
    toggle.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      toggle.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
      toggle.setAttribute('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
      toggle.setAttribute('aria-pressed', String(show));
      input.focus();
    });
    wrapper.appendChild(toggle);
  });

  const mobile = document.getElementById('btn-mobile-toggle');
  const navigation = document.getElementById('main-navigation');
  mobile?.addEventListener('click', () => {
    const open = navigation.classList.toggle('is-open');
    mobile.setAttribute('aria-expanded', String(open));
  });
  navigation?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
    navigation.classList.remove('is-open');
    mobile?.setAttribute('aria-expanded', 'false');
  }));
  document.addEventListener('click', event => {
    document.querySelectorAll('.account-menu[open], .notif-menu[open]').forEach(menu => {
      if (!menu.contains(event.target)) menu.removeAttribute('open');
    });
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
      document.querySelectorAll('.account-menu[open], .notif-menu[open]').forEach(menu => menu.removeAttribute('open'));
      navigation?.classList.remove('is-open');
      mobile?.setAttribute('aria-expanded', 'false');
    }
  });

  const tabs = document.querySelectorAll('.filter-tab');
  const cards = document.querySelectorAll('#jobs-section .job-card');
  tabs.forEach(tab => tab.addEventListener('click', () => {
    tabs.forEach(item => item.classList.toggle('active', item === tab));
    let count = 0;
    cards.forEach(card => {
      const visible = tab.dataset.filter === 'all' || card.dataset.category === tab.dataset.filter;
      card.hidden = !visible;
      if (visible) count++;
    });
    const empty = document.querySelector('.filter-empty');
    if (empty) empty.hidden = count !== 0;
  }));

  let toastTimer;
  function toast(message) {
    const box = document.getElementById('app-toast');
    if (!box) return;
    box.textContent = message;
    box.classList.add('visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => box.classList.remove('visible'), 3500);
  }
  document.querySelectorAll('[data-save-url]').forEach(button => {
    button.addEventListener('click', async event => {
      event.preventDefault();
      const saved = button.dataset.saved === 'true';
      button.disabled = true;
      try {
        const response = await fetch(button.dataset.saveUrl, {
          method: saved ? 'DELETE' : 'PUT',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
          credentials: 'same-origin'
        });
        if (response.redirected || response.status === 401 || response.status === 419) {
          toast('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
          return;
        }
        const data = await response.json();
        if (!response.ok) throw new Error('Không thể lưu việc làm này. Hãy tải lại trang và thử lại.');
        document.querySelectorAll('[data-save-url]').forEach(item => {
          if (item.dataset.saveUrl !== button.dataset.saveUrl) return;
          item.dataset.saved = String(data.saved);
          item.classList.toggle('saved', data.saved);
          item.setAttribute('aria-pressed', String(data.saved));
          item.setAttribute('aria-label', data.saved ? 'Bỏ lưu việc làm' : 'Lưu việc làm');
          item.title = item.getAttribute('aria-label');
          item.querySelector('i').className = (data.saved ? 'fa-solid' : 'fa-regular') + ' fa-bookmark';
        });
        toast(data.message);
        if (!data.saved && window.location.pathname.endsWith('/candidate/saved-jobs')) window.location.reload();
      } catch (error) {
        toast(error.message || 'Không thể kết nối. Vui lòng thử lại.');
      } finally {
        button.disabled = false;
      }
    });
  });
});
