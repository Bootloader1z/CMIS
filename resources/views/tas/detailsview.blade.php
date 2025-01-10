


<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Case Information</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td class="fw-bold" style="width: 30%;">Case No:</td>
                            <td>{{ $tasFile->case_no }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Driver:</td>
                            <td>{{ $tasFile->driver }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Contact No:</td>
                            <td>{{ $tasFile->contact_no }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">TOP:</td>
                            <td>{{ $tasFile->top ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Transaction No:</td>
                            <td>{{ $tasFile->transaction_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Received Date:</td>
                            <td>{{ $tasFile->date_received }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Plate No:</td>
                            <td>{{ $tasFile->plate_no }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Apprehending Officer:</td>
                            <td>{{ $tasFile->apprehending_officer ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date Recorded:</td>
                            <td>{{ $tasFile->created_at }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Case Status:</td>
                            <td style="background-color: {{ getStatusColor($tasFile->status) }};">{{ $tasFile->status }}</td>
                        </tr>

                        <tr>
                            <td class="fw-bold">Type of Vehicle:</td>
                            <td>{{ $tasFile->typeofvehicle }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Record Status</td>
                            <td class="{{ symbolBgColor($tasFile->symbols) }} text-white">{{ $tasFile->symbols }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Violation Details</h5>
            </div>
            <div class="card-body mt-3">
                <div class="mb-4">
                    <h6 class="text-muted">Violations:</h6>
                    @if (isset($relatedViolations) && !is_array($relatedViolations) && $relatedViolations->count() > 0)
                        <ul class="list-unstyled">
                            @foreach ($relatedViolations as $violation)
                                <li>{{ $violation->code }} - {{ $violation->violation }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No violations recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Remarks</h5>
            </div>
            <div class="card-body mt-3">
                @include('remarksupdate', ['remarks' => $remarks])

                <form action="{{ route('save.remarks') }}" id="remarksForm" method="POST" class="remarksForm">
                    @csrf
                    <input type="hidden" name="tas_file_id" value="{{ $tasFile->id }}">
                    <div class="mt-3">
                        <label for="remarks" class="form-label">Add Remark</label>
                        <hr>
                        <textarea class="form-control" name="remarks" id="remarks" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" id="saveRemarksBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Save Remarks
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">File Attachments</h5>
            </div>
            <div class="card-body mt-3">
                @if (!is_null($tasFile->file_attach))
                    @php
                        $decodedFiles = json_decode($tasFile->file_attach, true);
                    @endphp
                    @if (!is_null($decodedFiles))
                        <ol id="attachmentList">
                            @foreach ($decodedFiles as $filePath)
                                <li>
                                    <i class="bi bi-paperclip me-1"></i>
                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank">{{ basename($filePath) }}</a>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p>No attachments available.</p>
                    @endif
                @else
                    <p>No attachments available.</p>
                @endif
                <form id="uploadForm" action="{{ route('upload.file.contest', ['id' => $tasFile->id]) }}" method="POST" enctype="multipart/form-data" target="_self">
                    @csrf
                    <div class="form-group">
                        <label for="file_attach">File Attachment</label>
                        <input type="file" class="form-control" id="file_attach" name="file_attach" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </form>

            </div>
        </div>
    </div>
</div>


    <div class="modal-footer">
        {{-- <a href="{{ route('print.sub', ['id' => $tasFile->id]) }}" class="btn btn-primary" onclick="openInNewTabAndPrint('{{ route('print.sub', ['id' => $tasFile->id]) }}'); return false;">
            <span class="bi bi-printer"></span> Print Subpeona
        </a>  --}}
        <form action="{{ route('print.sub', ['id' => $tasFile->id]) }}" method="GET" target="_blank">

            <button type="submit" class="btn btn-primary " name="details" value="motionrelease1">M w/ MR</button>
            <button type="submit" class="btn btn-primary " name="details" value="motionrelease2">M w/o MR</button>
            <button type="submit" class="btn btn-primary " name="details" value="subpeona">Subpeona</button>
        </form>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#finishModal{{ $tasFile->id }}">Settled</button>
        <form action="{{ route('update.status', ['id' => $tasFile->id]) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-danger" name="status" value="closed">Close Case</button>
            <button type="submit" class="btn btn-warning" name="status" value="in-progress">In-progress</button>
        </form>
    </div>
