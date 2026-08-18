<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RuralConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px 20px; color: #2c3e50; margin: 0; }
        .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 1100px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; transition: background 0.3s;}
        .nav-button:hover { background: #219150; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #f0fdf4; padding-bottom: 15px; }
        h2 { color: #27ae60; margin: 0; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 15px; border-bottom: 1px solid #edf2f7; font-size: 15px; }
        th { color: #a0aec0; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; background: #f8fafc; }
        tr:hover { background-color: #fcfcfc; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-pending { background: #fee2e2; color: #c53030; }
        .status-verified { background: #d1fae5; color: #065f46; }
        .btn-verify { background-color: #27ae60; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; transition: 0.2s; box-shadow: 0 2px 4px rgba(39,174,96,0.2); }
        .btn-verify:hover { background-color: #219150; transform: translateY(-1px); }
        .action-complete { color: #a0aec0; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        .empty-state { text-align: center; padding: 40px; color: #718096; font-style: italic; }
    </style>
</head>
<body>
    <div style="max-width: 1100px; margin: 0 auto;">
        <a href="/" class="nav-button">&larr; Back to Home</a>
    </div>
    
    <div class="container">
        <div class="header-flex">
            <h2>🛡️ Platform Users Verification</h2>
            <span style="background: #e2e8f0; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 600; color: #4a5568;">
                Total Users: {{ count($users) }}
            </span>
        </div>
        
        @if(count($users) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td><a href="mailto:{{ $user->email }}" style="color: #3182ce; text-decoration: none;">{{ $user->email }}</a></td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                @if($user->is_verified)
                                    <span class="status-badge status-verified">✅ Verified</span>
                                @else
                                    <span class="status-badge status-pending">⏳ Pending</span>
                                @endif
                            </td>
                            <td>
                                @if(!$user->is_verified)
                                    <form action="/admin/verify/{{ $user->id }}" method="POST" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn-verify">Approve User</button>
                                    </form>
                                @else
                                    <span class="action-complete">✔️ Action Complete</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <span style="font-size: 30px; display: block; margin-bottom: 10px;">📭</span>
                No users currently require verification.
            </div>
        @endif
    </div>
</body>
</html>