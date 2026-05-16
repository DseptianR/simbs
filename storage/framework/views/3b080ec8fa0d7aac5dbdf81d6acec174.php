<nav class="navbar" id="navbar">
    <div class="navbar-brand">
        <span class="brand-logo">
            <i class="fas fa-recycle"></i>
        </span>
        <div class="brand-text">
            <span class="brand-title">SIMBS</span>
            <span class="brand-subtitle">Sistem Informasi Manajemen Bank Sampah</span>
        </div>
    </div>

    <ul class="navbar-menu" id="navMenu">
        <li><a href="<?php echo e(route('home')); ?>" class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">Beranda</a></li>
        <li><a href="<?php echo e(route('about')); ?>" class="nav-link <?php echo e(request()->routeIs('about') ? 'active' : ''); ?>">Tentang</a></li>
        <li><a href="<?php echo e(route('contact')); ?>" class="nav-link <?php echo e(request()->routeIs('contact') ? 'active' : ''); ?>">Kontak</a></li>
    </ul>

    <div class="navbar-actions">
        <a href="<?php echo e(url('/operator/login')); ?>" class="btn-login-nasabah">
            <i class="fas fa-lock"></i>
            <span>Login Operator</span>
        </a>
        <button class="btn-search" id="searchToggle" aria-label="Pencarian">
            <i class="fas fa-search"></i>
        </button>
        <button class="btn-hamburger" id="hamburger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    
    <div class="search-overlay" id="searchOverlay">
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Cari informasi bank sampah..." autofocus>
            <button class="search-close" id="searchClose"><i class="fas fa-times"></i></button>
        </div>
    </div>
</nav><?php /**PATH C:\laragon\www\banksampah\resources\views/partials/navbar.blade.php ENDPATH**/ ?>