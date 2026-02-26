

<?php $__env->startSection('content'); ?>
    <!-- En-tête avec statut -->
    <div style="text-align: right; margin-bottom: 20px;">
        <span class="badge badge-<?php echo e($colis->status_colis_livraison === 'LIVRE' ? 'success' : 'warning'); ?>">
            <?php echo e(match($colis->status_colis_livraison) {
                'EN_ATTENTE' => 'EN ATTENTE DE LIVRAISON',
                'LIVRE' => 'LIVRÉ',
                default => $colis->status_colis_livraison ?? 'NON DÉFINI'
            }); ?>

        </span>
    </div>

    <!-- Titre du document -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: #667eea; margin: 0;">BON DE LIVRAISON</h2>
        <p style="color: #666; font-size: 12px; margin: 5px 0;">N° BL: <?php echo e($colis->numero_bl); ?></p>
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
            <h3>Adresse de livraison</h3>
            <div class="info-row">
                <span class="info-label">Destinataire :</span>
                <span class="info-value"><?php echo e($colis->dossierTransit?->client?->nom ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Adresse :</span>
                <span class="info-value"><?php echo e($colis->dossierTransit?->client?->adresse ?? 'Adresse non renseignée'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone :</span>
                <span class="info-value"><?php echo e($colis->dossierTransit?->client?->telephone ?? 'Non renseigné'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email :</span>
                <span class="info-value"><?php echo e($colis->dossierTransit?->client?->email ?? 'Non renseigné'); ?></span>
            </div>
        </div>
    </div>

    <!-- Informations de livraison -->
    <div class="info-grid">
        <div class="info-box">
            <h3>Détails de la livraison</h3>
            <div class="info-row">
                <span class="info-label">Statut :</span>
                <span class="info-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->status_colis_livraison): ?>
                        <span class="badge badge-<?php echo e($colis->status_colis_livraison === 'LIVRE' ? 'success' : 'warning'); ?>">
                            <?php echo e($colis->status_colis_livraison === 'LIVRE' ? 'Livré' : 'En attente'); ?>

                        </span>
                    <?php else: ?>
                        Non défini
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Date prévue :</span>
                <span class="info-value"><?php echo e($colis->date_livraison_prevue ? \Carbon\Carbon::parse($colis->date_livraison_prevue)->format('d/m/Y H:i') : 'Non planifiée'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date effective :</span>
                <span class="info-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_livraison): ?>
                        <?php echo e(\Carbon\Carbon::parse($colis->date_livraison)->format('d/m/Y H:i')); ?>

                    <?php else: ?>
                        Non renseignée
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_livraison_prevue && $colis->date_livraison): ?>
            <div class="info-row">
                <span class="info-label">Délai :</span>
                <span class="info-value">
                    <?php
                        $prevue = \Carbon\Carbon::parse($colis->date_livraison_prevue);
                        $effective = \Carbon\Carbon::parse($colis->date_livraison);
                        $diff = $prevue->diffInDays($effective, false);
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($diff > 0): ?>
                        <span class="badge badge-danger"><?php echo e($diff); ?> jours de retard</span>
                    <?php elseif($diff < 0): ?>
                        <span class="badge badge-success"><?php echo e(abs($diff)); ?> jours d'avance</span>
                    <?php else: ?>
                        <span class="badge badge-success">À l'heure</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="info-box">
            <h3>Transport</h3>
            <div class="info-row">
                <span class="info-label">Port de départ :</span>
                <span class="info-value"><?php echo e($colis->port?->nom ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Mode de transport :</span>
                <span class="info-value">Maritime / Terrestre</span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_sortie_port): ?>
            <div class="info-row">
                <span class="info-label">Départ du port :</span>
                <span class="info-value"><?php echo e(\Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y')); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Récapitulatif du parcours -->
    <div style="margin: 30px 0;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            PARCOURS DU COLIS
        </h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #667eea; color: white;">
                    <th style="padding: 10px; text-align: left;">Étape</th>
                    <th style="padding: 10px; text-align: left;">Date</th>
                    <th style="padding: 10px; text-align: left;">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Enregistrement</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e($colis->created_at?->format('d/m/Y H:i') ?? '-'); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">✓</span>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_port): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Entrée au port</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y')); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">✓</span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_sortie_port): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Sortie du port</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e(\Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y')); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">✓</span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_douane): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Entrée en douane</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e(\Carbon\Carbon::parse($colis->date_entree_douane)->format('d/m/Y')); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">✓</span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_sortie_douane): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Sortie de douane</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e(\Carbon\Carbon::parse($colis->date_sortie_douane)->format('d/m/Y')); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">✓</span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->etat_expertise === 'EFFECTUEE' && $colis->typeColis?->nom === 'Véhicules'): ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Expertise</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <?php echo e($colis->updated_at?->format('d/m/Y') ?? '-'); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">✓</span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Livraison</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">
                        <?php echo e($colis->date_livraison ? \Carbon\Carbon::parse($colis->date_livraison)->format('d/m/Y H:i') : 'En cours'); ?>

                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->status_colis_livraison === 'LIVRE'): ?>
                            <span class="badge badge-success">✓ Livré</span>
                        <?php else: ?>
                            <span class="badge badge-warning">⏳ En cours</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Commentaires de livraison -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->commentaires_cloture): ?>
    <div style="margin: 20px 0;">
        <h3 style="color: #667eea; margin: 0 0 10px 0; font-size: 16px;">Commentaires</h3>
        <div style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
            <?php echo e($colis->commentaires_cloture); ?>

        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Documents de livraison -->
    <div style="margin: 20px 0;">
        <h3 style="color: #667eea; margin: 0 0 10px 0; font-size: 16px;">Documents de livraison</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_t1): ?>
            <div style="padding: 10px; background: #f8fafc; border: 1px solid #ddd; border-radius: 5px;">
                <strong>T1:</strong> <?php echo e($colis->num_t1); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->declaration_reference): ?>
            <div style="padding: 10px; background: #f8fafc; border: 1px solid #ddd; border-radius: 5px;">
                <strong>Déclaration:</strong> <?php echo e($colis->declaration_reference); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_pvc): ?>
            <div style="padding: 10px; background: #f8fafc; border: 1px solid #ddd; border-radius: 5px;">
                <strong>PVC:</strong> <?php echo e($colis->num_pvc); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_ae): ?>
            <div style="padding: 10px; background: #f8fafc; border: 1px solid #ddd; border-radius: 5px;">
                <strong>AE:</strong> <?php echo e($colis->num_ae); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Section signature -->
    <div class="signature-section" style="margin-top: 50px;">
        <div class="signature-box">
            <div class="signature-line">Livreur</div>
            <p style="margin: 5px 0 0 0; font-size: 11px;">Nom et signature</p>
        </div>
        <div class="signature-box">
            <div class="signature-line">Destinataire</div>
            <p style="margin: 5px 0 0 0; font-size: 11px;">Nom et signature</p>
        </div>
    </div>

    <!-- Cachet entreprise -->
    <div style="margin-top: 30px; text-align: center;">
        <div style="display: inline-block; padding: 10px 20px; border: 2px solid #667eea; border-radius: 5px;">
            <strong style="color: #667eea;">KGT TRANSIT</strong><br>
            <span style="font-size: 11px;">Cachet de l'entreprise</span>
        </div>
    </div>

    <!-- Mention légale -->
    <div style="margin-top: 30px; font-size: 10px; color: #999; text-align: center;">
        <p>Ce bon de livraison officialise la remise du colis au destinataire.</p>
        <p>Signature du destinataire requise pour validation de la livraison.</p>
        <p>Document généré le <?php echo e($date); ?> - KGT TRANSIT</p>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/pdf/etape-livraison.blade.php ENDPATH**/ ?>