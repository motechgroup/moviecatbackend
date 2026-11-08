<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use App\Models\Season;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function featured(): JsonResponse
    {
        $items = MediaItem::where('is_featured', true)
            ->where('status', 'published')
            ->orderByDesc('vote_average')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function trending(): JsonResponse
    {
        $items = MediaItem::where('is_trending', true)
            ->where('status', 'published')
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function movies(Request $request): JsonResponse
    {
        $query = MediaItem::where('media_type', 'movie')->where('status', 'published');

        if ($search = $request->query('query')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($genre = $request->query('genre')) {
            $query->whereJsonContains('genres', $genre);
        }

        if ($year = $request->query('year')) {
            $query->where('year', $year);
        }

        $items = $query->orderByDesc('year')
            ->orderByDesc('vote_average')
            ->paginate($request->query('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function series(Request $request): JsonResponse
    {
        $query = MediaItem::where('media_type', 'tv')->where('status', 'published');

        if ($search = $request->query('query')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($genre = $request->query('genre')) {
            $query->whereJsonContains('genres', $genre);
        }

        if ($year = $request->query('year')) {
            $query->where('year', $year);
        }

        $items = $query->orderByDesc('year')
            ->orderByDesc('vote_average')
            ->paginate($request->query('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $item = MediaItem::with(['seasons.episodes'])->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Media item not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    public function seasonEpisodes(string $id, int $seasonNumber): JsonResponse
    {
        $media = MediaItem::find($id);

        if (!$media) {
            return response()->json(['success' => false, 'message' => 'Series not found'], 404);
        }

        $season = Season::where('media_item_id', $media->id)
            ->where('season_number', $seasonNumber)
            ->with('episodes')
            ->first();

        return response()->json([
            'success' => true,
            'season' => $seasonNumber,
            'episodes' => $season ? $season->episodes : [],
        ]);
    }

    public function similar(string $id): JsonResponse
    {
        $item = MediaItem::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $similar = MediaItem::where('id', '!=', $item->id)
            ->where('media_type', $item->media_type)
            ->where('status', 'published')
            ->take(12)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $similar,
        ]);
    }

    public function trendingByCountry(Request $request): JsonResponse
    {
        $countryCode = strtoupper(trim((string)($request->query("country_code") ?: $request->header("CF-IPCountry") ?: "KE")));
        $limit = min(30, max(5, (int)$request->query("limit", 15)));

        try {
            $watched = \App\Models\WatchProgress::query()
                ->leftJoin("devices", "watch_progress.device_id", "=", "devices.device_id")
                ->where(function ($q) use ($countryCode) {
                    $q->where("watch_progress.country_code", $countryCode)
                      ->orWhere("devices.country_code", $countryCode);
                })
                ->where("watch_progress.current_time", ">=", 15)
                ->whereNotNull("watch_progress.title")
                ->whereNotNull("watch_progress.poster")
                ->selectRaw("
                    watch_progress.media_id,
                    watch_progress.media_type,
                    watch_progress.title,
                    watch_progress.poster,
                    count(distinct watch_progress.device_id) as viewers_count,
                    max(watch_progress.last_watched_at) as latest_watch
                ")
                ->groupBy("watch_progress.media_id", "watch_progress.media_type", "watch_progress.title", "watch_progress.poster")
                ->orderByDesc("viewers_count")
                ->orderByDesc("latest_watch")
                ->take($limit)
                ->get();

            $results = [];
            $seenTitles = [];

            foreach ($watched as $w) {
                $norm = strtolower(trim($w->title));
                if (isset($seenTitles[$norm])) continue;
                $seenTitles[$norm] = true;

                $results[] = [
                    "id" => is_numeric($w->media_id) ? (int)$w->media_id : $w->media_id,
                    "tmdbId" => is_numeric($w->media_id) ? (int)$w->media_id : null,
                    "title" => $w->title,
                    "mediaType" => $w->media_type ?: "movie",
                    "poster" => $w->poster,
                    "year" => (string)now()->year,
                    "voteAverage" => 8.5,
                    "overview" => "Top watched by viewers in " . $countryCode,
                    "viewersCount" => (int)$w->viewers_count,
                ];
            }

            if (count($results) < 10) {
                $fallbackMedia = MediaItem::where("status", "published")
                    ->orderByDesc("vote_average")
                    ->take(15)
                    ->get();

                foreach ($fallbackMedia as $fm) {
                    $norm = strtolower(trim($fm->title));
                    if (isset($seenTitles[$norm])) continue;
                    $seenTitles[$norm] = true;

                    $results[] = [
                        "id" => $fm->id,
                        "tmdbId" => $fm->tmdb_id ?: $fm->id,
                        "title" => $fm->title,
                        "mediaType" => $fm->media_type ?: "movie",
                        "poster" => $fm->poster_url,
                        "backdrop" => $fm->backdrop_url,
                        "year" => (string)($fm->year ?: now()->year),
                        "voteAverage" => (float)($fm->vote_average ?: 8.0),
                        "overview" => $fm->overview ?: "",
                    ];

                    if (count($results) >= 10) break;
                }
            }

            return response()->json([
                "success" => true,
                "country_code" => $countryCode,
                "data" => $results,
                "total" => count($results),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "error" => $e->getMessage(),
                "data" => [],
            ]);
        }
    }
}
