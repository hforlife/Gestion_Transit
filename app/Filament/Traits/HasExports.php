<?php

namespace App\Filament\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;

trait HasExports
{
    /**
     * Générer un PDF professionnel pour un colis
     */
    protected function generateColisPDF($colis, string $template, ?string $title = null)
    {
        $data = [
            'colis' => $colis,
            'title' => $title ?? $this->getDefaultTitle($template),
            'entreprise' => $this->getEntrepriseInfo(),
            'date' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView($template, $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf;
    }

    /**
     * Titre par défaut selon le template
     */
    protected function getDefaultTitle(string $template): string
    {
        return match ($template) {
            'pdf.etape-douane' => 'FICHE DE SUIVI DOUANIER',
            'pdf.bl' => 'BORDEREAU DE LIVRAISON',
            'pdf.recap-complet' => 'RÉCAPITULATIF COMPLET DU COLIS',
            'pdf.etape-port' => 'FICHE DE SUIVI PORTUAIRE',
            'pdf.etape-expertise' => 'RAPPORT D\'EXPERTISE ONT',
            'pdf.etape-livraison' => 'BON DE LIVRAISON',
            default => 'DOCUMENT KGT TRANSIT',
        };
    }

    /**
     * Retourne icône et couleur selon le template
     */
    protected function getActionStyle(string $template): array
    {
        return match ($template) {
            'pdf.etape-douane' => ['icon' => 'heroicon-o-document-text', 'color' => 'warning'],
            'pdf.bl' => ['icon' => 'heroicon-o-document', 'color' => 'primary'],
            'pdf.recap-complet' => ['icon' => 'heroicon-o-document-duplicate', 'color' => 'success'],
            'pdf.etape-port' => ['icon' => 'heroicon-o-briefcase', 'color' => 'secondary'],
            'pdf.etape-expertise' => ['icon' => 'heroicon-o-eye', 'color' => 'danger'],
            'pdf.etape-livraison' => ['icon' => 'heroicon-o-check', 'color' => 'success'],
            default => ['icon' => 'heroicon-o-document', 'color' => 'primary'],
        };
    }

    /**
     * Informations de l'entreprise
     */
    protected function getEntrepriseInfo(): array
    {
        return [
            'nom' => 'KGT TRANSIT',
            'logo' => public_path('images/logo.png'),
            'adresse' => '123, Avenue du Port, Dakar, Sénégal',
            'tel' => '+221 33 123 45 67',
            'email' => 'contact@kgt-transit.com',
            'rc' => 'RC: SN-DKR-2025-00123',
            'ninea' => 'NINEA: 12345678A',
        ];
    }

    /**
     * Bouton d'impression pour un colis avec icône et couleur dynamiques
     */
    protected function getPrintAction(string $template, string $label = null, ?string $title = null): Action
    {
        $style = $this->getActionStyle($template);

        return Action::make('print_' . str_replace('.', '_', $template))
            ->label($label ?? $this->getDefaultTitle($template))
            ->icon($style['icon'])
            ->color($style['color'])
            ->action(function ($record) use ($template, $title) {
                $pdf = $this->generateColisPDF($record, $template, $title);
                return response()->streamDownload(
                    fn () => print($pdf->output()),
                    $this->getFilename($record, $template) . '.pdf'
                );
            });
    }

    /**
     * Générer un nom de fichier uniforme
     */
    protected function getFilename($record, string $template): string
    {
        $prefix = match ($template) {
            'pdf.etape-douane' => 'DOUANE',
            'pdf.bl' => 'BL',
            'pdf.recap-complet' => 'RECAP',
            'pdf.etape-port' => 'PORT',
            'pdf.etape-expertise' => 'EXPERTISE',
            'pdf.etape-livraison' => 'LIVRAISON',
            default => 'DOCUMENT',
        };

        return sprintf(
            '%s-%s-%s',
            $prefix,
            $record->numero_bl ?? 'colis',
            now()->format('Y-m-d')
        );
    }
}