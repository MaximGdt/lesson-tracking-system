<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Занятие отмечено как проведенное</title>
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
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-block {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Занятие отмечено как проведенное</h1>
        </div>
        
        <div class="content">
            <p>Уважаемый администратор,</p>
            
            <p>Информируем вас о том, что занятие было отмечено как проведенное.</p>
            
            <div class="info-block">
                <div class="info-row">
                    <span class="label">Преподаватель:</span> {{ $teacherName }}
                </div>
                <div class="info-row">
                    <span class="label">Группа:</span> {{ $groupName }}
                </div>
                <div class="info-row">
                    <span class="label">Предмет:</span> {{ $subject }}
                </div>
                <div class="info-row">
                    <span class="label">Дата занятия:</span> {{ $date }}
                </div>
                <div class="info-row">
                    <span class="label">Время:</span> {{ $time }}
                </div>
                <div class="info-row">
                    <span class="label">Отмечено:</span> {{ $markedBy }}
                </div>
                <div class="info-row">
                    <span class="label">Время отметки:</span> {{ $markedAt }}
                </div>
            </div>
            
            <center>
                <a href="{{ url('/') }}" class="button">Перейти в систему</a>
            </center>
        </div>
        
        <div class="footer">
            <p>Это автоматическое уведомление от системы учета занятий.</p>
            <p>Пожалуйста, не отвечайте на это письмо.</p>
        </div>
    </div>
</body>
</html>