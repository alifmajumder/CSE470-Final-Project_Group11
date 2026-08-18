<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - RuralConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px 20px; color: #2c3e50; margin: 0; }
        .container { max-width: 800px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; }
        .nav-button:hover { background: #219150; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .card h2 { margin-top: 0; color: #27ae60; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px; margin-bottom: 20px; }
        
        /* Trust Score Styles */
        .trust-score-box { text-align: center; background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 30px; }
        .trust-score-title { font-size: 18px; color: #4a5568; font-weight: 600; margin-bottom: 10px; }
        .stars { font-size: 32px; color: #d97706; margin-bottom: 5px; }
        .score-number { font-size: 20px; font-weight: bold; color: #2c3e50; }
        .no-score { color: #a0aec0; font-style: italic; font-size: 16px; }

        /* Form Styles */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #4a5568; font-weight: 600; font-size: 15px; }
        .form-group input[type="text"] { width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-size: 15px; font-family: 'Hind Siliguri', sans-serif; }
        .form-group input:focus { border-color: #27ae60; outline: none; box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.2); }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 15px; color: #4a5568; font-weight: 600; cursor: pointer; }
        .checkbox-label input[type="checkbox"] { width: 18px; height: 18px; accent-color: #27ae60; cursor: pointer; }
        .btn-submit { background: #2c3e50; color: white; border: none; padding: 14px 24px; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.3s; }
        .btn-submit:hover { background: #1a252f; }
        
        .alert-success { background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 15px; font-weight: bold; }
        .alert-error { background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        
        @if($errors->any())
            <div class="alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <h2>Your Trust Score</h2>
            <div class="trust-score-box">
                <div class="trust-score-title">Lifetime Employer Rating</div>
                @if($trustScore > 0)
                    <div class="stars">
                        {{ str_repeat('⭐', round($trustScore)) }}{{ str_repeat('☆', 5 - round($trustScore)) }}
                    </div>
                    <div class="score-number">{{ number_format($trustScore, 1) }} / 5.0</div>
                @else
                    <div class="no-score">You haven't received any ratings from employers yet. Complete tasks to build your score!</div>
                @endif
            </div>
        </div>

        <div class="card">
            <h2>Profile Settings</h2>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>Mobile Number / Login ID (Unchangeable)</label>
                    <input type="text" value="{{ $user->phone ?? $user->email }}" disabled style="background-color: #edf2f7; color: #a0aec0; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="district">District *</label>
                        <input type="text" id="district" name="district" value="{{ old('district', $user->district) }}" required>
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="upazila">Upazila / Area (Optional)</label>
                        <input type="text" id="upazila" name="upazila" value="{{ old('upazila', $user->upazila) }}">
                    </div>
                </div>

                <div class="form-group" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="sms_opt_in" value="1" {{ old('sms_opt_in', $user->sms_opt_in) ? 'checked' : '' }}>
                        <span>Enable SMS Alerts & Missed Call Notifications</span>
                    </label>
                    <p style="margin: 5px 0 0 26px; font-size: 13px; color: #718096;">Receive instant job matches and payment confirmations directly to your mobile phone.</p>
                </div>

                <button type="submit" class="btn-submit">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>