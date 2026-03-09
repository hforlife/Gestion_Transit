<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        <?php
            $stats = [
                'total' => App\Models\ColisUnite::count(),
                'en_route' => App\Models\ColisUnite::where('etat', 'EN_ROUTE')->count(),
                'livre' => App\Models\ColisUnite::where('etat', 'LIVRE')->count(),
                'au_port' => App\Models\ColisUnite::where('etat', 'AU_PORT')->count(),
                'a_la_douane' => App\Models\ColisUnite::where('etat', 'A_LA_DOUANE')->count(),
                'expertise' => App\Models\ColisUnite::where('etat', 'EXPERTISE')->count(),
                
                // Statistiques par type
                'conteneurs' => App\Models\ColisUnite::where('type', 'CONTENEUR')->count(),
                'chassis' => App\Models\ColisUnite::whereIn('type', ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])->count(),
                
                // Statistiques avancées
                'aujourdhui' => App\Models\ColisUnite::whereDate('date_livraison', today())->count(),
                'avec_date_livraison' => App\Models\ColisUnite::whereNotNull('date_livraison')->count(),
            ];
            
            $totalNonLivre = $stats['en_route'] + $stats['au_port'] + $stats['a_la_douane'] + $stats['expertise'];
            $tauxReussite = $stats['total'] > 0 
                ? round(($stats['livre'] / $stats['total']) * 100) 
                : 0;
        ?>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total unités</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            <?php echo e($stats['total']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-cube','class' => 'w-6 h-6 text-blue-600 dark:text-blue-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-cube','class' => 'w-6 h-6 text-blue-600 dark:text-blue-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    <span>📦 <?php echo e($stats['conteneurs']); ?> conteneurs</span>
                    <span class="ml-2">🚛 <?php echo e($stats['chassis']); ?> châssis</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Au port</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            <?php echo e($stats['au_port']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En douane</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            <?php echo e($stats['a_la_douane']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-500/10 rounded-full">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-building-library','class' => 'w-6 h-6 text-purple-600 dark:text-purple-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-building-library','class' => 'w-6 h-6 text-purple-600 dark:text-purple-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Expertise</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            <?php echo e($stats['expertise']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-500/10 rounded-full">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-clipboard-document-check','class' => 'w-6 h-6 text-orange-600 dark:text-orange-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-clipboard-document-check','class' => 'w-6 h-6 text-orange-600 dark:text-orange-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En route</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            <?php echo e($stats['en_route']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-full">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-truck','class' => 'w-6 h-6 text-yellow-600 dark:text-yellow-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-truck','class' => 'w-6 h-6 text-yellow-600 dark:text-yellow-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deuxième ligne : Livraisons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Livrées</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            <?php echo e($stats['livre']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-check-circle','class' => 'w-6 h-6 text-green-600 dark:text-green-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-check-circle','class' => 'w-6 h-6 text-green-600 dark:text-green-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Avec date livraison</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            <?php echo e($stats['avec_date_livraison']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-500/10 rounded-full">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-calendar','class' => 'w-6 h-6 text-indigo-600 dark:text-indigo-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-calendar','class' => 'w-6 h-6 text-indigo-600 dark:text-indigo-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Livrées aujourd'hui</p>
                        <p class="text-2xl font-bold text-teal-600 dark:text-teal-400">
                            <?php echo e($stats['aujourdhui']); ?>

                        </p>
                    </div>
                    <div class="p-3 bg-teal-100 dark:bg-teal-500/10 rounded-full">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-calendar-days','class' => 'w-6 h-6 text-teal-600 dark:text-teal-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-calendar-days','class' => 'w-6 h-6 text-teal-600 dark:text-teal-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques avancées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Performance livraison</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Taux de livraison</span>
                        <span class="font-medium text-gray-900 dark:text-white"><?php echo e($tauxReussite); ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: <?php echo e($tauxReussite); ?>%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span><?php echo e($stats['livre']); ?> livrées</span>
                        <span><?php echo e($totalNonLivre); ?> en cours</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Livraisons du jour</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Aujourd'hui</span>
                        <span class="font-medium text-gray-900 dark:text-white"><?php echo e($stats['aujourdhui']); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total en cours</span>
                        <span class="font-medium text-yellow-600"><?php echo e($totalNonLivre); ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Répartition</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Conteneurs</span>
                        <span class="font-medium text-blue-600"><?php echo e($stats['conteneurs']); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Châssis</span>
                        <span class="font-medium text-orange-600"><?php echo e($stats['chassis']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des unités -->
        <?php echo e($this->table); ?>

    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Actualisation automatique toutes les 30 secondes
        setInterval(function() {
            Livewire.dispatch('refresh');
        }, 30000);
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/filament/pages/etape-expertise.blade.php ENDPATH**/ ?>