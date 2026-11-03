@extends('layouts.app', ['title' => 'Media Catalog'])

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                Media Catalog & Content
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage movies, TV series, featured carousels, and poster artworks.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-blue-500/10 text-blue-400 text-xs font-bold border border-blue-500/20">
                {{ $counts['movies'] }} Movies
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-purple-500/10 text-purple-400 text-xs font-bold border border-purple-500/20">
                {{ $counts['series'] }} Series
            </span>
        </div>
    </div>

    <!-- Quick Add Form -->
    <div class="glass-panel p-6 rounded-3xl space-y-4">
        <h3 class="text-sm font-bold text-white flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#FF2E4C]"></span>
            Add New Title to Catalog
        </h3>

        <form action="{{ route('admin.media.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Title</label>
                <input type="text" name="title" required placeholder="e.g. Gladiator II" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-[#FF2E4C]">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Media Type</label>
                <select name="media_type" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
                    <option value="movie">Movie</option>
                    <option value="tv">TV Series</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Year</label>
                <input type="text" name="year" placeholder="2026" class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Poster URL</label>
                <input type="url" name="poster" placeholder="https://image.tmdb.org/..." class="w-full bg-[#12131A] border border-white/10 rounded-xl px-3 py-2 text-xs text-slate-200">
            </div>
            <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-between pt-1">
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="accent-[#FF2E4C]">
                        Featured on Home Screen
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="is_trending" value="1" class="accent-[#FF2E4C]">
                        Trending Tag
                    </label>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#FF2E4C] hover:bg-[#E02440] text-white text-xs font-bold transition-all shadow-md shadow-[#FF2E4C]/25">
                    Save to Catalog
                </button>
            </div>
        </form>
    </div>

    <!-- Media Table -->
    <div class="glass-panel p-6 rounded-3xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-white">Catalog Titles ({{ $mediaItems->total() }})</h3>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.media.index') }}" class="px-2.5 py-1 rounded-lg text-xs {{ !request('type') ? 'bg-white/10 text-white font-bold' : 'text-slate-400 hover:text-white' }}">All</a>
                <a href="{{ route('admin.media.index', ['type' => 'movie']) }}" class="px-2.5 py-1 rounded-lg text-xs {{ request('type') === 'movie' ? 'bg-blue-500/20 text-blue-300 font-bold' : 'text-slate-400 hover:text-white' }}">Movies</a>
                <a href="{{ route('admin.media.index', ['type' => 'tv']) }}" class="px-2.5 py-1 rounded-lg text-xs {{ request('type') === 'tv' ? 'bg-purple-500/20 text-purple-300 font-bold' : 'text-slate-400 hover:text-white' }}">TV Series</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-slate-400 uppercase tracking-wider text-[11px]">
                        <th class="pb-3 font-semibold">Title</th>
                        <th class="pb-3 font-semibold">Type</th>
                        <th class="pb-3 font-semibold">Year</th>
                        <th class="pb-3 font-semibold">Rating</th>
                        <th class="pb-3 font-semibold">Tags</th>
                        <th class="pb-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($mediaItems as $item)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 font-bold text-white flex items-center gap-3">
                                @if($item->poster)
                                    <img src="{{ $item->poster }}" class="w-8 h-12 object-cover rounded bg-white/5">
                                @endif
                                <span>{{ $item->title }}</span>
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $item->media_type === 'tv' ? 'bg-purple-500/20 text-purple-300' : 'bg-blue-500/20 text-blue-300' }}">
                                    {{ $item->media_type }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-400 font-mono">{{ $item->year ?: '2026' }}</td>
                            <td class="py-3 text-amber-400 font-bold">★ {{ $item->vote_average }}</td>
                            <td class="py-3">
                                @if($item->is_featured)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#FF2E4C]/20 text-[#FF2E4C]">FEATURED</span>
                                @endif
                                @if($item->is_trending)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 ml-1">TRENDING</span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <form action="{{ route('admin.media.delete', $item->id) }}" method="POST" onsubmit="return confirm('Remove this title?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No media titles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mediaItems->hasPages())
            <div class="pt-4 border-t border-white/5">
                {{ $mediaItems->links() }}
            </div>
        @endif
    </div>
</div>
@endsection