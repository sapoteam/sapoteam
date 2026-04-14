<style>
  @keyframes pulse-star {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
  }

  #harvestBanner i {
    font-size: 1.2rem;
    animation: pulse-star 1.5s infinite;
  }
</style>

<style>
  @keyframes pulse-star {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
  }

  #harvestBanner i {
    font-size: 1.2rem;
    animation: pulse-star 1.5s infinite;
  }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
  fetch('../../controllers/PublicController.php')
    .then(async (response) => {
      if (!response.ok) throw new Error("HTTP error " + response.status);
      const text = await response.text();
      return JSON.parse(text);
    })
    .then(data => {
      if (data.status === 'success' && data.is_panen === true) {
        const banner = document.getElementById('harvestBanner');
        if (banner) banner.style.display = 'flex';
      }
    })
    .catch(err => console.error('Gagal ngecek status panen:', err));

  const page = document.querySelector('.page-content');
  if (!page) return;

  page.classList.add('fade-enter');

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      page.classList.remove('fade-enter');
    });
  });

  document.querySelectorAll('a[href]').forEach(link => {
    link.addEventListener('click', function (e) {
      const href = this.getAttribute('href');

      if (
        !href ||
        href.startsWith('#') ||
        href.startsWith('javascript:') ||
        this.target === '_blank' ||
        this.hasAttribute('download') ||
        e.ctrlKey || e.metaKey || e.shiftKey || e.altKey
      ) return;

      const url = new URL(this.href, window.location.href);
      if (url.origin !== window.location.origin) return;

      e.preventDefault();
      page.classList.add('fade-exit');

      setTimeout(() => {
        window.location.href = this.href;
      }, 450);
    });
  });
});
</script>