// Main JavaScript - Carlos & Elena Wedding
document.addEventListener('DOMContentLoaded', () => {
  // Countdown to wedding
  function updateCountdown() {
    const weddingDate = new Date('November 21, 2026 12:00:00').getTime();
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
  }

  // Update countdown every second
  updateCountdown();
  setInterval(updateCountdown, 1000);

  // RSVP Form → Google Forms
  const form = document.getElementById('rsvp-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(form);
      const submitBtn = form.querySelector('.btn-submit');
      submitBtn.textContent = 'Enviando...';
      submitBtn.disabled = true;

      fetch('https://docs.google.com/forms/d/e/1FAIpQLSdvYTljn6P28jXj3fQhuudVhBJybwEVdGL0HYcj5wddfNdG4w/formResponse', {
        method: 'POST',
        body: formData,
        mode: 'no-cors'
      }).then(function() {
        form.style.display = 'none';
        document.getElementById('rsvp-success').style.display = 'block';
      }).catch(function() {
        form.style.display = 'none';
        document.getElementById('rsvp-success').style.display = 'block';
      });
    });
  }
});

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
