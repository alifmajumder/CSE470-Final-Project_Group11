<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seasonal Labor Forecast</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px; color: #2c3e50; margin: 0; }
        .container { max-width: 1000px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; transition: background 0.3s; }
        .nav-button:hover { background: #219150; }
        
        .header-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; text-align: center; border-bottom: 5px solid #27ae60; }
        h2 { color: #27ae60; margin: 0 0 10px 0; font-size: 32px; }
        .subtitle { color: #718096; font-size: 16px; margin: 0; max-width: 600px; margin: 0 auto; }

        .grid-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card h3 { color: #2c3e50; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px; margin-top: 0; margin-bottom: 20px; font-size: 22px; }
        
        .demand-badge { display: inline-block; padding: 6px 15px; border-radius: 20px; font-weight: bold; font-size: 14px; text-transform: uppercase; margin-bottom: 15px; }
        .demand-Peak { background: #fee2e2; color: #c53030; }
        .demand-High { background: #feebc8; color: #c05621; }
        .demand-Medium { background: #e2e8f0; color: #4a5568; }
        .demand-Low { background: #c6f6d5; color: #22543d; }

        .skill-tags { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px; }
        .skill-tag { background: #ebf8ff; color: #2b6cb0; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600; }

        .upcoming-row { padding: 15px 0; border-bottom: 1px solid #edf2f7; }
        .upcoming-row:last-child { border-bottom: none; }
        .upcoming-month { font-size: 18px; font-weight: bold; color: #27ae60; margin-bottom: 5px; }
        .upcoming-season { font-size: 15px; color: #4a5568; font-weight: 600; }

        @media(max-width: 768px) {
            .grid-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        
        <div class="header-box">
            <h2>🌦️ Seasonal Labor Demand Forecast</h2>
            <p class="subtitle">Stay ahead of the agricultural curve. See what jobs are in high demand this month and prepare your skills for the upcoming seasons.</p>
        </div>

        <div class="grid-layout">
            <!-- Left: Current Season -->
            <div class="card" style="border-left: 5px solid #3182ce;">
                <h3>Current Outlook: {{ $monthName }}</h3>
                <p style="font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">{{ $currentData['season'] }}</p>
                
                <span class="demand-badge demand-{{ $currentData['demand'] }}">
                    Demand Level: {{ $currentData['demand'] }}
                </span>

                <p style="color: #718096; font-size: 15px; margin-top: 20px; margin-bottom: 5px;">Most Requested Skills Right Now:</p>
                <div class="skill-tags">
                    @foreach($currentData['skills'] as $skill)
                        <span class="skill-tag">✔️ {{ $skill }}</span>
                    @endforeach
                </div>
            </div>

            <!-- Right: Upcoming Seasons -->
            <div class="card">
                <h3>⏩ Upcoming Forecast</h3>
                <p style="color: #718096; font-size: 14px; margin-bottom: 20px;">Secure your equipment and availability for the next 60 days:</p>

                @foreach($upcoming as $month => $data)
                    <div class="upcoming-row">
                        <div class="upcoming-month">{{ $month }}</div>
                        <div class="upcoming-season">{{ $data['season'] }}</div>
                        <div style="margin-top: 8px;">
                            <span class="demand-badge demand-{{ $data['demand'] }}" style="padding: 3px 8px; font-size: 11px;">Expected Demand: {{ $data['demand'] }}</span>
                        </div>
                        <div style="font-size: 13px; color: #4a5568; margin-top: 5px;">
                            <strong>Prepare for:</strong> {{ implode(', ', $data['skills']) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>