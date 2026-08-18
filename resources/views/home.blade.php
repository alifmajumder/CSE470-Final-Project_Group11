<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RuralConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; margin: 0; background-color: #f4f7f6; color: #2c3e50; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 40px; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .navbar-brand { font-size: 24px; font-weight: bold; color: #27ae60; text-decoration: none; }
        .nav-links { display: flex; gap: 20px; align-items: center; }
        .nav-links a { text-decoration: none; color: #4a5568; font-weight: 600; transition: color 0.3s; }
        .nav-links a:hover { color: #27ae60; }
        .logout-form { margin: 0; padding: 0; }
        .logout-btn { background: none; border: none; color: #4a5568; font-weight: 600; font-size: 16px; cursor: pointer; font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; transition: color 0.3s; }
        .logout-btn:hover { color: #27ae60; }
        .lang-btn { background: #27ae60; color: white; border: none; border-radius: 20px; padding: 6px 16px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; font-family: 'Hind Siliguri', sans-serif; transition: background 0.2s; }
        .lang-btn:hover { background: #219150; }

        /* Notification Bell Styles */
        .notif-dropdown { position: relative; display: inline-block; margin-right: 15px; }
        .notif-content { display: none; position: absolute; right: 0; top: 30px; background-color: white; min-width: 280px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border-radius: 8px; border: 1px solid #e2e8f0; z-index: 100; max-height: 350px; overflow-y: auto; }
        .notif-dropdown:hover .notif-content { display: block; }
        .notif-item { padding: 12px; border-bottom: 1px solid #edf2f7; font-size: 13px; line-height: 1.4; color: #4a5568; }
        .notif-item:last-child { border-bottom: none; }
        .notif-badge { position: absolute; top: -5px; right: -8px; background: #e53e3e; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; }

        /* Two-Column Main Layout */
        .header-text { text-align: center; padding: 40px 20px 20px; }
        .header-text h1 { font-size: 48px; margin-bottom: 10px; color: #27ae60; margin-top: 0; }
        .header-text p { font-size: 20px; color: #4a5568; max-width: 600px; margin: 0 auto; }
        
        .main-container { display: flex; max-width: 1200px; margin: 0 auto; padding: 20px; gap: 30px; align-items: flex-start; }
        
        /* Left Column: Filters & Calculator */
        .left-sidebar { width: 350px; flex-shrink: 0; display: flex; flex-direction: column; gap: 20px; }
        
        /* Right Column: Job Cards */
        .right-content { flex-grow: 1; }

        /* Filter Sidebar Styles */
        .filter-section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .filter-section h3 { margin-top: 0; color: #27ae60; font-size: 20px; margin-bottom: 20px; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px; }
        .filter-form-group { margin-bottom: 15px; display: flex; flex-direction: column; text-align: left; }
        .filter-form-group label { font-weight: 600; font-size: 14px; margin-bottom: 5px; color: #4a5568; }
        .filter-form-group input, .filter-form-group select { padding: 10px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 15px; font-family: 'Hind Siliguri', sans-serif; }
        .filter-form-group input:focus, .filter-form-group select:focus { outline: none; border-color: #27ae60; }
        .filter-btn { background: #27ae60; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; transition: background 0.3s; margin-top: 10px; }
        .filter-btn:hover { background: #219150; }
        .clear-btn { background: #e2e8f0; color: #4a5568; border: none; padding: 10px; font-weight: bold; border-radius: 5px; cursor: pointer; width: 100%; font-size: 14px; transition: background 0.3s; margin-top: 10px; text-decoration: none; display: block; text-align: center; box-sizing: border-box; }
        .clear-btn:hover { background: #cbd5e0; }

        /* Job Card Styles */
        .job-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .job-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 5px solid #27ae60; transition: transform 0.2s; display: flex; flex-direction: column; }
        .job-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .job-title { font-size: 20px; font-weight: bold; color: #2c3e50; margin: 0 0 10px 0; }
        .job-meta { display: flex; align-items: center; gap: 15px; margin-bottom: 10px; font-size: 14px; color: #718096; }
        .job-badge { background: #f0fdf4; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px; display: inline-block; }
        .job-desc { font-size: 15px; color: #4a5568; margin-bottom: 20px; line-height: 1.5; flex-grow: 1; }
        .job-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 15px; border-top: 1px solid #edf2f7; }
        .job-wage { font-size: 18px; font-weight: bold; color: #27ae60; }
        .apply-btn { background: #2c3e50; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; font-weight: bold; transition: background 0.3s; }
        .apply-btn:hover { background: #1a252f; }
        .no-results { grid-column: 1 / -1; background: white; padding: 40px; text-align: center; border-radius: 10px; color: #718096; font-size: 18px; }

        /* Calculator Styles */
        .calculator-section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .calculator-section h3 { margin-top: 0; color: #27ae60; font-size: 20px; margin-bottom: 20px; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px; }
        .calc-form-group { margin-bottom: 15px; display: flex; flex-direction: column; text-align: left; }
        .calc-form-group label { font-weight: 600; font-size: 14px; margin-bottom: 5px; color: #4a5568; }
        .calc-form-group input { padding: 10px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 15px; }
        .calc-form-group input:focus { outline: none; border-color: #27ae60; }
        .calc-btn { background: #2c3e50; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; transition: background 0.3s; margin-top: 10px; }
        .calc-btn:hover { background: #1a252f; }
        .calc-result { margin-top: 15px; text-align: center; display: none; padding: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 14px; }
        .calc-result span { font-size: 24px; font-weight: bold; color: #166534; display: block; margin-top: 5px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <!-- LEFT: Brand Title, Welcome Message, and Admin Badge -->
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="/" class="navbar-brand">RuralConnect</a>
            
            @auth
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="color: #4a5568; font-size: 15px; font-weight: 600;">
                        {{ __('messages.nav.welcome', ['name' => Auth::user()->name]) }}
                    </span>
                    
                    @if(Auth::user()->role === 'admin')
                        <a href="/admin/users" style="background: #e53e3e; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">Admin Dashboard</a>
                    @endif
                </div>
            @endauth
        </div>

        <!-- RIGHT: Navigation Links -->
        <div class="nav-links">
            @guest
                <a href="/login">{{ __('messages.nav.login') }}</a>
                <a href="/register">{{ __('messages.nav.register') }}</a>
            @endguest

            @auth
                <!-- The old admin panel link was deleted from here! -->

                <a href="/tasks/create" style="background: #2c3e50; color: white; padding: 6px 16px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 600; margin-right: 15px;">Post a Job</a>
                <a href="/my-tasks" style="color: #4a5568; font-weight: 600; text-decoration: none; margin-right: 15px;">My Tasks</a>
                <a href="/my-payments" style="color: #4a5568; font-weight: 600; text-decoration: none; margin-right: 15px;">My Payments</a>
                <a href="/my-badges" style="color: #4a5568; font-weight: 600; text-decoration: none; margin-right: 15px;">My Badges</a>
                <a href="/sms/dashboard" style="color: #4a5568; font-weight: 600; text-decoration: none; margin-right: 15px;">{{ __('messages.nav.sms_dashboard') }}</a>
                
                <!-- NOTIFICATION BELL WIDGET -->
                <div class="notif-dropdown">
                    <span style="font-size: 20px; cursor: pointer;" onclick="markNotificationsAsRead()">🔔</span>
                    @php
                        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                        $recentNotifs = Auth::user()->notifications()->take(5)->get();
                    @endphp
                    
                    @if($unreadCount > 0)
                        <span id="notif-badge" class="notif-badge">{{ $unreadCount }}</span>
                    @endif
                    
                    <div class="notif-content">
                        @forelse($recentNotifs as $notif)
                            <div class="notif-item" style="{{ !$notif->is_read ? 'font-weight: 700; background: #f0fdf4;' : '' }}">
                                {{ $notif->message }}
                                <div style="font-size: 11px; color: #a0aec0; margin-top: 4px;">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="notif-item" style="text-align: center; font-style: italic;">No notifications yet.</div>
                        @endforelse
                    </div>
                </div>

                <a href="{{ route('profile.show') }}" style="color: #27ae60; font-weight: 700; text-decoration: none; margin-right: 15px;">Profile Settings</a>
                
                <form action="/logout" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">{{ __('messages.nav.logout') }}</button>
                </form>
            @endauth

            {{-- Language switcher --}}
            @php $switchTo = app()->getLocale() === 'en' ? 'bn' : 'en'; @endphp
            <a href="{{ route('lang.switch', $switchTo) }}" class="lang-btn">
                {{ __('messages.nav.switch_lang') }}
            </a>
        </div>
    </nav>

    <div class="header-text">
        <h1>{{ __('messages.home.tagline') }}</h1>
        <p>{{ __('messages.home.subtitle') }}</p>
        
        <!-- WHY I DID IT: Integrated Sabaha's Feature 20 Impact Dashboard CTA directly below your homepage banner text. -->
        <div style="margin: 24px 0; text-align: center;">
            <a href="/impact" style="display: inline-block; background: #27ae60; color: white; text-decoration: none; padding: 14px 26px; border-radius: 999px; font-size: 16px; font-weight: 700;">View Impact Dashboard</a>
        </div>
    </div>

    <div class="main-container">
        <!-- LEFT COLUMN: Filters & Calculator -->
        <div class="left-sidebar">
            
            <!-- Professional Task Finder Sidebar (Only for Logged-In Users) -->
            @auth
                <div class="filter-section">
                    <h3>🔍 Find Work</h3>
                    <form action="{{ route('home') }}" method="GET">
                        
                        <div class="filter-form-group">
                            <label>Keyword Search</label>
                            <input type="text" name="keyword" placeholder="e.g. Tractor, Harvesting" value="{{ request('keyword') }}">
                        </div>

                        <div class="filter-form-group">
                            <label>Category</label>
                            <select name="category">
                                <option value="">All Categories</option>
                                @foreach(config('skills.categories', []) as $slug => $label)
                                    <option value="{{ $slug }}" {{ request('category') == $slug ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-form-group">
                            <label>District</label>
                            <input type="text" name="district" placeholder="e.g. Dhaka" value="{{ request('district') }}">
                        </div>

                        <button type="submit" class="filter-btn">Apply Filters</button>
                        @if(request()->anyFilled(['keyword', 'category', 'district']))
                            <a href="{{ route('home') }}" class="clear-btn">Clear Filters</a>
                        @endif
                    </form>
                </div>
            @endauth

            @guest
                <div class="filter-section" style="text-align: center; padding: 30px 20px;">
                    <h3 style="border: none; margin-bottom: 10px;">🔒 Task Matchmaking</h3>
                    <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                        Please log in to access the advanced task finder, filter local micro-jobs, and claim tasks suited to your skills.
                    </p>
                    <a href="/login" class="filter-btn" style="text-decoration: none; display: block; box-sizing: border-box;">Log In to Find Work</a>
                </div>
            @endguest

            <!-- EXISTING: Interactive Fair Wage Calculator -->
            <div class="calculator-section">
                <h3>⚖️ Fair Wage Calculator</h3>
                <div class="calc-form-group">
                    <label>Total Budget / Wage (৳)</label>
                    <input type="number" id="calc-total-wage" placeholder="e.g. 1000" min="1">
                </div>
                <div class="calc-form-group">
                    <label>Number of Workers Needed</label>
                    <input type="number" id="calc-workers" placeholder="e.g. 4" min="1">
                </div>
                <button class="calc-btn" onclick="calculateFairWage()">Calculate Fair Cut</button>

                <div class="calc-result" id="calc-result-box">
                    Guaranteed Fair Wage:
                    <span id="calc-fair-wage-amount">৳0.00</span>
                </div>
            </div>

        </div> <!-- End Left Column -->

        <!-- RIGHT COLUMN: Dynamic Job Cards -->
        <div class="right-content">
            
            @auth
                @if(session('success'))
                    <div class="alert-success" style="background: #f0fdf4; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; font-weight: bold;">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-error" style="background: #fef2f2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca; font-weight: bold;">
                        ❌ {{ $errors->first() }}
                    </div>
                @endif
            @endauth

            <div class="job-grid">
                
                @guest
                    <div class="no-results" style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="color: #27ae60; margin-top: 0; font-size: 24px;">Welcome to RuralConnect</h3>
                        <p style="color: #4a5568; font-size: 16px; max-width: 500px; margin: 10px auto 20px;">
                            Please log in or register an account to browse available micro-tasks in your district, filter by your skillset, and start earning.
                        </p>
                        <div style="display: flex; gap: 15px; justify-content: center;">
                            <a href="/login" class="apply-btn" style="padding: 10px 25px; font-size: 15px; text-decoration: none;">Log In</a>
                            <a href="/register" class="apply-btn" style="background: #27ae60; padding: 10px 25px; font-size: 15px; text-decoration: none;">Register</a>
                        </div>
                    </div>
                @endguest

                @auth
                    @forelse($tasks ?? [] as $task)
                        <div class="job-card">
                            <h3 class="job-title">{{ $task->title }}</h3>
                            
                            <div class="job-meta">
                                <span>📍 {{ $task->district }}</span>
                                <span class="job-badge">{{ $task->categoryLabel() }}</span>
                            </div>

                            <!-- Vacancy Counter with Red/Green Status -->
                            <div style="font-size: 13px; color: #4a5568; margin-bottom: 10px; font-weight: 600;">
                                👥 Open Slots: 
                                <span style="color: {{ $task->registered_workers >= $task->required_workers ? '#e53e3e' : '#27ae60' }};">
                                    {{ $task->registered_workers }} / {{ $task->required_workers }} filled
                                    @if($task->registered_workers >= $task->required_workers)
                                        (Full)
                                    @endif
                                </span>
                            </div>
                            
                            <p class="job-desc">{{ Str::limit($task->description, 100) }}</p>
                            
                            <div class="job-footer">
                                <span class="job-wage">৳{{ $task->wage }}</span>
                                
                                <!-- UPDATED "APPLY" BUTTON -->
                                <form action="{{ route('tasks.workers.take', $task->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="apply-btn" style="border: none; cursor: pointer;">Apply for Job</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="no-results">
                            <p>No jobs found matching your criteria. Try adjusting your filters!</p>
                        </div>
                    @endforelse
                @endauth

            </div>
        </div> <!-- End Right Column -->

    </div> <!-- End Main Container -->

    <!-- Javascript for the Calculator -->
    <script>
        function calculateFairWage() {
            let totalWage = parseFloat(document.getElementById('calc-total-wage').value);
            let workers = parseInt(document.getElementById('calc-workers').value);
            let resultBox = document.getElementById('calc-result-box');
            let amountDisplay = document.getElementById('calc-fair-wage-amount');

            if (isNaN(totalWage) || isNaN(workers) || workers <= 0 || totalWage <= 0) {
                alert("Please enter valid numbers for the budget and group size.");
                return;
            }

            let fairWage = totalWage / workers;
            amountDisplay.innerText = "৳" + fairWage.toFixed(2);
            resultBox.style.display = "block";
        }
    </script>

    <!-- Javascript for Notifications -->
    <script>
        function markNotificationsAsRead() {
            let badge = document.getElementById('notif-badge');
            
            if (badge) {
                badge.style.display = 'none';
                
                fetch('{{ route('notifications.read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
            }
        }
    </script>
</body>
</html>