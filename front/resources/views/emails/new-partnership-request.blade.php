<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Partnership Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3b5998;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3b5998;
            margin: 0;
            font-size: 24px;
        }
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #ffc107;
            color: #000;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h3 {
            color: #3b5998;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            margin-left: 10px;
        }
        .courses-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .courses-list li {
            background-color: #f8f9fa;
            padding: 8px 15px;
            margin-bottom: 5px;
            border-radius: 4px;
            border-left: 3px solid #3b5998;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3b5998;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #2d4373;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤝 New Partnership Request</h1>
            <span class="badge">Pending Review</span>
        </div>

        <div class="info-section">
            <h3>📋 Institution Information</h3>
            <div class="info-row">
                <span class="info-label">Institution Name:</span>
                <span class="info-value">{{ $partnership->institution_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $partnership->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $partnership->phone }}</span>
            </div>
            @if($partnership->website)
            <div class="info-row">
                <span class="info-label">Website:</span>
                <span class="info-value"><a href="{{ $partnership->website }}">{{ $partnership->website }}</a></span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $partnership->institution_address }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3>📚 Requested Courses</h3>
            @php
                $courseNames = [
                    'sports_injuries' => 'Exam Preparation (IELTS / TOEFL)',
                    'sports_nutrition' => 'Business English',
                    'fitness_assistant' => 'Foundation English (FA)',
                    'fitness_trainer' => 'Fluency & Interaction (FI)',
                    'personal_trainer' => 'Proficiency Track (PT)',
                    'sports_rehabilitation' => 'Academic Writing',
                    'sports_psychology' => 'Young Learners',
                    'sports_management' => 'Teacher Training (TESOL)',
                ];
            @endphp
            @if($partnership->requested_courses && count($partnership->requested_courses) > 0)
                <ul class="courses-list">
                    @foreach($partnership->requested_courses as $course)
                        <li>{{ $courseNames[$course] ?? $course }}</li>
                    @endforeach
                </ul>
            @else
                <p>No specific courses selected.</p>
            @endif

            @if($partnership->additional_courses)
                <div style="margin-top: 15px;">
                    <strong>Additional Courses Request:</strong>
                    <p style="background-color: #f8f9fa; padding: 10px; border-radius: 4px;">{{ $partnership->additional_courses }}</p>
                </div>
            @endif
        </div>

        @if($partnership->profile_file)
        <div class="info-section">
            <h3>📎 Attached File</h3>
            <p>An institution profile file has been attached to this request.</p>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ env('ADMIN_URL', 'https://admin.upskillsacademy.com') }}/partnerships/{{ $partnership->id }}" class="btn">
                View Full Details
            </a>
        </div>

        <div class="footer">
            <p>This is an automated notification from Upskills Academy.</p>
            <p>Submitted on: {{ $partnership->created_at->format('F d, Y \a\t H:i') }}</p>
        </div>
    </div>
</body>
</html>
