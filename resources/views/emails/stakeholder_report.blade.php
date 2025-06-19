<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stakeholder Report</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f6f6; color: #333; }
        .container { background: #fff; margin: 30px auto; padding: 30px; border-radius: 8px; max-width: 600px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);}
        h1 { color: #4CAF50; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #888; }
        .report-section { margin-bottom: 20px; }
        a.button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 16px;
            background: #4CAF50;
            color: #fff !important;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 12px;}
        th, td { border: 1px solid #eee; padding: 8px; text-align: left;}
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Stakeholder Report</h1>
        <p>Hello {{ $stakeholder->name }},</p>

        <div class="report-section">
            <h2>{{ $report->title ?? 'Report' }}</h2>
            <p><strong>Date:</strong> {{ $report->created_at ? $report->created_at->toFormattedDateString() : \Carbon\Carbon::now()->toFormattedDateString() }}</p>
            @if(!empty($report->summary))
                <p><strong>Summary:</strong> {{ $report->summary }}</p>
            @endif
            @if(!empty($report->details))
                <div>
                    {!! nl2br(e($report->details)) !!}
                </div>
            @endif
        </div>

        @if(isset($report->data) && is_array($report->data) && count($report->data))
            <div class="report-section">
                <h3>Key Data</h3>
                <table>
                    <thead>
                        <tr>
                            @foreach(array_keys($report->data[0]) as $header)
                                <th>{{ ucwords(str_replace('_', ' ', $header)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->data as $row)
                            <tr>
                                @foreach($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p>
            <a href="{{ url('/') }}" class="button">View More in Dashboard</a>
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Coffee SCMS') }}. All rights reserved.<br>
            <a href="{{ url('/') }}">{{ url('/') }}</a>
        </div>
    </div>
</body>
</html>