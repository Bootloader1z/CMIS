

@section('title', env('APP_NAME'))

@include('layouts.title')

<body>

  <!-- ======= Header ======= -->
@include('layouts.header')

  <!-- ======= Sidebar ======= -->
 @include('layouts.sidebar')

  <main id="main" class="main">
  <section class="section dashboard">
    <div class="row">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="sidebarUsers card h-100">
                        <div class="card-header bg-primary text-white">
                            Online Users
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach($users as $user)
                                    <li class="user list-group-item d-flex align-items-center" data-user-id="{{ $user->id }}" data-last-active="{{ $user->isactive ? $user->updated_at->diffForHumans() : '' }}">
                                        @if($user->profile_pic)
                                            <img src="{{ $user->profile_pic }}" alt="{{ $user->fullname }}" class="avatar me-2 rounded-circle">
                                        @else
                                            <i class="bi bi-person-workspace avatar-icon me-2"></i>
                                        @endif
                                        <div class="flex-grow-1">
                                            <span class="username">{{ $user->fullname }}</span>
                                            <br>
                                            <small class="status {{ $user->isactive ? 'text-success' : 'text-muted' }}">{{ $user->isactive ? 'Online' : 'Offline' }}</small>
                                        </div>
                                        <div class="last-active-bubble ms-auto small text-muted">{{ $user->isactive ? $user->updated_at->diffForHumans() : '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white d-flex align-items-center">
                            <span class="flex-grow-1" id="chatHeader">Chat</span>
                            <button class="btn btn-outline-light btn-sm" id="closeChat">&times;</button>
                        </div>
                        <div class="card-body p-0">
                            <div class="chat-container overflow-auto p-3" id="chatContainer">
                                <div class="chat-box" id="chatBox">
                                    <!-- Messages will appear here -->
                                    @foreach($messages as $message)
                                        <div class="message{{ $message->user->id == Auth::user()->id ? ' outgoing' : ' incoming' }} mb-2">
                                            <div class="message-details{{ $message->user->id == Auth::user()->id ? ' text-end' : '' }}">
                                                <span class="message-sender"><strong>{{ $message->user->fullname }}</strong></span>
                                                <span class="message-time">{{ $message->created_at_formatted }} <i class="bi bi-clock"></i> </span>
                                                <span class="message-icon">
                                                    @if($message->is_read)
                                                        <i class="bi bi-book-fill"></i>
                                                    @else
                                                        <i class="bi bi-book"></i>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="message-content{{ $message->user->id == Auth::user()->id ? ' outgoing' : ' incoming' }}" onclick="displayMessage('{{ $message->message }}')">{{ $message->message }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <form id="chatForm" action="{{ route('chat.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="receiver_id" id="receiverId" value="">
                                <div class="input-group">
                                    <input type="text" class="form-control chat-input" placeholder="Type your message..." id="messageInput" name="message">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var currentUserId = null; // Track the current active user
        var pollInterval = null; // Track the polling interval

        // Function to update the message icon based on its read status
        function updateMessageIcon(message) {
            var isRead = message.find('.message-icon').data('is-read');
            var icon = isRead ? '<i class="bi bi-book-fill"></i>' : '<i class="bi bi-book"></i>';
            message.find('.message-icon').html(icon);
        }

        // Function to display the last active time on hover
        function displayLastActive() {
            $('.user').hover(function() {
                var lastActive = $(this).data('last-active');
                if (lastActive) {
                    $(this).append('<span class="last-active">' + lastActive + '</span>');
                }
            }, function() {
                $(this).find('.last-active').remove();
            });
        }

        displayLastActive();

        // Function to handle click events on user elements
        $('.user').click(function() {
            var userId = $(this).data('user-id');
            var username = $(this).find('.username').text();
            $('#receiverId').val(userId);
            $('#chatHeader').text('Chat with ' + username);
            startChat(userId); // Call function to start chatting with the clicked user
            $('#chatBox').removeClass('hidden'); // Show the chatbox when starting a chat
        });

        // Event listener for chat form submission
        $('#chatForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {
                    if (response.message && response.newMessage) {
                        toastr.success(response.message);
                        var messageHtml = '<div class="message' + (response.newMessage.user_id == response.user.id ? ' outgoing' : ' incoming') + '">' +
                            '<div class="message-details' + (response.newMessage.user_id == response.user.id ? ' text-end' : '') + '">' +
                            '<span class="message-sender"><strong>' + response.user.fullname + '</strong></span>' +
                            '<span class="message-time">' + moment(response.newMessage.created_at).format('MMM D, YYYY h:mm:ss A') + ' <i class="bi bi-clock"></i> </span>' +
                            '<span class="message-icon"></span>' +
                            '</div>' +
                            '<div class="message-content' + (response.newMessage.user_id == response.user.id ? ' outgoing' : ' incoming') + '">' + response.newMessage.message + '</div>' +
                            '</div>';
                        $('#chatBox').append(messageHtml); // Append the new message at the bottom
                        $('#messageInput').val('');
                        scrollToBottom();
                        var newMessage = $('#chatBox').find('.message').last(); // Find the newly added message
                        updateMessageIcon(newMessage);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error(xhr.responseJSON.error || 'Failed to send message.');
                }
            });
        });

        // Function to start a chat with a user
        function startChat(userId) {
            // If a different user is selected, clear the existing polling
            if (currentUserId !== userId) {
                currentUserId = userId;
                clearInterval(pollInterval);
                fetchChatData(userId);
            }
        }

        // Function to fetch chat data for a user using long polling
        function fetchChatData(userId) {
            function poll() {
                $.ajax({
                    url: '{{ route('chat.messages', ['userId' => ':userId']) }}'.replace(':userId', userId),
                    type: 'GET',
                    success: function(response) {
                        console.log("Fetched chat data for user ID: " + userId, response);
                        if (response.messages) {
                            $('#chatBox').empty();
                            for (var i = response.messages.length - 1; i >= 0; i--) {
                                var message = response.messages[i];
                                var messageHtml = '<div class="message' + (message.user.id == response.user.id ? ' outgoing' : ' incoming') + '">' +
                                    '<div class="message-details' + (message.user.id == response.user.id ? ' text-end' : '') + '">' +
                                    '<span class="message-sender"><strong>' + message.user.fullname + '</strong></span>' +
                                    '<span class="message-time">' + ' <i class="bi bi-clock"></i> </span>' + moment(message.created_at).format('MMM D, YYYY h:mm:ss A') +
                                    '<span class="message-icon" data-is-read="' + (message.is_read ? 'true' : 'false') + '"></span>' +
                                    '</div>' +
                                    '<div class="message-content' + (message.user.id == response.user.id ? ' outgoing' : ' incoming') + '">' + message.message + '</div>' +
                                    '</div>';
                                $('#chatBox').append(messageHtml);
                                updateMessageIcon($('#chatBox').find('.message').last());
                            }
                            $('#chatBox').show();
                            scrollToBottom();
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to fetch chat data.');
                    }
                });
            }

            // Start polling every 5 seconds
            poll(); // Initial call
            pollInterval = setInterval(poll, 5000);
        }

        // Function to scroll to the bottom of the chat container
        function scrollToBottom() {
            var chatContainer = document.getElementById('chatContainer');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Initial code to run when the document is ready
        $('.sidebarUsers').addClass('show');
    });
</script>

 
  </main><!-- End #main -->

 @include('layouts.footer')
</body>

</html>