<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partnership Request Update</title>
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
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3b5998;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .badge-approved {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #28a745;
            color: #fff;
        }
        .badge-rejected {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #dc3545;
            color: #fff;
        }
        .badge-pending {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #ffc107;
            color: #000;
        }
        .content {
            margin-bottom: 25px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .notes-box {
            background-color: #f8f9fa;
            border-left: 4px solid #3b5998;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .notes-box h4 {
            margin: 0 0 10px 0;
            color: #3b5998;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .info-section h3 {
            margin-top: 0;
            color: #3b5998;
        }
        .courses-list {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        .courses-list li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .courses-list li:last-child {
            border-bottom: none;
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
            <h1>Partnership Request Update</h1>
            @if($partnership->status === 'approved')
                <span class="badge-approved">✓ Approved</span>
            @elseif($partnership->status === 'rejected')
                <span class="badge-rejected">✗ Not Approved</span>
            @else
                <span class="badge-pending">⏳ Pending</span>
            @endif
        </div>

        <div class="content">
            <p>Dear <strong>{{ $partnership->institution_name }}</strong>,</p>

            @if($partnership->status === 'approved')
                <p>We are pleased to inform you that your partnership request with Upskills Academy has been <strong>approved</strong>!</p>
                <p>We are excited to work together and look forward to a successful collaboration. Our team will contact you shortly to discuss the next steps and finalize the partnership details.</p>
            @elseif($partnership->status === 'rejected')
                <p>Thank you for your interest in partnering with Upskills Academy. After careful review of your application, we regret to inform you that we are unable to proceed with the partnership at this time.</p>
                <p>This decision does not reflect on the quality of your institution. We encourage you to apply again in the future as our partnership criteria may change.</p>
            @else
                <p>Your partnership request is currently under review. We will notify you once a decision has been made.</p>
            @endif
        </div>

        @if($partnership->notes)
            <div class="notes-box">
                <h4>📝 Additional Notes</h4>
                <p style="margin: 0;">{{ $partnership->notes }}</p>
            </div>
        @endif

        <div class="info-section">
            <h3>📋 Your Request Summary</h3>
            <p><strong>Institution:</strong> {{ $partnership->institution_name }}</p>
            <p><strong>Email:</strong> {{ $partnership->email }}</p>
            <p><strong>Submitted:</strong> {{ $partnership->created_at->format('F d, Y') }}</p>

            @if($partnership->requested_courses && count($partnership->requested_courses) > 0)
                <p><strong>Requested Courses:</strong></p>
                <ul class="courses-list">
                    @foreach($partnership->courses_list as $course)
                        <li>• {{ $course }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div style="text-align: center;">
            <a href="{{ env('USER_URL', 'https://upskillsacademy.com') }}" class="btn">
                Visit Our Website
            </a>
        </div>

        <div class="footer">
            <p>Thank you for your interest in Upskills Academy.</p>
            <p>If you have any questions, please contact us at info@up-skills.academy</p>
            <p>&copy; {{ date('Y') }} Upskills Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
