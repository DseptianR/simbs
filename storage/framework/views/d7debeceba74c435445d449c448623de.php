<?php $__env->startSection('title', 'Beranda — SIMBS Bank Sampah'); ?>

<?php $__env->startSection('content'); ?>


<section class="hero" id="hero">
    
    <div class="hero-bg">
        <div class="hero-overlay"></div>
         <img src="<?php echo e(asset('images/bg sampah.jpeg')); ?>" alt="Bank Sampah"> 
        <div class="hero-bg-placeholder"></div>
    </div>

    <div class="hero-content" data-animate="fade-up">
        <div class="hero-badge">
            <i class="fas fa-leaf"></i>
            <span>Peduli Lingkungan, Peduli Masa Depan</span>
        </div>
        <h1 class="hero-title">Bank Sampah</h1>
        <p class="hero-desc">
            Sistem pengelolaan sampah yang dikelola seperti bank, tapi yang ditabung bukan uang,
            melainkan sampah kering yang sudah dipilah-pilah (plastik, kertas, kardus, logam)
        </p>
        <div class="hero-actions">
            <a href="<?php echo e(route('nasabah.register')); ?>" class="btn-primary">
                <span>Daftar</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            <a href="<?php echo e(url('/nasabah/login')); ?>" class="btn-ghost">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        </div>
    </div>

    
    <div class="hero-scroll">
        <span>Gulir ke bawah</span>
        <div class="scroll-line"></div>
    </div>
</section>


<section class="stats-section">
    <div class="stats-container">
        <div class="stat-card" data-animate="fade-up" data-delay="0">
            <div class="stat-icon"><i class="fas fa-recycle"></i></div>
            <div class="stat-number" data-count="<?php echo e($stats['sampah_terkumpul']); ?>">0</div>
            <div class="stat-unit">Kg</div>
            <div class="stat-label">Sampah Terkumpul</div>
        </div>
        <div class="stat-card" data-animate="fade-up" data-delay="100">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-number" data-count="<?php echo e($stats['nasabah_aktif']); ?>">0</div>
            <div class="stat-unit">+</div>
            <div class="stat-label">Nasabah Aktif</div>
        </div>
        <div class="stat-card" data-animate="fade-up" data-delay="200">
            <div class="stat-icon"><i class="fas fa-coins"></i></div>
            <div class="stat-number" data-count="<?php echo e($stats['total_tabungan']); ?>">0</div>
            <div class="stat-unit">Jt</div>
            <div class="stat-label">Total Tabungan</div>
        </div>
        <div class="stat-card" data-animate="fade-up" data-delay="300">
            <div class="stat-icon"><i class="fas fa-store"></i></div>
            <div class="stat-number" data-count="<?php echo e($stats['titik_pengumpulan']); ?>">0</div>
            <div class="stat-unit"></div>
            <div class="stat-label">Titik Pengumpulan</div>
        </div>
    </div>
</section>


<section class="about-section" id="about-section">
    <div class="about-container">
        <div class="about-visual" data-animate="fade-right">
            <div class="about-image-wrap">
                <div class="about-image-placeholder">
                    <i class="fas fa-recycle"></i>
                </div>
                <div class="about-badge-float">
                    <i class="fas fa-leaf"></i>
                    <span>Eco Friendly</span>
                </div>
            </div>
        </div>
        <div class="about-text" data-animate="fade-left">
            <span class="section-tag">Tentang SIMBS</span>
            <h2 class="section-title">Apa itu <em>Bank Sampah?</em></h2>
            <p>Bank Sampah adalah sistem pengelolaan sampah berbasis masyarakat yang mengadopsi mekanisme perbankan — warga "menabung" sampah terpilah dan mendapatkan nilai ekonomis dari sampah yang disetor.</p>
            <p>SIMBS hadir untuk mendigitalisasi seluruh proses: dari pencatatan setor, penimbangan, hingga pelaporan saldo tabungan nasabah secara transparan dan real-time.</p>
            <div class="about-features">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Pencatatan digital transparan</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Laporan saldo real-time</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Penjadwalan pickup otomatis</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Multi jenis sampah terpilah</span>
                </div>
            </div>
            <a href="<?php echo e(route('about')); ?>" class="btn-primary">
                Selengkapnya <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>


