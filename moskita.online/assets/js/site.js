(function () {
  // 1) 首次交互后显示导航（点击/滚动/按键）
  const topbar = document.getElementById('topbar');
  const heroOverlay = document.getElementById('heroOverlay');

  let revealed = false;
  function revealNav() {
    if (revealed) return;
    revealed = true;
    if (topbar) {
      topbar.classList.add('is-visible');
      topbar.setAttribute('aria-hidden', 'false');
    }
    if (heroOverlay) {
      heroOverlay.style.opacity = '0.0';
      heroOverlay.style.transition = 'opacity .35s ease';
      setTimeout(() => { heroOverlay.style.display = 'none'; }, 400);
    }
    window.removeEventListener('wheel', onFirstWheel, { passive: true });
    window.removeEventListener('click', revealNav);
    window.removeEventListener('keydown', revealNav);
  }

  function onFirstWheel() { revealNav(); }
  window.addEventListener('wheel', onFirstWheel, { passive: true });
  window.addEventListener('click', revealNav);
  window.addEventListener('keydown', revealNav);

  // 2) 时间线轮播左右切换（简单可用版）
  document.querySelectorAll('[data-carousel]').forEach((wrap) => {
    const track = wrap.querySelector('[data-track]');
    const prev = wrap.querySelector('[data-prev]');
    const next = wrap.querySelector('[data-next]');
    if (!track || !prev || !next) return;

    function step(dir) {
      const card = track.querySelector('.timeline-card');
      const w = card ? card.getBoundingClientRect().width : 320;
      track.scrollBy({ left: dir * (w + 14), behavior: 'smooth' });
    }
    prev.addEventListener('click', () => step(-1));
    next.addEventListener('click', () => step(1));
  });
})();