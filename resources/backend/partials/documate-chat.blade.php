<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title mb-0">DOCUMATE Assistant</h3>
    </div>
    <div class="card-body">
        <p class="doc-note small mb-3">
            Ask about signatories, office locations, transaction steps, appointment schedules, clearance reminders, or handbook guidance.
        </p>
        <div class="doc-search-bar mb-3">
            <textarea id="documate-chat-input" class="form-control border-0" rows="3" placeholder="Example: How many morning appointment slots are available for DOCUMATE?"></textarea>
        </div>
        <div class="doc-chip-row mb-3">
            <button type="button" class="btn btn-sm doc-suggestion-btn documate-chat-suggestion" data-message="Where is the VPSD office?">VPSD office</button>
            <button type="button" class="btn btn-sm doc-suggestion-btn documate-chat-suggestion" data-message="What are the steps for F-SDM-004?">F-SDM-004 steps</button>
            <button type="button" class="btn btn-sm doc-suggestion-btn documate-chat-suggestion" data-message="How many appointment slots are available each day?">Appointment slots</button>
            <button type="button" class="btn btn-sm doc-suggestion-btn documate-chat-suggestion" data-message="How does clearance affect transactions?">Clearance rules</button>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="doc-note small mb-2 mb-md-0">Tip: ask using a form code or office name for faster guidance.</div>
            <button type="button" id="documate-chat-send" class="btn btn-primary">Ask Assistant</button>
        </div>
        <div id="documate-chat-response" class="doc-chat-response border rounded p-3 bg-light mt-3 text-muted">
            The assistant reply will appear here.
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('documate-chat-input');
            const responseBox = document.getElementById('documate-chat-response');
            const sendButton = document.getElementById('documate-chat-send');
            const defaultLabel = sendButton ? sendButton.textContent.trim() : 'Ask Assistant';

            if (!input || !responseBox || !sendButton) {
                return;
            }

            const setReply = function(message, isError = false) {
                responseBox.textContent = message;
                responseBox.classList.toggle('text-danger', isError);
                responseBox.classList.toggle('text-muted', !isError);
            };

            document.querySelectorAll('.documate-chat-suggestion').forEach(function(button) {
                button.addEventListener('click', function() {
                    input.value = button.dataset.message || '';
                    sendButton.click();
                });
            });

            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    sendButton.click();
                }
            });

            sendButton.addEventListener('click', async function() {
                const message = input.value.trim();

                if (!message) {
                    setReply('Type a DOCUMATE question first.', true);
                    return;
                }

                setReply('Checking DOCUMATE guidance...');
                sendButton.disabled = true;
                sendButton.textContent = 'Checking...';

                try {
                    const reply = await fetch('{{ route('chat.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            message: message,
                        }),
                    });

                    const data = await reply.json();
                    setReply(data.reply || data.error || 'No response available.', !reply.ok);
                } catch (error) {
                    setReply('The assistant is unavailable right now. Please try again.', true);
                } finally {
                    sendButton.disabled = false;
                    sendButton.textContent = defaultLabel;
                }
            });
        });
    </script>
@endpush
