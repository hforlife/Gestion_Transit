<?php
    use Illuminate\Support\Collection;
    
    $stats = [
        'AU_PORT' => ['label' => 'Au port', 'count' => 0, 'color' => '#f59e0b', 'bg' => '#fef3c7'],
        'A_LA_DOUANE' => ['label' => 'En douane', 'count' => 0, 'color' => '#3b82f6', 'bg' => '#dbeafe'],
        'EXPERTISE' => ['label' => 'En expertise', 'count' => 0, 'color' => '#8b5cf6', 'bg' => '#ede9fe'],
        'EN_ROUTE' => ['label' => 'En route', 'count' => 0, 'color' => '#06b6d4', 'bg' => '#cffafe'],
        'LIVRE' => ['label' => 'Livré', 'count' => 0, 'color' => '#10b981', 'bg' => '#d1fae5'],
    ];
    
    $total = 0;
    foreach ($unites as $unite) {
        if (isset($stats[$unite->etat])) {
            $stats[$unite->etat]['count']++;
            $total++;
        }
    }
    
    $termine = $stats['LIVRE']['count'];
    $pourcentage = $total > 0 ? round(($termine / $total) * 100) : 0;
?>

<div class="mt-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
    <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Suivi des unités (<?php echo e($total); ?> au total)
    </h4>
    
    <!-- Barre de progression -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total > 0): ?>
    <div class="mb-5">
        <div class="flex justify-between text-xs text-gray-600 mb-1.5">
            <span class="font-medium">Progression globale</span>
            <span class="font-semibold text-green-600"><?php echo e($termine); ?>/<?php echo e($total); ?> livrés (<?php echo e($pourcentage); ?>%)</span>
        </div>
        <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-500" 
                 style="width: <?php echo e($pourcentage); ?>%"></div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <!-- Statistiques par état -->
    <div class="grid grid-cols-5 gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stat['count'] > 0): ?>
                <div class="text-center p-2 rounded-lg" style="background-color: <?php echo e($stat['bg']); ?>">
                    <span class="text-xs font-medium text-gray-600"><?php echo e($stat['label']); ?></span>
                    <div class="text-xl font-bold" style="color: <?php echo e($stat['color']); ?>">
                        <?php echo e($stat['count']); ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="text-center p-2 rounded-lg bg-gray-100 opacity-50">
                    <span class="text-xs text-gray-500"><?php echo e($stat['label']); ?></span>
                    <div class="text-xl font-bold text-gray-400">0</div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    
    <!-- Liste rapide des unités (optionnel) -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total > 0 && $total <= 10): ?>
    <div class="mt-4 pt-4 border-t border-gray-200">
        <h5 class="text-xs font-medium text-gray-500 mb-2">Détail des unités</h5>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $unites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div class="flex items-center gap-2 p-1.5 bg-white rounded border border-gray-100">
                    <span class="w-2 h-2 rounded-full" style="background-color: <?php echo e($stats[$unite->etat]['color']); ?>"></span>
                    <span class="font-mono"><?php echo e($unite->numero_chassis ?? $unite->numero_conteneur ?? 'N/A'); ?></span>
                    <span class="text-gray-500"><?php echo e($unite->type); ?></span>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/filament/components/unites-resume.blade.php ENDPATH**/ ?>