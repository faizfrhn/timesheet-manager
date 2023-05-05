<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Issuance Notification</title>
</head>
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
    }
    
    .table th,
    .table td {
        padding: 12px 15px;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #ddd;
        font-size: 14px;
    }
    
    .table th {
        background-color: #f5f5f5;
        font-weight: bold;
    }
    
    .table td.total {
        font-weight: bold;
    }
</style>
<body>
    <p>Timesheet has been approved for the following talent: {{ $user->name }}</p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Date Worked</th>
                <th>Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timesheets as $timesheet)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $timesheet->date_worked }}</td>
                    <td>{{ $timesheet->hours }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="total">Total Hours</td>
                <td class="total">{{ $timesheets->sum('hours') }}</td>
            </tr>
        </tbody>
    </table>

    <p>Thank you.</p>
</body>
</html>