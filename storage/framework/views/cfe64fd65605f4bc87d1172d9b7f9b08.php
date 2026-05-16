
<?php $__env->startSection('title', 'Kontak — SIMBS'); ?>

<?php $__env->startSection('content'); ?>
<section class="contact-section" id="contact" style="padding-top: 120px; padding-bottom: 120px;">
    <div class="contact-container" style="max-width: 1200px; margin: 0 auto; padding: 0 48px;">
        <div class="section-header" style="margin-bottom: 48px;">
            <span class="section-tag">Kontak</span>
            <h2 class="section-title">Kirim Pesan <em>Ke SIMBS</em></h2>
            <p class="section-desc" style="margin-top: 0;">Kami siap membantu Anda. Sampaikan pesan melalui form di bawah ini.</p>
        </div>

        <div class="contact-grid" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: start;">
            
            <div class="contact-form" data-animate="fade-up">
                <div style="background: white; border: 1px solid rgba(226,232,229,1); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm);">

                    <?php if(session('success')): ?>
                    <div style="background:#f0fdf6; border:1px solid #a3e6be; border-radius:10px; padding:12px 16px; margin-bottom:20px; color:#155230; font-size:14px; display:flex; align-items:center; gap:8px;">
                        ✅ <?php echo e(session('success')); ?>

                    </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:10px; padding:12px 16px; margin-bottom:20px; color:#ef4444; font-size:14px;">
                        ⚠️ <?php echo e($errors->first()); ?>

                    </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('kontak.kirim')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label style="display:block; font-weight:600; color: var(--green-dark); margin-bottom: 8px; font-size: 14px;">Nama <span style="color:#ef4444">*</span></label>
                                <input type="text" name="nama" value="<?php echo e(old('nama')); ?>" required
                                    style="width: 100%; border: 1px solid var(--gray-200); border-radius: 12px; padding: 12px 14px; outline: none; font-size: 14px; font-family:inherit;"
                                    placeholder="Nama lengkap Anda">
                            </div>
                            <div>
                                <label style="display:block; font-weight:600; color: var(--green-dark); margin-bottom: 8px; font-size: 14px;">Email <span style="color:#ef4444">*</span></label>
                                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                                    style="width: 100%; border: 1px solid var(--gray-200); border-radius: 12px; padding: 12px 14px; outline: none; font-size: 14px; font-family:inherit;"
                                    placeholder="email@contoh.com">
                            </div>
                        </div>

                        <div style="margin-top: 16px;">
                            <label style="display:block; font-weight:600; color: var(--green-dark); margin-bottom: 8px; font-size: 14px;">Subjek <span style="color:#ef4444">*</span></label>
                            <input type="text" name="subjek" value="<?php echo e(old('subjek')); ?>" required
                                style="width: 100%; border: 1px solid var(--gray-200); border-radius: 12px; padding: 12px 14px; outline: none; font-size: 14px; font-family:inherit;"
                                placeholder="Topik pesan Anda">
                        </div>

                        <div style="margin-top: 16px;">
                            <label style="display:block; font-weight:600; color: var(--green-dark); margin-bottom: 8px; font-size: 14px;">Pesan <span style="color:#ef4444">*</span></label>
                            <textarea name="pesan" rows="6" required
                                style="width: 100%; border: 1px solid var(--gray-200); border-radius: 12px; padding: 12px 14px; outline: none; font-size: 14px; resize: vertical; font-family:inherit;"
                                placeholder="Tulis pesan Anda di sini..."><?php echo e(old('pesan')); ?></textarea>
                        </div>

                        <div style="margin-top: 22px;">
                            <button type="submit" class="btn-primary" style="border: none; cursor: pointer;">
                                Kirim Pesan <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="contact-social" data-animate="fade-up" style="position: sticky; top: 90px;">
                <div style="background: white; border: 1px solid rgba(226,232,229,1); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--green-dark); margin-bottom: 14px;">
                        Sosial Media
                    </h3>
                    <p style="color: var(--gray-600); font-size: 14px; line-height: 1.7; margin-bottom: 18px;">
                        Hubungi kami melalui kanal berikut.
                    </p>

                    <div style="display: grid; gap: 12px;">
                        <a href="https://www.instagram.com/banksampahinduksurabaya?igsh=cHZ4ajZlYTBpMmRr" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap: 12px; padding: 12px 14px; border-radius: 14px; border: 1px solid var(--gray-200); transition: all var(--transition);">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--green-pale); display:flex; align-items:center; justify-content:center; color: var(--green-primary);">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--green-dark); font-size: 14px;">Instagram</div>
                                <div style="color: var(--gray-600); font-size: 13px;">@simbs</div>
                            </div>
                        </a>

                        <a href="https://www.facebook.com/share/1CXPjr4jcT/" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap: 12px; padding: 12px 14px; border-radius: 14px; border: 1px solid var(--gray-200); transition: all var(--transition);">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--green-pale); display:flex; align-items:center; justify-content:center; color: var(--green-primary);">
                                <i class="fab fa-facebook-f"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--green-dark); font-size: 14px;">Facebook</div>
                                <div style="color: var(--gray-600); font-size: 13px;">Bank Sampah SIMBS</div>
                            </div>
                        </a>

                        <a href="https://wa.me/085843730936" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap: 12px; padding: 12px 14px; border-radius: 14px; border: 1px solid var(--gray-200); transition: all var(--transition);">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--green-pale); display:flex; align-items:center; justify-content:center; color: var(--green-primary);">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--green-dark); font-size: 14px;">WhatsApp</div>
                                <div style="color: var(--gray-600); font-size: 13px;">+62 8xx-xxxx-xxxx</div>
                            </div>
                        </a>

                        <a href="https://x.com/bankgemahripah?s=21" target="_blank" rel="noopener noreferrer" style="display:flex; align-items:center; gap: 12px; padding: 12px 14px; border-radius: 14px; border: 1px solid var(--gray-200); transition: all var(--transition);">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--green-pale); display:flex; align-items:center; justify-content:center; color: var(--green-primary);">
                                <i class="fab fa-twitter"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--green-dark); font-size: 14px;">Twitter</div>
                                <div style="color: var(--gray-600); font-size: 13px;">@simbs</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\banksampah\resources\views/contact.blade.php ENDPATH**/ ?>