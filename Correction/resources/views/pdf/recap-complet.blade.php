@extends('pdf.layout')

@section('content')
    <!-- En-tête du document -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: #667eea; margin: 0;">RÉCAPITULATIF COMPLET DU COLIS</h2>
        <p style="color: #666; font-size: 12px; margin: 5px 0;">N° BL: {{ $colis->numero_bl }}</p>
        <p style="color: #666; font-size: 11px;">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- Statut général -->
    <div style="margin-bottom: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0; font-size: 18px;">STATUT GÉNÉRAL DU COLIS</h3>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $colis->numero_bl }}</p>
            </div>
            <div style="text-align: right;">
                <span style="background: white; color: #667eea; padding: 8px 20px; border-radius: 20px; font-weight: bold;">
                    {{ match($colis->etat_colis) {
                        'BL_ENREGISTRE' => 'ENREGISTRÉ',
                        'AU_PORT' => 'AU PORT',
                        'A_LA_DOUANE' => 'EN DOUANE',
                        'EXPERTISE' => 'EN EXPERTISE',
                        'EN_ROUTE' => 'EN ROUTE',
                        'LIVRE' => 'LIVRÉ',
                        'CLOTURE' => 'CLÔTURÉ',
                        default => $colis->etat_colis
                    } }}
                </span>
            </div>
        </div>
    </div>

    <!-- Section 1: Informations générales -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            📋 INFORMATIONS GÉNÉRALES
        </h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Colonne gauche -->
            <div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5; width: 40%;"><strong>N° BL</strong></td>
                        <td style="padding: 8px;">{{ $colis->numero_bl }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5;"><strong>Type de colis</strong></td>
                        <td style="padding: 8px;">{{ $colis->typeColis?->nom ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5;"><strong>Description</strong></td>
                        <td style="padding: 8px;">{{ $colis->description ?? 'Aucune description' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5;"><strong>Date de création</strong></td>
                        <td style="padding: 8px;">
                            {{ $colis->created_at ? \Carbon\Carbon::parse($colis->created_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Colonne droite -->
            <div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5; width: 40%;"><strong>Client</strong></td>
                        <td style="padding: 8px;">{{ $colis->dossierTransit?->client?->nom ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5;"><strong>Agent responsable</strong></td>
                        <td style="padding: 8px;">{{ $colis->agent?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5;"><strong>Port d'entrée</strong></td>
                        <td style="padding: 8px;">{{ $colis->port?->nom ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f5f5f5;"><strong>Référence dossier</strong></td>
                        <td style="padding: 8px;">{{ $colis->dossierTransit?->reference ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 2: Étape Port -->
    <div style="margin-bottom: 30px; page-break-inside: avoid;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            ⚓ ÉTAPE PORTUAIRE
        </h3>
        
        <div style="background: #f8fafc; padding: 15px; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <span class="badge badge-{{ $colis->status_colis_port === 'SORTI' ? 'success' : ($colis->status_colis_port !== 'EN_ATTENTE' ? 'warning' : 'secondary') }}">
                        Statut: {{ match($colis->status_colis_port) {
                            'EN_ATTENTE' => 'En attente',
                            'ENTRE' => 'Entré au port',
                            'SORTI' => 'Sorti du port',
                            default => $colis->status_colis_port ?? 'Non défini'
                        } }}
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Date d'entrée</strong></td>
                        <td style="padding: 8px;">
                            {{ $colis->date_entree_port ? \Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y') : 'Non renseignée' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0;"><strong>Date de sortie</strong></td>
                        <td style="padding: 8px;">
                            {{ $colis->date_sortie_port ? \Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y') : 'Non renseignée' }}
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Durée de séjour</strong></td>
                        <td style="padding: 8px;">
                            @if($colis->date_entree_port && $colis->date_sortie_port)
                                {{ \Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(\Carbon\Carbon::parse($colis->date_sortie_port)) }} jours
                            @elseif($colis->date_entree_port)
                                En cours depuis {{ \Carbon\Carbon::parse($colis->date_entree_port)->diffInDays(now()) }} jours
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 3: Étape Douane -->
    <div style="margin-bottom: 30px; page-break-inside: avoid;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            🏛️ ÉTAPE DOUANIÈRE
        </h3>
        
        <div style="background: #f8fafc; padding: 15px; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <span class="badge badge-{{ $colis->status_colis_douane === 'SORTI' ? 'success' : ($colis->status_colis_douane !== 'EN_ATTENTE' ? 'warning' : 'secondary') }}">
                        Statut: {{ match($colis->status_colis_douane) {
                            'EN_ATTENTE' => 'En attente',
                            'ENTRE' => 'Entré en douane',
                            'SORTI' => 'Sorti de douane',
                            default => $colis->status_colis_douane ?? 'Non défini'
                        } }}
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>N° T1</strong></td>
                        <td style="padding: 8px;">{{ $colis->num_t1 ?? 'Non fourni' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0;"><strong>État T1</strong></td>
                        <td style="padding: 8px;">
                            @if($colis->etat_t1)
                                <span class="badge badge-{{ $colis->etat_t1 === 'PAYE' ? 'success' : 'warning' }}">
                                    {{ $colis->etat_t1 }}
                                </span>
                            @else
                                Non défini
                            @endif
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Réf. déclaration</strong></td>
                        <td style="padding: 8px;">{{ $colis->declaration_reference ?? 'Non fournie' }}</td>
                    </tr>
                </table>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Date d'entrée</strong></td>
                        <td style="padding: 8px;">
                            {{ $colis->date_entree_douane ? \Carbon\Carbon::parse($colis->date_entree_douane)->format('d/m/Y') : 'Non renseignée' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0;"><strong>Date de sortie</strong></td>
                        <td style="padding: 8px;">
                            {{ $colis->date_sortie_douane ? \Carbon\Carbon::parse($colis->date_sortie_douane)->format('d/m/Y') : 'Non renseignée' }}
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Durée de séjour</strong></td>
                        <td style="padding: 8px;">
                            @if($colis->date_entree_douane && $colis->date_sortie_douane)
                                {{ \Carbon\Carbon::parse($colis->date_entree_douane)->diffInDays(\Carbon\Carbon::parse($colis->date_sortie_douane)) }} jours
                            @elseif($colis->date_entree_douane)
                                En cours depuis {{ \Carbon\Carbon::parse($colis->date_entree_douane)->diffInDays(now()) }} jours
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 4: Expertise (si véhicule) -->
    @if($colis->typeColis?->nom === 'Véhicules')
    <div style="margin-bottom: 30px; page-break-inside: avoid;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            🔧 EXPERTISE ONT
        </h3>
        
        <div style="background: #f8fafc; padding: 15px; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <span class="badge badge-{{ $colis->etat_expertise === 'EFFECTUEE' ? 'success' : 'warning' }}">
                        {{ $colis->etat_expertise === 'EFFECTUEE' ? 'Expertise effectuée' : 'Expertise en attente' }}
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <!-- PVC -->
                <div style="border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                    <h4 style="margin: 0 0 10px 0; color: #667eea;">PVC</h4>
                    <p style="margin: 5px 0;"><strong>N°:</strong> {{ $colis->num_pvc ?? 'Non fourni' }}</p>
                    <p style="margin: 5px 0;">
                        <strong>État:</strong> 
                        <span class="badge badge-{{ $colis->etat_pvc === 'PAYE' ? 'success' : ($colis->etat_pvc === 'RECU' ? 'warning' : 'danger') }}">
                            {{ $colis->etat_pvc ?? 'NON_RECU' }}
                        </span>
                    </p>
                </div>

                <!-- AE -->
                <div style="border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                    <h4 style="margin: 0 0 10px 0; color: #667eea;">AE</h4>
                    <p style="margin: 5px 0;"><strong>N°:</strong> {{ $colis->num_ae ?? 'Non fourni' }}</p>
                    <p style="margin: 5px 0;">
                        <strong>État:</strong> 
                        <span class="badge badge-{{ $colis->etat_ae === 'VALIDE' ? 'success' : 'danger' }}">
                            {{ $colis->etat_ae ?? 'NON_VALIDE' }}
                        </span>
                    </p>
                </div>

                <!-- CMC -->
                <div style="border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                    <h4 style="margin: 0 0 10px 0; color: #667eea;">CMC</h4>
                    <p style="margin: 5px 0;"><strong>N°:</strong> {{ $colis->num_cmc ?? 'Non fourni' }}</p>
                    <p style="margin: 5px 0;">
                        <strong>État:</strong> 
                        <span class="badge badge-{{ $colis->etat_cmc === 'RECU' ? 'success' : 'danger' }}">
                            {{ $colis->etat_cmc ?? 'NON_RECU' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Section 5: Livraison -->
    <div style="margin-bottom: 30px; page-break-inside: avoid;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            🚚 LIVRAISON
        </h3>
        
        <div style="background: #f8fafc; padding: 15px; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <span class="badge badge-{{ $colis->status_colis_livraison === 'LIVRE' ? 'success' : 'warning' }}">
                        {{ $colis->status_colis_livraison === 'LIVRE' ? 'Livré' : 'En attente de livraison' }}
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Date de livraison</strong></td>
                        <td style="padding: 8px;">
                            {{ $colis->date_livraison ? \Carbon\Carbon::parse($colis->date_livraison)->format('d/m/Y H:i') : 'Non renseignée' }}
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Commentaires</strong></td>
                        <td style="padding: 8px;">{{ $colis->commentaires_cloture ?? 'Aucun commentaire' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 6: Dossier Transit -->
    @if($colis->dossierTransit)
    <div style="margin-bottom: 30px; page-break-inside: avoid;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            📁 DOSSIER DE TRANSIT
        </h3>
        
        <div style="background: #f8fafc; padding: 15px; border-radius: 5px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Référence dossier</strong></td>
                        <td style="padding: 8px;">{{ $colis->dossierTransit->reference }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0;"><strong>Nom du dossier</strong></td>
                        <td style="padding: 8px;">{{ $colis->dossierTransit->nom }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0;"><strong>Type de dossier</strong></td>
                        <td style="padding: 8px;">{{ $colis->dossierTransit->typeDossier?->nom ?? 'N/A' }}</td>
                    </tr>
                </table>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; width: 40%;"><strong>Date de dépôt</strong></td>
                        <td style="padding: 8px;">
                            {{ $colis->dossierTransit->date_depot ? \Carbon\Carbon::parse($colis->dossierTransit->date_depot)->format('d/m/Y') : 'Non renseignée' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0;"><strong>Statut dossier</strong></td>
                        <td style="padding: 8px;">
                            <span class="badge badge-{{ $colis->dossierTransit->status === 'CLOTURE' ? 'success' : ($colis->dossierTransit->status === 'EN_COURS' ? 'warning' : 'primary') }}">
                                {{ $colis->dossierTransit->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Section 7: Commentaires additionnels -->
    @if($colis->commentaires_cloture)
    <div style="margin-bottom: 30px; page-break-inside: avoid;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            💬 COMMENTAIRES
        </h3>
        <div style="padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
            {{ $colis->commentaires_cloture }}
        </div>
    </div>
    @endif

    <!-- Section 8: Chronologie complète -->
    <div style="margin-bottom: 30px; page-break-inside: avoid;">
        <h3 style="color: #667eea; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">
            ⏱️ CHRONOLOGIE COMPLÈTE
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
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Création du colis</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ $colis->created_at ? \Carbon\Carbon::parse($colis->created_at)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>

                @if($colis->date_entree_port)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Entrée au port</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ \Carbon\Carbon::parse($colis->date_entree_port)->format('d/m/Y') }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                @endif

                @if($colis->date_sortie_port)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Sortie du port</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ \Carbon\Carbon::parse($colis->date_sortie_port)->format('d/m/Y') }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                @endif

                @if($colis->date_entree_douane)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Entrée en douane</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ \Carbon\Carbon::parse($colis->date_entree_douane)->format('d/m/Y') }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                @endif

                @if($colis->date_sortie_douane)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Sortie de douane</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ \Carbon\Carbon::parse($colis->date_sortie_douane)->format('d/m/Y') }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                @endif

                @if($colis->etat_expertise === 'EFFECTUEE' && $colis->typeColis?->nom === 'Véhicules')
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Expertise effectuée</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ $colis->updated_at ? \Carbon\Carbon::parse($colis->updated_at)->format('d/m/Y') : '-' }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                @endif

                @if($colis->date_livraison)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Livraison</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ \Carbon\Carbon::parse($colis->date_livraison)->format('d/m/Y H:i') }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Effectué</span>
                    </td>
                </tr>
                @endif

                @if($colis->status === 'TERMINE')
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Clôture du dossier</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        {{ $colis->updated_at ? \Carbon\Carbon::parse($colis->updated_at)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                        <span class="badge badge-success">Clôturé</span>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Section signature -->
<div style="margin-top: 50px; page-break-inside: avoid;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <!-- Signature KGT -->
        <div style="width: 45%; text-align: left;">
            <div style="border-top: 2px solid #333; padding-top: 10px;">
                <strong>Cachet et signature de KGT TRANSIT</strong>
            </div>
        </div>

        <!-- Signature Client -->
        <div style="width: 45%; text-align: right;">
            <div style="border-top: 2px solid #333; padding-top: 10px;">
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
        <p>Ce document est un récapitulatif officiel du processus de transit. Toute modification doit être approuvée par KGT TRANSIT.</p>
        <p>Document généré le {{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>
@endsection