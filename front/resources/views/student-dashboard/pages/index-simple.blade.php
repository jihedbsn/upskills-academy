<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1>Dashboard Test - {{ $stats['enrolled_courses'] ?? 0 }} Courses</h1>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Enrolled Courses</h5>
                        <h3>{{ $stats['enrolled_courses'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Completed Courses</h5>
                        <h3>{{ $stats['completed_courses'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quiz Attempts</h5>
                        <h3>{{ $stats['total_quiz_attempts'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Spent</h5>
                        <h3>${{ number_format($stats['total_spent'] ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Recent Activity</h3>
            @if(isset($recentActivity) && $recentActivity->count() > 0)
                @foreach($recentActivity as $activity)
                    <div class="alert alert-info">
                        <strong>{{ $activity['title'] ?? 'Activity' }}</strong><br>
                        {{ $activity['description'] ?? 'No description' }}
                    </div>
                @endforeach
            @else
                <p>No recent activity</p>
            @endif
        </div>
    </div>
</body>
</html>
