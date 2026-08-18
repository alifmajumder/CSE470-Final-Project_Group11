{{-- Expects: $progress (collection from BadgeService::progressFor) --}}
<div class="badge-grid">
    @foreach($progress as $item)
        <div class="badge-card {{ $item['earned'] ? 'earned' : 'locked' }}">
            <div class="badge-icon">{{ $item['icon'] }}</div>
            <div class="badge-label">{{ $item['label'] }}</div>
            <div class="badge-category">{{ $item['category_label'] }}</div>

            @if($item['earned'])
                <div class="badge-earned-tag">✓ Earned {{ $item['earned_at']?->format('d M Y') }}</div>
            @else
                @php $pct = $item['threshold'] > 0 ? min(100, (int) round(($item['completed_count'] / $item['threshold']) * 100)) : 0; @endphp
                <div class="badge-progress-track">
                    <div class="badge-progress-fill" style="width: {{ $pct }}%;"></div>
                </div>
                <div class="badge-progress-text">{{ $item['completed_count'] }} / {{ $item['threshold'] }} jobs completed</div>
            @endif
        </div>
    @endforeach
</div>

<style>
    .badge-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 16px; }
    .badge-card { border-radius: 10px; padding: 20px; text-align: center; border: 1px solid #e2e8f0; }
    .badge-card.earned { background: #f0fff4; border-color: #9ae6b4; }
    .badge-card.locked { background: #f7fafc; opacity: 0.75; }
    .badge-icon { font-size: 36px; margin-bottom: 8px; filter: grayscale(0); }
    .badge-card.locked .badge-icon { filter: grayscale(1); opacity: 0.5; }
    .badge-label { font-weight: bold; font-size: 15px; color: #2c3e50; }
    .badge-category { font-size: 12px; color: #a0aec0; margin-top: 2px; margin-bottom: 10px; }
    .badge-earned-tag { color: #276749; font-weight: 600; font-size: 12px; }
    .badge-progress-track { background: #e2e8f0; border-radius: 6px; height: 8px; overflow: hidden; margin-top: 4px; }
    .badge-progress-fill { background: #27ae60; height: 100%; }
    .badge-progress-text { font-size: 12px; color: #718096; margin-top: 6px; }
</style>
