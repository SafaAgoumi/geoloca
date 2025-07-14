<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Entreprises</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Export des Entreprises</h1>
        <p>Généré le {{ date('d/m/Y à H:i') }}</p>
        <p>Nombre d'entreprises: {{ $count }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                @foreach($fields as $field)
                    <th>{{ $headers[$field] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($exportData as $row)
                <tr>
                    @foreach($fields as $field)
                        <td>{{ $row[$field] ?? '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>