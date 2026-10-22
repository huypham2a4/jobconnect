document.addEventListener('DOMContentLoaded', function () {
  // ==========================================
  // 1. THEME TOGGLE: CHUẨN <-> TÁI SINH – BÙNG CHÁY
  // ==========================================
  const themeSwitchBtn = document.getElementById('theme-switch-btn');
  const themeSwitchText = document.getElementById('theme-switch-text');

  function updateThemeUI() {
    const isRebirth = document.documentElement.getAttribute('data-theme') === 'rebirth';
    if (themeSwitchText) {
      themeSwitchText.innerHTML = isRebirth ? 'Bùng Cháy 🔥' : 'Chuẩn ☀️';
    }
    if (isRebirth) {
      createEmberParticles();
    } else {
      removeEmberParticles();
    }
  }

  if (themeSwitchBtn) {
    themeSwitchBtn.addEventListener('click', function () {
      const isRebirth = document.documentElement.getAttribute('data-theme') === 'rebirth';
      if (isRebirth) {
        document.documentElement.removeAttribute('data-theme');
        try { localStorage.setItem('jobconnect_theme', 'default'); } catch (e) {}
      } else {
        document.documentElement.setAttribute('data-theme', 'rebirth');
        try { localStorage.setItem('jobconnect_theme', 'rebirth'); } catch (e) {}
      }
      updateThemeUI();
    });
  }

  // Initial call
  updateThemeUI();

  // Spark / Ember particles for Rebirth Theme in Hero
  function createEmberParticles() {
    const hero = document.querySelector('.hero-section');
    if (!hero || document.getElementById('ember-canvas')) return;

    const canvas = document.createElement('canvas');
    canvas.id = 'ember-canvas';
    canvas.style.position = 'absolute';
    canvas.style.inset = '0';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '3';
    canvas.style.opacity = '0.7';
    hero.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    let width = canvas.width = hero.offsetWidth;
    let height = canvas.height = hero.offsetHeight;

    window.addEventListener('resize', function() {
      if (canvas && hero) {
        width = canvas.width = hero.offsetWidth;
        height = canvas.height = hero.offsetHeight;
      }
    });

    const particles = [];
    const particleCount = 28;

    for (let i = 0; i < particleCount; i++) {
      particles.push({
        x: Math.random() * width,
        y: Math.random() * height,
        size: Math.random() * 2.5 + 1,
        speedY: -(Math.random() * 0.7 + 0.3),
        speedX: (Math.random() - 0.5) * 0.5,
        opacity: Math.random() * 0.7 + 0.3,
        color: Math.random() > 0.4 ? '#FF7A2B' : '#FFE2A8',
        life: Math.random() * 100
      });
    }

    function animate() {
      if (!document.getElementById('ember-canvas')) return;
      if (document.documentElement.getAttribute('data-theme') !== 'rebirth') {
        removeEmberParticles();
        return;
      }

      ctx.clearRect(0, 0, width, height);

      particles.forEach(p => {
        p.y += p.speedY;
        p.x += p.speedX;
        p.life++;

        if (p.y < 0) {
          p.y = height + 10;
          p.x = Math.random() * width;
        }

        ctx.fillStyle = p.color;
        ctx.globalAlpha = p.opacity * (1 - p.y / height * 0.2);
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fill();
      });

      requestAnimationFrame(animate);
    }

    animate();
  }

  function removeEmberParticles() {
    const existing = document.getElementById('ember-canvas');
    if (existing) {
      existing.remove();
    }
  }

  // ==========================================
  // 2. CATEGORY FILTER TABS
  // ==========================================
  const filterTabs = document.querySelectorAll('.filter-tab');
  const jobCards = document.querySelectorAll('.job-card');

  filterTabs.forEach(tab => {
    tab.addEventListener('click', function () {
      filterTabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      const filter = this.getAttribute('data-filter');

      jobCards.forEach(card => {
        if (filter === 'all' || card.getAttribute('data-category') === filter) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // ==========================================
  // 3. BOOKMARK BUTTON
  // ==========================================
  const bookmarkButtons = document.querySelectorAll('.bookmark-btn');
  bookmarkButtons.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.toggle('saved');
      const icon = this.querySelector('i');
      if (this.classList.contains('saved')) {
        icon.classList.remove('fa-regular');
        icon.classList.add('fa-solid');
      } else {
        icon.classList.remove('fa-solid');
        icon.classList.add('fa-regular');
      }
    });
  });

  // ==========================================
  // 4. MOBILE NAVIGATION
  // ==========================================
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navLinks = document.querySelector('.nav-links');
  if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', function () {
      const isVisible = navLinks.style.display === 'flex';
      navLinks.style.display = isVisible ? 'none' : 'flex';
      navLinks.style.flexDirection = 'column';
      navLinks.style.position = 'absolute';
      navLinks.style.top = '76px';
      navLinks.style.left = '0';
      navLinks.style.width = '100%';
      navLinks.style.padding = '20px';
      navLinks.style.boxShadow = '0 10px 25px rgba(0,0,0,0.3)';
    });
  }

  // ==========================================
  // 5. QUE DIÊM: SEARCH FOCUS & HERO WARMTH
  // ==========================================
  const searchInputs = document.querySelectorAll('.search-box-wrapper input, .search-box-wrapper select');
  const heroSection = document.querySelector('.hero-section');

  if (searchInputs.length > 0 && heroSection) {
    searchInputs.forEach(input => {
      input.addEventListener('focus', function () {
        if (document.documentElement.getAttribute('data-theme') === 'rebirth') {
          heroSection.classList.add('hero-ignited');
        }
      });
      input.addEventListener('blur', function () {
        heroSection.classList.remove('hero-ignited');
      });
    });
  }

  // ==========================================
  // 6. ANIMATED STATS COUNTER ("ĐỐM THAN")
  // ==========================================
  const statNumbers = document.querySelectorAll('.stat-number[data-target]');
  if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
    const counterObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.getAttribute('data-target'), 10) || 0;
          const suffix = el.getAttribute('data-suffix') || '';
          let count = 0;
          const duration = 1200;
          const stepTime = 30;
          const totalSteps = duration / stepTime;
          const increment = target / totalSteps;

          const timer = setInterval(() => {
            count += increment;
            if (count >= target) {
              el.textContent = target + suffix;
              clearInterval(timer);
            } else {
              el.textContent = Math.floor(count) + suffix;
            }
          }, stepTime);

          observer.unobserve(el);
        }
      });
    }, { threshold: 0.3 });

    statNumbers.forEach(el => counterObserver.observe(el));
  }
});