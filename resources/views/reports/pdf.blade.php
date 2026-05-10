<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>TicketEase Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
        }
        body {
            background: #ffffff;
            color: #1e293b;
            padding: 24px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #2563eb;
        }
        .header-left h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1e40af;
        }
        .header-left p {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .header-right {
            text-align: right;
        }
        .header-right p {
            font-size: 10px;
            color: #64748b;
            line-height: 1.6;
        }
        .header-right .badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Section title */
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Stats */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 20px;
        }
        .stats-table td {
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            width: 16.66%;
        }
        .stat-number {
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
        }
        .stat-label {
            font-size: 9px;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-blue   { background: #eff6ff; color: #2563eb; }
        .stat-yellow { background: #fffbeb; color: #d97706; }
        .stat-purple { background: #f5f3ff; color: #7c3aed; }
        .stat-orange { background: #fff7ed; color: #ea580c; }
        .stat-green  { background: #ecfdf5; color: #059669; }
        .stat-gray   { background: #f8fafc; color: #475569; }

        /* Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.data-table thead tr {
            background: #1e40af;
            color: #ffffff;
        }
        table.data-table thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        table.data-table tbody tr:nth-child(even) { background: #f8fafc; }
        table.data-table tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data-table tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
        }
        .status-open        { background: #dbeafe; color: #1d4ed8; }
        .status-in_progress { background: #ede9fe; color: #6d28d9; }
        .status-pending     { background: #fef3c7; color: #92400e; }
        .status-resolved    { background: #d1fae5; color: #065f46; }
        .status-closed      { background: #f1f5f9; color: #475569; }
        .priority-low       { background: #f1f5f9; color: #475569; }
        .priority-medium    { background: #dbeafe; color: #1d4ed8; }
        .priority-high      { background: #fef3c7; color: #92400e; }
        .priority-critical  { background: #fee2e2; color: #991b1b; }

        /* Progress bar */
        .progress-wrap {
            background: #e2e8f0;
            border-radius: 3px;
            height: 6px;
            width: 80px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 4px;
        }
        .progress-bar {
            background: #2563eb;
            border-radius: 3px;
            height: 6px;
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <h1>TicketEase &mdash; Report</h1>
            <p>Helpdesk &amp; Ticketing System</p>
        </div>
        <div class="header-right">
            <p><strong>Date Range:</strong>
                {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}
                to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </p>
            <p><strong>Category:</strong> {{ $categoryName }}</p>
            <p><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</p>
            <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                TicketEase Report
            </span>
        </div>
    </div>

    {{-- Summary Stats --}}
    <p class="section-title">Summary</p>
    <table class="stats-table">
        <tr>
            <td class="stat-blue">
                <div class="stat-number">{{ $totalTickets }}</div>
                <div class="stat-label">Total</div>
            </td>
            <td class="stat-yellow">
                <div class="stat-number">{{ $openTickets }}</div>
                <div class="stat-label">Open</div>
            </td>
            <td class="stat-purple">
                <div class="stat-number">{{ $inProgress }}</div>
                <div class="stat-label">In Progress</div>
            </td>
            <td class="stat-orange">
                <div class="stat-number">{{ $pending }}</div>
                <div class="stat-label">Pending</div>
            </td>
            <td class="stat-green">
                <div class="stat-number">{{ $resolvedTickets }}</div>
                <div class="stat-label">Resolved</div>
            </td>
            <td class="stat-gray">
                <div class="stat-number">{{ $resolutionRate }}%</div>
                <div class="stat-label">Resolution Rate</div>
            </td>
        </tr>
    </table>

    {{-- Agent Performance --}}
    @if($agentPerformance->count() > 0)
        <p class="section-title">Agent Performance</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Agent Name</th>
                    <th>Assigned</th>
                    <th>Resolved</th>
                    <th>Resolution Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agentPerformance as $agent)
                    <tr>
                        <td><strong>{{ $agent->name }}</strong></td>
                        <td>{{ $agent->assigned_count }}</td>
                        <td>{{ $agent->resolved_count }}</td>
                        <td>
                            <div class="progress-wrap">
                                <div class="progress-bar" style="width: {{ $agent->resolution_rate }}%;"></div>
                            </div>
                            {{ $agent->resolution_rate }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Top Requesters --}}
    @if(count($topRequesters) > 0)
        <p class="section-title">Top Requesters</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Requester</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Open</th>
                    <th>Resolved</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topRequesters as $requester)
                    <tr>
                        <td><strong>{{ $requester->full_name }}</strong></td>
                        <td>{{ $requester->email }}</td>
                        <td>{{ $requester->total_tickets }}</td>
                        <td>{{ $requester->open_tickets }}</td>
                        <td>{{ $requester->resolved_tickets }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Ticket List --}}
    <div class="page-break"></div>
    <p class="section-title" style="margin-top:0;">
        Detailed Ticket List ({{ $tickets->count() }} tickets)
    </p>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Requester</th>
                <th>Category</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assigned To</th>
                <th>Created</th>
                <th>Resolved</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td style="font-family:monospace; color:#94a3b8;">#{{ $ticket->id }}</td>
                    <td>{{ \Str::limit($ticket->subject, 35) }}</td>
                    <td>{{ $ticket->requester->full_name }}</td>
                    <td>{{ $ticket->category->name }}</td>
                    <td>
                        <span class="badge status-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge priority-{{ $ticket->priority }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td>{{ $ticket->assignedUser?->name ?? '—' }}</td>
                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                    <td>{{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; color:#94a3b8; padding:20px;">
                        No tickets found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>TicketEase Helpdesk System &mdash; Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        <p>This report is confidential and intended for authorized personnel only.</p>
    </div>

</body>
</html>