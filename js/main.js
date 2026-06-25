// Main JavaScript - Carlos & Elena Wedding
document.addEventListener('DOMContentLoaded', () => {
  // Countdown to wedding
  function updateCountdown() {
    const weddingDate = new Date('2026-11-21T12:00:00+01:00').getTime();
    const now = new Date().getTime();
    const timeLeft = weddingDate - now;

    if (timeLeft > 0) {
      const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
      const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

      document.getElementById('countdown-days').textContent = String(days).padStart(3, '0');
      document.getElementById('countdown-hours').textContent = String(hours).padStart(2, '0');
      document.getElementById('countdown-minutes').textContent = String(minutes).padStart(2, '0');
      document.getElementById('countdown-seconds').textContent = String(seconds).padStart(2, '0');
    } else {
      document.getElementById('countdown-days').textContent = '000';
      document.getElementById('countdown-hours').textContent = '00';
      document.getElementById('countdown-minutes').textContent = '00';
      document.getElementById('countdown-seconds').textContent = '00';
    }
    
      // Show debugging info: local now and Europe/Madrid now, plus target label
      try {
        const infoEl = document.getElementById('countdown-info');
        if (infoEl) {
          const nowLocal = new Date();
          const nowLocalStr = nowLocal.toLocaleString();
          const madridFormatter = new Intl.DateTimeFormat('es-ES', {
            timeZone: 'Europe/Madrid',
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
          });
          const nowMadridParts = madridFormatter.format(nowLocal);
          infoEl.textContent = `Local: ${nowLocalStr} — Madrid: ${nowMadridParts} — Target: 2026-11-21 12:00 (Europe/Madrid)`;
        }
      } catch (e) {
        // ignore in older browsers
        console.warn('Countdown info format not available', e);
      }
  }

  // Update countdown every second
  updateCountdown();
  setInterval(updateCountdown, 1000);

// RSVP Form
  const form = document.getElementById('rsvp-form');
  if (form) {
    // Show/hide allergies details field conditionally
    const allergiesYes = document.getElementById('allergies-yes');
    const allergiesNo = document.getElementById('allergies-no');
    const allergiesDetails = document.getElementById('allergies-details');
    
    if (allergiesYes && allergiesNo && allergiesDetails) {
      const toggleAllergiesDetails = () => {
        if (allergiesYes.checked) {
          allergiesDetails.style.display = 'block';
        } else {
          allergiesDetails.style.display = 'none';
        }
      };
      allergiesYes.addEventListener('change', toggleAllergiesDetails);
      allergiesNo.addEventListener('change', toggleAllergiesDetails);
    }

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const submitBtn = form.querySelector('.btn-submit');
      submitBtn.textContent = 'Enviando...';
      submitBtn.disabled = true;

      const params = new URLSearchParams({
        emailAddress:        form.querySelector('[name="emailAddress"]').value,
        nombre:              form.querySelector('[name="entry.965443207"]').value,
        asistencia:          (form.querySelector('[name="entry.1017429299"]:checked') || {}).value || '',
        alergias:            (form.querySelector('[name="entry.1163954892"]:checked') || {}).value || '',
        detallesAlergias:    form.querySelector('[name="entry.1794076804"]') ? form.querySelector('[name="entry.1794076804"]').value : '',
        autobus:             (form.querySelector('[name="entry.477625878"]:checked') || {}).value || '',
        mensaje:             form.querySelector('[name="entry.897303289"]').value,
      });

      function attemptSubmit(attemptsLeft) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        fetch('save-rsvp.php?' + params.toString(), { signal: controller.signal })
          .then(function(res) {
            clearTimeout(timeoutId);
            if (!res.ok) throw new Error('http_error');
            return res.json();
          })
          .then(function(data) {
            if (data && data.result === 'ok') {
              form.style.display = 'none';
              document.getElementById('rsvp-success').style.display = 'block';
            } else {
              throw new Error('bad_response');
            }
          })
          .catch(function() {
            clearTimeout(timeoutId);
            if (attemptsLeft > 1) {
              attemptSubmit(attemptsLeft - 1);
            } else {
              submitBtn.textContent = 'Enviar Confirmación';
              submitBtn.disabled = false;
              alert('Ha ocurrido un error al enviar. Por favor inténtalo de nuevo.');
            }
          });
      }

      attemptSubmit(3);
    });
  }
});

// ── EFFECT 1: Fade-in on scroll ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const animTargets = document.querySelectorAll(
    '.story-section, .venue-section, .rsvp-section, .faq-item, .photos-section > div, .reco-section > div'
  );
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  animTargets.forEach(el => {
    el.classList.add('will-animate');
    observer.observe(el);
  });
});

// ── EFFECT 2: Title letter-by-letter animation ────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const title = document.querySelector('.hero-names');
  if (!title) return;
  const text = title.textContent;
  title.textContent = '';
  title.style.opacity = '1';
  [...text].forEach((char, i) => {
    const span = document.createElement('span');
    span.textContent = char === ' ' ? '\u00A0' : char;
    span.style.cssText = `
      display: inline-block;
      opacity: 0;
      transform: translateY(30px);
      animation: letterIn 0.5s ease forwards;
      animation-delay: ${0.3 + i * 0.045}s;
    `;
    title.appendChild(span);
  });

  // Also fade in the date
  const date = document.querySelector('.hero-date');
  if (date) {
    date.style.cssText += 'opacity:0; animation: heroDateIn 0.8s ease forwards; animation-delay: 1.4s;';
  }
});

// ── EFFECT 3: Parallax on scroll ─────────────────────────────────────────
window.addEventListener('scroll', () => {
  const scrollY = window.scrollY;

  // Hero flowers parallax
  const flowersTl = document.querySelector('.flowers-tl');
  const flowersTr = document.querySelector('.flowers-tr');
  if (flowersTl) flowersTl.style.transform = `translateY(${scrollY * 0.18}px)`;
  if (flowersTr) flowersTr.style.transform = `scaleX(-1) rotate(90deg) translateY(${scrollY * -0.12}px)`;

  // Story photos parallax
  const storyPhoto = document.querySelector('.story-photos img');
  if (storyPhoto) storyPhoto.style.transform = `translateY(${scrollY * 0.06}px)`;

  // Venue images parallax
  document.querySelectorAll('.venue-illustration img').forEach(img => {
    const rect = img.closest('.venue-wrapper').getBoundingClientRect();
    const offset = (window.innerHeight / 2 - rect.top) * 0.06;
    img.style.transform = `translateY(${offset}px) scale(1.06)`;
  });
}, { passive: true });

// FAQ collapsible behavior
document.addEventListener('DOMContentLoaded', () => {
  const faqButtons = document.querySelectorAll('.faq-question');
  faqButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.faq-item');
      const content = item.querySelector('.faq-content');
      const isOpen = btn.getAttribute('aria-expanded') === 'true';

      // update aria
      btn.setAttribute('aria-expanded', String(!isOpen));
      item.setAttribute('aria-open', String(!isOpen));

      if (!isOpen) {
        // open: set max-height to scrollHeight to animate
        content.classList.add('open');
        // add small buffer to avoid text clipping due to padding/line-height
        content.style.maxHeight = (content.scrollHeight + 24) + 'px';
      } else {
        // close: remove max-height after forcing reflow
        content.style.maxHeight = content.scrollHeight + 'px';
        requestAnimationFrame(() => {
          content.style.maxHeight = '0px';
          content.classList.remove('open');
        });
      }
    });
  });
});
