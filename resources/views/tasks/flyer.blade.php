<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Job Flyer - {{ $task->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; 
            background: #e2e8f0; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            justify-content: center; 
        }
        
        .a4-sheet { 
            background: white; 
            width: 210mm; 
            height: 297mm; 
            padding: 15mm 20mm;
            box-sizing: border-box; 
            position: relative; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            display: flex; 
            flex-direction: column; 
            border: 12px solid #27ae60; 
            border-radius: 8px;
            overflow: hidden; 
        }

        .brand-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #edf2f7; padding-bottom: 10px; margin-bottom: 25px; }
        .brand-name { font-size: 26px; font-weight: 800; color: #27ae60; text-transform: uppercase; letter-spacing: 1px; }
        .brand-tagline { font-size: 16px; color: #718096; font-weight: 600; }

        .flyer-header { text-align: center; margin-bottom: 20px; }
        .flyer-header h1 { font-size: 60px; color: #e53e3e; margin: 0; line-height: 1.1; text-shadow: 2px 2px 0 rgba(229, 62, 62, 0.1); }
        .flyer-header h2 { font-size: 32px; color: #1a202c; margin: 15px 0 0 0; background: #f0fdf4; display: inline-block; padding: 8px 30px; border-radius: 50px; border: 2px solid #27ae60; }

        .job-details { flex-grow: 1; padding: 0 5px; }
        .detail-row { margin-bottom: 15px; background: #f7fafc; padding: 15px 20px; border-radius: 12px; border-left: 8px solid #27ae60; }
        .detail-label { font-size: 18px; color: #4a5568; font-weight: 700; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
        .detail-value { font-size: 26px; color: #2d3748; font-weight: 700; line-height: 1.3; }


        .wage-box { background: #166534; color: white; padding: 20px; text-align: center; margin: 20px 0; border-radius: 16px; box-shadow: 0 10px 25px rgba(22, 101, 52, 0.2); }
        .wage-box .detail-label { color: #bbf7d0; justify-content: center; font-size: 22px; border: none; margin-bottom: 5px; }
        .wage-box .wage-amount { font-size: 70px; font-weight: 800; line-height: 1; text-shadow: 3px 3px 0 rgba(0,0,0,0.3); }

        .contact-box { text-align: center; margin-top: auto; padding-top: 15px; border-top: 3px dashed #cbd5e0; background: white; }
        .contact-title { font-size: 22px; color: #4a5568; font-weight: bold; margin-bottom: 5px; }
        .contact-phone { font-size: 60px; color: #e53e3e; font-weight: 800; letter-spacing: 2px; line-height: 1; margin-bottom: 5px; }

        @page { size: A4; margin: 0; }
        @media print {
            body { background: white; padding: 0; margin: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .a4-sheet { 
                width: 210mm; 
                height: 297mm; 
                margin: 0; 
                border: 12px solid #27ae60; 
                border-radius: 0;
                box-shadow: none; 
                page-break-after: avoid; 
                page-break-inside: avoid; 
            }
            .no-print { display: none !important; }
        }

        .print-btn { background: #2c3e50; color: white; border: none; padding: 15px 30px; font-size: 20px; font-weight: bold; border-radius: 8px; cursor: pointer; position: fixed; top: 20px; right: 20px; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .print-btn:hover { background: #1a252f; }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print print-btn">🖨️ Print Here</button>
    
    <div class="a4-sheet">
        <div class="brand-header">
            <div class="brand-name">Rural</div>
            <div class="brand-name">Connect</div>
        </div>

        <div class="flyer-header">
            <h1>Hiring For Job</h1>
            <h2>{{ $task->title }}</h2>
        </div>
        
        <div class="job-details">
            <div class="detail-row">
                <div class="detail-label">📍 Place</div>
                <div class="detail-value">{{ $task->location }}, {{ $task->district }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">📝 Job Description</div>
                <div class="detail-value" style="font-size: 28px; font-weight: 600;">{{ $task->description }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">👥 Required Workers</div>
                <div class="detail-value">{{ $task->required_workers }} people</div>
            </div>
            
            <div class="wage-box">
                <div class="detail-label">💰 Total Wage</div>
                <div class="wage-amount">{{ $task->wage }}Tk</div>
            </div>
        </div>
        
        <div class="contact-box">
            <div class="contact-title">For more information, call:</div>
            <div class="contact-phone">📞 {{ $task->employer->phone ?? 'Contact us' }}</div>
        </div>
    </div>
</body>
</html>