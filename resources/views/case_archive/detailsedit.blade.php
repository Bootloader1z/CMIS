
<form id="editViolationForm{{ $recentViolationsToday->id }}"  action="{{ route('updatedetailarchives', ['id' => $recentViolationsToday->id]) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="container">
<div class="row">
<div class="col-md-12">
<!-- Violation details section -->
<h6 class="fw-bold mb-3">Violation Details</h6>
<table class="table table-bordered">
    <tbody>
        <tr>
            <th>Case No.</th>
            <td>
                <input type="text" class="form-control" id="resolutionNo{{ $recentViolationsToday->id }}" name="tas_no" value="{{ $recentViolationsToday->tas_no }}">
            </td>
        </tr>
        <tr>
            <th>TOP</th>
            <td>
                <input type="text" class="form-control" id="top{{ $recentViolationsToday->id }}" name="top" value="{{ $recentViolationsToday->top }}">
            </td>
        </tr>
        <tr>
            <th>Driver</th>
            <td>
                <input type="text" class="form-control" id="driver{{ $recentViolationsToday->id }}" name="driver" value="{{ $recentViolationsToday->driver }}">
            </td>
        </tr>
        <tr>
            <th>Apprehending Officer</th>
            <td>
                <input type="text" class="form-control" id="apprehendingOfficer{{ $recentViolationsToday->id }}" name="apprehending_officer" value="{{ $recentViolationsToday->apprehending_officer }}">
            </td>
        </tr>
    </tbody>
</table>

<!-- Violations section -->
<h6 class="fw-bold mb-3">Violations</h6>
<div id="violationsContainer{{ $recentViolationsToday->id }}">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Violation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($recentViolationsToday->violation))
                @foreach(json_decode($recentViolationsToday->violation) as $index => $recentViolationsTodayItem)
                    <tr id="violationField{{ $recentViolationsToday->id }}_{{ $index }}">
                        <td>
                            <input type="text" class="form-control" id="violation{{ $recentViolationsToday->id }}_{{ $index }}" name="violations[]" value="{{ $recentViolationsTodayItem }}" list="suggestions" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger bi bi-trash3-fill" onclick="deleteViolation('{{ $recentViolationsToday->id }}', {{ $index }})"></button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="input-group mt-2">
        <span class="bi bi-bookmark-plus input-group-text custom-new-badge" onclick="addNewViolation({{ $recentViolationsToday->id }})"></span>
        <input type="text" class="form-control" id="violation{{ $recentViolationsToday->id }}_new" name="violation[]" value="" list="datl" placeholder="Add new Violation">
        <datalist id="datl">
            @foreach ($violationz as $vio)
            <option value="{{ $vio->code }}">{{ $vio->violation }}</option>
            @endforeach
        </datalist>
    </div>
</div>
</div>
</div>

<div class="row mt-4">
<div class="col-md-12">
<!-- Additional details section -->
<h6 class="fw-bold mb-3">Additional Details</h6>
<table class="table table-bordered">
    <tbody>
        <tr>
            <th>Transaction No.</th>
            <td>
                <input type="text" class="form-control" id="transactionNo{{ $recentViolationsToday->id }}" name="transaction_no" value="{{ $recentViolationsToday->transaction_no }}">
            </td>
        </tr>
        <tr>
            <th>Date Received</th>
            <td>
                <input type="date" class="form-control" id="dateReceived{{ $recentViolationsToday->id }}" name="date_received" value="{{ $recentViolationsToday->date_received }}">
            </td>
        </tr>
        <tr>
            <th>Plate No.</th>
            <td>
                <input type="text" class="form-control" id="plateNo{{ $recentViolationsToday->id }}" name="plate_no" value="{{ $recentViolationsToday->plate_no }}">
            </td>
        </tr>
        <tr>
            <th>Contact No.</th>
            <td>
                <input type="text" class="form-control" id="contactNo{{ $recentViolationsToday->id }}" name="contact_no" value="{{ $recentViolationsToday->contact_no }}">
            </td>
        </tr>
        <tr>
            <th>Remarks 
                <span style="color: red"><br>Note: 5 remarks can deleted</span>
            </th>
<td>

@foreach ($remarks as $index => $remark)
    <div class="row mb-2" id="remark_row_{{ $recentViolationsToday->id }}_{{ $index }}">
        <div class="col-md-12">
            <div class="input-group">
                <span class="input-group-text bi bi-clipboard-check"></span>
                <input type="text" class="form-control" id="text{{ $recentViolationsToday->id }}_{{ $index }}" name="remarks[{{ $index }}]" value="{{ str_replace(['"', '[', ']'], '', $remark) }}" placeholder="Remark">
                <button type="button" class="btn btn-danger bi bi-trash3-fill" onclick="deleteRemark('{{ $recentViolationsToday->id }}', {{ $index }})"></button>
            </div>
        </div>
    </div>
@endforeach

</td>
</tr>
    </tbody>
</table>
</div>
</div>

    
    <hr>

    @php
    $attachments = json_decode($recentViolationsToday->file_attach, true);
    @endphp

    @if (!empty($attachments))
    @foreach ($attachments as $index => $attachment)
        <div class="row mb-2" id="attachment_row_{{ $recentViolationsToday->id }}_{{ $index }}">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-text bi bi-paperclip"></span>
                    <input type="text" class="form-control" id="attachmentText{{ $recentViolationsToday->id }}_{{ $index }}" name="attachments[{{ $index }}]" value="{{ $attachment }}" readonly>
                    <button type="button" class="btn btn-danger bi bi-trash3-fill delete-attachment" data-violation-id="{{ $recentViolationsToday->id }}" data-attachment="{{ $attachment }}" data-index="{{ $index }}"></button>
                </div>
            </div>
        </div>
    @endforeach
    @endif

    <div class="input-group mt-2">
    <input type="file" class="form-control" name="file_attach_existing[]" multiple>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        
                {{-- <button type="submit" class="btn btn-success ">Save changes</button> --}}
                <button type="button" class="btn btn-primary" onclick="saveChangesAndReloadModal({{ $recentViolationsToday->id }})">Save changes</button>
                <button type="button" class="btn btn-danger delete-cases" data-violation-id="{{ $recentViolationsToday->id }}">Delete Case</button>
    </div>
</form>




