 

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Case Information</h5>
            </div>
            <div class="card-body mt-3">
                <div class="mb-4">
                    <h6 class="text-muted">Admitted No:</h6>
                    <p class="fw-bold">{{ $admitted->admittedno }}</p>
                </div>
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">Driver:</h6>
                    <p class="fw-bold">{{ $admitted->driver }}</p>
                </div>
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">Contact No:</h6>
                    <p class="fw-bold">{{ $admitted->contact_no }}</p>
                </div>
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">TOP:</h6>
                    <p class="fw-bold">{{ $admitted->top ?? 'N/A' }}</p>
                </div>
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">Transaction No:</h6>
                    <p class="fw-bold">{{ $admitted->transaction_no ?? 'N/A' }}</p>
                </div>
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">Received Date:</h6>
                    <p class="fw-bold">{{ $admitted->date_received }}</p>
                </div>
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
                    <h6 class="text-muted">Plate No:</h6>
                    <p class="fw-bold">{{ $admitted->plate_no }}</p>
                </div>
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">Apprehending Officer:</h6>
                    <p class="fw-bold">{{ $admitted->apprehending_officer ?? 'N/A' }}</p>
                </div>
                <hr>
                <div class="mb-4">
                    <h6 class="text-muted">Date Recorded:</h6>
                    <p class="fw-bold">{{ $admitted->created_at }}</p>
                </div>
                <hr>
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

                <form action="{{ route('admitremark') }}" id="remarksForm" method="POST" class="remarksForm">
                    @csrf
                    <input type="hidden" name="admitted_dataid" value="{{ $admitted->id }}">
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
                @if (!is_null($admitted->file_attach))
                    @php
                        $decodedFiles = json_decode($admitted->file_attach, true);
                    @endphp
                    @if (!is_null($decodedFiles))
                        <ol>
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
                <form action="{{ route('upload.file.admit', ['id' => $admitted->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file_attach">File Attachments</label>
                        <input type="file" class="form-control" id="file_attach"  name="file_attach[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"  multiple>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Files</button>
                </form>
                
            </div>
        </div>
    </div>
</div>


<div class="modal-footer">
    <form action="{{ route('print.sub', ['id' => $admitted->id]) }}" method="GET" target="_blank">

        <button type="submit" class="btn btn-primary " name="details" value="motionrelease1">Manual MR</button>
        <button type="submit" class="btn btn-primary " name="details" value="motionrelease2">MR</button>
        <button type="submit" class="btn btn-primary " name="details" value="subpeona">Subpeona</button>
    </form>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#finishModal{{ $admitted->id }}">Settled</button>
    <form action="{{ route('updateStatusadmitted', ['id' => $admitted->id]) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-danger" name="status" value="closed">Close Case</button>
        <button type="submit" class="btn btn-warning" name="status" value="in-progress">In-progress</button>
    </form>
</div>
<div class="modal fade" id="finishModal{{ $admitted->id }}" tabindex="-1" role="dialog" aria-labelledby="finishModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form action="{{ route('finishCase_admitted', ['id' => $admitted->id]) }}" method="POST"> @csrf <div class="modal-header">
            <h5 class="modal-title" id="finishModalLabel">Finish Case</h5>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="fine_fee">Fine Fee</label>
              <input type="number" step="0.01" class="form-control" id="fine_fee" name="fine_fee" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Finish</button>
          </div>
        </form>
      </div>
    </div>
  </div>