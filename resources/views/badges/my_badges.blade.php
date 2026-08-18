<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Badges</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px; color: #2c3e50; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); max-width: 1000px; margin: 0 auto; }
        h2 { color: #27ae60; margin-top: 0; margin-bottom: 5px; }
        .subtitle { color: #718096; font-size: 14px; margin-bottom: 25px; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; }
        .nav-button:hover { background: #219150; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        <h2>My Skill Badges</h2>
        <p class="subtitle">Complete jobs in a category to earn verified badges &mdash; your digital resume that employers can see.</p>

        @include('badges._grid', ['progress' => $progress])
    </div>
</body>
</html>
