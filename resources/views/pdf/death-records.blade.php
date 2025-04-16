<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Death Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin-bottom: 5px;
        }
        .header p {
            margin-top: 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .type-primary {
            background-color: #ffcccc;
        }
        .type-dependent {
            background-color: #fff2cc;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Death Records Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Record Type</th>
                <th>Name</th>
                <th>No Ahli</th>
                <th>IC Number</th>
                <th>Date of Death</th>
                <th>Place of Death</th>
                <th>Cause of Death</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            @php
            $recordType = $record->deceased_type === 'App\Models\User' ? 'Primary Member' : 'Dependent';
            $rowClass = $record->deceased_type === 'App\Models\User' ? 'type-primary' : 'type-dependent';

            $name = '';
            $noAhli = 'N/A';
            $icNumber = 'N/A';

            // Handle Primary Member (User)
            if ($record->deceased_type === 'App\Models\User') {
                $user = \App\Models\User::find($record->deceased_id);
                if ($user) {
                    $name = $user->name ?? 'Unknown Member';
                    $noAhli = $user->No_Ahli ?? 'N/A';
                    $icNumber = $user->ic_number ?? 'N/A';
                }
            }
            // Handle Dependent
            else {
                $dependent = \App\Models\Dependent::find($record->deceased_id);
                if ($dependent) {
                    $name = $dependent->full_name ?? 'Unknown Dependent';
                    $icNumber = $dependent->ic_number ?? 'N/A';

                    if ($dependent->user) {
                        $noAhli = $dependent->user->No_Ahli ?? 'N/A';
                    }
                }
            }
        @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $recordType }}</td>
                    <td>{{ $name }}</td>
                    <td>{{ $noAhli }}</td>
                    <td>{{ $icNumber }}</td>
                    <td>{{ $record->date_of_death ? $record->date_of_death->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $record->place_of_death ?? 'N/A' }}</td>
                    <td>{{ $record->cause_of_death ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Confidential - For internal use only</p>
    </div>
</body>
</html>
