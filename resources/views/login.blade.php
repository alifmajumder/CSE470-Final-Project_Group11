<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.auth.page_login') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .auth-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 420px; }
        .auth-card h2 { text-align: center; color: #2c3e50; margin-top: 0; margin-bottom: 25px; font-size: 26px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #4a5568; font-weight: 600; font-size: 14px; }
        .form-group input[type="text"],
        .form-group input[type="password"] { width: 100%; padding: 12px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-size: 15px; transition: border-color 0.2s; font-family: 'Hind Siliguri', sans-serif; }
        .form-group input:focus { border-color: #27ae60; outline: none; box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.2); }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #4a5568; font-weight: 600; cursor: pointer; margin-bottom: 20px; }
        .checkbox-label input[type="checkbox"] { width: 18px; height: 18px; accent-color: #27ae60; cursor: pointer; }
        .btn-submit { width: 100%; padding: 14px; background-color: #27ae60; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s; margin-bottom: 15px; font-family: 'Hind Siliguri', sans-serif; }
        .btn-submit:hover { background-color: #219150; }
        .error-box { background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .error-box ul { margin: 0; padding-left: 20px; color: #b91c1c; font-size: 14px; }
        .link-text { text-align: center; font-size: 14px; color: #718096; }
        .link-text a { color: #27ae60; text-decoration: none; font-weight: bold; }
        .link-text a:hover { text-decoration: underline; }
        .lang-bar { text-align: right; margin-bottom: 18px; }
        .lang-btn { display: inline-block; background: #27ae60; color: white; border-radius: 20px; padding: 5px 14px; font-size: 13px; font-weight: 600; text-decoration: none; font-family: 'Hind Siliguri', sans-serif; transition: background 0.2s; }
        .lang-btn:hover { background: #219150; }
    </style>
</head>
<body>
    <div class="auth-card">
        @php $switchTo = app()->getLocale() === 'en' ? 'bn' : 'en'; @endphp
        <div class="lang-bar">
            <a href="{{ route('lang.switch', $switchTo) }}" class="lang-btn">
                {{ __('messages.nav.switch_lang') }}
            </a>
        </div>

        <h2>{{ __('messages.auth.login_heading') }}</h2>

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf

            <!-- Upgraded to accept Phone OR Email -->
            <div class="form-group">
                <label for="login_id">{{ __('messages.auth.login_id_label') }}</label>
                <input type="text" id="login_id" name="login_id" value="{{ old('login_id') }}" required
                       placeholder="{{ __('messages.auth.login_id_placeholder') }}">
            </div>

            <div class="form-group">
                <label for="password">{{ __('messages.auth.password') }}</label>
                <input type="password" id="password" name="password" required
                       placeholder="{{ __('messages.auth.password_placeholder_login') }}">
            </div>

            <!-- New Bilingual Remember Me Checkbox -->
            <label class="checkbox-label">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span>{{ __('messages.auth.remember_me') }}</span>
            </label>

            <button type="submit" class="btn-submit">{{ __('messages.auth.login_btn') }}</button>

            <div class="link-text">
                {{ __('messages.auth.no_account') }}
                <a href="/register">{{ __('messages.auth.register_link') }}</a>
            </div>
        </form>
    </div>
</body>
</html>