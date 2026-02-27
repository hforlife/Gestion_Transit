@extends('pdf.layout')

@section('content')
    <div style="text-align: right; margin-bottom: 20px;">
        <span class="badge badge-{{ $colis->status_colis_douane === 'SORTI' ? 'success' : 'warning' }}">
            {{ match($colis->status_colis_douane) {
                'EN_ATTENTE' => 'EN ATTENTE',
                'ENTRE' => 'ENTRÉ EN DOUANE',
                'SORTI' => 'SORTI DE DOUANE',
                default => $colis->status_colis_douane
            } }}
        </span>
    </div>

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
            <h3>Informations douanières</h3>
            <div class="info-row">
                <span class="info-label">N° T1 :</span>
                <span class="info-value">{{ $colis->num_t1 ?? 'Non fourni' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">État T1 :</span>
                <span class="info-value">
                    @if($colis->etat_t1)
                        <span class="badge badge-{{ $colis->etat_t1 === 'PAYE' ? 'success' : 'warning' }}">
                            {{ $colis->etat_t1 }}
                        </span>
                    @else
                        Non défini
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Déclaration :</span>
                <span class="info-value">{{ $colis->declaration_reference ?? 'Non fournie' }}</span>
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Dates douane</h3>
            <div class="info-row">
                <span class="info-label">Entrée :</span>
                <span class="info-value">{{ $colis->date_entree_douane ? $colis->date_entree_douane->format('d/m/Y') : 'Non renseignée' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sortie :</span>
                <span class="info-value">{{ $colis->date_sortie_douane ? $colis->date_sortie_douane->format('d/m/Y') : 'Non renseignée' }}</span>
            </div>
            @if($colis->date_entree_douane && $colis->date_sortie_douane)
            <div class="info-row">
                <span class="info-label">Durée séjour :</span>
                <span class="info-value">{{ $colis->date_entree_douane->diffInDays($colis->date_sortie_douane) }} jours</span>
            </div>
            @endif
        </div>

        <div class="info-box">
            <h3>Autres informations</h3>
            <div class="info-row">
                <span class="info-label">Port :</span>
                <span class="info-value">{{ $colis->port?->nom ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date création :</span>
                <span class="info-value">{{ $colis->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    @if($colis->etat_colis === 'A_LA_DOUANE')
    <div style="margin-top: 30px; padding: 15px; background: #f0f9ff; border-left: 4px solid #667eea;">
        <h4 style="margin: 0 0 10px 0; color: #667eea;">Suivant :</h4>
        <p>Le colis est actuellement à la douane. Prochaine étape : 
            @if($colis->typeColis?->nom === 'Véhicules')
                Expertise
            @else
                Livraison
            @endif
        </p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Agent des douanes</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Représentant KGT</div>
        </div>
    </div>
@endsection