<?php

namespace App\Application\Ads\Services;

use App\Application\Ads\DTO\AdItemDto;
use App\Domain\Ads\Enums\AdMediaType;
use App\Models\Advertisement;
use App\Models\DisplayScreen;

/**
 * Monta uma fila completa do carrossel intercalando anúncios estáticos e trechos de vídeo.
 *
 * Ex.: com ads_before_video = 3 → 3 imagens, trecho de vídeo, 3 imagens, continuação do vídeo…
 * Gera um ciclo completo; o player repete a fila.
 */
final class CarouselPlaylistBuilder
{
    /**
     * @param  list<Advertisement>  $advertisements  ordenados por sort_order
     * @return list<AdItemDto>
     */
    public function build(DisplayScreen $screen, array $advertisements): array
    {
        $images = [];
        $videos = [];

        foreach ($advertisements as $advertisement) {
            if ($this->isVideoLike($advertisement)) {
                $videos[] = $advertisement;
            } else {
                $images[] = $advertisement;
            }
        }

        if ($videos === []) {
            return array_map(fn (Advertisement $ad) => $this->toDto($ad), $images);
        }

        if ($images === []) {
            return $this->videoOnlyPlaylist($videos, $screen);
        }

        $batchSize = max(1, (int) $screen->ads_before_video);
        $segmentSeconds = max(1, (int) $screen->video_segment_seconds);

        $playlist = [];
        $imageIndex = 0;

        foreach ($videos as $video) {
            $offset = 0;
            $total = $this->videoTotalSeconds($video);

            while ($offset < $total) {
                for ($i = 0; $i < $batchSize && $imageIndex < count($images); $i++) {
                    $playlist[] = $this->toDto($images[$imageIndex]);
                    $imageIndex++;
                }

                $playlist[] = $this->toDto($video, $offset, min($segmentSeconds, $total - $offset));
                $offset += $segmentSeconds;
            }
        }

        while ($imageIndex < count($images)) {
            $batch = array_slice($images, $imageIndex, $batchSize);
            foreach ($batch as $image) {
                $playlist[] = $this->toDto($image);
            }
            $imageIndex += count($batch);
        }

        return $playlist;
    }

    /** @param list<Advertisement> $videos */
    private function videoOnlyPlaylist(array $videos, DisplayScreen $screen): array
    {
        $segmentSeconds = max(1, (int) $screen->video_segment_seconds);
        $playlist = [];

        foreach ($videos as $video) {
            $offset = 0;
            $total = $this->videoTotalSeconds($video);

            while ($offset < $total) {
                $duration = min($segmentSeconds, $total - $offset);
                $playlist[] = $this->toDto($video, $offset, $duration);
                $offset += $segmentSeconds;
            }
        }

        return $playlist;
    }

    private function isVideoLike(Advertisement $advertisement): bool
    {
        return in_array($advertisement->media_type, [AdMediaType::Video, AdMediaType::Youtube], true);
    }

    private function videoTotalSeconds(Advertisement $advertisement): int
    {
        return max(
            1,
            (int) ($advertisement->video_total_seconds ?? $advertisement->duration_seconds ?? 60),
        );
    }

    private function toDto(Advertisement $advertisement, int $videoStart = 0, ?int $segmentDuration = null): AdItemDto
    {
        return new AdItemDto(
            id: $advertisement->id,
            title: $advertisement->title,
            mediaType: $advertisement->media_type->value,
            mediaUrl: $advertisement->mediaUrl(),
            clickUrl: $advertisement->click_url,
            durationSeconds: $segmentDuration ?? $advertisement->duration_seconds,
            videoStartSeconds: $videoStart > 0 ? $videoStart : null,
            videoSegmentSeconds: $segmentDuration,
        );
    }
}
