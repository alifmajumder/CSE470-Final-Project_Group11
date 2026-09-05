<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RuralConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px 20px; color: #2c3e50; margin: 0; }
        .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 1100px; margin: 0 auto; margin-bottom: 30px; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #f0fdf4; padding-bottom: 15px; }
        h2 { color: #27ae60; margin: 0; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 15px; border-bottom: 1px solid #edf2f7; font-size: 15px; }
        th { color: #a0aec0; font-size: 12px; text-transform: uppercase; font-weight: 700; background: #f8fafc; }
        tr:hover { background-color: #fcfcfc; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-pending { background: #fee2e2; color: #c53030; }
        .status-verified { background: #d1fae5; color: #065f46; }
        .btn-verify { background-color: #27ae60; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; }
        .form-group { margin-bottom: 15px; }
        .form-group input { padding: 10px; border: 1px solid #cbd5e0; border-radius: 5px; width: 100%; box-sizing: border-box; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
    </style>
</head>
<body>
    <div style="max-width: 1100px; margin: 0 auto;">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">✅ {{ session('success') }}</div>
        @endif
    </div>
    
    <div class="container">
        <div class="header-flex">
            <h2>🛡️ Platform Users Verification</h2>
            <span style="background: #e2e8f0; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; color: #4a5568;">Total Users: {{ count($users) }}</span>
        </div>
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td><span class="status-badge {{ $user->is_verified ? 'status-verified' : 'status-pending' }}">{{ $user->is_verified ? 'Verified' : 'Pending' }}</span></td>
                        <td>
                            @if(!$user->is_verified)
                                <form action="/admin/verify/{{ $user->id }}" method="POST" style="margin:0;">@csrf <button class="btn-verify">Approve</button></form>
                            @else ✔️ Complete @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- FEATURE 13: Krishi Market Admin Panel -->
    <div class="container">
        <div class="header-flex"><h2>🌾 Krishi Market Management</h2></div>
        <form action="{{ route('market.store') }}" method="POST" style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
            @csrf
            <div class="grid-3">
                <div class="form-group">
                    <label style="font-weight: bold; font-size: 13px;">Item / Service Name</label>
                    <input type="text" name="item_name" placeholder="e.g. Tractor Rental" required>
                </div>
                <div class="form-group">
                    <label style="font-weight: bold; font-size: 13px;">Unit Metric</label>
                    <input type="text" name="unit" placeholder="e.g. Per Hour" required>
                </div>
                <div class="form-group">
                    <label style="font-weight: bold; font-size: 13px;">Price (৳)</label>
                    <input type="number" name="price" placeholder="e.g. 500" required>
                </div>
            </div>
            <button type="submit" class="btn-verify" style="background: #2b6cb0;">Add to Market Board</button>
        </form>

        <table>
            <thead><tr><th>Item</th><th>Unit</th><th>Standard Price</th><th>Action</th></tr></thead>
            <tbody>
                @foreach($marketPrices as $item)
                    <tr>
                        <td><strong>{{ $item->item_name }}</strong></td>
                        <td>{{ $item->unit }}</td>
                        <td style="color: #27ae60; font-weight: bold;">৳{{ number_format($item->price, 2) }}</td>
                        <td>
                            <form action="{{ route('market.destroy', $item->id) }}" method="POST" style="margin:0;">
                                @csrf @method('DELETE') <button type="submit" style="background: #e53e3e; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-weight: bold;">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>