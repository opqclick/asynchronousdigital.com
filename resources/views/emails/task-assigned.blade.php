<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Assigned</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 32px 24px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px 24px;
            color: #333333;
            line-height: 1.6;
        }
        .detail-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 16px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .detail-item {
            margin: 8px 0;
            font-size: 15px;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            min-width: 110px;
        }
        .cta {
            text-align: center;
            margin-top: 24px;
        }
        .cta a {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 24px;
            text-align: center;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>You have been assigned a task</h1>
    </div>

    <div class="email-body">
        <p>Hello {{ $assignee->name }},</p>
        <p>A new task has been assigned to you by <strong>{{ $assignedBy->name }}</strong>.</p>

        <div class="detail-box">
            <div class="detail-item"><span class="detail-label">Task:</span> {{ $task->title }}</div>
            <div class="detail-item"><span class="detail-label">Project:</span> {{ $projectName ?? 'N/A' }}</div>
            <div class="detail-item"><span class="detail-label">Priority:</span> {{ ucfirst($task->priority) }}</div>
            <div class="detail-item"><span class="detail-label">Status:</span> {{ str_replace('_', ' ', ucfirst($task->status)) }}</div>
            <div class="detail-item"><span class="detail-label">Due Date:</span> {{ $task->due_date ? $task->due_date->format('M d, Y') : 'Not set' }}</div>
        </div>

        @if(!empty($task->description))
            <p><strong>Description:</strong> {{ $task->description }}</p>
        @endif

        <div class="cta">
            <a href="{{ $dashboardUrl }}">Open Dashboard</a>
        </div>
    </div>

    <div class="email-footer">
        <p><strong>Asynchronous Digital</strong></p>
        <p>This is an automated message, please do not reply.</p>
    </div>
</div>
</body>
</html>
