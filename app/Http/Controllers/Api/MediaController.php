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
}