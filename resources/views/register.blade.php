<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.auth.page_register') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 40px 15px; box-sizing: border-box; }
        .register-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 550px; }
        .register-card h2 { text-align: center; color: #2c3e50; margin-top: 0; margin-bottom: 25px; font-size: 26px; }
        .form-group { margin-bottom: 18px; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 6px; color: #4a5568; font-weight: 600; font-size: 14px; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group select { width: 100%; padding: 11px; border: 1px solid #cbd5e0; border-radius: 6px; box-sizing: border-box; font-size: 15px; transition: border-color 0.2s; font-family: 'Hind Siliguri', sans-serif; background-color: white; }
        .form-group input:focus, .form-group select:focus { border-color: #27ae60; outline: none; box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.2); }
        .skills-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; background: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-radius: 6px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #4a5568; font-weight: normal; cursor: pointer; margin: 0; }
        .checkbox-label input[type="checkbox"] { width: 18px; height: 18px; accent-color: #27ae60; cursor: pointer; }
        .btn-submit { width: 100%; padding: 14px; background-color: #27ae60; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s; font-family: 'Hind Siliguri', sans-serif; margin-top: 10px; }
        .btn-submit:hover { background-color: #219150; }
        .error-box { background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .error-box ul { margin: 0; padding-left: 20px; color: #b91c1c; font-size: 14px; }
        .lang-bar { text-align: right; margin-bottom: 18px; }
        .lang-btn { display: inline-block; background: #27ae60; color: white; border-radius: 20px; padding: 5px 14px; font-size: 13px; font-weight: 600; text-decoration: none; font-family: 'Hind Siliguri', sans-serif; transition: background 0.2s; }
        .lang-btn:hover { background: #219150; }
        @media (max-width: 500px) { .form-row { flex-direction: column; gap: 0; } .skills-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="register-card">
        @php $switchTo = app()->getLocale() === 'en' ? 'bn' : 'en'; @endphp
        <div class="lang-bar">
            <a href="{{ route('lang.switch', $switchTo) }}" class="lang-btn">
                {{ __('messages.nav.switch_lang') }}
            </a>
        </div>

        <h2>{{ __('messages.auth.register_heading') }}</h2>

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/register" method="POST">
            @csrf

            <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

            <div class="form-group">
                <label for="name">{{ __('messages.auth.full_name') }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="{{ __('messages.auth.name_placeholder') }}">
            </div>

            <div class="form-group">
                <label for="phone">{{ __('messages.auth.mobile_number') }} *</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="{{ __('messages.auth.phone_placeholder') }}">
            </div>

            <div class="form-group">
                <label for="email">{{ __('messages.auth.email') }} *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="{{ __('messages.auth.email_placeholder') }}">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="district">{{ __('messages.auth.district_label') }} *</label>
                    <input type="text" id="district" name="district" value="{{ old('district') }}" required placeholder="{{ __('messages.auth.district_placeholder') }}">
                </div>
                <div class="form-group">
                    <label for="upazila">{{ __('messages.auth.upazila_label') }}</label>
                    <input type="text" id="upazila" name="upazila" value="{{ old('upazila') }}" placeholder="{{ __('messages.auth.upazila_placeholder') }}">
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('messages.auth.skills_label') }}</label>
                <div class="skills-grid">
                    @php
                        $availableSkills = [
                            'Farming' => __('messages.skills.farming'),
                            'Harvesting' => __('messages.skills.harvesting'),
                            'Fishing' => __('messages.skills.fishing'),
                            'Livestock' => __('messages.skills.livestock'),
                            'Equipment Repair' => __('messages.skills.repair'),
                            'Daily Labor' => __('messages.skills.labor')
                        ];
                    @endphp
                    @foreach($availableSkills as $slug => $translatedLabel)
                        <label class="checkbox-label">
                            <input type="checkbox" name="skills[]" value="{{ $slug }}" {{ is_array(old('skills')) && in_array($slug, old('skills')) ? 'checked' : '' }}>
                            <span>{{ $translatedLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label for="nid">{{ __('messages.auth.nid_label') }}</label>
                <input type="text" id="nid" name="nid" value="{{ old('nid') }}" placeholder="{{ __('messages.auth.nid_placeholder') }}">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">{{ __('messages.auth.password') }} *</label>
                    <input type="password" id="password" name="password" required placeholder="{{ __('messages.auth.password_placeholder') }}">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">{{ __('messages.auth.confirm_password') }} *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="{{ __('messages.auth.confirm_placeholder') }}">
                </div>
            </div>

            <div class="form-group" style="margin-top: 5px;">
                <label class="checkbox-label" style="font-weight: 600; color: #2c3e50;">
                    <input type="checkbox" name="sms_opt_in" value="1" {{ old('sms_opt_in', true) ? 'checked' : '' }}>
                    <span>{{ __('messages.auth.sms_opt_in') }}</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">{{ __('messages.auth.create_btn') }}</button>
        </form>
    </div>
</body>
</html>