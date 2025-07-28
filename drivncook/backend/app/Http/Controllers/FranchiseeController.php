<?php
 
 namespace App\Http\Controllers;
 
use App\Models\Franchisee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

 
 class FranchiseeController extends Controller
 {
     public function index() { 
         return Franchisee::all(); 
     }
 
     public function store(Request $request) {
         
         $validated = $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|unique:franchisees,email',
             'phone' => 'required|string|max:20',
             'address' => 'required|string',
             'city' => 'required|string|max:100',
             'postal_code' => 'required|string|max:10',
             'country' => 'required|string|max:100',
             'siret_number' => 'required|digits:14|unique:franchisees,siret_number',
             'franchise_code' => 'required|string|max:50|unique:franchisees,franchise_code',
             'entry_fee_paid' => 'boolean',
             'sales_percentage' => 'required|numeric|min:0|max:100',
         ]);
 
         $franchisee = Franchisee::create($validated);
         
         return response()->json($franchisee, 201);
     }
 
     public function show($id) { 
         return Franchisee::findOrFail($id); 
     }
 
     public function update(Request $request, $id) {
         $franchisee = Franchisee::findOrFail($id);
         
         $validated = $request->validate([
             'name' => 'sometimes|string|max:255',
             'email' => 'sometimes|email|unique:franchisees,email,' . $id,
             'phone' => 'sometimes|string|max:20',
             'address' => 'sometimes|string',
             'city' => 'sometimes|string|max:100',
             'postal_code' => 'sometimes|string|max:10',
             'country' => 'sometimes|string|max:100',
             'siret_number' => 'sometimes|digits:14|unique:franchisees,siret_number,' . $id,
             'franchise_code' => 'sometimes|string|max:50|unique:franchisees,franchise_code,' . $id,
             'entry_fee_paid' => 'sometimes|boolean',
             'sales_percentage' => 'sometimes|numeric|min:0|max:100',
         ]);
 
            $franchisee->update($validated);
            return $franchisee;
 
     }



        public function generatePdf($id)
        {
            $franchisee = Franchisee::with('trucks')->findOrFail($id);
            $pdf = Pdf::loadView('pdf.franchisee_report', compact('franchisee'));
            return $pdf->download("rapport_{$franchisee->franchise_code}.pdf");
        }

 
        public function destroy($id) {
            $franchisee = Franchisee::findOrFail($id);
            $franchisee->delete();
        return response()->noContent();
        }
 }
 