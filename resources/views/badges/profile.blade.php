<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profileUser->name }} — Worker Profile</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px; color: #2c3e50; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); max-width: 1000px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; }
        .nav-button:hover { background: #219150; }
        .profile-head { display: flex; align-items: center; gap: 15px; margin-bottom: 5px; }
        .avatar { width: 56px; height: 56px; border-radius: 50%; background: #27ae60; color: white; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; }
        h2 { margin: 0; }
        .verified-tag { display: inline-block; background: #ebf8ff; color: #2b6cb0; font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 10px; margin-left: 8px; }
        .subtitle { color: #718096; font-size: 14px; margin-bottom: 25px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()" class="nav-button">&larr; Back</a>

        <div class="profile-head">
            <div class="avatar">{{ strtoupper(substr($profileUser->name, 0, 1)) }}</div>
            <div>
                <h2>{{ $profileUser->name }}
                    @if($profileUser->is_verified)
                        <span class="verified-tag">Verified Account</span>
                    @endif
                </h2>
            </div>
        </div>
        <p class="subtitle">Skill badges earned by completing jobs through RuralConnect.</p>

        @include('badges._grid', ['progress' => $progress])
    </div>
</body>
</html>
