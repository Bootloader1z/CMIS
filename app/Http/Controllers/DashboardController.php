<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TasFile;
use App\Models\TasFileHistory;
use App\Models\admitted;
use App\Models\ApprehendingOfficer;
use App\Models\TrafficViolation;
use App\Models\department;
use App\Models\G5ChatMessage;
use App\Models\archives;
use DateTime;


class DashboardController extends Controller
{
    public function indexa(Request $request){

        if ($request->ajax()) {
            // Fetch paginated officers for AJAX request
            $officers = TasFile::leftJoin('apprehending_officers', 'tas_files.apprehending_officer', '=', 'apprehending_officers.officer')
                ->select('tas_files.apprehending_officer', 'apprehending_officers.department')
                ->selectRaw('COUNT(tas_files.apprehending_officer) as total_cases')
                ->selectRaw('GROUP_CONCAT(tas_files.case_no) as case_numbers')
                ->groupBy('tas_files.apprehending_officer', 'apprehending_officers.department')
                ->orderByDesc('total_cases')
                ->paginate(10); // Paginate with 10 officers per page

            // Return a partial view for the AJAX request
            return view('officers.table', compact('officers'))->render();
        }
            $revenueThisMonth = TasFile::whereMonth('date_received', date('m'))->count();

            $previousMonthRevenue = TasFile::whereMonth('date_received', Carbon::now()->subMonth())->count();

            // // Calculate the percentage change
            // $percentageChange = $previousMonthRevenue > 0 ? (($revenueThisMonth - $previousMonthRevenue) / $previousMonthRevenue) * 100 : 0;

            // $percentageChange = $previousYearCustomers > 0 ? (($customersThisYear - $previousYearCustomers) / $previousYearCustomers) * 100 : 0;
            $recentActivity = TasFile::whereDate('created_at', today())->latest()->take(5)->get();
            $customersThisYear = TasFile::whereYear('date_received', now())->count();
            $recentSalesToday = TasFile::whereDate('created_at', today())->latest()->take(5)->get();
            $averageSalesLastWeek = TasFile::whereBetween('created_at', [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->subDays(1)->endOfDay()])->count() / 7;
            $admittedData = Admitted::all();
            $tasFileData = TasFile::all();
            $chartData = $admittedData->map(function ($item) {
            $violationCount = 0;
            if ($item->violation) {
                $violations = json_decode($item->violation);
                $violationCount = is_array($violations) ? count($violations) : 0;
            }
            return [
                'name' => $item->name,
                'violation_count' => $violationCount,
                'transaction_date' => $item->transaction_date,
            ];
        });
        $departmentsData = ApprehendingOfficer::all();
        $unreadMessageCount = G5ChatMessage::where('is_read', false)->count();
        $messages = G5ChatMessage::latest()->with('user')->limit(10)->get();
        $user = Auth::user();
        $name = $user->name;
        $department = $user->department;
        $allMonths = collect(range(1, 12))->map(function ($month) {
            return ['month' => $month, 'record_count' => 0];
        });
        $countByMonth = TasFile::select(
                DB::raw('MONTH(date_received) as month'),
                DB::raw('COUNT(*) as record_count')
            )
            ->groupBy(DB::raw('MONTH(date_received)'))
            ->get()
            ->keyBy('month');

        $countByMonth = $allMonths->map(function ($month) use ($countByMonth) {
            return $countByMonth->has($month['month']) ? $countByMonth[$month['month']] : $month;
        });

        $countByMonth = $countByMonth->sortBy('month')->values();
        $yearlyData = TasFile::select(
        DB::raw('IFNULL(YEAR(date_received), "Unknown") as year'),
        DB::raw('COUNT(*) as record_count')
        )
        ->groupBy(DB::raw('IFNULL(YEAR(date_received), "Unknown")'))
        ->get()
        ->keyBy('year');
        // Get today's date
        $today = Carbon::now()->format('Y-m-d');

        // Fetch the data created on today's date
        $salesToday = TasFile::whereDate('created_at', $today)->get();
        $officers = TasFile::leftJoin('apprehending_officers', 'tas_files.apprehending_officer', '=', 'apprehending_officers.officer')
        ->select('tas_files.apprehending_officer', 'apprehending_officers.department')
        ->selectRaw('COUNT(tas_files.apprehending_officer) as total_cases')
        ->selectRaw('GROUP_CONCAT(tas_files.case_no) as case_numbers')
        ->groupBy('tas_files.apprehending_officer', 'apprehending_officers.department')
        ->orderByDesc('total_cases')
        ->paginate(10); // Paginate with 10 officers per page


        return view('index', compact('officers','yearlyData','countByMonth','unreadMessageCount','messages', 'name', 'department','departmentsData','tasFileData','admittedData','chartData','recentActivity', 'recentSalesToday', 'salesToday', 'revenueThisMonth', 'customersThisYear', 'averageSalesLastWeek'));
       // return view('index', compact('recentActivity', 'recentSalesToday', 'salesToday', 'revenueThisMonth', 'customersThisYear', 'averageSalesLastWeek','previousYearCustomers', 'previousMonthRevenue', 'percentageChange'));
    }

    public function tableofficerdash(Request $request)
    {
        if ($request->ajax()) {
            // Fetch paginated officers for AJAX request
            $officers = TasFile::leftJoin('apprehending_officers', 'tas_files.apprehending_officer', '=', 'apprehending_officers.officer')
                ->select('tas_files.apprehending_officer', 'apprehending_officers.department')
                ->selectRaw('COUNT(tas_files.apprehending_officer) as total_cases')
                ->selectRaw('GROUP_CONCAT(tas_files.case_no) as case_numbers')
                ->groupBy('tas_files.apprehending_officer', 'apprehending_officers.department')
                ->orderByDesc('total_cases')
                ->paginate(10); // Paginate with 10 officers per page

            // Return a partial view for the AJAX request
            return view('table', compact('officers'))->render();
        }

        // For normal HTTP requests, return the full view
        $officers = TasFile::leftJoin('apprehending_officers', 'tas_files.apprehending_officer', '=', 'apprehending_officers.officer')
            ->select('tas_files.apprehending_officer', 'apprehending_officers.department')
            ->selectRaw('COUNT(tas_files.apprehending_officer) as total_cases')
            ->selectRaw('GROUP_CONCAT(tas_files.case_no) as case_numbers')
            ->groupBy('tas_files.apprehending_officer', 'apprehending_officers.department')
            ->orderByDesc('total_cases')
            ->paginate(10); // Paginate with 10 officers per page

        return view('index', compact('officers'));
    }

    public function editViolation(Request $request, $id){
        $violation = Violation::find($id);


        if (!$violation) {
            return redirect()->back()->with('error', 'Violation not found.');
        }
        $validatedData = $request->validate([
        ]);
        $violation->update($validatedData);
        return redirect()->back()->with('success', 'Violation updated successfully.');
    }
    public function getByDepartmentName($departmentName){
        $officers = ApprehendingOfficer::where('department', $departmentName)->get();
        return response()->json($officers);
    }
    public function tables(){
        return view('layout');
    }
    public function tasManage(){
        $officers = ApprehendingOfficer::select('officer', 'department')->get();
        // dd($recentViolationsToday[1]);
        $violations = TrafficViolation::orderBy('code', 'asc')->get();
        return view('tas.manage',compact('officers','violations'));
    }
    public function updateAdmittedCase(Request $request, $id){
        // Validate the request
        $validatedData = $request->validate([
            'editTop' => 'required|string',
            'editName' => 'required|string',
            'editViolation' => 'required|string',
            'editTransactionNo' => 'required|string',
            'editTransactionDate' => 'required|date',
            'editPlateNo' => 'required|string',
            'editContactNo' => 'required|string',
            'editRemarks' => 'nullable|string',
        ]);

        // Find the admitted case by id
        $admittedCase = AdmittedCase::findOrFail($id);

        // Update attributes
        $admittedCase->update([
            'top' => $request->input('editTop'),
            'name' => $request->input('editName'),
            'violation' => $request->input('editViolation'),
            'transaction_no' => $request->input('editTransactionNo'),
            'transaction_date' => $request->input('editTransactionDate'),
            'plate_no' => $request->input('editPlateNo'),
            'contact_no' => $request->input('editContactNo'),
            'remarks' => $request->input('editRemarks'),
            // Add other attributes if needed
        ]);

        // Redirect back or to a success page
        return redirect()->back()->with('success', 'Admitted case updated successfully');
    }
    public function caseIndex(){
        return view('case_archives');
    }
    public function reportsview(Request $request) {
        // Get the month parameter from the request, defaulting to the current month if not provided
        $selectedMonth = $request->input('date_received', Carbon::now()->format('Y-m'));
    
        // Determine the start and end dates of the selected month
        $startDate = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endDate = Carbon::parse($selectedMonth . '-01')->endOfMonth();
    
        // Query TasFiles with date range
        $tasFiles = TasFile::whereBetween('date_received', [$startDate, $endDate])->get();
    
        // Initialize total fine per violation and total fine for all data for the month
        $totalFinePerViolation = collect();
        $totalFineForMonth = 0;
    
        // Process each TasFile to attach related violations
        foreach ($tasFiles as $tasFile) {
            $violations = json_decode($tasFile->violation);
            $relatedViolations = collect(); // Initialize related violations collection
    
            if ($violations) {
                if (is_array($violations)) {
                    $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
                } else {
                    $relatedViolations = TrafficViolation::where('code', $violations)->get();
                }
            }
    
            // Calculating total partial fine per violation for each TasFile
            $totalFinePerFile = $relatedViolations->sum('fine');
            $totalFinePerViolation = $totalFinePerViolation->merge($relatedViolations->pluck('fine'));
    
            // Update the total fine for all data for the month
            $totalFineForMonth += $totalFinePerFile;
    
            $tasFile->relatedViolations = $relatedViolations;
            $tasFile->partialFinePerFile = $totalFinePerFile;
        }
    
        // Format monthYear based on the selected month
        $monthYear = strtoupper(Carbon::parse($selectedMonth)->format('F Y'));
        
        return view('sub.reports', [
            'tasFiles' => $tasFiles,
            'monthYear' => $monthYear,
            'totalFinePerViolation' => $totalFinePerViolation,
            'totalFineForMonth' => $totalFineForMonth
        ]);
    }
       
public function tasView()
{
    try {
        $pageSize = 15; // Define the default page size
        $tasFiles = TasFile::all()->sortByDesc('case_no');
        $officers = collect();
        
        foreach ($tasFiles as $tasFile) {
            // Update completeness symbols for each TasFile
            $tasFile->checkCompleteness();

            // Handle deletion case for each TasFile
            $tasFile->handleDeletion();

            $officerName = $tasFile->apprehending_officer;
            $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
            $officers = $officers->merge($officersForFile);
            $tasFile->relatedofficer = $officersForFile;
            
            if (is_string($tasFile->remarks)) {
                $remarks = json_decode($tasFile->remarks, true);
                if ($remarks === null) {
                    $remarks = [];
                }
            } else if (is_array($tasFile->remarks)) {
                $remarks = $tasFile->remarks;
            } else {
                $remarks = [];
            }
            $tasFile->remarks = $remarks;

            $violations = json_decode($tasFile->violation);
            if ($violations) {
                if (is_array($violations)) {
                    $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
                } else {
                    $relatedViolations = TrafficViolation::where('code', $violations)->get();
                }
            } else {
                $relatedViolations = [];
            }
            $tasFile->relatedViolations = $relatedViolations;
        }

        return view('tas.view', compact('tasFiles'));
    } catch (\Exception $e) {
        \Log::error('Error viewing TAS: ' . $e->getMessage());
        throw new \Exception('Error viewing TAS: ' . $e->getMessage());
    }
}
    
