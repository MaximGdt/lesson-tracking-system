<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.daily_schedule_reminder') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .schedule-table th, .schedule-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .schedule-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('app.daily_schedule_reminder') }}</h1>
        </div>
        
        <div class="content">
            <p>{{ __('app.hello') }}, {{ $teacher->first_name }}!</p>
            
            @if($schedules->isEmpty())
                <p>{{ __('app.no_lessons_today') }}</p>
            @else
                <p>{{ __('app.your_lessons_today') }}:</p>
                
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>{{ __('app.time') }}</th>
                            <th>{{ __('app.group') }}</th>
                            <th>{{ __('app.subject') }}</th>
                            <th>{{ __('app.room') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->time_range }}</td>
                                <td>{{ $schedule->group->code }}</td>
                                <td>{{ $schedule->subject }}</td>
                                <td>{{ $schedule->room ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        
        <div class="footer">
            <p>{{ __('app.automatic_notification') }}</p>
        </div>
    </div>
</body>
</html>