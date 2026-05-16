<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-logo">
                <i class="fas fa-recycle"></i>
                <span>SIMBS</span>
            </div>
            <p class="footer-tagline">Sistem Informasi Manajemen Bank Sampah — membangun ekosistem daur ulang yang berkelanjutan untuk Indonesia.</p>
            <div class="footer-socials">
                <a href="https://www.facebook.com/share/1CXPjr4jcT/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/banksampahinduksurabaya?igsh=cHZ4ajZlYTBpMmRr" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://x.com/bankgemahripah?s=21" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://wa.me/085843730936" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <div class="footer-links">
            <h4>Navigasi</h4>
            <ul>
                <li><a href="{{ route('home') }}">Beranda</a></li>
                <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                <li><a href="{{ route('contact') }}">Kontak</a></li>
            </ul>
        </div>

        <div class="footer-links">
            <h4>Layanan</h4>
            <ul>
                <li><a href="#">Setor Sampah</a></li>
                <li><a href="#">Cek Saldo</a></li>
                <li><a href="#">Jadwal Pickup</a></li>
                <li><a href="#">Laporan</a></li>
            </ul>
        </div>

        <div class="footer-contact">
            <h4>Kontak</h4>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> Surabaya, Jawa Timur</li>
                <li><i class="fas fa-phone"></i> +62 31 123 4567</li>
                <li><i class="fas fa-envelope"></i> info@simbs.id</li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} SIMBS. Semua hak dilindungi.</p>
    </div>
</footer>

