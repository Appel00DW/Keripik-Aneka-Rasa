<div id="welcomePopup" class="popup-overlay">
    <div class="popup-content">
        <button id="closePopup" class="popup-close">&times;</button>
        <h2>Selamat Datang!</h2>
        <p>Nikmati diskon spesial <strong>10%</strong> untuk pesanan pertama Anda dengan kode voucher di bawah ini.</p>
        <div class="popup-voucher-code">SELAMATDATANG</div>
        <a href="#produk" id="shopNowBtn" class="btn btn-primary">Mulai Belanja</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const popupOverlay = document.getElementById('welcomePopup');
    const closeButton = document.getElementById('closePopup');
    const shopNowButton = document.getElementById('shopNowBtn');

    function hidePopup() {
        if (popupOverlay) {
            popupOverlay.style.display = 'none';
        }
    }

    // --- LOGIKA BARU DIMULAI DI SINI ---
    // 1. Cek apakah ada parameter 'login=success' di URL
    const urlParams = new URLSearchParams(window.location.search);
    const justLoggedIn = urlParams.get('login') === 'success';

    // 2. Cek apakah ini kunjungan pertama kali
    const isFirstVisit = !localStorage.getItem('popupShown');

    // 3. GABUNGAN: Tampilkan pop-up jika ini kunjungan pertama ATAU jika baru saja login
    if (isFirstVisit || justLoggedIn) {
        
        // Tampilkan pop-up setelah sedikit jeda
        setTimeout(function() {
            if (popupOverlay) {
                popupOverlay.style.display = 'flex';
            }
        }, 1500);

        // HANYA set penanda jika ini adalah kunjungan pertama
        if (isFirstVisit) {
            localStorage.setItem('popupShown', 'true');
        }
    }
    // --- AKHIR LOGIKA BARU ---


    // Event listener untuk tombol tutup (tetap sama)
    if (closeButton) { closeButton.addEventListener('click', hidePopup); }
    if (shopNowButton) { shopNowButton.addEventListener('click', hidePopup); }
    if (popupOverlay) {
        popupOverlay.addEventListener('click', function(event) {
            if (event.target === popupOverlay) {
                hidePopup();
            }
        });
    }
});
</script>