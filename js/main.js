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
});
