<div style="margin-bottom: 30px; page-break-inside: avoid;">
    <h3 style="color: #667eea; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
        ⚓ ÉTAPE PORTUAIRE
    </h3>

    <div style="background: #f8fafc; padding: 15px; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
            <div>
                <span class="badge badge-<?php echo e($colis->status_colis_port === 'SORTI' ? 'success' : ($colis->status_colis_port !== 'EN_ATTENTE' ? 'warning' : 'secondary')); ?>">
                    Statut: <?php echo e(match($colis->status_colis_port) {
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré au port',
                        'SORTI' => 'Sorti du port',
                        default => $colis->status_colis_port ?? 'Non défini'
                    }); ?>

                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Date d'entrée</strong></td>
                    <td style="padding: 8px;">
                        <?php echo e($colis->date_entree_port ? \Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y') : 'Non renseignée'); ?>

                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px; background: #f0f0f0;"><strong>Date de sortie</strong></td>
                    <td style="padding: 8px;">
                        <?php echo e($colis->date_sortie_port ? \Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y') : 'Non renseignée'); ?>

                    </td>
                </tr>
            </table>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Durée de séjour</strong></td>
                    <td style="padding: 8px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colis->date_entree_port && $colis->date_sortie_port): ?>
                            <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(\Carbon\Carbon::parse($colis->date_sortie_port))); ?> jours
                        <?php elseif($colis->date_entree_port): ?>
                            En cours depuis <?php echo e(\Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(now())); ?> jours
                        <?php else: ?>
                            -
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/pdf/sections/port.blade.php ENDPATH**/ ?>