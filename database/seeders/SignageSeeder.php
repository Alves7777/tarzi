<?php

namespace Database\Seeders;

use App\Domain\Ads\Enums\AdMediaType;
use App\Domain\Ads\Enums\AdPlacement;
use App\Domain\Billing\Enums\InvoiceStatus;
use App\Models\AdPlacement as AdPlacementModel;
use App\Models\Advertisement;
use App\Models\Advertiser;
use App\Models\BillingPlan;
use App\Models\DisplayScreen;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class SignageSeeder extends Seeder
{
    public function run(): void
    {
        $advertiser = Advertiser::query()->updateOrCreate(
            ['email' => 'contato@sertanustecnologia.com.br'],
            [
                'name' => 'Sertanus Tecnologia',
                'phone' => '(85) 98869-2529',
                'is_active' => true,
                'registration_fee_cents' => 9900,
            ],
        );

        $screen = DisplayScreen::query()->updateOrCreate(
            ['uuid' => '11111111-2222-3333-4444-555555555555'],
            [
                'name' => 'Poste Demo — Beira Mar',
                'location' => 'Fortaleza - CE',
                'is_active' => true,
                'carousel_seconds' => 10,
            ],
        );

        BillingPlan::query()->updateOrCreate(
            ['slug' => 'basico'],
            [
                'name' => 'Plano Basico',
                'description' => 'Cadastro + slots de anuncio avulsos',
                'monthly_price_cents' => 19900,
                'ad_slot_price_cents' => 4900,
                'registration_fee_cents' => 9900,
                'is_active' => true,
            ],
        );

        $demoAds = [
            [
                'title' => 'Tarzi — Publicidade em movimento',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 0,
                'media_type' => AdMediaType::Video,
                'media_path' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'duration_seconds' => 25,
            ],
            [
                'title' => 'LB Solucoes Contabeis — Motoristas Uber',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/01_lb_solucoes_contabeis.png',
                'duration_seconds' => 10,
            ],
            [
                'title' => 'Sertanus — Importancia de um site profissional',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 2,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/02_sertanus_importancia_site.png',
                'duration_seconds' => 10,
            ],
            [
                'title' => 'Sertanus Tecnologia — Desenvolvimento de software',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 3,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/03_sertanus_contato.png',
                'duration_seconds' => 10,
            ],
            [
                'title' => 'Sertanus — Sites e apps sob medida',
                'placement' => AdPlacement::Sidebar2,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/02_sertanus_importancia_site.png',
                'duration_seconds' => 8,
            ],
            [
                'title' => 'Sertanus — @sertanustecnologia',
                'placement' => AdPlacement::Sidebar1,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/03_sertanus_contato.png',
                'duration_seconds' => 8,
            ],
            [
                'title' => 'LB Solucoes: regularizacao de CPF e abertura de empresas',
                'placement' => AdPlacement::Footer1,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/01_lb_solucoes_contabeis.png',
                'duration_seconds' => 8,
            ],
            [
                'title' => 'Fortaleza: feiras e eventos movimentam economia local',
                'placement' => AdPlacement::Footer2,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/02_sertanus_importancia_site.png',
                'duration_seconds' => 8,
            ],
        ];

        foreach ($demoAds as $demo) {
            $ad = Advertisement::query()->updateOrCreate(
                ['title' => $demo['title'], 'advertiser_id' => $advertiser->id],
                [
                    'media_type' => $demo['media_type'],
                    'media_path' => $demo['media_path'],
                    'duration_seconds' => $demo['duration_seconds'],
                    'is_active' => true,
                ],
            );

            AdPlacementModel::query()->updateOrCreate(
                [
                    'advertisement_id' => $ad->id,
                    'display_screen_id' => $screen->id,
                    'placement' => $demo['placement'],
                ],
                [
                    'sort_order' => $demo['sort'],
                    'is_active' => true,
                    'price_cents' => 4900,
                ],
            );
        }

        Invoice::query()->updateOrCreate(
            ['reference' => 'INV-'.now()->format('Ym').'-001'],
            [
                'advertiser_id' => $advertiser->id,
                'description' => 'Cadastro + 8 slots demo (Tarzi poste DOOH)',
                'amount_cents' => 44200,
                'status' => InvoiceStatus::Pending,
                'due_at' => now()->addDays(7)->toDateString(),
            ],
        );
    }
}