<section class="waste-section">
    <div class="section-header" data-animate="fade-up">
        <span class="section-tag">Kategori</span>
        <h2 class="section-title">Jenis Sampah <em>Diterima</em></h2>
        <p class="section-desc">Kami menerima berbagai jenis sampah kering yang telah dipilah dan dibersihkan</p>
    </div>

    <?php
        $colors = ['#3b82f6','#f59e0b','#8b5cf6','#ef4444','#22874f','#06b6d4'];
        $kategoriList = \App\Models\KategoriSampah::where('is_active', true)->get();
    ?>

    <div class="waste-grid" style="grid-template-columns: repeat(3, 1fr);">
        <?php $__empty_1 = true; $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="waste-card" data-animate="fade-up" data-delay="<?php echo e(($i % 3) * 100); ?>">
            <div class="waste-icon" style="--c: <?php echo e($colors[$i % count($colors)]); ?>">
                <span style="font-size:28px; line-height:1;"><?php echo e($kat->ikon); ?></span>
            </div>
            <h3><?php echo e($kat->nama); ?></h3>
            <p><?php echo e($kat->deskripsi); ?></p>
            <div class="waste-price">Rp <?php echo e(number_format($kat->harga_per_satuan, 0, ',', '.')); ?> – <?php echo e(number_format($kat->harga_jual, 0, ',', '.')); ?> / kg</div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="grid-column:1/-1; text-align:center; color:#6b7280; padding:40px;">
            Belum ada kategori sampah yang tersedia.
        </div>
        <?php endif; ?>
    </div>
</section>


<section class="how-section">
    <div class="section-header" data-animate="fade-up">
        <span class="section-tag">Cara Kerja</span>
        <h2 class="section-title">Mudah & <em>Menguntungkan</em></h2>
        <p class="section-desc">Proses menabung sampah hanya dalam 4 langkah sederhana</p>
    </div>

    <div class="steps-container">
        <div class="step-item" data-animate="fade-up" data-delay="0">
            <div class="step-number">01</div>
            <div class="step-content">
                <div class="step-icon"><i class="fas fa-sort-amount-down"></i></div>
                <h3>Pilah Sampah</h3>
                <p>Pisahkan sampah berdasarkan jenisnya: plastik, kertas, kardus, dan logam di rumah</p>
            </div>
        </div>
        <div class="step-connector"></div>
        <div class="step-item" data-animate="fade-up" data-delay="150">
            <div class="step-number">02</div>
            <div class="step-content">
                <div class="step-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Jadwalkan Pickup</h3>
                <p>Buat jadwal pengambilan melalui aplikasi atau datang langsung ke titik pengumpulan</p>
            </div>
        </div>
        <div class="step-connector"></div>
        <div class="step-item" data-animate="fade-up" data-delay="300">
            <div class="step-number">03</div>
            <div class="step-content">
                <div class="step-icon"><i class="fas fa-weight-hanging"></i></div>
                <h3>Timbang & Catat</h3>
                <p>Petugas kami menimbang dan mencatat sampah yang disetor ke dalam sistem digital</p>
            </div>
        </div>
        <div class="step-connector"></div>
        <div class="step-item" data-animate="fade-up" data-delay="450">
            <div class="step-number">04</div>
            <div class="step-content">
                <div class="step-icon"><i class="fas fa-piggy-bank"></i></div>
                <h3>Tabungan Bertambah</h3>
                <p>Nilai ekonomis sampah langsung masuk ke saldo tabungan Anda secara otomatis</p>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Intersection Observer for scroll animations
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.delay || 0;
                    setTimeout(() => {
                        entry.target.classList.add('is-visible');
                    }, parseInt(delay));
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));

        // Counter animation
        document.querySelectorAll('.stat-number').forEach(el => {
            const target = parseInt(el.dataset.count);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = Math.floor(current).toLocaleString('id-ID');
            }, 16);
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\banksampah\resources\views/index.blade.php ENDPATH**/ ?>