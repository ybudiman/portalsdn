<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class TicketingController extends Controller
{
    public function index()
    {
        $tickets = Ticketing::paginate(20);

        // Chart data compatible with SQL Server
        $chartData = [
            'environmentCount' => Ticketing::select('environment', DB::raw('COUNT(*) AS total'))
                ->where('status', '!=', 'Closed')
                ->groupBy('environment')
                ->pluck('total', 'environment'),

            'productCount' => Ticketing::select('product', DB::raw('COUNT(*) AS total'))
                ->where('status', '!=', 'Closed')
                ->groupBy('product')
                ->pluck('total', 'product'),

            'priorityCount' => Ticketing::select('priority', DB::raw('COUNT(*) AS total'))
                ->where('status', '!=', 'Closed')
                ->groupBy('priority')
                ->pluck('total', 'priority'),

            'typeCount' => Ticketing::select('type', DB::raw('COUNT(*) AS total'))
                ->where('status', '!=', 'Closed')
                ->groupBy('type')
                ->pluck('total', 'type'),

            'statusCount' => Ticketing::select('status', DB::raw('COUNT(*) AS total'))
                ->where('status', '!=', 'Closed')
                ->groupBy('status')
                ->pluck('total', 'status'),

            'categoryCount' => Ticketing::select('epic', DB::raw('COUNT(*) AS total'))
                ->where('status', '!=', 'Closed')
                ->groupBy('epic')
                ->pluck('total', 'epic'),

            'monthlyCount' => Ticketing::select(
                DB::raw("FORMAT(TRY_CONVERT(datetime, created_at), 'yyyy-MM') AS monthYear"),
                DB::raw('COUNT(*) AS total')
            )
                ->groupBy(DB::raw("FORMAT(TRY_CONVERT(datetime, created_at), 'yyyy-MM')"))
                ->orderBy(DB::raw("FORMAT(TRY_CONVERT(datetime, created_at), 'yyyy-MM')"))
                ->pluck('total', 'monthYear'),
        ];

        return view('ticketing.index', compact('tickets', 'chartData'));
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        foreach ($rows as $index => $row) {
            if ($index === 1) continue;

            Ticketing::create([
                'project'        => $row['A'] ?? null,
                'epic'           => $row['B'] ?? null,
                'code'           => $row['C'] ?? null,
                'ref_code'       => $row['D'] ?? null,
                'name'           => $row['E'] ?? null,
                'content'        => $row['F'] ?? null,
                'product'        => $row['G'] ?? null,
                'environment'    => $row['H'] ?? null,
                'owner'          => $row['I'] ?? null,
                'pic_dev'        => $row['J'] ?? null,
                'pic_QA'         => $row['K'] ?? null,
                'pic_SIT'        => $row['L'] ?? null,
                'pic_UAT'        => $row['M'] ?? null,
                'status'         => $row['N'] ?? null,
                'type'           => $row['O'] ?? null,
                'priority'       => $row['P'] ?? null,
                'created_at'     => isset($row['Q']) 
                                    ? (is_numeric($row['Q'])
                                        ? Date::excelToDateTimeObject($row['Q'])
                                        : date('Y-m-d H:i:s', strtotime($row['Q'])))
                                    : now(),
                'inprogress_at'  => isset($row['R']) ? date('Y-m-d H:i:s', strtotime($row['R'])) : null,
                'resolved_at'    => isset($row['S']) ? date('Y-m-d H:i:s', strtotime($row['S'])) : null,
                'closed_at'      => isset($row['T']) ? date('Y-m-d H:i:s', strtotime($row['T'])) : null,
                'related_tickets'=> $row['U'] ?? null,
            ]);
        }

        return redirect()->route('ticketing.index')->with('success', 'Excel imported successfully');
    }
}
