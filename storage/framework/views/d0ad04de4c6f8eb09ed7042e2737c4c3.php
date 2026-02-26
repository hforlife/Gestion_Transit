<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Document KGT'); ?></title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #667eea;
        }
        .company-details {
            font-size: 12px;
            color: #666;
        }
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            margin: 30px 0;
        }
        .content {
            padding: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 20px 0;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 16px;
        }
        .info-row {
            display: flex;
            margin: 5px 0;
            font-size: 14px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #667eea;
            color: white;
            padding: 10px;
            font-size: 14px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 11px;
            color: #999;
            padding: 10px;
            border-top: 1px solid #eee;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background: #10b981;
            color: white;
        }
        .badge-warning {
            background: #f59e0b;
            color: white;
        }
        .badge-danger {
            background: #ef4444;
            color: white;
        }
        .watermark {
            position: fixed;
            bottom: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            opacity: 0.1;
            font-size: 60px;
            transform: rotate(-45deg);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">KGT TRANSIT</div>
    
    <div class="company-info">
        <div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($entreprise['logo']) && file_exists($entreprise['logo'])): ?>
                <img src="<?php echo e($entreprise['logo']); ?>" alt="Logo" style="height: 60px;">
            <?php else: ?>
                <h1 style="color: #667eea;"><?php echo e($entreprise['nom']); ?></h1>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="company-details">
            <div><?php echo e($entreprise['adresse']); ?></div>
            <div>Tel: <?php echo e($entreprise['tel']); ?> | Email: <?php echo e($entreprise['email']); ?></div>
            <div><?php echo e($entreprise['rc']); ?> | <?php echo e($entreprise['ninea']); ?></div>
        </div>
    </div>

    <div class="document-title">
        <?php echo e($title); ?>

    </div>

    <div class="content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <div class="footer">
        Document généré le <?php echo e($date); ?> - KGT TRANSIT - Tous droits réservés
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\Gestion_Transit\resources\views/pdf/layout.blade.php ENDPATH**/ ?>