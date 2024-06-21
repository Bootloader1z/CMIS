<form id="editViolationForm{{ $recentViolationsToday->id }}"  action="{{ route('violations.updateTas', ['id' => $recentViolationsToday->id]) }}" method="POST" enctype="multipart/form-data">
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
                            <input type="text" class="form-control" id="resolutionNo{{ $recentViolationsToday->id }}" name="case_no" value="{{ $recentViolationsToday->case_no }}">
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
                        <th>Fine fee</th>
                        <td>
                            <input type="text" class="form-control" id="fine_fee{{ $recentViolationsToday->id }}" name="fine_fee" value="{{ $recentViolationsToday->fine_fee }}">
                        </td>
                    </tr>
                    <tr>
                        <th>Type of Vehicle</th>
                        <td>
                            <input type="text" class="form-control" id="typeofvehicle{{ $recentViolationsToday->id }}" name="typeofvehicle" value="{{ $recentViolationsToday->typeofvehicle }}">
                        </td>
                    </tr>
                    <tr>
    <th class="text-start">Case Status</th>
    <td class="text-start">
        <select class="form-select" id="status{{ $recentViolationsToday->id }}" name="status">
            <option value="in-progress" {{ $recentViolationsToday->status == 'in-progress' ? 'selected' : '' }}>In-Progress</option>
            <option value="closed" {{ $recentViolationsToday->status == 'closed' ? 'selected' : '' }}>Close Case</option>
            <option value="settled" {{ $recentViolationsToday->status == 'settled' ? 'selected' : '' }}>Settled</option>
           
        </select>
    </td>
</tr>

                  <tr>
                        <th>Remarks</th>
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
    <input type="file" class="form-control" name="file_attach_existing[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"  multiple>
    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                {{-- <button type="submit" class="btn btn-success ">Save changes</button> --}}
                                <button type="button" class="btn btn-primary" onclick="saveChangesAndReloadModal({{ $recentViolationsToday->id }})">Save changes</button>
                                <button type="button" class="btn btn-danger delete-cases" data-violation-id="{{ $recentViolationsToday->id }}">Delete Case</button>
                    </div>
                    </form>
            </div>
        </div>
    </div>
 
<!-- History section -->
<div class="col-md-12">
    <h6 class="fw-bold mt-4">History</h6>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>User</th>
                    <th>Timestamp</th>
                    <th>Changes</th>
                </tr>
            </thead>
            <tbody>
                @if ($recentViolationsToday->history)
                    @foreach($recentViolationsToday->history as $historyItem)
                        <tr>
                            <td>{{ $historyItem['action'] }}</td>
                            <td>
                                @if (isset($historyItem['fullname']))
                                    {{ $historyItem['fullname'] }}
                                @elseif (isset($historyItem['username']))
                                    {{ $historyItem['username'] }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($historyItem['timestamp'])->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <ul class="list-group">
                                    @foreach($historyItem['changes'] as $field => $values)
                                        <li class="list-group-item">
                                            <strong>{{ $field }}:</strong>
                                            @if (is_array($values) && isset($values['old_value']) && isset($values['new_value']))
                                                <span class="badge bg-primary">{{ is_string($values['old_value']) ? htmlspecialchars($values['old_value'], ENT_NOQUOTES) : 'N/A' }}</span>
                                                <i class="bi bi-caret-right mx-2"></i>
                                                <span class="badge bg-success">{{ is_string($values['new_value']) ? htmlspecialchars($values['new_value'], ENT_NOQUOTES) : 'N/A' }}</span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">No history available</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

