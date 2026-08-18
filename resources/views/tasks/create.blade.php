<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Group Task</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; color: #2c3e50; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { color: #27ae60; margin-top: 0; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 5px; font-size: 14px; }
        input, textarea, select { padding: 10px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 14px; font-family: 'Segoe UI', sans-serif; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #27ae60; }
        .btn-submit { background: #27ae60; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px; width: 100%; }
        .btn-submit:hover { background: #219150; }
        .error-box { background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .error-box ul { margin: 0; padding-left: 20px; color: #b91c1c; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Post a Group Task</h2>

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/tasks" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="title" required placeholder="e.g. Rice Harvesting">
            </div>

            <div class="form-group">
                <label>Job Category</label>
                <select name="category" required>
                    <option value="" disabled selected>Select a category&hellip;</option>
                    @foreach(config('skills.categories') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <small style="color:#718096; margin-top: 4px;">Workers who repeatedly complete jobs in a category earn a verified skill badge for it.</small>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" required placeholder="Describe the work required..."></textarea>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Total Wage (BDT)</label>
                    <input type="number" name="wage" required min="1">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Workers Needed</label>
                    <input type="number" name="required_workers" required min="1" value="1">
                </div>
            </div>

            <div class="form-group">
                <label>District</label>
                <input type="text" name="district" required placeholder="e.g. Rajshahi">
            </div>

            <div class="form-group">
                <label>Specific Location</label>
                <input type="text" name="location" required placeholder="e.g. Village North Field">
            </div>

            <button type="submit" class="btn-submit">Publish Job</button>
        </form>
    </div>
</body>
</html>