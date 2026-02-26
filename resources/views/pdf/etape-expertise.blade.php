@extends('pdf.layout')

@section('content')
    <!-- En-tête avec statut -->
    <div style="text-align: right; margin-bottom: 20px;">
        <span class="badge badge-{{ $colis->etat_expertise === 'EFFECTUEE' ? 'success' : 'warning' }}">
            {{ match($colis->etat_expertise) {
                'EN_ATTENTE' => 'EN ATTENTE D\'EXPERTISE',
                'EFFECTUEE' => 'EXPERTISE EFFECTUÉE',
                default => $colis->etat_expertise
            } }}
        </span>
    </div>

    <!-- Informations générales du colis -->
    <div class="info-grid">
        <div class="info-box">
            <h3>Informations du colis</h3>
            <div class="info-row">
                <span class="info-label">N° BL :</span>
                <span class="info-value">{{ $colis->numero_bl }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Type :</span>
                <span class="info-value">{{ $colis->typeColis?->nom ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Description :</span>
                <span class="info-value">{{ $colis->description ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Client :</span>
                <span class="info-value">{{ $colis->dossierTransit?->client?->nom ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Agent :</span>
                <span class="info-value">{{ $colis->agent?->name ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="info-box">
            <h3>Statut expertise</h3>
            <div class="info-row">
                <span class="info-label">État :</span>
                <span class="info-value">
                    @if($colis->etat_expertise === 'EFFECTUEE')
                        <span class="badge badge-success">Effectuée</span>
                    @else
                        <span class="badge badge-warning">En attente</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Documents :</span>
                <span class="info-value">
                    @php
                        $totalDocs = ($colis->num_pvc ? 1 : 0) + ($colis->num_ae ? 1 : 0) + ($colis->num_cmc ? 1 : 0);
                        $completedDocs = ($colis->etat_pvc === 'PAYE' ? 1 : 0) + 
                                         ($colis->etat_ae === 'VALIDE' ? 1 : 0) + 
                                         ($colis->etat_cmc === 'RECU' ? 1 : 0);
                    @endphp
                    {{ $completedDocs }}/3 documents validés
                </span>
            </div>
        </div>
    </div>

    <!-- Documents d'expertise -->
    <div style="margin: 30px 0;">
        <h3 style="color: #667eea; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            DOCUMENTS D'EXPERTISE
        </h3>

        <!-- PVC -->
        <div style="margin-bottom: 20px;">
            <h4 style="color: #667eea; margin: 0 0 10px 0;">1. Procès-Verbal de Contrôle (PVC)</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 15px; border-radius: 5px;">
                <div>
                    <div class="info-row">
                        <span class="info-label">Numéro :</span>
                        <span class="info-value">{{ $colis->num_pvc ?? 'Non fourni' }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-row">
                        <span class="info-label">État :</span>
                        <span class="info-value">
                            @if($colis->etat_pvc)
                                <span class="badge badge-{{ $colis->etat_pvc === 'PAYE' ? 'success' : ($colis->etat_pvc === 'RECU' ? 'warning' : 'danger') }}">
                                    {{ match($colis->etat_pvc) {
                                        'NON_RECU' => 'Non reçu',
                                        'RECU' => 'Reçu',
                                        'PAYE' => 'Payé',
                                        default => $colis->etat_pvc
                                    } }}
                                </span>
                            @else
                                Non défini
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AE -->
        <div style="margin-bottom: 20px;">
            <h4 style="color: #667eea; margin: 0 0 10px 0;">2. Autorisation d'Enlèvement (AE)</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 15px; border-radius: 5px;">
                <div>
                    <div class="info-row">
                        <span class="info-label">Numéro :</span>
                        <span class="info-value">{{ $colis->num_ae ?? 'Non fourni' }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-row">
                        <span class="info-label">État :</span>
                        <span class="info-value">
                            @if($colis->etat_ae)
                                <span class="badge badge-{{ $colis->etat_ae === 'VALIDE' ? 'success' : 'danger' }}">
                                    {{ match($colis->etat_ae) {
                                        'NON_VALIDE' => 'Non valide',
                                        'VALIDE' => 'Valide',
                                        default => $colis->etat_ae
                                    } }}
                                </span>
                            @else
                                Non défini
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CMC -->
        <div style="margin-bottom: 20px;">
            <h4 style="color: #667eea; margin: 0 0 10px 0;">3. Certificat de Mise en Conformité (CMC)</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fafc; padding: 15px; border-radius: 5px;">
                <div>
                    <div class="info-row">
                        <span class="info-label">Numéro :</span>
                        <span class="info-value">{{ $colis->num_cmc ?? 'Non fourni' }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-row">
                        <span class="info-label">État :</span>
                        <span class="info-value">
                            @if($colis->etat_cmc)
                                <span class="badge badge-{{ $colis->etat_cmc === 'RECU' ? 'success' : 'danger' }}">
                                    {{ match($colis->etat_cmc) {
                                        'NON_RECU' => 'Non reçu',
                                        'RECU' => 'Reçu',
                                        default => $colis->etat_cmc
                                    } }}
                                </span>
                            @else
                                Non défini
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Récapitulatif des documents -->
    <div style="margin: 30px 0;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            RÉCAPITULATIF
        </h3>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #667eea; color: white;">
                    <th style="padding: 10px; text-align: left;">Document</th>
                    <th style="padding: 10px; text-align: left;">Numéro</th>
                    <th style="padding: 10px; text-align: left;">État</th>
                    <th style="padding: 10px; text-align: center;">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">PVC</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $colis->num_pvc ?? '-' }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        @if($colis->etat_pvc)
                            {{ match($colis->etat_pvc) {
                                'NON_RECU' => 'Non reçu',
                                'RECU' => 'Reçu',
                                'PAYE' => 'Payé',
                                default => $colis->etat_pvc
                            } }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: center;">
                        @if($colis->etat_pvc === 'PAYE')
                            <span class="badge badge-success">✓</span>
                        @elseif($colis->etat_pvc === 'RECU')
                            <span class="badge badge-warning">⏳</span>
                        @else
                            <span class="badge badge-danger">✗</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">AE</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $colis->num_ae ?? '-' }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        @if($colis->etat_ae)
                            {{ match($colis->etat_ae) {
                                'NON_VALIDE' => 'Non valide',
                                'VALIDE' => 'Valide',
                                default => $colis->etat_ae
                            } }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: center;">
                        @if($colis->etat_ae === 'VALIDE')
                            <span class="badge badge-success">✓</span>
                        @else
                            <span class="badge badge-danger">✗</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">CMC</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $colis->num_cmc ?? '-' }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        @if($colis->etat_cmc)
                            {{ match($colis->etat_cmc) {
                                'NON_RECU' => 'Non reçu',
                                'RECU' => 'Reçu',
                                default => $colis->etat_cmc
                            } }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: center;">
                        @if($colis->etat_cmc === 'RECU')
                            <span class="badge badge-success">✓</span>
                        @else
                            <span class="badge badge-danger">✗</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Dates importantes -->
    <div class="info-grid">
        <div class="info-box">
            <h3>Dates clés</h3>
            <div class="info-row">
                <span class="info-label">Création :</span>
                <span class="info-value">{{ $colis->created_at?->format('d/m/Y H:i') ?? 'Non renseignée' }}</span>
            </div>
            @if($colis->date_entree_douane)
            <div class="info-row">
                <span class="info-label">Entrée douane :</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($colis->date_entree_douane)->format('d/m/Y') }}</span>
            </div>
            @endif
            @if($colis->date_sortie_douane)
            <div class="info-row">
                <span class="info-label">Sortie douane :</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($colis->date_sortie_douane)->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>

        <div class="info-box">
            <h3>Informations complémentaires</h3>
            <div class="info-row">
                <span class="info-label">Port :</span>
                <span class="info-value">{{ $colis->port?->nom ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut dossier :</span>
                <span class="info-value">
                    @if($colis->status)
                        <span class="badge badge-{{ $colis->status === 'TERMINE' ? 'success' : 'warning' }}">
                            {{ $colis->status }}
                        </span>
                    @else
                        N/A
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Commentaires éventuels -->
    @if($colis->commentaires_cloture)
    <div style="margin-top: 30px;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px;">Commentaires</h3>
        <div style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
            {{ $colis->commentaires_cloture }}
        </div>
    </div>
    @endif

    <!-- Section signature -->
    <div class="signature-section" style="margin-top: 50px;">
        <div class="signature-box">
            <div class="signature-line">Expert ONT</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Représentant KGT</div>
        </div>
    </div>

    <!-- Mention légale -->
    <div style="margin-top: 30px; font-size: 10px; color: #999; text-align: center;">
        <p>Ce rapport d'expertise est un document officiel de KGT TRANSIT.</p>
        <p>Il atteste du contrôle technique effectué sur le colis désigné.</p>
    </div>
@endsection