    // public function tasView(){
    //     $pageSize = 15; // Define the default page size
    //     $tasFiles = TasFile::all()->sortByDesc('case_no');
    //     $officers = collect();
        
    //     foreach ($tasFiles as $tasFile) {
    //         $officerName = $tasFile->apprehending_officer;
    //         $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
    //         $officers = $officers->merge($officersForFile);
    //         $tasFile->relatedofficer = $officersForFile;
            
    //         if (is_string($tasFile->remarks)) {
    //             $remarks = json_decode($tasFile->remarks, true);
    //             if ($remarks === null) {
    //                 $remarks = [];
    //             }
    //         } else if (is_array($tasFile->remarks)) {
    //             $remarks = $tasFile->remarks;
    //         } else {
    //             $remarks = [];
    //         }
    //         $tasFile->remarks = $remarks;

    //         $violations = json_decode($tasFile->violation);
    //         if ($violations) {
    //             if (is_array($violations)) {
    //                 $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
    //             } else {
    //                 $relatedViolations = TrafficViolation::where('code', $violations)->get();
    //             }
    //         } else {
    //             $relatedViolations = [];
    //         }
    //         $tasFile->relatedViolations = $relatedViolations;
    //     }

    //     return view('tas.view', compact('tasFiles'));
    // }
    public function admitmanage(){
        $officers = ApprehendingOfficer::select('officer', 'department')->get();
        // dd($recentViolationsToday[1]);
        $violations = TrafficViolation::orderBy('code', 'asc')->get();
        // return view('tas.manage',compact('officers','violations'));
        return view('admitted.manage', compact('officers','violations'));
    }
    public function admitview(){
        // Retrieve admitted data
        $admitted = Admitted::all()->sortByDesc('admittedno');

        foreach ($admitted as $admit) {
            $admit->checkCompleteness();
            $violations = json_decode($admit->violation);
            $officerName = $admit->apprehending_officer;
                $officer = ApprehendingOfficer::firstOrCreate(['officer' => $officerName]);
                $admit->relatedofficer = $officer;
            if ($violations) {
                $relatedViolations = TrafficViolation::whereIn('id', $violations)->get();
            } else {
                // If $violations is null, set $relatedViolations to an empty collection
                $relatedViolations = [];
            }

            $admit->relatedViolations = $relatedViolations;
        }

        $pageSize = 15; // Define the default page size
        $admitted = Admitted::all()->sortByDesc('admittedno');
        $officers = collect();
        
        foreach ($admitted as $admit) {
            $officerName = $admit->apprehending_officer;
            $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
            $officers = $officers->merge($officersForFile);
            $admit->relatedofficer = $officersForFile;
            
            if (is_string($admit->remarks)) {
                $remarks = json_decode($admit->remarks, true);
                if ($remarks === null) {
                    $remarks = [];
                }
            } else if (is_array($admit->remarks)) {
                $remarks = $admit->remarks;
            } else {
                $remarks = [];
            }
            $admit->remarks = $remarks;
    
            $violations = json_decode($admit->violation);
            if ($violations) {
                if (is_array($violations)) {
                    $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
                } else {
                    $relatedViolations = TrafficViolation::where('code', $violations)->get();
                }
            } else {
                $relatedViolations = [];
            }
            $admit->relatedViolations = $relatedViolations;
        }
    
        return view('admitted.view', compact('admitted'));
    }
    public function saveRemarks(Request $request) {
        $request->validate([
            'remarks' => 'required|string',
            'tas_file_id' => 'required|exists:tas_files,id',
        ]);
    
        try {
            $id = $request->input('tas_file_id');
            $remarks = $request->input('remarks');
            $tasFile = TasFile::findOrFail($id);
            $existingRemarks = json_decode($tasFile->remarks, true) ?? [];
            $timestamp = Carbon::now('Asia/Manila')->format('g:ia m/d/y');
            $newRemark = $remarks . ' - ' . $timestamp .' - '. Auth::user()->fullname;
            $existingRemarks[] = $newRemark;
            $updatedRemarksJson = json_encode($existingRemarks);
    
            DB::beginTransaction();
            $tasFile->update(['remarks' => $updatedRemarksJson]);
            DB::commit();
    
            // Send back a response with 201 Created status code
            // Here, we are also returning a success message in the response body
            return response()->json(['message' => 'Remarks saved successfully.'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            logger()->error('Error saving remarks: ' . $th->getMessage());
            return response()->json(['error' => $th->getMessage()], 500); // You can return a different error status code if needed
        }
    }
    //admitted remarks
    public function admitremark(Request $request){
        $request->validate([
            'remarks' => 'required|string',
            'admitted_dataid' => 'required|exists:admitteds,id',
        ]);
    
        try {
            $id = $request->input('admitted_dataid');
            $remarks = $request->input('remarks');
            $tasFile = admitted::findOrFail($id);
            $existingRemarks = json_decode($tasFile->remarks, true) ?? [];
            $timestamp = Carbon::now('Asia/Manila')->format('g:ia m/d/y');
            $newRemark = $remarks . ' - ' . $timestamp .' - '. Auth::user()->fullname;
            $existingRemarks[] = $newRemark;
            $updatedRemarksJson = json_encode($existingRemarks);
    
            DB::beginTransaction();
            $tasFile->update(['remarks' => $updatedRemarksJson]);
            DB::commit();
    
            // Send back a response with 201 Created status code
            // Here, we are also returning a success message in the response body
            return response()->json(['message' => 'Remarks saved successfully.'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            logger()->error('Error saving remarks: ' . $th->getMessage());
            return response()->json(['error' => $th->getMessage()], 500); // You can return a different error status code if needed
        }
    }
    public function submitForm(Request $request){
        try {
            $validatedData = $request->validate([
                'case_no' => 'required|string',
                'top' => 'nullable|string',
                'driver' => 'required|string',
                'apprehending_officer' => 'required|string',
                'violation' => 'required|string',
                'transaction_no' => 'nullable|string',
                'date_received' => 'required|date',
                'contact_no' => 'required|string',
                'plate_no' => 'required|string',
                'status' => 'required|string|in:closed,in-progress,settled,unsettled',
                'file_attachment' => 'nullable|array',
                'file_attachment.*' => 'nullable|file|max:512000',
                'typeofvehicle' => 'required|string', // Add validation for typeofvehicle
            ]);

            DB::beginTransaction();

            $existingTasFile = TasFile::where('case_no', $validatedData['case_no'])->first();

            if (!$existingTasFile) {
                $tasFile = new TasFile([
                    'case_no' => $validatedData['case_no'],
                    'top' => $validatedData['top'],
                    'driver' => $validatedData['driver'],
                    'apprehending_officer' => $validatedData['apprehending_officer'],
                    'violation' => json_encode(explode(', ', $validatedData['violation'])),
                    'transaction_no' => $validatedData['transaction_no'] ? "TRX-LETAS-" . $validatedData['transaction_no'] : null,
                    'plate_no' => $validatedData['plate_no'],
                    'date_received' => $validatedData['date_received'],
                    'contact_no' => $validatedData['contact_no'],
                    'status' => $validatedData['status'],
                    'typeofvehicle' => $validatedData['typeofvehicle'], // Add typeofvehicle field to be saved
                ]);

                if ($request->hasFile('file_attachment')) {
                    $filePaths = [];
                    $cx = 1;
                    foreach ($request->file('file_attachment') as $file) {
                        $x = "CS-".$validatedData['case_no'] . "_documents_" . $cx . "_";
                        $fileName = $x . time();
                        $file->storeAs('attachments', $fileName, 'public');
                        $filePaths[] = 'attachments/' . $fileName;
                        $cx++;
                    }
                    $tasFile->file_attach = json_encode($filePaths);
                }

                $tasFile->save();
            } else {
                return redirect()->back()->with('error', 'Case no. already exists.');
            }

            DB::commit();

            return redirect()->back()->with('success', 'Form submitted successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
 ///////////////////////////////////////////////////=============================================================================================================================       
 ///////////////////////////////////////////////////=============================================================================================================================    
 ///////////////////////////////////////////////////=============================================================================================================================    
 ///////////////////////////////////////////////////=============================================================================================================================    
 ///////////////////////////////////////////////////=============================================================================================================================    
 ///////////////////////////////////////////////////=============================================================================================================================    
 ///////////////////////////////////////////////////=============================================================================================================================   
 ///////////////////////////////////////////////////////=============================================================================================================================
    public function archivesmanage(){
        $officers = ApprehendingOfficer::select('officer', 'department')->get();
        // dd($recentViolationsToday[1]);
        $violations = TrafficViolation::orderBy('code', 'asc')->get();
        return view('case_archive.manage',compact('officers','violations'));
    }
    public function archivessubmit(Request $request){
        try {
            $validatedData = $request->validate([
                'tas_no' => 'required|string',
                'top' => 'nullable|string',
                'driver' => 'required|string',
                'apprehending_officer' => 'required|string',
                'violation' => 'required|string',
                'transaction_no' => 'nullable|string',
                'date_received' => 'required|date',
                'contact_no' => 'required|string',
                'plate_no' => 'required|string',
                'status' => 'required|string|in:closed,in-progress,settled,unsettled',
                'file_attachment' => 'nullable|array',
                'file_attachment.*' => 'nullable|file|max:512000',
                'typeofvehicle' => 'required|string', // Add validation for typeofvehicle
            ]);

            DB::beginTransaction();

            $existingarchive = archives::where('tas_no', $validatedData['tas_no'])->first();

            if (!$existingarchive) {
                $archive = new archives([
                    'tas_no' => $validatedData['tas_no'],
                    'top' => $validatedData['top'],
                    'driver' => $validatedData['driver'],
                    'apprehending_officer' => $validatedData['apprehending_officer'],
                    'violation' => json_encode(explode(', ', $validatedData['violation'])),
                    'transaction_no' => $validatedData['transaction_no'] ? "TRX-LETAS-" . $validatedData['transaction_no'] : null,
                    'plate_no' => $validatedData['plate_no'],
                    'date_received' => $validatedData['date_received'],
                    'contact_no' => $validatedData['contact_no'],
                    'status' => $validatedData['status'],
                    'typeofvehicle' => $validatedData['typeofvehicle'], // Add typeofvehicle field to be saved
                ]);

                if ($request->hasFile('file_attachment')) {
                    $filePaths = [];
                    $cx = 1;
                    foreach ($request->file('file_attachment') as $file) {
                        $x = "CS-".$validatedData['tas_no'] . "_documents_" . $cx . "_";
                        $fileName = $x . time();
                        $file->storeAs('attachments', $fileName, 'public');
                        $filePaths[] = 'attachments/' . $fileName;
                        $cx++;
                    }
                    $archive->file_attach = json_encode($filePaths);
                }

                $archive->save();
            } else {
                return redirect()->back()->with('error', 'tas no. already exists.');
            }

            DB::commit();

            return redirect()->back()->with('success', 'Form submitted successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function archivesview(){
        $pageSize = 15; // Define the default page size
        $archives = archives::all()->sortByDesc('case_no');
        $officers = collect();
        
        foreach ($archives as $archive) {
            $archive->checkCompleteness();
            $officerName = $archive->apprehending_officer;
            $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
            $officers = $officers->merge($officersForFile);
            $archive->relatedofficer = $officersForFile;
            
            if (is_string($archive->remarks)) {
                $remarks = json_decode($archive->remarks, true);
                if ($remarks === null) {
                    $remarks = [];
                }
            } else if (is_array($archive->remarks)) {
                $remarks = $archive->remarks;
            } else {
                $remarks = [];
            }
            $archive->remarks = $remarks;

            $violations = json_decode($archive->violation);
            if ($violations) {
                if (is_array($violations)) {
                    $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
                } else {
                    $relatedViolations = TrafficViolation::where('code', $violations)->get();
                }
            } else {
                $relatedViolations = [];
            }
            $archive->relatedViolations = $relatedViolations;
        }

        return view('case_archive.view', compact('archives'));
    }
    public function saveRemarksarchives(Request $request) {
        $request->validate([
            'remarks' => 'required|string',
            'archives_dataid' => 'required|exists:archives,id',
        ]);
    
        try {
            $id = $request->input('archives_dataid');
            $remarks = $request->input('remarks');
            $archives = archives::findOrFail($id);
            $existingRemarks = json_decode($archives->remarks, true) ?? [];
            $timestamp = Carbon::now('Asia/Manila')->format('g:ia m/d/y');
            $newRemark = $remarks . ' - ' . $timestamp .' - '. Auth::user()->fullname;
            $existingRemarks[] = $newRemark;
            $updatedRemarksJson = json_encode($existingRemarks);
    
            DB::beginTransaction();
            $archives->update(['remarks' => $updatedRemarksJson]);
            DB::commit();
    
            // Send back a response with 201 Created status code
            // Here, we are also returning a success message in the response body
            return response()->json(['message' => 'Remarks saved successfully.'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            logger()->error('Error saving remarks: ' . $th->getMessage());
            return response()->json(['error' => $th->getMessage()], 500); // You can return a different error status code if needed
        }
    }
    public function detailsarchives(Request $request, $id){
        try {
            // Find the archives by its ID or throw a ModelNotFoundException
            $archives = archives::findOrFail($id);

            // Retrieve related ApprehendingOfficers
            $relatedOfficers = ApprehendingOfficer::where('officer', $archives->apprehending_officer)->get();

            // Retrieve related TrafficViolations
            $violations = json_decode($archives->violation, true);
            $relatedViolations = [];
            if ($violations) {
                $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
            }
            
            $remarks = json_decode($archives->remarks);
            // Check if $remarks is an array
            if (is_array($remarks)) {
                $remarks = array_reverse($remarks);
            } else {
                // If $remarks is not an array, set it to an empty array
                $remarks = [];
            }
            // dd($remarks);
            // Return the view with archives and related data
            return view('case_archive.detailsview', compact('archives', 'relatedOfficers', 'relatedViolations', 'remarks'));

        } catch (ModelNotFoundException $e) {
            // Handle case where archives with $id is not found
            return response()->view('errors.404', [], 404);
        }
    }
    public function detailarchivesedit(Request $request, $id) {
        try {
            // Find the archives by its ID or throw a ModelNotFoundException
            $recentViolationsToday = archives::findOrFail($id);

            // Retrieve all Traffic Violations ordered by code ascending
            $violationz = TrafficViolation::orderBy('code', 'asc')->get();

            // Prepare a collection for officers
            $officers = collect();

            // Get the apprehending officer name from the archives
            $officerName = $recentViolationsToday->apprehending_officer;

            // Query the ApprehendingOfficer model for officers with the given name
            $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();

            // Merge the officers into the collection
            $officers = $officers->merge($officersForFile);

            // Decode remarks if they are JSON-encoded
            $remarks = json_decode($recentViolationsToday->remarks, true);
            
            // Check if $remarks is an array and reverse it if so
            if (is_array($remarks)) {
                $remarks = array_reverse($remarks);
            } else {
                // If $remarks is not an array or JSON decoding failed, set it to an empty array
                $remarks = [];
            }

            // Pass data to the view
            return view('case_archive.detailsedit', compact('recentViolationsToday', 'officers', 'violationz', 'remarks'));

        } catch (ModelNotFoundException $e) {
            // Handle the case where the archives with the specified ID is not found
            return response()->view('errors.404', [], 404);
        }
    }
    
    public function updatearchives(){
        // Fetch all traffic violations
        
        
        // Fetch recent TasFiles ordered by case number descending
        $recentViolationsToday = archives::orderBy('tas_no', 'desc')->get();
        
        // Fetch all codes (assuming TrafficViolation model provides codes)
        $violation = TrafficViolation::all();
        
        // Prepare a collection for officers
        $officers = collect();
       
        
        // Iterate through each TrafficViolation record
        foreach ($recentViolationsToday as $tasFile) {
            $officerName = $tasFile->apprehending_officer;
            $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
            $officers = $officers->merge($officersForFile);
            $tasFile->relatedofficer = $officersForFile;
            
        }
  
        // Pass data to the view, including the new variable $violationData
        return view('case_archive.edit', compact('recentViolationsToday', 'violation', 'officers'));
    }
    public function updateStatusarchives(Request $request, $id){
        try {
            // Log the request data for debugging
            \Log::info('Request data: ', $request->all());
    
            $archives = archives::findOrFail($id);
    
            // Log the received status for debugging
            \Log::info('Received status: ' . $request->status);
    
            $archives->status = $request->status;
            $archives->save();
    
            return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            // Log any errors for debugging
            \Log::error('Error updating status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
    public function updatedetailarchives(Request $request, $id) {
        try {
            // Find the violation by ID
            $violation = archives::findOrFail($id);
    
            // Validate the incoming request data
            $validatedData = $request->validate([
                'tas_no' => 'nullable|string|max:255',
                'top' => 'nullable|string|max:255',
                'driver' => 'nullable|string|max:255',
                'apprehending_officer' => 'nullable|string|max:255',
                'violation' => 'nullable|array',
                'transaction_no' => 'nullable|string|max:255',
                'date_received' => 'nullable|date',
                'plate_no' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'contact_no' => 'nullable|string|max:255',
                'remarks.*.text' => 'nullable|string',
                'file_attach_existing.*' => 'nullable|file|max:512000', // Added file validation rules
            ]);
    
            // Attach files
            if ($request->hasFile('file_attach_existing')) {
                // Retrieve existing file attachments and decode them
                $existingFilePaths = json_decode($violation->file_attach, true) ?? [];
    
                $cx = count($existingFilePaths) + 1;
                foreach ($request->file('file_attach_existing') as $file) {
                    // Check if the file was actually uploaded
                    if ($file->isValid()) {
                        $x = "CS-".$violation->tas_no . "_documents_" . $cx . "_";
                        $fileName = $x . time();
                        $file->storeAs('attachments', $fileName, 'public');
                        $existingFilePaths[] = 'attachments/' . $fileName; // Append the new file path
                        $cx++;
                    } else {
                        // File upload failed, return an error response
                        return response()->json(['error' => 'Failed to upload files.'], 500);
                    }
                }
                $violation->file_attach = json_encode($existingFilePaths); // Save the updated array
            }
    
            // Process remarks
            if (isset($validatedData['remarks']) && is_array($validatedData['remarks'])) {
                $remarksArray = [];
                foreach ($validatedData['remarks'] as $remark) {
                    $remarksArray[] = $remark['text'];
                }
                $validatedData['remarks'] = json_encode($remarksArray);
            }
    
            // Merge new violations into existing violations array
            if (!empty($validatedData['violation'])) {
                $existingViolations = json_decode($violation->violation, true) ?? [];
                $newViolations = array_filter($validatedData['violation'], function ($value) {
                    return $value !== null;
                });
                $validatedData['violation'] = json_encode(array_unique(array_merge($existingViolations, $newViolations)));
            }
    
            // Update the violation with validated data
            $violation->update($validatedData);
    
            // If new violations were added, add them to the archives model
            if (!empty($newViolations)) {
                foreach ($newViolations as $newViolation) {
                    $violation->addViolation($newViolation);
                }
                // Refresh the model after adding new violations
                $violation = archives::findOrFail($id);
            }
    
            // Return JSON response with updated violation details
            return response()->json(['success' => "Update Successfully"], 200);
    
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating Violation: ' . $e->getMessage());
    
            // Set error message and return JSON response
            return response()->json(['error' => 'Error updating Violation: ' . $e->getMessage()], 500);
        }
    }
    public function removeAttachmentarchives(Request $request, $id)
    {
        try {
            $archive = Archives::findOrFail($id);
            $attachmentToRemove = $request->input('attachment');
    
            if ($attachmentToRemove) {
                $attachments = json_decode($archive->file_attach, true) ?? [];
    
                // Check if the attachment exists in the array
                if (($key = array_search($attachmentToRemove, $attachments)) !== false) {
                    unset($attachments[$key]);
                    $archive->file_attach = json_encode(array_values($attachments)); // Reindex array and encode back to JSON
                    $archive->save();
    
                    // Optionally, delete the file from the storage
                    if (Storage::exists($attachmentToRemove)) {
                        Storage::delete($attachmentToRemove);
                    }
    
                    // Update completeness symbols after removing attachment
                    $archive->checkCompleteness();
    
                    return response()->json(['success' => 'Attachment removed successfully.']);
                } else {
                    return response()->json(['error' => 'Attachment not found in the list.'], 404);
                }
            } else {
                return response()->json(['error' => 'Attachment parameter is missing.'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to remove attachment', ['exception' => $e]);
            return response()->json(['error' => 'Failed to remove attachment.'], 500);
        }
    }
    
    public function finishCase_archives(Request $request, $id){
        $tasFile = archives::findOrFail($id);
        $tasFile->status = 'closed';
        $tasFile->fine_fee = $request->fine_fee;
        $tasFile->save();

       // Determine response type based on request headers
       if ($request->expectsJson()) {
        return response()->json(['success' => 'Case Closed Successfully'], 200);
    } else {
        return redirect()->back()->with('success', 'Case Closed Successfully');
    }
} 
    public function delete_edit_violation(Request $request, $id) {
        $archive = Archives::findOrFail($id); // Ensure the model name 'Archives' matches your actual model
        $violationIndex = $request->input('index');
    
        // Retrieve existing violations
        $violations = json_decode($archive->violation, true) ?? [];
    
        if (isset($violations[$violationIndex])) {
            array_splice($violations, $violationIndex, 1);
            $archive->violation = json_encode($violations);
            $archive->save();
        }
    
        return response()->json(['message' => 'Violation deleted successfully', 'violations' => json_decode($archive->violation)]);
    }
    function delete_archives($id){
        try {
            $violation = archives::findOrFail($id);
            $violation->delete();
            return response()->json(['success' => 'Cases deleted successfully'],200);
        } catch (\Exception $e) {
            Log::error('Error deleting Violation: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting Violation: ' . $e->getMessage()], 500);
        }
    }
    public function update_archive_violation(Request $request, $id){
        $archives = archives::findOrFail($id);
        $violationIndex = $request->input('index');
        $newViolation = $request->input('violation');
    
        // Retrieve existing violations
        $violations = json_decode($archives->violation, true) ?? [];
    
        if (isset($violations[$violationIndex])) {
            $violations[$violationIndex] = $newViolation;
            $archives->violation = json_encode($violations);
            $archives->save();
        }
    
        return response()->json(['message' => 'Violation updated successfully', 'violations' => json_decode($archives->violation)]);
    }
    public function deleteRemark_archives(Request $request){
        // Retrieve data from AJAX request
        $violationId = $request->input('violation_id');
        $index = $request->input('index');

        // Find the archives by violation ID (assuming archives is your model)
        try {
            $archives = archives::findOrFail($violationId);

            // Decode remarks from JSON to array
            $remarks = json_decode($archives->remarks, true);

            // Check if remarks exist and if the index is valid
            if (is_array($remarks) && array_key_exists($index, $remarks)) {
                // Remove the remark at the specified index
                unset($remarks[$index]);

                // Encode the updated remarks array back to JSON and update the archives
                $archives->remarks = json_encode(array_values($remarks)); // Re-index array
                $archives->save();

                // Return a success response
                return response()->json(['message' => 'Remark deleted successfully']);
            } else {
                // Return an error response if remark or index is invalid
                return response()->json(['error' => 'Invalid remark index'], 404);
            }
        } catch (\Exception $e) {
            // Return an error response if archives is not found or any other exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
///////////////////////////////////////////////////=============================================================================================================================    
///////////////////////////////////////////////////=============================================================================================================================    
///////////////////////////////////////////////////=============================================================================================================================    
///////////////////////////////////////////////////=============================================================================================================================    
///////////////////////////////////////////////////=============================================================================================================================    
    
    public function admittedsubmit(Request $request) {
         // dd($request->all());
         try {
            $validatedData = $request->validate([
                'top' => 'nullable|string',
                'admittedno' => 'nullable|string',
                'driver' => 'required|string',
                'apprehending_officer' => 'required|string',
                'violation' => 'required|string',
                'transaction_no' => 'nullable|string',
                'contact_no' => 'required|string',
                'plate_no' => 'required|string',
                'date_received' => 'required|date',
                'file_attachment' => 'nullable|array',
                'file_attachment.*' => 'nullable|file|max:5120',
            ]);
            DB::beginTransaction();
            $currentYear = date('Y');
            $existingadmitted = admitted::where('admittedno', $validatedData['admittedno'])->first();
            if (!$existingadmitted) {
                $admitted = new admitted([
                    'admittedno' => 'CS-' . $currentYear .'-'. $validatedData['admittedno'],
                    'top' => $validatedData['top'],
                    'driver' => $validatedData['driver'],
                    'apprehending_officer' => $validatedData['apprehending_officer'],
                    'violation' => json_encode(explode(', ', $validatedData['violation'])),
                    'date_received' => $validatedData['date_received'],
                    'transaction_no' => $validatedData['transaction_no'] ? "TRX-LETAS-" . $validatedData['transaction_no'] : null,
                    'plate_no' => $validatedData['plate_no'],
                    'contact_no' => $validatedData['contact_no'],

                ]);

                if ($request->hasFile('file_attachment')) {
                    $filePaths = [];
                    $cx = 1;
                    foreach ($request->file('file_attachment') as $file) {
                        $x = $validatedData['admittedno'] . "_documents_" . $cx . "_";
                        $fileName = $x . time();
                        $file->storeAs('attachments', $fileName, 'public');
                        $filePaths[] = 'attachments/' . $fileName;
                        $cx++;
                    }
                    $admitted->file_attach = json_encode($filePaths);
                }

                $admitted->save();
            } else {
                return redirect()->back()->with('error', 'Admitted no. already exists.');
            }
            DB::commit();
            return redirect()->back()->with('success', 'Form submitted successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function profile(Request $request){
        $userId = $request->id;
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('dashboard')->with('error', 'User not found.');
        }
        return view('profile', ['user' => $user]);
    }
    public function edit($id){
        $user = User::findOrFail($id);
        return view('edit_profile', compact('user'));
    }
    public function update(Request $request, $id){
        try {
            $request->validate([
                'fullname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'role' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:6144'
            ]);
            $user = User::findOrFail($id);
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture');
                $filename = Auth::user()->id . '_' . $profilePicture->getClientOriginalName();
                // Store the uploaded file in storage/app/public/profiles directory
                $path = $profilePicture->storeAs('public/profiles', $filename);
            }
            $user->update([
                'fullname' => $request->input('fullname'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    } 
    public function updatePicture(Request $request, $id){
        try {
            $request->validate([
                'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:6144' // Adjust the validation rules as needed
            ]);
    
            $user = User::findOrFail($id);
    
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageName = $user->username .'-dp.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $imageName);
    
                // Update the user's profile picture file path in the database
                $user->profile_pic = 'uploads/' . $imageName; // Store the file path, not the file content
                $user->save();
            }
    
            return redirect()->back()->with('success', 'Profile picture uploaded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while uploading the profile picture.');
        }
    }
    public function change($id){
        $user = User::findOrFail($id);
        return view('change_password', compact('user'));
    }
    public function updatePassword(Request $request){
        try {
            $user = Auth::user();
            
            if (!password_verify($request->current_password, Crypt::decryptString($user->password))) {
                return back()->with('error', 'Current password does not match.');
            }
    
            $user->password = Crypt::encryptString($request->new_password);
            $user->save();
    
            return back()->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function management(){
        $users = User::all()->map(function($user) {
            $user->decrypted_password = Crypt::decryptString($user->password);
            return $user;
        });

        return view('user_management', ['users' => $users]);
    }
    public function userdestroy(User $user){
        $user->delete();

        return redirect()->route('user_management')->with('success', 'User deleted successfully');
    }
    public function add_user(){
        return view('add-user');
    }
    public function store_user(Request $request){
        try {
            // Validate the incoming request data
            $request->validate([
                'fullname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Begin a database transaction
            DB::beginTransaction();


            $user = new User([
                'fullname' => $request->input('fullname'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
                'email_verified_at' => now(),
                'password' => bcrypt($request->input('password')),
            ]);


            $user->save();


            DB::commit();


            return redirect()->route('user_management')->with('success', 'User created successfully');
        } catch (\Exception $e) {

            DB::rollBack();


            Log::error('Error creating user: ' . $e->getMessage());


            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }
    public function violationadd(){
        return view('ao.addvio');
    }
    public function officergg(){
        $departments = department::all();
        return view('ao.addoffi', compact('departments'));
    }    
    public function department(){
        return view('ao.adddep');
    }    


public function departmentsave(Request $request) {
    try {
        // Validate the request data
        $request->validate([
            'department' => 'required|string',
        ]);
        
        // Check if the department already exists
        $existingDepartment = Department::where('department', $request->input('department'))->first();
        
        if ($existingDepartment) {
            return redirect()->back()->with('error', 'Department already exists');
        }
        
        // Generate a random string
        $randomString = Str::random(10);

        // Begin a database transaction
        DB::beginTransaction();

        // Create a new Department instance
        $department = new Department([
            'dep_unique' => $randomString,
            'department' => $request->input('department'),
        ]);
        $department->save();
        DB::commit();
        return redirect()->back()->with('success', 'Department created successfully');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error creating Department: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error creating Department: ' . $e->getMessage());
    }
}
public function editdepp(){

    $deps = Department::all();

    // dd($officers[1]);
    return view('ao.editdep', compact('deps'));
}
    public function editoffi(){

        $officers = ApprehendingOfficer::all();

        // dd($officers[1]);
        return view('ao.editoffi', compact('officers'));
    }
    public function edivio(){

        $violations = TrafficViolation::orderBy('code', 'asc')->get();

        // dd($officers[1]);
        return view('ao.editvio', compact('violations'));
    }
    //add officer
    public function save_offi(Request $request){
        try {
            $request->validate([
                'officer' => 'required|string',
                'department' => 'required|string',
            ]);

            // Check if the officer already exists
            $existingOfficer = ApprehendingOfficer::where('officer', $request->input('officer'))
                ->where('department', $request->input('department'))
                ->first();

            if ($existingOfficer) {
                return redirect()->back()->with('error', 'Officer already exists.');
            }

            DB::beginTransaction();
            $user = new ApprehendingOfficer([
                'officer' => $request->input('officer'),
                'department' => $request->input('department'),
            ]);

            $user->save();

            DB::commit();

            return redirect()->back()->with('success', 'Officer created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Officer: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Error creating Officer: ' . $e->getMessage());
        }
    }
    // add violation//
    public function addvio(Request $request){
        try {
            $request->validate([
                'code' => 'string',
                'violation' => 'string',
            ]);


            DB::beginTransaction();
            $user = new TrafficViolation([
                'code' => $request->input('code'),
                'violation' => $request->input('violation'),
                ]);


            $user->save();

            DB::commit();

            return redirect()->back()->with('success', 'Violation created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Violation: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Error creating Violation: ' . $e->getMessage());
        }
    }
    public function updateTas(Request $request, $id)
{
    try {
        // Find the violation by ID
        $violation = TasFile::findOrFail($id);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'case_no' => 'nullable|string|max:255',
            'top' => 'nullable|string|max:255',
            'driver' => 'nullable|string|max:255',
            'apprehending_officer' => 'nullable|string|max:255',
            'violation' => 'nullable|array',
            'transaction_no' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'plate_no' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:255',
            'remarks.*.text' => 'nullable|string',
            'file_attach_existing.*' => 'nullable|file', 
            'fine_fee' => 'nullable|numeric', 
            'typeofvehicle' => 'nullable|string|max:255',  
            'status' => 'nullable|string|in:in-progress,closed,settled,unsettled', 
        ]);

        // Attach files
        if ($request->hasFile('file_attach_existing')) {
            // Retrieve existing file attachments and decode them
            $existingFilePaths = json_decode($violation->file_attach, true) ?? [];
            
            $cx = count($existingFilePaths) + 1;
            foreach ($request->file('file_attach_existing') as $file) {
                // Check if the file was actually uploaded
                if ($file->isValid()) {
                    $x = "CS-".$violation->case_no . "_documents_" . $cx . "_";
                    $fileName = $x . time();
                    $file->storeAs('attachments', $fileName, 'public');
                    $existingFilePaths[] = 'attachments/' . $fileName; // Append the new file path
                    $cx++;
                } else {
                    // File upload failed, return an error response
                    return back()->with('error', 'Failed to upload files.');
                }
            }
            $violation->file_attach = json_encode($existingFilePaths); // Save the updated array
        }

        // Process remarks
        if (isset($validatedData['remarks']) && is_array($validatedData['remarks'])) {
            $remarksArray = [];
            foreach ($validatedData['remarks'] as $remark) {
                $remarksArray[] = $remark['text'];
            }
            $validatedData['remarks'] = json_encode($remarksArray);
        }

        // Merge new violations into existing violations array
        if (!empty($validatedData['violation'])) {
            $existingViolations = json_decode($violation->violation, true) ?? [];
            $newViolations = array_filter($validatedData['violation'], function ($value) {
                return $value !== null;
            });
            $validatedData['violation'] = json_encode(array_unique(array_merge($existingViolations, $newViolations)));
        }

        // Check if transaction_no has changed
        if (isset($validatedData['transaction_no']) && $validatedData['transaction_no'] !== $violation->transaction_no) {
            // Remove any existing "TRX-LETAS-" prefix from the new transaction_no
            $validatedData['transaction_no'] = preg_replace('/^TRX-LETAS-/', '', $validatedData['transaction_no']);
            // Ensure only one "TRX-LETAS-" prefix is added
            $validatedData['transaction_no'] = "TRX-LETAS-" . ltrim($validatedData['transaction_no'], "TRX-LETAS-");
        } else {
            // Transaction number did not change, so retain the current value
            unset($validatedData['transaction_no']);
        }

        // Capture changes before updating
        $originalData = $violation->getOriginal();

        // Update the violation with validated data
        $violation->update($validatedData);

        // If new violations were added, add them to the TasFile model
        if (!empty($newViolations)) {
            foreach ($newViolations as $newViolation) {
                $violation->addViolation($newViolation);
            }
            // Refresh the model after adding new violations
            $violation = TasFile::findOrFail($id);
        }

        // Log history if there are changes
        $changes = [];
        foreach ($validatedData as $key => $value) {
            if ($violation->$key != $originalData[$key]) {
                $changes[$key] = [
                    'old_value' => $originalData[$key],
                    'new_value' => $value
                ];
            }
        }

        if (!empty($changes)) {
            $violation->logHistory('updated', $changes);
        }

        return back()->with('success', 'Violation updated successfully')->with('status', 201);

    } catch (\Exception $e) {
        // Log the error
        Log::error('Error updating Violation: ' . $e->getMessage());

        // Set error message
        return back()->with('error', 'Error updating Violation: ' . $e->getMessage());
    }
}

    
    
    public function uploadFileAdmit(Request $request, $id)
    {
        // Validate the request data for file upload
        $request->validate([
            'file_attach.*' => 'required|file|  mimes:pdf,doc,docx', // Allow multiple files
        ]);
    
        try {
            // Find the Admitted record
            $admitted = Admitted::findOrFail($id);
    
            // Handle file upload
            if ($request->hasFile('file_attach')) {
                $existingFilePaths = json_decode($admitted->file_attach, true) ?? [];
                $cx = count($existingFilePaths) + 1;
    
                foreach ($request->file('file_attach') as $file) {
                    if ($file->isValid()) {
                        $x = "CS-" . $admitted->case_no . "_documents_" . $cx . "_";
                        $fileName = $x . time() . '.' . $file->getClientOriginalExtension();
                        $file->storeAs('attachments', $fileName, 'public');
                        $existingFilePaths[] = 'attachments/' . $fileName;
                        $cx++;
                    } else {
                        // File upload failed, return an error response
                        return response()->json(['error' => 'Failed to upload files.'], 400);
                    }
                }
    
                // Update the Admitted record with the updated file attachments array
                $admitted->file_attach = json_encode($existingFilePaths);
            }
    
            // Save the Admitted record
            $admitted->save();
            return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            // Log any errors for debugging
            \Log::error('Error updating status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
    

    public function uploadFileTas(Request $request, $id)
{
    // Validate the request data for file upload
    $request->validate([
        'file_attach' => 'required|file|mimes:pdf,doc,docx|max:2048', // Example validation rules
    ]);

    try {
        // Find the TasFile record
        $tasFile = TasFile::findOrFail($id);

        // Handle file upload
        if ($request->hasFile('file_attach')) {
            $file = $request->file('file_attach');
            $fileName = 'CS-' . $tasFile->case_no . '_document_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('attachments', $fileName, 'public');

            // Retrieve existing file attachments and decode them
            $existingFilePaths = json_decode($tasFile->file_attach, true) ?? [];
            // Append the new file path to the array
            $existingFilePaths[] = 'attachments/' . $fileName;

            // Update the TasFile record with the updated file attachments array
            $tasFile->file_attach = json_encode($existingFilePaths);
        }

        // Save the TasFile record
        $tasFile->save();

        // Return JSON response indicating success
        return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            // Log any errors for debugging
            \Log::error('Error updating status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id){
        try {
            // Log the request data for debugging
            \Log::info('Request data: ', $request->all());

            $tasFile = TasFile::findOrFail($id);

            // Log the received status for debugging
            \Log::info('Received status: ' . $request->status);

            $tasFile->status = $request->status;
            $tasFile->save();

            return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            // Log any errors for debugging
            \Log::error('Error updating status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
    public function finishCase(Request $request, $id)
    {
        $tasFile = TasFile::findOrFail($id);
        $tasFile->status = 'settled';
        $tasFile->fine_fee = $request->fine_fee;
        $tasFile->save();
    
        // Determine response type based on request headers
        if ($request->expectsJson()) {
            return response()->json(['success' => 'Case Closed Successfully'], 200);
        } else {
            return redirect()->back()->with('success', 'Case Closed Successfully');
        }
    }
    public function printsub(Request $request, $id){
        $tasFile = TasFile::findOrFail($id);
        $changes = $tasFile;
        $officerName = $changes->apprehending_officer;
        $officers = ApprehendingOfficer::where('officer', $officerName)->get();

        if (!empty($changes->violation)) {
            $violations = json_decode($changes->violation);
            if ($violations !== null) {
                $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
            } else {
                $relatedViolations = [];
            }
        } else {
            $relatedViolations = [];
        }

        $holidays = [
            '01-01', // New Year's Day
            '04-09', // Araw ng Kagitingan
            '05-01', // Labor Day
            '06-12', // Independence Day
            '08-26', // National Heroes Day
            '11-30', // Bonifacio Day
            '12-25', // Christmas Day
            '02-25', // EDSA People Power Revolution Anniversary
            '08-21', // Ninoy Aquino Day
            '11-01', // All Saints' Day
            '11-02', // All Souls' Day
            '12-30', // Rizal Day
            '02-14', // Valentine's Day
            '03-08', // International Women's Day
            '10-31', // Halloween
            '04-20', // 420 (Cannabis Culture)
            '07-04', // Independence Day (United States)
            '05-14', // Additional holiday declared by the government
            '11-15', // Regional holiday
        ];

        // Get the current date
        $startDate = Carbon::now();
        $formattedDate = $startDate->format('F j, Y');

        // Calculate the new date excluding weekends and holidays
        $currentDate = clone $startDate; // Clone to avoid modifying the original start date
        $numDays = 3;

        while ($numDays > 0) {
            $currentDate->addDay();

            // Check if the current day is a weekend or a holiday
            if ($currentDate->isWeekend() || in_array($currentDate->format('m-d'), $holidays)) {
                continue; // Skip weekends and holidays
            }

            $numDays--;
        }

        $endDate = $currentDate->format('F j, Y');

        $compactData = [
            'changes' => $changes,
            'officers' => $officers,
            'relatedViolations' => $relatedViolations,
            'date' => $formattedDate,
            'hearing' => $endDate,
        ];

        // dd($compactData);
        $status = $request->input('details');
        switch ($status) {
            case "subpeona":
                return view('sub.print', compact('tasFile', 'compactData'));
            case "motionrelease1":
                return view('sub.motionreleasep1', compact('tasFile', 'compactData'));
            case "motionrelease2":
                return view('sub.motionreleasep2', compact('tasFile', 'compactData'));
            default:
                // Handle default case if necessary
                return view('sub.print', compact('tasFile', 'compactData'));
        }
    }
    public function deleteTas($id) {
        try {
            $violation = TasFile::findOrFail($id);
            $violation->delete();
            return response()->json(['success' => 'Cases deleted successfully'],200);
        } catch (\Exception $e) {
            Log::error('Error deleting Violation: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting Violation: ' . $e->getMessage()], 500);
        }
    }
    public function updateContest()
    {
        try {
            // Fetch all traffic violations
            $violation = TrafficViolation::all();
            
            // Fetch recent TasFiles ordered by case number descending
            $recentViolationsToday = TasFile::all()->sortByDesc('case_no');
            
            // Prepare a collection for officers
            $officers = collect();
            
            foreach ($recentViolationsToday as $tasFile) {
                // Update completeness symbols for each TasFile
                $tasFile->checkCompleteness();
                
             
                $officerName = $tasFile->apprehending_officer;
                $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
                $officers = $officers->merge($officersForFile);
                $tasFile->relatedofficer = $officersForFile;
            }
    
            // Debugging: Dump the officers collection to check the data
            // dd($officers);
            // dd($recentViolationsToday[100]);
    
            // Pass data to the view, including the new variable $violation
            return view('tas.edit', compact('recentViolationsToday', 'violation', 'officers'));
        } catch (\Exception $e) {
            \Log::error('Error updating contest: ' . $e->getMessage());
            throw new \Exception('Error updating contest: ' . $e->getMessage());
        }
    }
    
    public function updateAdmitted(){
        // Fetch all traffic violations
        
        
        // Fetch recent TasFiles ordered by case number descending
        $recentViolationsToday = Admitted::all()->sortByDesc('admittedno');
          // Update completeness symbols for each TasFile
       
        // Fetch all codes (assuming TrafficViolation model provides codes)
        $codes = TrafficViolation::all();
        
        // Prepare a collection for officers
        $officers = collect();
       
        
        // Iterate through each TrafficViolation record
        foreach ($recentViolationsToday as $tasFile) {
            $tasFile->checkCompleteness();


            $officerName = $tasFile->apprehending_officer;
            $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
            $officers = $officers->merge($officersForFile);
            $tasFile->relatedofficer = $officersForFile;
            
        }
  
        // Pass data to the view, including the new variable $violationData
        return view('admitted.edit', compact('recentViolationsToday', 'codes', 'officers' ));
    }
    public function historyIndex() {

        return view('history');
    }
    public function fetchRemarks($id){
        $tasFile = TasFile::findOrFail($id);
        $remarks = json_decode($tasFile->remarks);

        return response()->json(['remarks' => $remarks]);
    }
    public function updateoffi(Request $request, $id){
        try {
            // Validate incoming request
            $request->validate([
                'officer' => 'required|string',
                'department' => 'required|string',
                'statusis' => 'required|string',
            ]);

            // Update officer details
            $officer = ApprehendingOfficer::findOrFail($id);
            $officer->officer = $request->input('officer');
            $officer->department = $request->input('department');
            $officer->isactive = $request->input('statusis');
            $officer->save();

            // Redirect back with success message
            return back()->with('success', 'Officer details updated successfully.');
        } catch (ModelNotFoundException $e) {
            // Handle case where officer with $id is not found
            return back()->with('error', 'Officer not found.');
        } catch (ValidationException $e) {
            // Handle validation errors
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            // Handle other unexpected errors
            return back()->with('error', 'Failed to update officer details. Please try again.');
        }
    }
    public function updatedeps(Request $request, $id){
        try {
            // Validate incoming request
            $request->validate([
                'department' => 'required|string',
            ]);

            // Update officer details
            $officer = department::findOrFail($id);
            $officer->department = $request->input('department');
            $officer->save();

            // Redirect back with success message
            return back()->with('success', 'Officer details updated successfully.');
        } catch (ModelNotFoundException $e) {
            // Handle case where officer with $id is not found
            return back()->with('error', 'Officer not found.');
        } catch (ValidationException $e) {
            // Handle validation errors
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            // Handle other unexpected errors
            return back()->with('error', 'Failed to update officer details. Please try again.');
        }
    }
    public function updateviolation(Request $request, $id){
        try {
            // Validate incoming request
            $request->validate([
                'officer' => 'required|string',
                'department' => 'required|string',
            ]);

            // Update officer details
            $officer = ApprehendingOfficer::findOrFail($id);
            $officer->officer = $request->input('officer');
            $officer->department = $request->input('department');
            $officer->save();

            // Redirect back with success message
            return back()->with('success', 'Officer details updated successfully.');
        } catch (ModelNotFoundException $e) {
            // Handle case where officer with $id is not found
            return back()->with('error', 'Officer not found.');
        } catch (ValidationException $e) {
            // Handle validation errors
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            // Handle other unexpected errors
            return back()->with('error', 'Failed to update officer details. Please try again.');
        }
    }
    public function detailstasfile(Request $request, $id){
        try {
            // Find the TasFile by its ID or throw a ModelNotFoundException
            $tasFile = TasFile::findOrFail($id);

            // Retrieve related ApprehendingOfficers
            $relatedOfficers = ApprehendingOfficer::where('officer', $tasFile->apprehending_officer)->get();

            // Retrieve related TrafficViolations
            $violations = json_decode($tasFile->violation, true);
            $relatedViolations = [];
            if ($violations) {
                $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
            }
            
            $remarks = json_decode($tasFile->remarks);
            // Check if $remarks is an array
            if (is_array($remarks)) {
                $remarks = array_reverse($remarks);
            } else {
                // If $remarks is not an array, set it to an empty array
                $remarks = [];
            }
            // dd($remarks);
            // Return the view with TasFile and related data
            return view('tas.detailsview', compact('tasFile', 'relatedOfficers', 'relatedViolations', 'remarks'));

        } catch (ModelNotFoundException $e) {
            // Handle case where TasFile with $id is not found
            return response()->view('errors.404', [], 404);
        }
    }
    public function detailsedit(Request $request, $id) {
        try {
            // Find the TasFile by its ID or throw a ModelNotFoundException
            $recentViolationsToday = TasFile::findOrFail($id);
    
            // Retrieve all Traffic Violations ordered by code ascending
            $violationz = TrafficViolation::orderBy('code', 'asc')->get();
    
            // Prepare a collection for officers
            $officers = collect();
    
            // Get the apprehending officer name from the TasFile
            $officerName = $recentViolationsToday->apprehending_officer;
    
            // Query the ApprehendingOfficer model for officers with the given name
            $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
    
            // Merge the officers into the collection
            $officers = $officers->merge($officersForFile);
    
            // Decode remarks if they are JSON-encoded
            $remarks = json_decode($recentViolationsToday->remarks, true);
            
            // Check if $remarks is an array and reverse it if so
            if (is_array($remarks)) {
                $remarks = array_reverse($remarks);
            } else {
                // If $remarks is not an array or JSON decoding failed, set it to an empty array
                $remarks = [];
            }
    
            // Pass data to the view, including authenticated user information
            $user = Auth::user(); // Assuming you are using Laravel's built-in authentication
            $fullname = $user->fullname; // Adjust this based on your User model's field
    
            return view('tas.detailsedit', compact('recentViolationsToday', 'officers', 'violationz', 'remarks', 'fullname', 'user'));
    
        } catch (ModelNotFoundException $e) {
            // Handle the case where the TasFile with the specified ID is not found
            return response()->view('errors.404', [], 404);
        }
    }
    
    
    public function detailsadmitted(Request $request, $id){
        try {
            // Find the TasFile by its ID or throw a ModelNotFoundException
            $admitted = admitted::findOrFail($id);

            // Retrieve related ApprehendingOfficers
            $relatedOfficers = ApprehendingOfficer::where('officer', $admitted->apprehending_officer)->get();

            // Retrieve related TrafficViolations
            $violations = json_decode($admitted->violation, true);
            $relatedViolations = [];
            if ($violations) {
                $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
            }
            
            $remarks = json_decode($admitted->remarks);
            // Check if $remarks is an array
            if (is_array($remarks)) {
                $remarks = array_reverse($remarks);
            } else {
                // If $remarks is not an array, set it to an empty array
                $remarks = [];
            }
            // dd($remarks);
            // Return the view with TasFile and related data
            return view('admitted.detailsview', compact('admitted', 'relatedOfficers', 'relatedViolations', 'remarks'));

        } catch (ModelNotFoundException $e) {
            // Handle case where TasFile with $id is not found
            return response()->view('errors.404', [], 404);
        }
    }
    public function finishtasfile(Request $request, $id){
        try {
            // Find the TasFile by its ID or throw a ModelNotFoundException
            $tasFile = TasFile::findOrFail($id);

            // Retrieve related ApprehendingOfficers
            $relatedOfficers = ApprehendingOfficer::where('officer', $tasFile->apprehending_officer)->get();

            // Retrieve related TrafficViolations
            $violations = json_decode($tasFile->violation, true);
            $relatedViolations = [];
            if ($violations) {
                $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
            }
            
            $remarks = json_decode($tasFile->remarks);
            // Check if $remarks is an array
            if (is_array($remarks)) {
                $remarks = array_reverse($remarks);
            } else {
                // If $remarks is not an array, set it to an empty array
                $remarks = [];
            }
            // dd($remarks);
            // Return the view with TasFile and related data
            return view('tas.detailsview', compact('tasFile', 'relatedOfficers', 'relatedViolations', 'remarks'));

        } catch (ModelNotFoundException $e) {
            // Handle case where TasFile with $id is not found
            return response()->view('errors.404', [], 404);
        }
    }
    public function fetchFinishData($id){
        try {
            // Find the TasFile by its ID or throw a ModelNotFoundException
            $tasFile = TasFile::findOrFail($id);
    
            // Retrieve related ApprehendingOfficers
            $relatedOfficers = ApprehendingOfficer::where('officer', $tasFile->apprehending_officer)->get();
    
            // Retrieve related TrafficViolations
            $violations = json_decode($tasFile->violation, true);
            $relatedViolations = [];
            if ($violations) {
                $relatedViolations = TrafficViolation::whereIn('code', $violations)->get();
            }
            
            // Check if $tasFile->remarks is already an array
            $remarks = is_array($tasFile->remarks) ? $tasFile->remarks : [];
    
            // Reverse the array if it's an array
            $remarks = array_reverse($remarks);
    
            // Return the view with TasFile and related data
            return view('tas.detailsview', compact('tasFile', 'relatedOfficers', 'relatedViolations', 'remarks'));
    
        } catch (ModelNotFoundException $e) {
            // Handle case where TasFile with $id is not found
            return response()->view('errors.404', [], 404);
        }
    }
    public function removeAttachment(Request $request, $id)
    {
        try {
            $tasFile = TasFile::findOrFail($id);
            $attachmentToRemove = $request->input('attachment');
    
            if ($attachmentToRemove) {
                $attachments = json_decode($tasFile->file_attach, true) ?? [];
    
                // Check if the attachment exists in the array
                if (($key = array_search($attachmentToRemove, $attachments)) !== false) {
                    unset($attachments[$key]);
                    $tasFile->file_attach = json_encode(array_values($attachments)); // Reindex array and encode back to JSON
                    $tasFile->save();
    
                    // Optionally, delete the file from the storage
                    Storage::delete($attachmentToRemove);
    
                    // Update completeness symbols after removing attachment
                    $tasFile->checkCompleteness();
    
                    return response()->json(['success' => 'Attachment removed successfully.']);
                } else {
                    return response()->json(['error' => 'Attachment not found in the list.'], 404);
                }
            } else {
                return response()->json(['error' => 'Attachment parameter is missing.'], 400);
            }
        } catch (\Exception $e) {
            // Log the error or handle it as per your application's needs
            return response()->json(['error' => 'Failed to remove attachment.'], 500);
        }
    }
    
    public function UPDATEVIO(Request $request, $id){
        $tasFile = TasFile::findOrFail($id);
        $violationIndex = $request->input('index');
        $newViolation = $request->input('violation');

        // Retrieve existing violations
        $violations = json_decode($tasFile->violation, true) ?? [];

        if (isset($violations[$violationIndex])) {
            $violations[$violationIndex] = $newViolation;
            $tasFile->violation = json_encode($violations);
            $tasFile->save();
        }

        return response()->json(['message' => 'Violation updated successfully', 'violations' => json_decode($tasFile->violation)]);
    }
    public function DELETEVIO(Request $request, $id) {
        try {
            $tasFile = TasFile::findOrFail($id);
            $violationIndex = $request->input('index');
    
            // Retrieve existing violations
            $violations = json_decode($tasFile->violation, true) ?? [];
    
            if (isset($violations[$violationIndex])) {
                array_splice($violations, $violationIndex, 1);
                $tasFile->violation = json_encode($violations);
                $tasFile->save();
                
                // Log the deletion of the violation
                \Log::info("Violation at index $violationIndex deleted successfully for TasFile ID: $id.");
    
              
    
                // Log the update of the completeness status
                \Log::info("TasFile ID: $id completeness checked and symbols updated.");
            } else {
                \Log::warning("Violation index $violationIndex not found for TasFile ID: $id.");
                return response()->json(['message' => 'Violation index not found'], 404);
            }
    
            return response()->json(['message' => 'Violation deleted successfully', 'violations' => json_decode($tasFile->violation)]);
        } catch (\Exception $e) {
            \Log::error('Error deleting violation: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting violation', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function deleteRemark(Request $request)
    {
        // Retrieve data from AJAX request
        $violationId = $request->input('violation_id');
        $index = $request->input('index');

        // Find the TasFile by violation ID (assuming TasFile is your model)
        try {
            $tasFile = TasFile::findOrFail($violationId);

            // Decode remarks from JSON to array
            $remarks = json_decode($tasFile->remarks, true);

            // Check if remarks exist and if the index is valid
            if (is_array($remarks) && array_key_exists($index, $remarks)) {
                // Remove the remark at the specified index
                unset($remarks[$index]);

                // Encode the updated remarks array back to JSON and update the TasFile
                $tasFile->remarks = json_encode(array_values($remarks)); // Re-index array
                $tasFile->save();

                // Return a success response
                return response()->json(['success' => 'Remark deleted successfully']);
            } else {
                // Return an error response if remark or index is invalid
                return response()->json(['error' => 'Invalid remark index'], 404);
            }
        } catch (\Exception $e) {
            // Return an error response if TasFile is not found or any other exception occurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //====================================================================================================================================================================
    //====================================================================================================================================================================
    //====================================================================================================================================================================
    //====================================================================================================================================================================
    //====================================================================================================================================================================
    //====================================================================================================================================================================
    

    function delete_admitted($id){
           try {
               $violation = admitted::findOrFail($id);
               $violation->delete();
               return response()->json(['success' => 'Cases deleted successfully'],200);
           } catch (\Exception $e) {
               Log::error('Error deleting Violation: ' . $e->getMessage());
               return response()->json(['error' => 'Error deleting Violation: ' . $e->getMessage()], 500);
           }
       }
       public function detailsadmittededit(Request $request, $id) {
           try {
               // Find the admitted by its ID or throw a ModelNotFoundException
               $recentViolationsToday = admitted::findOrFail($id);
       
               // Retrieve all Traffic Violations ordered by code ascending
               $violationz = TrafficViolation::orderBy('code', 'asc')->get();
       
               // Prepare a collection for officers
               $officers = collect();
       
               // Get the apprehending officer name from the admitted
               $officerName = $recentViolationsToday->apprehending_officer;
       
               // Query the ApprehendingOfficer model for officers with the given name
               $officersForFile = ApprehendingOfficer::where('officer', $officerName)->get();
       
               // Merge the officers into the collection
               $officers = $officers->merge($officersForFile);
       
               // Decode remarks if they are JSON-encoded
               $remarks = json_decode($recentViolationsToday->remarks, true);
               
               // Check if $remarks is an array and reverse it if so
               if (is_array($remarks)) {
                   $remarks = array_reverse($remarks);
               } else {
                   // If $remarks is not an array or JSON decoding failed, set it to an empty array
                   $remarks = [];
               }
       
               // Pass data to the view
               return view('admitted.detailsedit', compact('recentViolationsToday', 'officers', 'violationz', 'remarks'));
       
           } catch (ModelNotFoundException $e) {
               // Handle the case where the admitted with the specified ID is not found
               return response()->view('errors.404', [], 404);
           }
       }
       public function delete_edit_violation_admitted(Request $request, $id) {
           $archive = admitted::findOrFail($id); // Ensure the model name 'admitted' matches your actual model
           $violationIndex = $request->input('index');
       
           // Retrieve existing violations
           $violations = json_decode($archive->violation, true) ?? [];
       
           if (isset($violations[$violationIndex])) {
               array_splice($violations, $violationIndex, 1);
               $archive->violation = json_encode($violations);
               $archive->save();
           }
       
           return response()->json(['message' => 'Violation deleted successfully', 'violations' => json_decode($archive->violation)]);
       }
       public function removeAttachmentadmitted(Request $request, $id)
       {
           try {
               $archive = admitted::findOrFail($id);
               $attachmentToRemove = $request->input('attachment');
       
               if ($attachmentToRemove) {
                   $attachments = json_decode($archive->file_attach, true) ?? [];
       
                   // Check if the attachment exists in the array
                   if (($key = array_search($attachmentToRemove, $attachments)) !== false) {
                       unset($attachments[$key]);
                       $archive->file_attach = json_encode(array_values($attachments)); // Reindex array and encode back to JSON
                       $archive->save();
       
                       // Optionally, delete the file from the storage
                       if (Storage::exists($attachmentToRemove)) {
                           Storage::delete($attachmentToRemove);
                       }
       
                       // Update completeness symbols after removing attachment
                       $archive->checkCompleteness();
       
                       return response()->json(['success' => 'Attachment removed successfully.']);
                   } else {
                       return response()->json(['error' => 'Attachment not found in the list.'], 404);
                   }
               } else {
                   return response()->json(['error' => 'Attachment parameter is missing.'], 400);
               }
           } catch (\Exception $e) {
               \Log::error('Failed to remove attachment', ['exception' => $e]);
               return response()->json(['error' => 'Failed to remove attachment.'], 500);
           }
       }
       
       public function deleteRemark_admitted(Request $request){
           // Retrieve data from AJAX request
           $violationId = $request->input('violation_id');
           $index = $request->input('index');
   
           // Find the admitted by violation ID (assuming admitted is your model)
           try {
               $admitted = admitted::findOrFail($violationId);
   
               // Decode remarks from JSON to array
               $remarks = json_decode($admitted->remarks, true);
   
               // Check if remarks exist and if the index is valid
               if (is_array($remarks) && array_key_exists($index, $remarks)) {
                   // Remove the remark at the specified index
                   unset($remarks[$index]);
   
                   // Encode the updated remarks array back to JSON and update the admitted
                   $admitted->remarks = json_encode(array_values($remarks)); // Re-index array
                   $admitted->save();
   
                   // Return a success response
                   return response()->json(['success' => 'Remark deleted successfully']);
               } else {
                   // Return an error response if remark or index is invalid
                   return response()->json(['error' => 'Invalid remark index'], 404);
               }
           } catch (\Exception $e) {
               // Return an error response if admitted is not found or any other exception occurs
               return response()->json(['error' => $e->getMessage()], 500);
           }
       }
       public function updatedetailadmitted(Request $request, $id)
       {
           try {
               // Find the violation by ID
               $violation = Admitted::findOrFail($id);
       
               // Capture original data before updating
               $originalData = $violation->getOriginal();
       
               // Validate the incoming request data
               $validatedData = $request->validate([
                   'admittedno' => 'nullable|string|max:255',
                   'top' => 'nullable|string|max:255',
                   'driver' => 'nullable|string|max:255',
                   'apprehending_officer' => 'nullable|string|max:255',
                   'violation' => 'nullable|array',
                   'transaction_no' => 'nullable|string|max:255',
                   'date_received' => 'nullable|date',
                   'plate_no' => 'nullable|string|max:255',
                   'status' => 'nullable|string|max:255',
                   'contact_no' => 'nullable|string|max:255',
                   'remarks.*.text' => 'nullable|string',
                   'file_attach_existing.*' => 'nullable|file|max:512000', // Added file validation rules
               ]);
       
               // Attach files
               if ($request->hasFile('file_attach_existing')) {
                   // Retrieve existing file attachments and decode them
                   $existingFilePaths = json_decode($violation->file_attach, true) ?? [];
       
                   $cx = count($existingFilePaths) + 1;
                   foreach ($request->file('file_attach_existing') as $file) {
                       // Check if the file was actually uploaded
                       if ($file->isValid()) {
                           $x = "CS-" . $violation->admittedno . "_documents_" . $cx . "_";
                           $fileName = $x . time();
                           $file->storeAs('attachments', $fileName, 'public');
                           $existingFilePaths[] = 'attachments/' . $fileName; // Append the new file path
                           $cx++;
                       } else {
                           // File upload failed, return an error response
                           return response()->json(['error' => 'Failed to upload files.'], 500);
                       }
                   }
                   $violation->file_attach = json_encode($existingFilePaths); // Save the updated array
               }
       
               // Process remarks
               if (isset($validatedData['remarks']) && is_array($validatedData['remarks'])) {
                   $remarksArray = [];
                   foreach ($validatedData['remarks'] as $remark) {
                       $remarksArray[] = $remark['text'];
                   }
                   $validatedData['remarks'] = json_encode($remarksArray);
               }
       
               // Merge new violations into existing violations array
               if (!empty($validatedData['violation'])) {
                   $existingViolations = json_decode($violation->violation, true) ?? [];
                   $newViolations = array_filter($validatedData['violation'], function ($value) {
                       return $value !== null;
                   });
                   $validatedData['violation'] = json_encode(array_unique(array_merge($existingViolations, $newViolations)));
               }
       
               // Update the violation with validated data
               $violation->update($validatedData);
       
               // If new violations were added, add them to the admitted model
               if (!empty($newViolations)) {
                   foreach ($newViolations as $newViolation) {
                       $violation->addViolation($newViolation);
                   }
                   // Refresh the model after adding new violations
                   $violation = Admitted::findOrFail($id);
               }
       
               // Log history if there are changes
               $changes = [];
               foreach ($validatedData as $key => $value) {
                   if ($violation->$key != $originalData[$key]) {
                       $changes[$key] = [
                           'old_value' => $originalData[$key],
                           'new_value' => $value
                       ];
                   }
               }
       
               if (!empty($changes)) {
                   $violation->logHistory('updated', $changes);
               }
       
               // Return JSON response with updated violation details
               return response()->json(['success' => "Update Successfully"], 200);
       
           } catch (\Exception $e) {
               // Log the error
               Log::error('Error updating Violation: ' . $e->getMessage());
       
               // Set error message and return JSON response
               return response()->json(['error' => 'Error updating Violation: ' . $e->getMessage()], 500);
           }
       }
       
      
       
    public function updateStatusadmitted(Request $request, $id){
        try {
            // Log the request data for debugging
            \Log::info('Request data: ', $request->all());
    
            $archives = admitted::findOrFail($id);
    
            // Log the received status for debugging
            \Log::info('Received status: ' . $request->status);
    
            $archives->status = $request->status;
            $archives->save();
    
            return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (\Exception $e) {
            // Log any errors for debugging
            \Log::error('Error updating status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
    public function finishCase_admitted(Request $request, $id){
        $tasFile = admitted::findOrFail($id);
        $tasFile->status = 'closed';
        $tasFile->fine_fee = $request->fine_fee;
        $tasFile->save();

         // Determine response type based on request headers
         if ($request->expectsJson()) {
            return response()->json(['success' => 'Case Closed Successfully'], 200);
        } else {
            return redirect()->back()->with('success', 'Case Closed Successfully');
        }
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                                       ////////////////////////////////////////////////ANALYTICS//////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

    public function analyticsDash()
    {
        // Fetch data from the TasFile model
        $tasFiles = TasFile::all();

        // Count the number of complete and incomplete entries
        $completeCount = $tasFiles->where('symbols', 'complete')->count();
        $incompleteCount = $tasFiles->where('symbols', 'incomplete')->count();

        // Prepare data for the chart
        $chartData = [
            ['label' => 'Complete', 'value' => $completeCount],
            ['label' => 'Incomplete', 'value' => $incompleteCount],
        ];

        // Fetch data for the violation chart
        $violationsData = TasFile::select(DB::raw('violation, count(*) as count'))
            ->groupBy('violation')
            ->get();

        // Prepare data for ApexCharts
        $labels = $violationsData->pluck('violation')->toArray();
        $data = $violationsData->pluck('count')->toArray();

    

        $yearsWithData = TasFile::distinct()
        ->selectRaw('YEAR(date_received) as year')
        ->pluck('year');


        // Pass data to the view
        return view('analytics', compact('chartData','yearsWithData', 'labels', 'data'  ));
    }

    public function show($year, $month)
    {
        $forecastData = Forecast::where('year', $year)
            ->where('month', $month)
            ->first();

        return response()->json($forecastData);
    }

    public function fetchMonthlyTypeOfVehicle(Request $request)
    {
        $selectedMonth = $request->input('month');
        $comparisonMonth = $request->input('comparison_month');

        // Query to get the count of each type of vehicle for the selected month
        $selectedMonthData = TasFile::select(DB::raw("DATE_FORMAT(date_received, '%Y-%m') as month"), 'typeofvehicle', DB::raw('COUNT(*) as total'))
            ->whereRaw("DATE_FORMAT(date_received, '%Y-%m') = ?", [$selectedMonth])
            ->groupBy('month', 'typeofvehicle')
            ->get();

        // Query to get the count of each type of vehicle for the comparison month
        $comparisonMonthData = TasFile::select(DB::raw("DATE_FORMAT(date_received, '%Y-%m') as month"), 'typeofvehicle', DB::raw('COUNT(*) as total'))
            ->whereRaw("DATE_FORMAT(date_received, '%Y-%m') = ?", [$comparisonMonth])
            ->groupBy('month', 'typeofvehicle')
            ->get();

        // Prepare data for charting
        $chartData = [];
        foreach ($selectedMonthData as $row) {
            $chartData[$row->typeofvehicle]['selected_month'] = $row->total;
        }

        foreach ($comparisonMonthData as $row) {
            $chartData[$row->typeofvehicle]['comparison_month'] = $row->total;
        }

        // Prepare labels and datasets for chart
        $labels = [];
        $selectedMonthValues = [];
        $comparisonMonthValues = [];
        foreach ($chartData as $vehicle => $counts) {
            $labels[] = $vehicle;

            $selectedMonthValues[] = $counts['selected_month'] ?? 0;
            $comparisonMonthValues[] = $counts['comparison_month'] ?? 0;
        }

        // Prepare data to be returned as JSON
        $jsonData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Selected Month',
                    'data' => $selectedMonthValues,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Comparison Month',
                    'data' => $comparisonMonthValues,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];

        return response()->json($jsonData);
    }
    public function getDateReceivedData(Request $request)
    {
        try {
            // Validate the request data to ensure 'month' parameter is present
            $request->validate([
                'month' => 'required|date_format:Y-m',
            ]);
    
            // Extract the year and month from the 'month' input
            $monthInput = $request->input('month');
            $year = intval(substr($monthInput, 0, 4));
            $month = intval(substr($monthInput, 5, 2));
    
            // Fetch all records for the specified month and year
            $data = TasFile::whereMonth('date_received', $month)
                           ->whereYear('date_received', $year)
                           ->get();
    
            // Count occurrences by date_received
            $dateCounts = $data->groupBy(function ($item) {
                return $item->date_received instanceof \Carbon\Carbon
                    ? $item->date_received->format('Y-m-d')
                    : \Carbon\Carbon::parse($item->date_received)->format('Y-m-d');
            })->map(function ($group) {
                return $group->count();
            });
    
            $formattedData = $dateCounts->map(function ($count, $date) {
                return [
                    'date' => $date,
                    'count' => $count,
                ];
            })->values();
    
            return response()->json($formattedData);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Error fetching date received data: ' . $e->getMessage());
    
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }
    
    
    public function fetchViolations(Request $request)
    {
        $month1 = $request->query('month_1');
        $year1 = $request->query('year_1');
        $month2 = $request->query('month_2');
        $year2 = $request->query('year_2');

        // Validate inputs
        if (!$month1 || !$year1 || !$month2 || !$year2) {
            return response()->json(['error' => 'Invalid months or years selected'], 400);
        }

        // Fetch violations for the selected months and years
        $violationsMonth1 = TasFile::whereMonth('date_received', $month1)
            ->whereYear('date_received', $year1)
            ->count();
        $violationsMonth2 = TasFile::whereMonth('date_received', $month2)
            ->whereYear('date_received', $year2)
            ->count();

        // Prepare data for ApexCharts
        $data = [
            'series' => [$violationsMonth1, $violationsMonth2],
            'labels' => [
                date('F Y', mktime(0, 0, 0, $month1, 10, $year1)),
                date('F Y', mktime(0, 0, 0, $month2, 10, $year2))
            ],
        ];

        // Return data as JSON
        return response()->json($data);
    }

 
    public function getViolationRankings(Request $request)
    {
        // Validate page number from request or default to 1
        $page = $request->query('page', 1);

        // Fetch all TasFile records (you may need to adjust based on your actual needs)
        $tasFiles = TasFile::all();

        // Array to hold the count of each violation
        $violationsCount = [];

        foreach ($tasFiles as $tasFile) {
            // Extract violation codes
            preg_match_all('/\"(.*?)\"|([A-Za-z0-9\s]+(?:\s+AND\s+[A-Za-z0-9\s]+)*)/', $tasFile->violation, $matches);
            $elements = array_merge($matches[1], $matches[2]);

            foreach ($elements as $element) {
                $subElements = explode(',', $element);
                foreach ($subElements as $subElement) {
                    $subElement = trim($subElement);
                    if (!empty($subElement)) {
                        if (!isset($violationsCount[$subElement])) {
                            $violationsCount[$subElement] = 0;
                        }
                        $violationsCount[$subElement]++;
                    }
                }
            }
        }

        // Sort violations by count in descending order
        arsort($violationsCount);

        // Prepare paginated response
        $perPage = 10; // Adjust as per your requirement
        $total = count($violationsCount);
        $lastPage = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $paginatedViolations = array_slice($violationsCount, $offset, $perPage, true);

        // Fetch violation names from TrafficViolation model
        $response = [];
        foreach ($paginatedViolations as $violationCode => $count) {
            $violation = TrafficViolation::where('code', $violationCode)->first();
            if ($violation) {
                $response[] = [
                    'violation' => $violation->violation,
                    'count' => $count
                ];
            }
        }

        // Prepare pagination metadata
        $paginationData = [
            'current_page' => $page,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total,
        ];

        return response()->json([
            'data' => $response,
            'pagination' => $paginationData,
        ]);
    }


    public function getPieChartData(Request $request)
    {
        try {
            $month = $request->query('month');
            $query = TasFile::query();

            if ($month) {
                $query->whereMonth('date_received', '=', Carbon::parse($month)->month)
                    ->whereYear('date_received', '=', Carbon::parse($month)->year);
            }

            // Retrieve all records from the TasFile model
            $tasFiles = $query->get();
            Log::info('TasFile Records Retrieved: ', $tasFiles->toArray());

            // Prepare a count array for violations
            $violationCounts = [];

            // Iterate through each record and count violations
            foreach ($tasFiles as $file) {
                $violations = explode(',', $file->violation);
                Log::info('Violations for TasFile: ', $violations);

                foreach ($violations as $violationCode) {
                    $violationCode = trim($violationCode, '[]" '); // Trim any whitespace and unwanted characters
                    if (!empty($violationCode)) {
                        if (isset($violationCounts[$violationCode])) {
                            $violationCounts[$violationCode]++;
                        } else {
                            $violationCounts[$violationCode] = 1;
                        }
                    }
                }
            }

            Log::info('Violation Counts: ', $violationCounts);

            // Fetch the violation details from TrafficViolation model
            $violationDetails = TrafficViolation::whereIn('code', array_keys($violationCounts))->get();
            Log::info('Violation Details: ', $violationDetails->toArray());

            // Prepare final data structure
            $pieChartData = [];
            foreach ($violationDetails as $violationDetail) {
                $code = $violationDetail->code;
                if (isset($violationCounts[$code])) {
                    $pieChartData[] = [
                        'code' => $code,
                        'violation' => $violationDetail->violation,
                        'count' => $violationCounts[$code],
                    ];
                } else {
                    Log::warning('Violation code not found in counts: ' . $code);
                }
            }

            // Return the data as JSON response
            return response()->json($pieChartData);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Error generating pie chart data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate pie chart data'], 500);
        }
    }

////////////////////////////////////////////////////////////////////////COMMUNICATION//////////////////////////////////////////////////////////////////////
//                                                                                                                                                       // 
  //                                                                                                                                                   //  
//                                                                                                                                                       //
////////////////////////////////////////////////////////////////////////COMMUNICATION//////////////////////////////////////////////////////////////////////

    public function chatIndex($userId = null)
    {
        $messages = G5ChatMessage::latest()->with('user')->limit(10)->get();
        $user = Auth::user();
        $name = $user->name;
        $department = $user->department;
        $unreadMessageCount = G5ChatMessage::where('is_read', false)->count();
        $userId = $userId ?? Auth::id(); // Get the current user's ID if $userId is not provided


        
        // Fetch the list of users for the sidebar
        $users = User::where('id', '!=', $userId)->get();

        return view('chat', compact('unreadMessageCount', 'messages', 'name', 'department', 'userId', 'users'));
    }
    public function storeMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|integer|exists:users,id',
        ]);

        try {
            $message = new G5ChatMessage();
            $message->message = $request->input('message');
            $message->user_id = Auth::id();
            $message->receiver_id = $request->input('receiver_id');
            $message->save();

            // Return the newly created message along with success message and user details
            return response()->json([
                'message' => 'Message sent successfully.',
                'newMessage' => $message,
                'user' => Auth::user(),
            ], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error storing chat message: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Failed to send message.'], 500);
        }
    }
    public function getChatData($userId)
    {
        try {
            // Set a timeout for long polling (adjust as needed)
            $timeout = 30; // Timeout in seconds
    
            $startTime = time();
            while (true) {
                // Check if the time limit for long polling has been reached
                if (time() - $startTime >= $timeout) {
                    // If the timeout is reached, return an empty response to indicate no new messages
                    return response()->json(['messages' => []]);
                }
    
                // Fetch messages between the current user and the selected user, ordered by created_at in descending order
                $messages = G5ChatMessage::where(function($query) use ($userId) {
                    $query->where('user_id', Auth::id())->where('receiver_id', $userId);
                })->orWhere(function($query) use ($userId) {
                    $query->where('user_id', $userId)->where('receiver_id', Auth::id());
                })->orderBy('created_at', 'desc')->with('user', 'receiver')->limit(10)->get();
                
                // Transform messages and format date
                $messages->transform(function ($message) {
                    $message->created_at_formatted = Carbon::parse($message->created_at)->format('M d, Y H:i A');
                    return $message;
                });
    
                // Get current user details
                $user = Auth::user();
    
                if ($messages->isNotEmpty()) {
                    // If messages are available, return them along with the current user details
                    return response()->json(['messages' => $messages, 'user' => $user]);
                }
    
                // Sleep for a short interval before checking again
                usleep(500000); // Sleep for 0.5 seconds (adjust as needed)
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching chat messages: ' . $e->getMessage());
            
            // Return an error response
            return response()->json(['error' => 'Failed to fetch chat messages.'], 500);
        }
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////                     ////////////////////////////////////////////////////
    /////////////////////////////////////                HISTORY             ///////////////////////////////////////////
    ///////////////////////////////////////////                        ///////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    


}