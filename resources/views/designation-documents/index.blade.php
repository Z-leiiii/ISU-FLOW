@extends('layouts.app')

@section('title', 'My Designation Documents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-file-contract"></i> My Designation Documents</h1>
    <a href="{{ route('designation-documents.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Upload New Document
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Designation Documents</h5>
    </div>
    <div class="card-body">
        @if($documents->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Document Title</th>
                            <th>Designation</th>
                            <th>Status</th>
                            <th>Uploaded Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                            <tr>
                                <td>{{ $document->document_title }}</td>
                                <td>{{ $document->designation->title }}</td>
                                <td>
                                    <span class="badge badge-{{ $document->status }}">
                                        {{ ucfirst($document->status) }}
                                    </span>
                                    @if($document->is_current)
                                        <span class="badge bg-info ms-1">Current</span>
                                    @endif
                                </td>
                                <td>{{ $document->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('designation-documents.show', $document) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('designation-documents.download', $document) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        @if($document->status === 'pending')
                                            <a href="{{ route('designation-documents.edit', $document) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('designation-documents.destroy', $document) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $documents->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="fas fa-file-contract fa-3x mb-3"></i>
                <h4>No Designation Documents</h4>
                <p>You haven't uploaded any designation documents yet.</p>
                <a href="{{ route('designation-documents.create') }}" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Your First Document
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
