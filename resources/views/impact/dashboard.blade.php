<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.impact.page_title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background: #f4f7f6; color: #2c3e50; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 22px 40px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .brand { color: #27ae60; font-weight: 700; font-size: 24px; text-decoration: none; }
        .nav-links { display: flex; gap: 18px; align-items: center; }
        .nav-links a { color: #4a5568; text-decoration: none; font-weight: 600; }
        .hero { max-width: 1080px; margin: 36px auto; padding: 0 20px; }
        .hero h1 { font-size: 34px; margin-bottom: 12px; }
        .hero p { font-size: 16px; color: #4a5568; max-width: 720px; line-height: 1.7; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, minmax(220px, 1fr)); gap: 18px; margin-top: 30px; }
        .stat-card { background: white; border-radius: 16px; padding: 28px 24px; box-shadow: 0 10px 30px rgba(0,0,0,.05); }
        .stat-card span { display: block; font-size: 14px; color: #718096; margin-bottom: 10px; }
        .stat-card strong { display: block; font-size: 42px; color: #2c3e50; line-height: 1.05; }
        .stat-card .accent { color: #27ae60; }
        .note { margin-top: 24px; font-size: 14px; color: #4a5568; }
        .btn { display: inline-block; margin-top: 22px; padding: 12px 22px; border-radius: 999px; background: #27ae60; color: white; text-decoration: none; font-weight: 700; }
        @media (max-width: 900px) { .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="/" class="brand">RuralConnect</a>
        <div class="nav-links">
            <a href="/">{{ __('messages.nav.home') }}</a>
            <a href="/sms/dashboard">{{ __('messages.nav.sms_dashboard') }}</a>
            <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'bn' : 'en') }}">{{ __('messages.nav.switch_lang') }}</a>
        </div>
    </nav>

    <main class="hero">
        <h1>{{ __('messages.impact.heading') }}</h1>
        <p>{{ __('messages.impact.subtitle') }}</p>

        <div class="stats-grid">
            <div class="stat-card">
                <span>{{ __('messages.impact.jobs_created') }}</span>
                <strong class="accent">{{ number_format($jobsCreated) }}</strong>
            </div>
            <div class="stat-card">
                <span>{{ __('messages.impact.income_generated') }}</span>
                <strong class="accent">৳{{ number_format($incomeGenerated, 2) }}</strong>
            </div>
            <div class="stat-card">
                <span>{{ __('messages.impact.families_supported') }}</span>
                <strong class="accent">{{ number_format($familiesSupported) }}</strong>
            </div>
        </div>

        <p class="note">{{ __('messages.impact.note') }}</p>
        <a href="/register" class="btn">{{ __('messages.impact.cta') }}</a>
    </main>
</body>
</html>