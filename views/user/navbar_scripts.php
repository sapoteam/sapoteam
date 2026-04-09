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
document.addEventListener("DOMContentLoaded", function() {
    fetch('/sapoteam/controllers/PublicController.php')
        .then(async (response) => {
            if (!response.ok) throw new Error("HTTP error " + response.status);
            const text = await response.text(); 
            try { return JSON.parse(text); } 
            catch (err) { throw err; }
        })
        .then(data => {
            if(data.status === 'success' && data.is_panen === true) {
                const banner = document.getElementById('harvestBanner');
                banner.style.display = 'flex';
            }
        })
        .catch(err => console.error('Gagal ngecek status panen:', err));
});
</script>