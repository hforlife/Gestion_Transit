<?php $__env->startSection('content'); ?>
    <div style="text-align: right; margin-bottom: 20px;">
        <span class="badge badge-<?php echo e($colis->status_colis_douane === 'SORTI' ? 'success' : 'warning'); ?>">
            <?php echo e(match($colis->status_colis_douane) {
                'EN_ATTENTE' => 'EN ATTENTE',
                'ENTRE' => 'ENTRÉ EN DOUANE',
                'SORTI' => 'SORTI DE DOUANE',
                default => $colis->status_colis_douane
            }); ?>

        </span>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Informations du colis</h3>
            <div class="info-row">
                <span class="info-label">N° BL :</span>
                <span class="info-value"><?php echo e($colis->numero_bl); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Type :</span>
                <span class="info-value"><?php echo e($colis->typeColis?->nom ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Description :</span>
                <span class="info-value"><?php echo e($colis->description ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Client :</span>
                <span class="info-value"><?php echo e($colis->dossierTransit?->client?->nom ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Agent :</span>
                <span class="info-value"><?php echo e($colis->agent?->name ?? 'N/A'); ?></span>
            </div>
        </div>

        <div class="info-box">
            <h3>Informations douanières</h3>
            <div class="info-row">
                <span class="info-label">N° T1 :</span>
                <span class="info-value"><?php echo e($colis->num_t1 ?? 'Non fourni'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">État T1 :</span>
                <span class="info-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->etat_t1): ?>
                        <span class="badge badge-<?php echo e($colis->etat_t1 === 'PAYE' ? 'success' : 'warning'); ?>">
                            <?php echo e($colis->etat_t1); ?>

                        </span>
                    <?php else: ?>
                        Non défini
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Déclaration :</span>
                <span class="info-value"><?php echo e($colis->declaration_reference ?? 'Non fournie'); ?></span>
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Dates douane</h3>
            <div class="info-row">
                <span class="info-label">Entrée :</span>
                <span class="info-value"><?php echo e($colis->date_entree_douane ? $colis->date_entree_douane->format('d/m/Y') : 'Non renseignée'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Sortie :</span>
                <span class="info-value"><?php echo e($colis->date_sortie_douane ? $colis->date_sortie_douane->format('d/m/Y') : 'Non renseignée'); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_douane && $colis->date_sortie_douane): ?>
            <div class="info-row">
                <span class="info-label">Durée séjour :</span>
                <span class="info-value"><?php echo e($colis->date_entree_douane->diffInDays($colis->date_sortie_douane)); ?> jours</span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="info-box">
            <h3>Autres informations</h3>
            <div class="info-row">
                <span class="info-label">Port :</span>
                <span class="info-value"><?php echo e($colis->port?->nom ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date création :</span>
                <span class="info-value"><?php echo e($colis->created_at->format('d/m/Y H:i')); ?></span>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->etat_colis === 'A_LA_DOUANE'): ?>
    <div style="margin-top: 30px; padding: 15px; background: #f0f9ff; border-left: 4px solid #667eea;">
        <h4 style="margin: 0 0 10px 0; color: #667eea;">Suivant :</h4>
        <p>Le colis est actuellement à la douane. Prochaine étape : 
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->typeColis?->nom === 'Véhicules'): ?>
                Expertise
            <?php else: ?>
                Livraison
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Agent des douanes</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Représentant KGT</div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/pdf/etape-douane.blade.php ENDPATH**/ ?>