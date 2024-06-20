<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link dashboard collapsed" href="{{ route('dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->
    <li class="nav-item">
      <a class="nav-link analytics collapsed" data-bs-target="#tables-nav" href="{{ route('analytics.index') }}">
        <i class="bi bi-graph-up-arrow"></i><span>Analytics</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link chat collapsed" data-bs-target="#tables-nav" href="{{ route('chat.index') }}">
        <i class="bi bi-chat-left-text"></i><span>Chat</span>
      </a>
    </li>
{{-- ============================================================ ADMIN ============================================================ --}}
    @if (Auth::user()->role == 9)
    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">AO/Violation</li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="aoViolationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-file-earmark-text"></i><span>AO/Violation</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="aoViolationDropdown">
        <li><a class="dropdown-item" href="{{ route('see.vio') }}"><span class="bi bi-plus-circle">Add Violation</a></li>
        <li><a class="dropdown-item" href="{{ route('edit.vio') }}"><span class="bi bi-pencil-square">Edit Violation</a></li>
        <li><a class="dropdown-item" href="{{ route('see.dep') }}"><span class="bi bi-plus-circle">Add Department</a></li>
        <li><a class="dropdown-item" href="{{ route('edit.deps') }}"><span class="bi bi-pencil-square">Edit Department</a></li>
        <li><a class="dropdown-item" href="{{ route('see.offi') }}"><span class="bi bi-plus-circle">Add Apprehending Officer</a></li>
        <li><a class="dropdown-item" href="{{ route('edit.offi') }}"><span class="bi bi-pencil-square">Edit Apprehending Officer</a></li>
        
      </ul>
    </li>
    
    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">Contested Case</li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="contestedCaseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-menu-button"></i><span>Contested Case</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="contestedCaseDropdown">
        <li><a class="dropdown-item" href="{{ route('tas.manage') }}"><span class="bi bi-plus-circle"></span> Add </a></li>
        <li><a class="dropdown-item" href="{{ route('tas.view') }}"><span class="bi bi-eye"></span> View </a></li>
        <li><a class="dropdown-item" href="{{ route('update.contest.index') }}"><span class="bi bi-pencil-square"></span> Update / Edit </a></li>
      </ul>
    </li>

    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">Admitted Case</li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="admittedCaseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-file-earmark-check"></i><span>Admitted Case</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="admittedCaseDropdown">
        <li><a class="dropdown-item" href="{{ route('admitted.manage') }}"><span class="bi bi-plus-circle"></span> Add </a></li>
        <li><a class="dropdown-item" href="{{ route('admitted.view') }}"><span class="bi bi-eye"></span> View </a></li>
        <li><a class="dropdown-item" href="{{ route('update.admit.index') }}"><span class="bi bi-pencil-square"></span> Update / Edit</a></li>
      </ul>
    </li>

    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-item">
      <a class="nav-link dropdown-toggle" href="#" id="casearv" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-file-earmark-check"></i><span>Case Archives</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="casearv">
        <li><a class="dropdown-item" href="{{ route('archivesmanage') }}"><span class="bi bi-plus-circle"></span> Add</a></li>
        <li><a class="dropdown-item" href="{{ route('archivesview') }}"><span class="bi bi-eye"></span> View</a></li>
        <li><a class="dropdown-item" href="{{ route('updatearchives') }}"><span class="bi bi-pencil-square"></span> Update / Edit</a></li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link dropdown-toggle" href="#" id="his" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-clock-history"></i><span>History</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="his">
        <li><a class="dropdown-item" href="{{ route('audit.trail') }}"><span class="bi bi-hourglass-split"></span> Audit Trail </a></li>
        {{-- <li><a class="dropdown-item" href="{{ route('admitted.view') }}"><span class="bi bi-eye"></span> View</a></li>
        <li><a class="dropdown-item" href="{{ route('update.admit.index') }}"><span class="bi bi-pencil-square"></span> Update / Edit</a></li> --}}
      </ul>
    </li>
    
    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">Manage Users</li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="{{ route('profile', ['id' => Auth::id()]) }}">
        <i class="bi bi-person"></i><span>Profile</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="{{route('user_management')}}">
        <i class="bi bi-person-fill-add"></i><span>User Management</span>
      </a>
    </li>

{{-- ============================================ ENCODER ====================================================================--}}
    @elseif (Auth::user()->role == 2)
    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">AO/Violation</li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="aoViolationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-file-earmark-text"></i><span>AO/Violation</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="aoViolationDropdown">
        <li><a class="dropdown-item" href="{{ route('see.vio') }}">Add Violation</a></li>
        <li><a class="dropdown-item" href="{{ route('see.dep') }}">Add Department</a></li>
        <li><a class="dropdown-item" href="{{ route('see.offi') }}">Add Apprehending Officer</a></li>
      </ul>
    </li>

    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">Contested Case</li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="contestedCaseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-menu-button"></i><span>Contested Case</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="contestedCaseDropdown">
        <li><a class="dropdown-item" href="{{ route('tas.manage') }}"><span class="bi bi-plus-circle"> Add</span></a></li>
        <li><a class="dropdown-item" href="{{ route('tas.view') }}"><span class="bi bi-eye"> View</span></a></li>
        <li><a class="dropdown-item" href="{{ route('update.contest.index') }}"><span class="bi bi-pencil-square"> Update / Edit</span></a></li>
      </ul>
    </li>

    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">Admitted Case</li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="admittedCaseDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-file-earmark-check"></i><span>Admitted Case</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="admittedCaseDropdown">
        <li><a class="dropdown-item" href="{{ route('admitted.manage') }}"><span class="bi bi-plus-circle"> Add</span></a></li>
        <li><a class="dropdown-item" href="{{ route('admitted.view') }}"><span class="bi bi-eye"> View</span></a></li>
        <li><a class="dropdown-item" href="{{ route('update.admit.index') }}"><span class="bi bi-pencil-square"> Update / Edit </span></a></li>
      </ul>
    </li>

    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-item">
      <a class="nav-link dropdown-toggle" href="#" id="casearv" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-file-earmark-check"></i><span>Case Archives</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="casearv">
        <li><a class="dropdown-item" href="{{ route('archivesmanage') }}"><span class="bi bi-plus-circle"></span> Add</a></li>
        <li><a class="dropdown-item" href="{{ route('archivesview') }}"><span class="bi bi-eye"></span> View</a></li>
        <li><a class="dropdown-item" href="{{ route('updatearchives') }}"><span class="bi bi-pencil-square"></span> Update / Edit</a></li>
      </ul>
    </li>
   
{{-- ================================================================= VIEWER ===================================================================================== --}}
    @else

    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">Contested Case</li>
    <li class="nav-item">
      <a class="nav-link contested collapsed" href="{{ route('tas.view') }}">
        <i class="bi bi-layout-text-window-reverse"></i><span>View TAS</span>
      </a>
    </li>
    
    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-heading">Admitted Case</li>
    <li class="nav-item">
      <a class="nav-link admitted collapsed" href="{{ route('admitted.view') }}">
        <i class="bi bi-layout-text-window-reverse"></i><span>View TAS</span>
      </a>
    </li>
    
    <li class="nav-heading" style="border-bottom: 1px solid #000;"></li>
    <li class="nav-item">
      <a class="nav-link dropdown-toggle" href="#" id="casearv" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-file-earmark-check"></i><span>Case Archives</span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="casearv">
        <li><a class="dropdown-item" href="{{ route('admitted.view') }}"><span class="bi bi-eye"></span> View</a></li>
      </ul>
    </li>
    
  </li>
    @endif
  </ul>
</aside><!-- End Sidebar -->
