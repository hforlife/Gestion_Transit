

<?php $__env->startSection('content'); ?>
    <!-- En-tête du document -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: #667eea; margin: 0;">BORDEREAU DE LIVRAISON</h2>
        <p style="color: #666; font-size: 12px; margin: 5px 0;">N° BL: <?php echo e($colis->numero_bl); ?></p>
    </div>

    <!-- Statut et QR Code (simulé) -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div>
            <span class="badge badge-<?php echo e($colis->etat_colis === 'LIVRE' ? 'success' : 'warning'); ?>" style="font-size: 14px; padding: 8px 15px;">
                <?php echo e(match($colis->etat_colis) {
                    'BL_ENREGISTRE' => 'ENREGISTRÉ',
                    'AU_PORT' => 'AU PORT',
                    'A_LA_DOUANE' => 'EN DOUANE',
                    'EXPERTISE' => 'EN EXPERTISE',
                    'EN_ROUTE' => 'EN ROUTE',
                    'LIVRE' => 'LIVRÉ',
                    'CLOTURE' => 'CLÔTURÉ',
                    default => $colis->etat_colis
                }); ?>

            </span>
        </div>
        <div style="text-align: right;">
            <div style="background: #f0f0f0; padding: 10px; border-radius: 5px; display: inline-block;">
                <span style="font-size: 12px; color: #666;">Date d'émission</span><br>
                <strong><?php echo e(now()->format('d/m/Y')); ?></strong>
            </div>
        </div>
    </div>

    <!-- Informations expéditeur/destinataire -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Expéditeur -->
        <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #f9f9f9;">
            <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
                EXPÉDITEUR
            </h3>
            <div style="font-size: 14px;">
                <p style="margin: 5px 0;"><strong>KGT TRANSIT</strong></p>
                <p style="margin: 5px 0;">123, Avenue du Port</p>
                <p style="margin: 5px 0;">Dakar, Sénégal</p>
                <p style="margin: 5px 0;">Tel: +221 33 123 45 67</p>
                <p style="margin: 5px 0;">Email: contact@kgt-transit.com</p>
            </div>
        </div>

        <!-- Destinataire -->
        <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #f9f9f9;">
            <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
                DESTINATAIRE
            </h3>
            <div style="font-size: 14px;">
                <p style="margin: 5px 0;"><strong><?php echo e($colis->dossierTransit?->client?->nom ?? 'N/A'); ?></strong></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->dossierTransit?->client): ?>
                    <p style="margin: 5px 0;"><?php echo e($colis->dossierTransit->client->adresse ?? 'Adresse non renseignée'); ?></p>
                    <p style="margin: 5px 0;">Tel: <?php echo e($colis->dossierTransit->client->telephone ?? 'Non renseigné'); ?></p>
                    <p style="margin: 5px 0;">Email: <?php echo e($colis->dossierTransit->client->email ?? 'Non renseigné'); ?></p>
                <?php else: ?>
                    <p style="margin: 5px 0;">Client non spécifié</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Informations du colis -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            DÉTAILS DU COLIS
        </h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5; width: 30%;"><strong>Type de colis</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->typeColis?->nom ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Description</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->description ?? 'Aucune description'); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Port d'entrée</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->port?->nom ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Agent responsable</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->agent?->name ?? 'N/A'); ?></td>
            </tr>
        </table>
    </div>

    <!-- Suivi des étapes -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            SUIVI DES ÉTAPES
        </h3>
        
        <div style="display: flex; justify-content: space-between; margin: 20px 0;">
            <!-- Étape Port -->
            <div style="text-align: center; flex: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 10px; 
                    background: <?php echo e($colis->status_colis_port === 'SORTI' ? '#10b981' : ($colis->status_colis_port !== 'EN_ATTENTE' ? '#f59e0b' : '#ddd')); ?>;">
                </div>
                <div style="font-size: 12px;">
                    <strong>PORT</strong><br>
                    <span style="color: #666;">
                        <?php echo e(match($colis->status_colis_port) {
                            'EN_ATTENTE' => 'En attente',
                            'ENTRE' => 'Entré',
                            'SORTI' => 'Sorti',
                            default => '-'
                        }); ?>

                    </span>
                </div>
            </div>

            <!-- Étape Douane -->
            <div style="text-align: center; flex: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 10px; 
                    background: <?php echo e($colis->status_colis_douane === 'SORTI' ? '#10b981' : ($colis->status_colis_douane !== 'EN_ATTENTE' ? '#f59e0b' : '#ddd')); ?>;">
                </div>
                <div style="font-size: 12px;">
                    <strong>DOUANE</strong><br>
                    <span style="color: #666;">
                        <?php echo e(match($colis->status_colis_douane) {
                            'EN_ATTENTE' => 'En attente',
                            'ENTRE' => 'Entré',
                            'SORTI' => 'Sorti',
                            default => '-'
                        }); ?>

                    </span>
                </div>
            </div>

            <!-- Étape Expertise (si véhicule) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->typeColis?->nom === 'Véhicules'): ?>
            <div style="text-align: center; flex: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 10px; 
                    background: <?php echo e($colis->etat_expertise === 'EFFECTUEE' ? '#10b981' : ($colis->etat_expertise !== 'EN_ATTENTE' ? '#f59e0b' : '#ddd')); ?>;">
                </div>
                <div style="font-size: 12px;">
                    <strong>EXPERTISE</strong><br>
                    <span style="color: #666;">
                        <?php echo e($colis->etat_expertise === 'EFFECTUEE' ? 'Effectuée' : 'En attente'); ?>

                    </span>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Étape Livraison -->
            <div style="text-align: center; flex: 1;">
                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 10px; 
                    background: <?php echo e($colis->status_colis_livraison === 'LIVRE' ? '#10b981' : '#ddd'); ?>;">
                </div>
                <div style="font-size: 12px;">
                    <strong>LIVRAISON</strong><br>
                    <span style="color: #666;">
                        <?php echo e($colis->status_colis_livraison === 'LIVRE' ? 'Livré' : 'En attente'); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents associés -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            DOCUMENTS ASSOCIÉS
        </h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_t1): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5; width: 30%;"><strong>Document T1</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->num_t1); ?></td>
                <td style="padding: 8px; border: 1px solid #ddd; width: 15%; text-align: center;">
                    <span class="badge badge-<?php echo e($colis->etat_t1 === 'PAYE' ? 'success' : 'warning'); ?>">
                        <?php echo e($colis->etat_t1 ?? 'N/A'); ?>

                    </span>
                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->declaration_reference): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Déclaration</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;" colspan="2"><?php echo e($colis->declaration_reference); ?></td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->typeColis?->nom === 'Véhicules'): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_pvc): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>PVC</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->num_pvc); ?></td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                        <span class="badge badge-<?php echo e($colis->etat_pvc === 'PAYE' ? 'success' : ($colis->etat_pvc === 'RECU' ? 'warning' : 'danger')); ?>">
                            <?php echo e($colis->etat_pvc ?? 'N/A'); ?>

                        </span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_ae): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>AE</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->num_ae); ?></td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                        <span class="badge badge-<?php echo e($colis->etat_ae === 'VALIDE' ? 'success' : 'danger'); ?>">
                            <?php echo e($colis->etat_ae ?? 'N/A'); ?>

                        </span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->num_cmc): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>CMC</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo e($colis->num_cmc); ?></td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                        <span class="badge badge-<?php echo e($colis->etat_cmc === 'RECU' ? 'success' : 'danger'); ?>">
                            <?php echo e($colis->etat_cmc ?? 'N/A'); ?>

                        </span>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>
    </div>

    <!-- Commentaires -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->commentaires_cloture): ?>
    <div style="margin-bottom: 30px;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            COMMENTAIRES
        </h3>
        <div style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
            <?php echo e($colis->commentaires_cloture); ?>

        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Récapitulatif des dates -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            HISTORIQUE DES DATES
        </h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5; width: 40%;"><strong>Date de création</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo e($colis->created_at ? \Carbon\Carbon::parse($colis->created_at)->format('d/m/Y H:i') : '-'); ?>

                </td>
            </tr>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_port): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Entrée au port</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y')); ?>

                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_sortie_port): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Sortie du port</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo e(\Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y')); ?>

                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_douane): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Entrée en douane</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo e(\Carbon\Carbon::parse($colis->date_entree_douane)->format('d/m/Y')); ?>

                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_sortie_douane): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Sortie de douane</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo e(\Carbon\Carbon::parse($colis->date_sortie_douane)->format('d/m/Y')); ?>

                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_livraison): ?>
            <tr>
                <td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Date de livraison</strong></td>
                <td style="padding: 8px; border: 1px solid #ddd;">
                    <?php echo e(\Carbon\Carbon::parse($colis->date_livraison)->format('d/m/Y H:i')); ?>

                </td>
            </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>
    </div>

    <!-- Section signature -->
    <div style="margin-top: 50px;">
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 45%;">
                <div style="border-top: 2px solid #333; padding-top: 10px; text-align: center;">
                    <strong>Cachet et signature de KGT TRANSIT</strong>
                </div>
            </div>
            <div style="width: 45%;">
                <div style="border-top: 2px solid #333; padding-top: 10px; text-align: center;">
                    <strong>Cachet et signature du client</strong>
                    <p style="font-size: 11px; color: #666; margin-top: 5px;">Lu et approuvé</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mentions légales -->
    <div style="margin-top: 30px; font-size: 10px; color: #999; text-align: center;">
        <p>KGT TRANSIT - RC: SN-DKR-2025-00123 - NINEA: 12345678A</p>
        <p>Siège social: 123, Avenue du Port, Dakar, Sénégal - Tél: +221 33 123 45 67</p>
        <p>Ce document fait office de bordereau de livraison. Toute modification doit être approuvée par KGT TRANSIT.</p>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pdf.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/pdf/bl.blade.php ENDPATH**/ ?>