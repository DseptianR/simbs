
<?php $__env->startSection('title', 'Tentang — SIMBS'); ?>

<?php $__env->startSection('content'); ?>
<section class="about-section" id="about-section" style="padding-top: 120px;">
    <div class="about-container">
        <div class="about-visual" data-animate="fade-right">
            <div class="about-image-wrap">
                <img
                    src="<?php echo e(asset('images/sampah about.jpeg')); ?>"
                    alt="Tentang Bank Sampah"
                >
                <div class="about-badge-float">
                    <i class="fas fa-recycle"></i>
                    <span>Ekonomi Sirkular</span>
                </div>
            </div>
        </div>

        <div class="about-text" data-animate="fade-left">
            <span class="section-tag">Tentang SIMBS</span>
            <h2 class="section-title">Visi, Misi, dan Tujuan <em>Bank Sampah</em></h2>

            <h3 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--green-dark); margin: 20px 0 10px;">Visi</h3>
            <p>
                Menjadi pusat pengelolaan sampah mandiri yang mewujudkan lingkungan bersih, asri,
                dan meningkatkan kesejahteraan ekonomi masyarakat melalui prinsip ekonomi sirkular
            </p>

            <h3 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--green-dark); margin: 22px 0 10px;">Misi</h3>
            <p>
                Membangun kesadaran masyarakat akan pentingnya pemilahan sampah dari rumah.
                Mengubah pandangan masyarakat bahwa sampah memiliki nilai guna dan nilai ekonomi.
                Menyelenggarakan sistem manajemen tabungan sampah yang profesional, transparan, dan terpercaya.
                Mendorong kreativitas warga dalam mengolah sampah menjadi produk bernilai tambah (kerajinan/kompos).
            </p>

            <h3 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--green-dark); margin: 22px 0 10px;">Tujuan</h3>
            <ul style="margin-left: 18px; color: var(--gray-600); font-size: 15px; line-height: 1.9;">
                <li><strong>Lingkungan:</strong> Mengurangi sampah yang dibuang ke TPA dan menciptakan lingkungan yang bersih.</li>
                <li><strong>Ekonomi:</strong> Mengubah sampah menjadi uang atau tabungan yang bernilai bagi masyarakat.</li>
                <li><strong>Edukasi:</strong> Mengajarkan warga cara memilah sampah secara mandiri sejak dari rumah.</li>
            </ul>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\banksampah\resources\views/about.blade.php ENDPATH**/ ?>