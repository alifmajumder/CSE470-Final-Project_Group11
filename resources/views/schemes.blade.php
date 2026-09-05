<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Govt Scheme Eligibility</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background-color: #f4f7f6; color: #2c3e50; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; transition: background 0.3s; }
        .nav-button:hover { background: #219150; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        h2 { color: #27ae60; margin-top: 0; font-size: 28px; }
        p.subtitle { color: #718096; margin-bottom: 25px; }
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 200px; display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 8px; font-size: 15px; }
        input, select { padding: 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 15px; }
        input:focus, select:focus { outline: none; border-color: #27ae60; }
        .btn-submit { background: #2c3e50; color: white; border: none; padding: 14px 20px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 16px; margin-top: 10px; transition: background 0.3s; }
        .btn-submit:hover { background: #1a252f; }
        .scheme-card { background: #f0fdf4; border-left: 5px solid #27ae60; padding: 20px; border-radius: 8px; margin-bottom: 15px; }
        .scheme-title { font-size: 20px; font-weight: bold; color: #166534; margin: 0 0 5px 0; }
        .scheme-agency { font-size: 13px; color: #2f855a; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .scheme-desc { color: #4a5568; margin: 0; line-height: 1.5; }
        .no-match { background: #fff5f5; border-left: 5px solid #e53e3e; padding: 20px; border-radius: 8px; color: #c53030; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        
        <div class="card">
            <h2>🏛️ Government Scheme Eligibility Checker</h2>
            <p class="subtitle">Enter your details below to instantly see which state subsidies, agricultural grants, or safety net programs you qualify for in Bangladesh.</p>
            
            <form action="{{ route('schemes.check') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" required min="18" placeholder="e.g. 45" value="{{ old('age') }}">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="" disabled selected>Select...</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Estimated Monthly Income (৳)</label>
                        <input type="number" name="income" required min="0" placeholder="e.g. 3500" value="{{ old('income') }}">
                    </div>
                    <div class="form-group">
                        <label>Total Land Owned (in Acres)</label>
                        <input type="number" name="land_size" required min="0" step="0.01" placeholder="e.g. 0.25" value="{{ old('land_size') }}">
                    </div>
                </div>
                <button type="submit" class="btn-submit">Check Eligibility</button>
            </form>
        </div>

        @if(isset($checked))
            <div class="card">
                <h2>📋 Your Results</h2>
                
                @if(count($eligibleSchemes) > 0)
                    <p style="color: #4a5568; margin-bottom: 20px;">Based on the criteria provided, you may be eligible for the following programs:</p>
                    @foreach($eligibleSchemes as $scheme)
                        <div class="scheme-card">
                            <h3 class="scheme-title">{{ $scheme['name'] }}</h3>
                            <div class="scheme-agency">{{ $scheme['agency'] }}</div>
                            <p class="scheme-desc">{{ $scheme['description'] }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="no-match">
                        Currently, there are no primary government schemes matching these specific metrics in our database. Please consult your local Union Parishad office for undocumented safety nets.
                    </div>
                @endif
            </div>
        @endif
    </div>
</body>
</html>