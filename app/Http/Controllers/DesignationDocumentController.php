<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DesignationDocument;
use App\Models\Designation;
use App\Models\User;
use App\Models\Notification;

class DesignationDocumentController extends Controller
{
    /**
     * Display a listing of designation documents.
     */
    public function index()
    {
        $user = Auth::user();
        
        $documents = DesignationDocument::where('user_id', $user->id)
            ->with('designation')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('designation-documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new designation document.
     */
    public function create()
    {
        $designations = Designation::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('designation-documents.create', compact('designations'));
    }

    /**
     * Store a newly created designation document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'designation_id' => 'required|exists:designations,id',
            'document_title' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();
        
        // Handle file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('designation-documents', $fileName, 'public');
        }

        // Create designation document
        $document = DesignationDocument::create([
            'user_id' => $user->id,
            'designation_id' => $request->designation_id,
            'document_title' => $request->document_title,
            'file_path' => $filePath,
            'status' => 'pending',
        ]);

        // Create notification for HR
        $hrUsers = User::role('hr')->get();
        foreach ($hrUsers as $hr) {
            Notification::create([
                'user_id' => $hr->id,
                'title' => 'New Designation Document Uploaded',
                'message' => "{$user->full_name} has uploaded a designation document for review.",
                'type' => 'designation_upload',
                'data' => [
                    'document_id' => $document->id,
                    'user_id' => $user->id,
                ],
            ]);
        }

        return redirect()->route('designation-documents.index')
            ->with('success', 'Designation document uploaded successfully and is pending review.');
    }

    /**
     * Display the specified designation document.
     */
    public function show(DesignationDocument $designationDocument)
    {
        $this->authorize('view', $designationDocument);
        
        $designationDocument->load(['user', 'designation', 'approvedBy', 'rejectedBy']);

        return view('designation-documents.show', compact('designationDocument'));
    }

    /**
     * Show the form for editing the specified designation document.
     */
    public function edit(DesignationDocument $designationDocument)
    {
        $this->authorize('update', $designationDocument);
        
        if ($designationDocument->status !== 'pending') {
            return redirect()->route('designation-documents.index')
                ->with('error', 'Cannot edit document that is already processed.');
        }

        $designations = Designation::where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('designation-documents.edit', compact('designationDocument', 'designations'));
    }

    /**
     * Update the specified designation document.
     */
    public function update(Request $request, DesignationDocument $designationDocument)
    {
        $this->authorize('update', $designationDocument);
        
        if ($designationDocument->status !== 'pending') {
            return redirect()->route('designation-documents.index')
                ->with('error', 'Cannot update document that is already processed.');
        }

        $request->validate([
            'designation_id' => 'required|exists:designations,id',
            'document_title' => 'required|string|max:255',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'designation_id' => $request->designation_id,
            'document_title' => $request->document_title,
        ];

        // Handle file upload if new file provided
        if ($request->hasFile('document_file')) {
            // Delete old file
            Storage::disk('public')->delete($designationDocument->file_path);
            
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('designation-documents', $fileName, 'public');
            $data['file_path'] = $filePath;
        }

        $designationDocument->update($data);

        return redirect()->route('designation-documents.index')
            ->with('success', 'Designation document updated successfully.');
    }

    /**
     * Remove the specified designation document.
     */
    public function destroy(DesignationDocument $designationDocument)
    {
        $this->authorize('delete', $designationDocument);
        
        if ($designationDocument->status !== 'pending') {
            return redirect()->route('designation-documents.index')
                ->with('error', 'Cannot delete document that is already processed.');
        }

        // Delete file
        Storage::disk('public')->delete($designationDocument->file_path);
        
        $designationDocument->delete();

        return redirect()->route('designation-documents.index')
            ->with('success', 'Designation document deleted successfully.');
    }

    /**
     * Approve designation document (HR only)
     */
    public function approve(Request $request, DesignationDocument $designationDocument)
    {
        $this->authorize('approve', $designationDocument);
        
        $request->validate([
            'hr_remarks' => 'nullable|string|max:500',
        ]);

        // Set previous current documents to not current
        DesignationDocument::where('user_id', $designationDocument->user_id)
            ->where('id', '!=', $designationDocument->id)
            ->update(['is_current' => false]);

        // Update user's designation
        $user = User::find($designationDocument->user_id);
        $user->designation_id = $designationDocument->designation_id;
        $user->save();

        // Approve document
        $designationDocument->update([
            'status' => 'approved',
            'hr_remarks' => $request->hr_remarks,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'is_current' => true,
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $designationDocument->user_id,
            'title' => 'Designation Document Approved',
            'message' => 'Your designation document has been approved. Your leave earning rates have been updated.',
            'type' => 'designation_approved',
            'data' => [
                'document_id' => $designationDocument->id,
                'designation_id' => $designationDocument->designation_id,
            ],
        ]);

        return redirect()->route('hr.designation-documents.index')
            ->with('success', 'Designation document approved successfully.');
    }

    /**
     * Reject designation document (HR only)
     */
    public function reject(Request $request, DesignationDocument $designationDocument)
    {
        $this->authorize('approve', $designationDocument);
        
        $request->validate([
            'hr_remarks' => 'required|string|max:500',
        ]);

        $designationDocument->update([
            'status' => 'rejected',
            'hr_remarks' => $request->hr_remarks,
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $designationDocument->user_id,
            'title' => 'Designation Document Rejected',
            'message' => 'Your designation document has been rejected. Please review the remarks and resubmit.',
            'type' => 'designation_rejected',
            'data' => [
                'document_id' => $designationDocument->id,
            ],
        ]);

        return redirect()->route('hr.designation-documents.index')
            ->with('success', 'Designation document rejected successfully.');
    }

    /**
     * Download designation document
     */
    public function download(DesignationDocument $designationDocument)
    {
        $this->authorize('view', $designationDocument);
        
        if (!Storage::disk('public')->exists($designationDocument->file_path)) {
            return abort(404, 'File not found');
        }

        return Storage::disk('public')->download($designationDocument->file_path);
    }
}
