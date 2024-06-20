@foreach($officers as $index => $officer)
    <tr class="{{ $index < 3 ? 'top-officer' : '' }}"> <!-- Highlight top 3 officers -->
        <th scope="row">{{ $index + $officers->firstItem() }}</th>
        <td>
            <a href="#" data-bs-toggle="modal" data-bs-target="#officerModal{{ $index }}">{{ $officer->apprehending_officer ?: 'Unknown' }}</a>
        </td>
        <td>{{ $officer->department }}</td>
        <td>{{ $officer->total_cases }}</td>
 
    </tr>
@endforeach
