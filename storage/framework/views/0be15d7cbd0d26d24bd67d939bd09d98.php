

<?php $__env->startSection('content'); ?>
    <!-- En-tête avec statut -->
    <div style="text-align: right; margin-bottom: 20px;">
        <span class="badge badge-<?php echo e($colis->status_colis_port === 'SORTI' ? 'success' : ($colis->status_colis_port === 'ENTRE' ? 'warning' : 'secondary')); ?>">
            <?php echo e(match($colis->status_colis_port) {
                'EN_ATTENTE' => 'EN ATTENTE',
                'ENTRE' => 'ENTRÉ AU PORT',
                'SORTI' => 'SORTI DU PORT',
                default => $colis->status_colis_port ?? 'NON DÉFINI'
            }); ?>

        </span>
    </div>

    <!-- Informations générales du colis -->
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
            <h3>Informations portuaires</h3>
            <div class="info-row">
                <span class="info-label">Port :</span>
                <span class="info-value"><?php echo e($colis->port?->nom ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut :</span>
                <span class="info-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->status_colis_port): ?>
                        <span class="badge badge-<?php echo e($colis->status_colis_port === 'SORTI' ? 'success' : ($colis->status_colis_port === 'ENTRE' ? 'warning' : 'secondary')); ?>">
                            <?php echo e(match($colis->status_colis_port) {
                                'EN_ATTENTE' => 'En attente',
                                'ENTRE' => 'Entré',
                                'SORTI' => 'Sorti',
                                default => $colis->status_colis_port
                            }); ?>

                        </span>
                    <?php else: ?>
                        Non défini
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Dates portuaires -->
    <div class="info-grid">
        <div class="info-box">
            <h3>Dates de séjour au port</h3>
            <div class="info-row">
                <span class="info-label">Date d'entrée :</span>
                <span class="info-value">
                    <?php echo e($colis->date_entree_port ? \Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y') : 'Non renseignée'); ?>

                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de sortie :</span>
                <span class="info-value">
                    <?php echo e($colis->date_sortie_port ? \Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y') : 'Non renseignée'); ?>

                </span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_port && $colis->date_sortie_port): ?>
            <div class="info-row">
                <span class="info-label">Durée de séjour :</span>
                <span class="info-value">
                    <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(\Carbon\Carbon::parse($colis->date_sortie_port))); ?> jours
                </span>
            </div>
            <?php elseif($colis->date_entree_port): ?>
            <div class="info-row">
                <span class="info-label">Séjour actuel :</span>
                <span class="info-value">
                    <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(now())); ?> jours
                </span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="info-box">
            <h3>Documents associés</h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_t1 || $colis->declaration_reference): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_t1): ?>
                <div class="info-row">
                    <span class="info-label">N° T1 :</span>
                    <span class="info-value"><?php echo e($colis->num_t1); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->etat_t1): ?>
                <div class="info-row">
                    <span class="info-label">État T1 :</span>
                    <span class="info-value">
                        <span class="badge badge-<?php echo e($colis->etat_t1 === 'PAYE' ? 'success' : 'warning'); ?>">
                            <?php echo e($colis->etat_t1); ?>

                        </span>
                    </span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->declaration_reference): ?>
                <div class="info-row">
                    <span class="info-label">Déclaration :</span>
                    <span class="info-value"><?php echo e($colis->declaration_reference); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <p style="color: #999; font-style: italic; margin: 0;">Aucun document associé</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Détails des opérations portuaires -->
    <div style="margin: 30px 0;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            DÉTAILS DES OPÉRATIONS
        </h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #667eea; color: white;">
                    <th style="padding: 10px; text-align: left;">Opération</th>
                    <th style="padding: 10px; text-align: left;">Date</th>
                    <th style="padding: 10px; text-align: left;">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Enregistrement au port</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e($colis->created_at?->format('d/m/Y H:i') ?? '-'); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_port): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Entrée au port</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y H:i')); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_sortie_port): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Sortie du port</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e(\Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y H:i')); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Informations sur l'escale -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->port): ?>
    <div style="margin: 20px 0; padding: 15px; background: #f8fafc; border-radius: 5px;">
        <h4 style="color: #667eea; margin: 0 0 10px 0;">Informations sur l'escale</h4>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <span style="font-size: 12px; color: #666;">Port</span><br>
                <strong><?php echo e($colis->port->nom); ?></strong>
            </div>
            <div>
                <span style="font-size: 12px; color: #666;">Code</span><br>
                <strong><?php echo e($colis->port->code_port ?? 'N/A'); ?></strong>
            </div>
            <div>
                <span style="font-size: 12px; color: #666;">Localisation</span><br>
                <strong><?php echo e($colis->port->localisation ?? 'N/A'); ?></strong>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Prochaine étape -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->status_colis_port === 'SORTI'): ?>
    <div style="margin-top: 30px; padding: 15px; background: #f0f9ff; border-left: 4px solid #667eea;">
        <h4 style="margin: 0 0 10px 0; color: #667eea;">Prochaine étape :</h4>
        <p>Le colis a quitté le port et se dirige vers la douane.</p>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
            Date de sortie : <?php echo e(\Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y H:i')); ?>

        </p>
    </div>
    <?php elseif($colis->status_colis_port === 'ENTRE'): ?>
    <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
        <h4 style="margin: 0 0 10px 0; color: #856404;">En cours :</h4>
        <p>Le colis est actuellement au port en attente de sortie.</p>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
            Durée de séjour actuelle : 
            <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(now())); ?> jours
        </p>
    </div>
    <?php else: ?>
    <div style="margin-top: 30px; padding: 15px; background: #f8fafc; border-left: 4px solid #667eea;">
        <h4 style="margin: 0 0 10px 0; color: #667eea;">En attente :</h4>
        <p>Le colis est en attente d'arrivée au port.</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Section signature -->
    <div class="signature-section" style="margin-top: 50px;">
        <div class="signature-box">
            <div class="signature-line">Agent portuaire</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Représentant KGT</div>
        </div>
    </div>

    <!-- Mention légale -->
    <div style="margin-top: 30px; font-size: 10px; color: #999; text-align: center;">
        <p>Ce document est une fiche de suivi portuaire officielle de KGT TRANSIT.</p>
        <p>Il atteste du passage du colis au port de <?php echo e($colis->port?->nom ?? 'destination'); ?>.</p>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/pdf/etape-port.blade.php ENDPATH**/ ?>