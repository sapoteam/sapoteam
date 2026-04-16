<script>
function closeHarvestBanner() {
    const banner = document.getElementById('harvestBanner');
    if (banner) {
        banner.classList.remove('show');
        banner.classList.add('hide');
        
        setTimeout(() => {
            banner.style.display = 'none';
        }, 500); 
    }
}

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
        if (banner) {
            banner.style.display = 'flex'; 
            
            setTimeout(() => {
                banner.classList.add('show');
            }, 50);
        }
      }
    })
    .catch(err => console.error('Gagal ngecek status panen:', err));
});
</script>