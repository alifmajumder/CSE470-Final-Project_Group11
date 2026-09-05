<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bini-Moy Exchange - RuralConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f4f7f6; padding: 40px 20px; color: #2c3e50; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #805ad5; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; border-top: 5px solid #805ad5; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        input { padding: 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 15px; font-family: 'Hind Siliguri', sans-serif; }
        .btn-submit { background: #805ad5; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 16px; width: 100%; }
        .barter-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .barter-item { background: #faf5ff; border: 1px solid #e9d8fd; border-radius: 8px; padding: 20px; position: relative; }
        .b-badge { background: #805ad5; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .b-text { font-size: 16px; font-weight: 600; color: #44337a; margin-top: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        @if(session('success')) <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">✅ {{ session('success') }}</div> @endif
        
        <div class="card">
            <h2 style="color: #553c9a; margin-top: 0;">🔄 Post a Barter Trade</h2>
            <form action="{{ route('barter.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group"><label>I am offering...</label><input type="text" name="offering" placeholder="e.g. 5kg Aman Rice Seeds" required></div>
                    <div class="form-group"><label>I am seeking...</label><input type="text" name="seeking" placeholder="e.g. 2 hours of weeding labor" required></div>
                </div>
                <button type="submit" class="btn-submit">Post to Bini-Moy Board</button>
            </form>
        </div>

        <div class="barter-grid">
            @forelse($barters as $barter)
                <div class="barter-item">
                    <span class="b-badge" style="background: #3182ce;">Offering</span>
                    <div class="b-text">{{ $barter->offering }}</div>
                    <span class="b-badge" style="background: #d97706;">Seeking</span>
                    <div class="b-text">{{ $barter->seeking }}</div>
                    <div style="font-size: 13px; color: #718096; border-top: 1px solid #e9d8fd; padding-top: 10px;">
                        📞 Contact <strong>{{ $barter->user->name }}</strong> at {{ $barter->user->phone }}
                    </div>
                    @if(Auth::check() && (Auth::id() === $barter->user_id || Auth::user()->role === 'admin'))
                        <form action="{{ route('barter.destroy', $barter->id) }}" method="POST" style="position: absolute; top: 15px; right: 15px;">
                            @csrf @method('DELETE') <button type="submit" style="background: #e53e3e; color: white; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer; font-size: 11px;">Delete</button>
                        </form>
                    @endif
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; color: #a0aec0; padding: 30px;">No barter trades posted yet.</div>
            @endforelse
        </div>
    </div>
</body>
</html>