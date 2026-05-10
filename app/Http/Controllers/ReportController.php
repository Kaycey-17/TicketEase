<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use App\Models\Requester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $categoryId = $request->input('category_id');

        // Build base query
        $query = Ticket::whereBetween('tickets.created_at', [$startDate, $endDate]);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Get all categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        // Calculate report data
        $reportData = $this->calculateReportData($query, $startDate, $endDate);

        // Get agent performance
        $agentPerformance = $this->getAgentPerformance($startDate, $endDate, $categoryId);

        // Get top requesters
        $topRequesters = $this->getTopRequesters($startDate, $endDate, $categoryId);

        // Get detailed tickets list
        $tickets = (clone $query)
            ->with(['requester', 'category', 'assignedUser'])
            ->orderBy('tickets.created_at', 'desc')
            ->paginate(20);

        return view('reports.index', compact(
            'categories',
            'reportData',
            'agentPerformance',
            'topRequesters',
            'tickets'
        ));
    }

    private function calculateReportData($query, $startDate, $endDate)
    {
        $tickets = clone $query;
        $totalTickets = $tickets->count();
        $resolvedTickets = (clone $query)->whereIn('status', ['resolved', 'closed'])->count();
        
        $resolutionRate = $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0;

        // Calculate average response time (time to first reply)
        // Using subquery to avoid ambiguity
        $avgResponseTime = DB::table('tickets')
            ->join('ticket_replies', 'tickets.id', '=', 'ticket_replies.ticket_id')
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->whereRaw('ticket_replies.created_at = (SELECT MIN(created_at) FROM ticket_replies WHERE ticket_id = tickets.id)')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, ticket_replies.created_at)) as avg_time')
            ->value('avg_time');

        // Calculate average resolution time
        $avgResolutionTime = DB::table('tickets')
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, tickets.resolved_at)) as avg_time')
            ->value('avg_time');

        return [
            'total_tickets' => $totalTickets,
            'resolved_tickets' => $resolvedTickets,
            'resolution_rate' => $resolutionRate,
            'avg_response_time' => round($avgResponseTime ?? 0, 1),
            'avg_resolution_time' => round($avgResolutionTime ?? 0, 1),
        ];
    }

    private function getAgentPerformance($startDate, $endDate, $categoryId)
    {
        $agents = User::whereIn('role', ['support_agent', 'supervisor', 'admin'])->get();

        return $agents->map(function ($user) use ($startDate, $endDate, $categoryId) {
            $assignedQuery = Ticket::where('assigned_user_id', $user->id)
                ->whereBetween('tickets.created_at', [$startDate, $endDate]);
            
            if ($categoryId) {
                $assignedQuery->where('category_id', $categoryId);
            }

            $assignedCount = $assignedQuery->count();
            $resolvedCount = (clone $assignedQuery)->whereIn('status', ['resolved', 'closed'])->count();
            $resolutionRate = $assignedCount > 0 ? round(($resolvedCount / $assignedCount) * 100, 1) : 0;

            // Calculate average response time for this agent
            $avgResponseTime = DB::table('tickets')
                ->join('ticket_replies', 'tickets.id', '=', 'ticket_replies.ticket_id')
                ->where('tickets.assigned_user_id', $user->id)
                ->where('ticket_replies.user_id', $user->id)
                ->whereBetween('tickets.created_at', [$startDate, $endDate])
                ->whereRaw('ticket_replies.created_at = (SELECT MIN(created_at) FROM ticket_replies WHERE ticket_id = tickets.id AND user_id = ?)', [$user->id])
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, ticket_replies.created_at)) as avg_time')
                ->value('avg_time');

            // Calculate average resolution time for this agent
            $avgResolutionTime = DB::table('tickets')
                ->where('assigned_user_id', $user->id)
                ->whereBetween('tickets.created_at', [$startDate, $endDate])
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, tickets.resolved_at)) as avg_time')
                ->value('avg_time');

            return (object) [
                'name' => $user->name,
                'assigned_count' => $assignedCount,
                'resolved_count' => $resolvedCount,
                'resolution_rate' => $resolutionRate,
                'avg_response_time' => round($avgResponseTime ?? 0, 1),
                'avg_resolution_time' => round($avgResolutionTime ?? 0, 1),
            ];
        })->filter(function ($agent) {
            return $agent->assigned_count > 0;
        });
    }

    private function getTopRequesters($startDate, $endDate, $categoryId)
    {
        // ✅ Use withCount instead of JOIN + GROUP BY to avoid MySQL strict mode issues
        $query = Requester::withCount([
            'tickets as total_tickets' => function ($q) use ($startDate, $endDate, $categoryId) {
                $q->whereBetween('tickets.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate   . ' 23:59:59',
                ]);
                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
            },
            'tickets as open_tickets' => function ($q) use ($startDate, $endDate, $categoryId) {
                $q->whereBetween('tickets.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate   . ' 23:59:59',
                ])->where('status', 'open');
                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
            },
            'tickets as resolved_tickets' => function ($q) use ($startDate, $endDate, $categoryId) {
                $q->whereBetween('tickets.created_at', [
                    $startDate . ' 00:00:00',
                    $endDate   . ' 23:59:59',
                ])->whereIn('status', ['resolved', 'closed']);
                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
            },
        ])
        ->having('total_tickets', '>', 0)
        ->orderByDesc('total_tickets')
        ->take(10)
        ->get();

        return $query->map(function ($requester) {
            return (object) [
                'full_name'        => $requester->full_name,
                'email'            => $requester->email,
                'total_tickets'    => $requester->total_tickets,
                'open_tickets'     => $requester->open_tickets,
                'resolved_tickets' => $requester->resolved_tickets,
            ];
        });
    }
    
    public function export(Request $request)
    {
        // Get filter parameters
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $categoryId = $request->input('category_id');

        // Build query
        $query = Ticket::with(['requester', 'category', 'assignedUser'])
            ->whereBetween('tickets.created_at', [$startDate, $endDate]);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $tickets = $query->get();

        // Generate CSV
        $filename = 'tickets_report_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Ticket ID',
                'Subject',
                'Requester',
                'Category',
                'Status',
                'Priority',
                'Assigned To',
                'Created At',
                'Resolved At',
                'Response Time (hours)',
                'Resolution Time (hours)'
            ]);

            // Add data rows
            foreach ($tickets as $ticket) {
                $responseTime = $ticket->replies->first() 
                    ? round($ticket->created_at->diffInHours($ticket->replies->first()->created_at), 1)
                    : '-';
                
                $resolutionTime = $ticket->resolved_at
                    ? round($ticket->created_at->diffInHours($ticket->resolved_at), 1)
                    : '-';

                fputcsv($file, [
                    $ticket->id,
                    $ticket->subject,
                    $ticket->requester->full_name,
                    $ticket->category->name,
                    ucfirst(str_replace('_', ' ', $ticket->status)),
                    ucfirst($ticket->priority),
                    $ticket->assignedUser->name ?? 'Unassigned',
                    $ticket->created_at->format('Y-m-d H:i:s'),
                    $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i:s') : '-',
                    $responseTime,
                    $resolutionTime
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function exportPdf(Request $request)
    {
        $startDate  = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate    = $request->get('end_date', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id');

        // Tickets
        $query = Ticket::with(['requester', 'category', 'assignedUser'])
            ->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59',
            ]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        // Summary stats
        $totalTickets    = $tickets->count();
        $resolvedTickets = $tickets->whereIn('status', ['resolved', 'closed'])->count();
        $openTickets     = $tickets->where('status', 'open')->count();
        $inProgress      = $tickets->where('status', 'in_progress')->count();
        $pending         = $tickets->where('status', 'pending')->count();
        $resolutionRate  = $totalTickets > 0
            ? round(($resolvedTickets / $totalTickets) * 100, 1)
            : 0;

        // Agent performance
        $agentPerformance = User::whereIn('role', ['admin', 'supervisor', 'support_agent'])
            ->withCount([
                'assignedTickets as assigned_count' => function ($q) use ($startDate, $endDate, $categoryId) {
                    $q->whereBetween('tickets.created_at', [
                        $startDate . ' 00:00:00',
                        $endDate   . ' 23:59:59',
                    ]);
                    if ($categoryId) $q->where('category_id', $categoryId);
                },
                'assignedTickets as resolved_count' => function ($q) use ($startDate, $endDate, $categoryId) {
                    $q->whereBetween('tickets.created_at', [
                        $startDate . ' 00:00:00',
                        $endDate   . ' 23:59:59',
                    ])->whereIn('status', ['resolved', 'closed']);
                    if ($categoryId) $q->where('category_id', $categoryId);
                },
            ])
            ->having('assigned_count', '>', 0)
            ->orderByDesc('assigned_count')
            ->get()
            ->map(function ($agent) {
                $agent->resolution_rate = $agent->assigned_count > 0
                    ? round(($agent->resolved_count / $agent->assigned_count) * 100, 1)
                    : 0;
                return $agent;
            });

        // Top requesters
        $topRequesters = $this->getTopRequesters($startDate, $endDate, $categoryId);

        // Category label
        $categoryName = $categoryId
            ? Category::find($categoryId)?->name ?? 'All Categories'
            : 'All Categories';

        $pdf = Pdf::loadView('reports.pdf', compact(
            'tickets',
            'startDate',
            'endDate',
            'categoryName',
            'totalTickets',
            'resolvedTickets',
            'openTickets',
            'inProgress',
            'pending',
            'resolutionRate',
            'agentPerformance',
            'topRequesters'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("ticketease-report-{$startDate}-to-{$endDate}.pdf");
    }
}