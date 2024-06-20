@if ($remarks !== null)
    <div class="remarks-list">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($remarks as $remark)
                        <tr>
                            <td>
                                <div class="input-group">
                                    <textarea class="form-control bi bi-bookmark-check-fill" rows="3" readonly>{{ str_replace(['"', '[', ']'], '', $remark) }}</textarea>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <p>No remarks available.</p>
@endif