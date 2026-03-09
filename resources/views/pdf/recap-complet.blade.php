@extends('pdf.layout')

@section('content')
    @php
        function icon($type, $color = '#667eea')
        {
$icons = [
    'info' =>
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="' . $color . '">
            <circle cx="12" cy="12" r="10"/>
            <rect x="11" y="10" width="2" height="7" fill="#fff"/>
            <circle cx="12" cy="7" r="1.5" fill="#fff"/>
        </svg>',

    'port' =>
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="' . $color . '">
            <path d="M3 20h18v2H3z"/>
            <path d="M6 18h12L12 4z"/>
        </svg>',

    'douane' =>
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="' . $color . '">
            <path d="M3 10l9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
            <rect x="9" y="14" width="6" height="7" fill="#ffffff"/>
        </svg>',

    'expertise' =>
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="' . $color . '">
            <path d="M3 17l6 4 12-12-6-6L3 17z"/>
        </svg>',

    'livraison' =>
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="' . $color . '">
            <rect x="1" y="5" width="15" height="10"/>
            <rect x="16" y="8" width="6" height="7"/>
            <circle cx="6" cy="18" r="2"/>
            <circle cx="18" cy="18" r="2"/>
        </svg>',

    'signature' =>
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="' . $color . '">
            <path d="M2 17c3-3 6-3 9 0s6 3 9 0v2c-3 3-6 3-9 0s-6-3-9 0z"/>
        </svg>',

    'calendar' =>
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="' . $color . '">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <rect x="3" y="9" width="18" height="2" fill="#ffffff"/>
        </svg>',

    'check' =>
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="' . $color . '">
            <path d="M9 16l-4-4-2 2 6 6L21 8l-2-2z"/>
        </svg>',

    'clock' =>
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="' . $color . '">
            <circle cx="12" cy="12" r="10"/>
            <rect x="11" y="6" width="2" height="7" fill="#ffffff"/>
            <rect x="12" y="12" width="5" height="2" fill="#ffffff"/>
        </svg>',
];
            return $icons[$type] ?? '';
        }

        function formatStatut($statut)
        {
            return match ($statut) {
                'EN_ATTENTE' => ['label' => 'En attente', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
                'ENTRE' => ['label' => 'En cours', 'color' => '#3b82f6', 'bg' => '#dbeafe'],
                'SORTI', 'LIVRE', 'EFFECTUEE', 'TERMINE', 'PAYE', 'VALIDE', 'RECU' => [
                    'label' => 'Terminé',
                    'color' => '#10b981',
                    'bg' => '#d1fae5',
                ],
                default => ['label' => $statut ?? 'Non défini', 'color' => '#6b7280', 'bg' => '#f3f4f6'],
            };
        }
    @endphp

    <!-- Carte de statut général -->
    <div
        style="margin-bottom: 5px; background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%); padding: 20px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div
                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    {{-- <span style="color: white; font-size: 24px;">📋</span> --}}
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; color: #666; font-size: 14px;">Statut général</p>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <h3 style="margin: 0; color: #1e2b4f; font-size: 22px;">
                            {{ match ($colis->etat_colis) {
                                'BL_ENREGISTRE' => 'ENREGISTRÉ',
                                'AU_PORT' => 'AU PORT',
                                'A_LA_DOUANE' => 'EN DOUANE',
                                'EXPERTISE' => 'EN EXPERTISE',
                                'EN_ROUTE' => 'EN ROUTE',
                                'LIVRE' => 'LIVRÉ',
                                'CLOTURE' => 'CLÔTURÉ',
                                default => $colis->etat_colis,
                            } }}
                        </h3>
                        @php $statutInfo = formatStatut($colis->etat_colis); @endphp
                        <span
                            style="background: {{ $statutInfo['bg'] }}; color: {{ $statutInfo['color'] }}; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            {!! icon('check', $statutInfo['color']) !!} Actif
                        </span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; color: #666; font-size: 12px;">Mis à jour le</p>
                    <p style="margin: 0; color: #1e2b4f; font-weight: 600;">
                        {{ $colis->updated_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille d'information principale -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Client -->
        <div
            style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #f0f0f0; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <div
                    style="width: 40px; height: 40px; background: #667eea10; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    {{-- <span style="color: #667eea; font-size: 20px;">👤</span> --}}
                </div>
                <h4 style="margin: 0; color: #1e2b4f; font-size: 16px;">INFORMATIONS CLIENT</h4>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 100px;">Raison sociale</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e2b4f;">
                        {{ $colis->dossierTransit?->client?->nom ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Dossier</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e2b4f;">
                        {{ $colis->dossierTransit?->reference ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Type dossier</td>
                    <td style="padding: 8px 0;"><span
                            style="background: #667eea10; color: #667eea; padding: 3px 10px; border-radius: 15px; font-size: 12px;">{{ $colis->dossierTransit?->typeDossier?->nom ?? 'N/A' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Agent & Port -->
        <div
            style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #f0f0f0; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <div
                    style="width: 40px; height: 40px; background: #667eea10; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    {{-- <span style="color: #667eea; font-size: 20px;">⚓</span> --}}
                </div>
                <h4 style="margin: 0; color: #1e2b4f; font-size: 16px;">AGENT & PORT</h4>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 100px;">Agent</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e2b4f;">{{ $colis->agent?->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Port d'entrée</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e2b4f;">{{ $colis->port?->nom ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Type</td>
                    <td style="padding: 8px 0;">
                        <span
                            style="background: {{ $colis->typeColis?->nom === 'Véhicules' ? '#f59e0b20' : '#667eea20' }}; color: {{ $colis->typeColis?->nom === 'Véhicules' ? '#f59e0b' : '#667eea' }}; padding: 3px 10px; border-radius: 15px; font-size: 12px;">
                            {{ $colis->typeColis?->nom ?? 'N/A' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Description du colis -->
    @if ($colis->description)
        <div
            style="background: #f9fafc; border-radius: 15px; padding: 20px; margin-bottom: 30px; border-left: 4px solid #667eea;">
            <div style="display: flex; gap: 10px;">
                {{-- <span style="color: #667eea; font-size: 20px;">📝</span> --}}
                <div>
                    <p style="margin: 0 0 5px 0; color: #666; font-size: 12px;">Description du colis</p>
                    <p style="margin: 0; color: #1e2b4f;">{{ $colis->description }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Timeline des étapes - Version tableau compact -->
    <div style="margin: 30px 0;">
        <h3 style="color: #1e2b4f; margin: 0 0 15px 0; font-size: 18px; display: flex; align-items: center; gap: 10px;">
            <span
                style="width: 30px; height: 30px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px;">✓</span>
            PARCOURS DU COLIS
        </h3>

        <!-- Tableau compact des étapes -->
        <table style="width: 100%; border-collapse: collapse; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <thead>
                <tr style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                    <th style="padding: 10px; text-align: left; border-radius: 8px 0 0 0;">Étape</th>
                    <th style="padding: 10px; text-align: left;">Statut</th>
                    <th style="padding: 10px; text-align: left;">Dates clés</th>
                    <th style="padding: 10px; text-align: left;">Documents</th>
                    <th style="padding: 10px; text-align: left; border-radius: 0 8px 0 0;">Durée</th>
                </tr>
            </thead>
            <tbody>
                <!-- Étape Port -->
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px; background: #f9fafc;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            {{-- {!! icon('port', '#667eea') !!} --}}
                            <span style="font-weight: 600; color: #1e2b4f;">Port</span>
                        </div>
                    </td>
                    <td style="padding: 12px;">
                        @php $statutPort = formatStatut($colis->status_colis_port); @endphp
                        <span
                            style="display: inline-block; background: {{ $statutPort['bg'] }}; color: {{ $statutPort['color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                            {{ $statutPort['label'] }}
                        </span>
                    </td>
                    <td style="padding: 12px; font-size: 13px;">
                        <div><span style="color: #666;">Entrée:</span>
                            <strong>{{ $colis->date_entree_port ? \Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y') : '-' }}</strong>
                        </div>
                        <div><span style="color: #666;">Sortie:</span>
                            <strong>{{ $colis->date_sortie_port ? \Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y') : '-' }}</strong>
                        </div>
                    </td>
                    <td style="padding: 12px; font-size: 13px;">-</td>
                    <td style="padding: 12px; font-size: 13px;">
                        @if ($colis->date_entree_port && $colis->date_sortie_port)
                            <strong>{{ \Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(\Carbon\Carbon::parse($colis->date_sortie_port)) }}j</strong>
                        @elseif($colis->date_entree_port)
                            <span style="color: #f59e0b;">En cours</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>

                <!-- Étape Douane -->
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px; background: #f9fafc;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            {{-- {!! icon('douane', '#667eea') !!} --}}
                            <span style="font-weight: 600; color: #1e2b4f;">Douane</span>
                        </div>
                    </td>
                    <td style="padding: 12px;">
                        @php $statutDouane = formatStatut($colis->status_douane); @endphp
                        <span
                            style="display: inline-block; background: {{ $statutDouane['bg'] }}; color: {{ $statutDouane['color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                            {{ $statutDouane['label'] }}
                        </span>
                    </td>
                    <td style="padding: 12px; font-size: 13px;">
                        <div><span style="color: #666;">Entrée:</span>
                            <strong>{{ $colis->date_entree_douane ? \Carbon\Carbon::parse($colis->date_entree_douane)->format('d/m/Y') : '-' }}</strong>
                        </div>
                        <div><span style="color: #666;">Sortie:</span>
                            <strong>{{ $colis->date_sortie_douane ? \Carbon\Carbon::parse($colis->date_sortie_douane)->format('d/m/Y') : '-' }}</strong>
                        </div>
                    </td>
                    <td style="padding: 12px; font-size: 13px;">
                        @if ($colis->num_t1)
                            <div><span style="color: #666;">T1:</span> {{ $colis->num_t1 }}</div>
                            @if ($colis->etat_t1)
                                <span
                                    style="font-size: 11px; background: {{ $colis->etat_t1 === 'PAYE' ? '#10b98120' : '#f59e0b20' }}; color: {{ $colis->etat_t1 === 'PAYE' ? '#10b981' : '#f59e0b' }}; padding: 2px 6px; border-radius: 10px;">{{ $colis->etat_t1 }}</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 12px; font-size: 13px;">
                        @if ($colis->date_entree_douane && $colis->date_sortie_douane)
                            <strong>{{ \Carbon\Carbon::parse($colis->date_entree_douane)->diffInDays(\Carbon\Carbon::parse($colis->date_sortie_douane)) }}j</strong>
                        @elseif($colis->date_entree_douane)
                            <span style="color: #f59e0b;">En cours</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>

                <!-- Expertise (si véhicule) -->
                @if ($colis->typeColis?->nom === 'Véhicules')
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px; background: #f9fafc;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                {{-- {!! icon('expertise', '#667eea') !!} --}}
                                <span style="font-weight: 600; color: #1e2b4f;">Expertise</span>
                            </div>
                        </td>
                        <td style="padding: 12px;">
                            @php $statutExpertise = formatStatut($colis->etat_expertise); @endphp
                            <span
                                style="display: inline-block; background: {{ $statutExpertise['bg'] }}; color: {{ $statutExpertise['color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                {{ $statutExpertise['label'] }}
                            </span>
                        </td>
                        <td style="padding: 12px; font-size: 13px;">
                            <span style="color: #666;">Réalisée:</span>
                            <strong>{{ $colis->etat_expertise === 'EFFECTUEE' ? $colis->updated_at?->format('d/m/Y') ?? '-' : '-' }}</strong>
                        </td>
                        <td style="padding: 12px; font-size: 13px;">
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                @if ($colis->num_pvc)
                                    <span style="background: #667eea10; padding: 2px 8px; border-radius: 12px;">
                                        PVC <span
                                            style="color: {{ $colis->etat_pvc === 'PAYE' ? '#10b981' : ($colis->etat_pvc === 'RECU' ? '#f59e0b' : '#ef4444') }};">●</span>
                                    </span>
                                @endif
                                @if ($colis->num_ae)
                                    <span style="background: #667eea10; padding: 2px 8px; border-radius: 12px;">
                                        AE <span
                                            style="color: {{ $colis->etat_ae === 'VALIDE' ? '#10b981' : '#ef4444' }};">●</span>
                                    </span>
                                @endif
                                @if ($colis->num_cmc)
                                    <span style="background: #667eea10; padding: 2px 8px; border-radius: 12px;">
                                        CMC <span
                                            style="color: {{ $colis->etat_cmc === 'RECU' ? '#10b981' : '#ef4444' }};">●</span>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 12px; font-size: 13px;">-</td>
                    </tr>
                @endif

                <!-- Étape Livraison -->
                <tr>
                    <td style="padding: 12px; background: #f9fafc; border-radius: 0 0 0 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            {{-- {!! icon('livraison', '#667eea') !!} --}}
                            <span style="font-weight: 600; color: #1e2b4f;">Livraison</span>
                        </div>
                    </td>
                    <td style="padding: 12px;">
                        @php $statutLivraison = formatStatut($colis->status_colis_livraison); @endphp
                        <span
                            style="display: inline-block; background: {{ $statutLivraison['bg'] }}; color: {{ $statutLivraison['color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                            {{ $statutLivraison['label'] }}
                        </span>
                    </td>
                    <td style="padding: 12px; font-size: 13px;">
                        <div><span style="color: #666;">Date:</span>
                            <strong>{{ $colis->date_livraison ? \Carbon\Carbon::parse($colis->date_livraison)->format('d/m/Y H:i') : '-' }}</strong>
                        </div>
                        @if ($colis->commentaires_cloture)
                            <div style="font-size: 11px; color: #666; margin-top: 4px;">
                                {{ \Str::limit($colis->commentaires_cloture, 30) }}</div>
                        @endif
                    </td>
                    <td style="padding: 12px; font-size: 13px;">-</td>
                    <td style="padding: 12px; font-size: 13px; border-radius: 0 0 8px 0;">
                        @if ($colis->created_at && $colis->date_livraison)
                            @php
                                $delai = \Carbon\Carbon::parse($colis->created_at)->diffInDays(
                                    \Carbon\Carbon::parse($colis->date_livraison),
                                );
                            @endphp
                            <span style="color: {{ $delai > 7 ? '#ef4444' : '#10b981' }};">
                                <strong>{{ $delai }}j</strong>
                            </span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Tableau des documents associés - Version ultra compacte -->
    <div style="margin: 25px 0;">
        <h3 style="color: #1e2b4f; margin: 0 0 12px 0; font-size: 16px; display: flex; align-items: center; gap: 8px;">
            DOCUMENTS ASSOCIÉS
        </h3>

        <table
            style="width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
            <thead>
                <tr style="background: #667eea; color: white;">
                    <th style="padding: 8px 12px; text-align: left; font-size: 12px;">Document</th>
                    <th style="padding: 8px 12px; text-align: left; font-size: 12px;">Numéro</th>
                    <th style="padding: 8px 12px; text-align: center; font-size: 12px;">État</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $documents = [
                        [
                            'name' => 'T1',
                            'icon' => '✓',
                            'num' => $colis->num_t1,
                            'etat' => $colis->etat_t1,
                            'colors' => ['PAYE' => 'success', 'FOURNI' => 'warning'],
                        ],
                        [
                            'name' => 'PVC',
                            'icon' => '✓',
                            'num' => $colis->num_pvc,
                            'etat' => $colis->etat_pvc,
                            'colors' => ['PAYE' => 'success', 'RECU' => 'warning', 'NON_RECU' => 'danger'],
                        ],
                        [
                            'name' => 'AE',
                            'icon' => '✓',
                            'num' => $colis->num_ae,
                            'etat' => $colis->etat_ae,
                            'colors' => ['VALIDE' => 'success', 'NON_VALIDE' => 'danger'],
                        ],
                        [
                            'name' => 'CMC',
                            'icon' => '✓',
                            'num' => $colis->num_cmc,
                            'etat' => $colis->etat_cmc,
                            'colors' => ['RECU' => 'success', 'NON_RECU' => 'danger'],
                        ],
                    ];
                @endphp

                @foreach ($documents as $doc)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 10px 12px; background: #f9fafc;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="font-size: 14px;">{{ $doc['icon'] }}</span>
                                <span
                                    style="font-weight: 600; color: #1e2b4f; font-size: 13px;">{{ $doc['name'] }}</span>
                            </div>
                        </td>
                        <td style="padding: 10px 12px;">
                            <span
                                style="font-weight: 500; color: #1e2b4f; font-size: 13px;">{{ $doc['num'] ?? '—' }}</span>
                        </td>
                        <td style="padding: 10px 12px; text-align: center;">
                            @if ($doc['etat'])
                                @php
                                    $color = match (true) {
                                        in_array($doc['etat'], ['PAYE', 'VALIDE', 'RECU']) => '#10b981',
                                        in_array($doc['etat'], ['FOURNI']) => '#f59e0b',
                                        default => '#ef4444',
                                    };
                                    $bg = $color . '20';
                                @endphp
                                <span
                                    style="display: inline-block; background: {{ $bg }}; color: {{ $color }}; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500;">
                                    {{ $doc['etat'] }}
                                </span>
                            @else
                                <span style="color: #999; font-size: 11px;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- Section signature - Format bloc carré -->
<div style="margin-top: 50px; display: flex; justify-content: space-between; gap: 20px;">
    
    <!-- Signature KGT -->
    <div style="width: 48%;">
        <div style="border: 2px solid #667eea; border-radius: 8px; overflow: hidden; margin-top: 20px;">
            <!-- Titre -->
            <div style="background: #667eea; padding: 12px; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    {{-- {!! icon('signature', 'white') !!} --}}
                    <span style="color: white; font-weight: 600; font-size: 14px;">KGT TRANSIT</span>
                </div>
            </div>
            
            <!-- Corps -->
            <div style="padding: 20px; background: white; min-height: 120px; display: flex; flex-direction: column; justify-content: flex-end;">
                <div style="border: 1px dashed #667eea; border-radius: 6px; padding: 15px; text-align: center; background: #f9fafc;">
                    <div style="height: 50px;"></div>
                    <div style="border-top: 2px solid #667eea; padding-top: 8px;">
                        <p style="margin: 0; color: #667eea; font-size: 11px; font-weight: 500;">Cachet et signature</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Client -->
    {{-- <div style="width: 48%;">
        <div style="border: 2px solid #10b981; border-radius: 8px; overflow: hidden;">
            <!-- Titre -->
            <div style="background: #10b981; padding: 12px; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    
                    <span style="color: white; font-weight: 600; font-size: 14px;">DESTINATAIRE</span>
                </div>
            </div>
            
            <!-- Corps -->
            <div style="padding: 20px; background: white; min-height: 120px; display: flex; flex-direction: column;">
                <div style="margin-bottom: 10px; text-align: center;">
                    <p style="margin: 0; color: #1e2b4f; font-weight: 600; font-size: 14px;">
                        {{ $colis->dossierTransit?->client?->nom ?? 'CLIENT' }}
                    </p>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end;">
                    <div style="border: 1px dashed #10b981; border-radius: 6px; padding: 15px; text-align: center; background: #f9fafc;">
                        <div style="height: 30px;"></div>
                        <div style="border-top: 2px solid #10b981; padding-top: 8px;">
                            <p style="margin: 0; color: #10b981; font-size: 11px; font-weight: 500;">Lu et approuvé</p>
                            <p style="margin: 2px 0 0 0; color: #666; font-size: 10px;">Cachet et signature</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>

    <!-- Mentions légales élégantes -->
    <div style="margin-top: 40px; background: #f9fafc; border-radius: 10px; padding: 20px; text-align: center;">
        <div style="display: flex; justify-content: center; gap: 30px; margin-bottom: 15px; flex-wrap: wrap;">
            <span style="color: #666; font-size: 11px;">RC: SN-DKR-2025-00123</span>
            <span style="color: #666; font-size: 11px;">NINEA: 12345678A</span>
            <span style="color: #666; font-size: 11px;">Tél: +223 33 123 45 67</span>
        </div>
        <p style="margin: 0; color: #999; font-size: 10px;">
            KGT TRANSIT - 123, Avenue du Port, Dakar, Sénégal - www.kgt-transit.com
        </p>
        <p style="margin: 5px 0 0 0; color: #999; font-size: 9px;">
            Document généré le {{ now()->format('d/m/Y à H:i:s') }} - Ce document est un récapitulatif officiel du
            processus de transit
        </p>
    </div>
@endsection
