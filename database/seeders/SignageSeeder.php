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
use App\Support\AdvertisementMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SignageSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyAssets();

        $tarzi = Advertiser::query()->updateOrCreate(
            ['email' => 'contato@tarzi.com.br'],
            [
                'name' => 'Tarzi Publicidade',
                'phone' => '(85) 99429-8785',
                'is_active' => true,
                'registration_fee_cents' => 9900,
            ],
        );

        $sertanus = Advertiser::query()->updateOrCreate(
            ['email' => 'contato@sertanustecnologia.com.br'],
            [
                'name' => 'Sertanus Tecnologia',
                'phone' => '(85) 98869-2529',
                'is_active' => true,
                'registration_fee_cents' => 9900,
            ],
        );

        $lb = Advertiser::query()->updateOrCreate(
            ['email' => 'contato@lbscontabil.com.br'],
            [
                'name' => 'LB Soluções Contábeis',
                'phone' => '(85) 99419-1861',
                'is_active' => true,
                'registration_fee_cents' => 9900,
            ],
        );

        $zeivoll = Advertiser::query()->updateOrCreate(
            ['email' => 'contato@zeivoll.com.br'],
            [
                'name' => 'Zeivoll Tune',
                'phone' => '(85) 99429-8785',
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
                'qr_url' => 'https://tune.zeivoll.com.br/ride/22222222-3333-4444-5555-666666666666',
                'qr_label' => 'Zeivoll Tune',
                'qr_caption' => 'Escaneie e escolha a música',
            ],
        );

        BillingPlan::query()->updateOrCreate(
            ['slug' => 'basico'],
            [
                'name' => 'Plano Básico',
                'description' => 'Cadastro + slots de anúncio avulsos',
                'monthly_price_cents' => 19900,
                'ad_slot_price_cents' => 4900,
                'registration_fee_cents' => 9900,
                'is_active' => true,
            ],
        );

        // Limpa anúncios e slots antigos acumulados por seeds anteriores.
        AdPlacementModel::query()->delete();
        Advertisement::query()->delete();

        /** 11 anúncios: 10 imagens ativas + 1 vídeo no carrossel principal. */
        $demoAds = [
            [
                'advertiser_id' => $tarzi->id,
                'title' => 'Curiosidade: polvos possuem três corações e sangue azul',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 0,
                'media_type' => AdMediaType::Video,
                'media_path' => 'advertisements/curiosidade_animais.webm',
                'duration_seconds' => 60,
            ],
            [
                'advertiser_id' => $zeivoll->id,
                'title' => 'Zeivoll Tune — O ritmo da sua viagem',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/06_zeivoll_tune.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $sertanus->id,
                'title' => 'Sertanus — Importância de um site profissional',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 2,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/07_sertanus_site.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $lb->id,
                'title' => 'LB Soluções Contábeis — Seja bem-vindo',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 3,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/09_lb_solucoes.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $tarzi->id,
                'title' => 'Tarzi — Anuncie aqui',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 4,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/05_tarzi_anuncie_aqui.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $sertanus->id,
                'title' => 'Sertanus — Contato e redes sociais',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 5,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/08_sertanus_contato.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $lb->id,
                'title' => 'LB Soluções — Bem-vindo',
                'placement' => AdPlacement::MainCarousel,
                'sort' => 6,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/04_lb_bem_vindo.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $lb->id,
                'title' => 'LB Soluções — Contato e serviços',
                'placement' => AdPlacement::Sidebar1,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/03_sidebar_lb_bem_vindo.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $sertanus->id,
                'title' => 'Sertanus Tecnologia — @sertanustecnologia',
                'placement' => AdPlacement::Sidebar2,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/10_sidebar_sertanus.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $lb->id,
                'title' => 'LB Soluções: regularização de CPF e abertura de empresas',
                'placement' => AdPlacement::Footer1,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/09_lb_solucoes.png',
                'duration_seconds' => 10,
            ],
            [
                'advertiser_id' => $tarzi->id,
                'title' => 'Fortaleza: feiras e eventos movimentam economia local',
                'placement' => AdPlacement::Footer2,
                'sort' => 1,
                'media_type' => AdMediaType::Image,
                'media_path' => 'advertisements/05_tarzi_anuncie_aqui.png',
                'duration_seconds' => 10,
            ],
        ];

        foreach ($demoAds as $demo) {
            $advertiserId = $demo['advertiser_id'];
            unset($demo['advertiser_id']);

            $ad = Advertisement::query()->create([
                'advertiser_id' => $advertiserId,
                'title' => $demo['title'],
                'media_type' => $demo['media_type'],
                'media_path' => $demo['media_path'],
                'duration_seconds' => $demo['duration_seconds'],
                'is_active' => true,
                'status' => \App\Domain\Ads\Enums\AdvertisementStatus::Approved,
            ]);

            AdPlacementModel::query()->create([
                'advertisement_id' => $ad->id,
                'display_screen_id' => $screen->id,
                'placement' => $demo['placement'],
                'sort_order' => $demo['sort'],
                'is_active' => true,
                'price_cents' => 4900,
            ]);
        }

        Invoice::query()->updateOrCreate(
            ['reference' => 'INV-'.now()->format('Ym').'-001'],
            [
                'advertiser_id' => $sertanus->id,
                'description' => 'Cadastro + 11 slots demo (Tarzi poste DOOH)',
                'amount_cents' => 53900,
                'status' => InvoiceStatus::Pending,
                'due_at' => now()->addDays(7)->toDateString(),
            ],
        );
    }

    private function copyAssets(): void
    {
        $destination = storage_path('app/public/advertisements');
        File::ensureDirectoryExists($destination);

        $files = [
            '03_sidebar_lb_bem_vindo.png',
            '04_lb_bem_vindo.png',
            '05_tarzi_anuncie_aqui.png',
            '06_zeivoll_tune.png',
            '07_sertanus_site.png',
            '08_sertanus_contato.png',
            '09_lb_solucoes.png',
            '10_sidebar_sertanus.png',
            'curiosidade_animais.webm',
        ];

        $flutterAds = realpath(base_path('../zeivoll-display/assets/ads'));
        $localAds = base_path('storage/app/public/advertisements');

        foreach ($files as $file) {
            $target = $destination.DIRECTORY_SEPARATOR.$file;

            if (is_file($target)) {
                continue;
            }

            $fromFlutter = $flutterAds !== false
                ? $flutterAds.DIRECTORY_SEPARATOR.$file
                : null;
            $fromLocal = $localAds.DIRECTORY_SEPARATOR.$file;

            if ($fromFlutter !== null && is_file($fromFlutter)) {
                File::copy($fromFlutter, $target);
            } elseif (is_file($fromLocal)) {
                File::copy($fromLocal, $target);
            }
        }

        if (AdvertisementMedia::disk() === 's3') {
            foreach ($files as $file) {
                $localPath = $destination.DIRECTORY_SEPARATOR.$file;
                if (! is_file($localPath)) {
                    continue;
                }

                $remotePath = AdvertisementMedia::directory().'/'.$file;
                Storage::disk('s3')->put($remotePath, file_get_contents($localPath), 'public');
            }
        }
    }
}
