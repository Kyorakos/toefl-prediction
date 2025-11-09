<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Batch;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResultsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamSession::with(['user', 'batch'])
                           ->where('status', 'completed');
        
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('batch_id') && $request->batch_id) {
            $query->where('batch_id', $request->batch_id);
        }
        
        if ($request->has('min_score') && $request->min_score) {
            $query->where('total_score', '>=', $request->min_score);
        }
        
        if ($request->has('max_score') && $request->max_score) {
            $query->where('total_score', '<=', $request->max_score);
        }
        
        $results = $query->orderBy('completed_at', 'desc')->paginate(15);
        $batches = Batch::all();
        
        return view('admin.results.index', compact('results', 'batches'));
    }
    
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        $query = ExamSession::with(['user', 'batch'])
                           ->where('status', 'completed');
        
        if ($request->has('batch_id') && $request->batch_id) {
            $query->where('batch_id', $request->batch_id);
        }
        
        $results = $query->orderBy('completed_at', 'desc')->get();
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.results.pdf', compact('results'));
            return $pdf->download('toefl-results.pdf');
        }
        
        return Excel::download(new ResultsExport($results), 'toefl-results.xlsx');
    }
}
