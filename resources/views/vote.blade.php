@extends('layouts.app')

@section('content')
    <div class="row">
        <h3 class="text-center">Voting System with Linkable Ring Signature</h3>
        <div class="jumbotron text-center">
            <div class="row">
                <p id="message"></p>
                {{-- @dd($agenda) --}}
                @if ($agenda)
                     <div class="col-md-12 p-4">
                        {{-- <input type="hidden" id="signer_public_key" value="{{ $agenda->signature->pk_signer }}"> --}}
                        <p>{{ $agenda->title }}</p>
                        @foreach ($agenda->agendaAnswers as $answer)
                            <input type="radio" class="form-control vote-radio" id="answer-{{ $answer->id }}" name="answer" value="{{ $answer->id }}"><br>
                            <label for="answer">{{ $answer->answer }}</label>
                        @endforeach
                        
                    </div>
                @else  
                    <p class="text-danger">Malicious voting!</p>
                @endif
            </div>

           
        </div>
        <div class="row">
            <div class="form-control-wrap mb-4" style="display: flex; align-items: center;">
                <label for="voter-sk">Secret Key: </label>&nbsp;
                <input type="text" class="form-control" id="voter-sk" value="*******************************" readonly style="flex: 1; margin-right: 10px;">
                <div class="form-icon form-icon-right">
                    <i style="cursor: pointer" class="bi bi-copy copy-sk-btn" onclick="copyKey('voter-sk')">Copy</i>
                </div>
            </div> 
            
            <div class="form-control-wrap" style="display: flex; align-items: center;">
                <label for="voter-pk">Public Key: </label>&nbsp;
                <input type="text" class="form-control" id="voter-pk" value="*******************************" readonly style="flex: 1; margin-right: 10px;">
                <div class="form-icon form-icon-right">
                    <i style="cursor: pointer" class="bi bi-copy copy-pk-btn" onclick="copyKey('voter-pk')">Copy</i>
                </div>
            </div>  
              
        </div>
      
    </div>
@endsection

@section('js')
    <script src="js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let agendaId = "{{ request('id') }}";
        let token = "{{ request('token') }}";
        let storedSecretKey = localStorage.getItem('sk');
        let storedPublicKey = localStorage.getItem('pk');
        let routeUrl = "{{ route('voting.page') }}";
        let fullUrl = routeUrl + '?id=' + encodeURIComponent(agendaId) + '&token=' + encodeURIComponent(token);

        if (storedSecretKey) {
            document.getElementById('voter-sk').value = storedSecretKey;
        }

        if (storedPublicKey) {
            document.getElementById('voter-pk').value = storedPublicKey;
        }
        
        function copyKey(inputId) {
            var keyInput = document.getElementById(inputId);
            if (keyInput == "*******************************") {
                alert('Invalid key');
                return;
            }
            keyInput.select();
            keyInput.setSelectionRange(0, 99999); 
            document.execCommand('copy');
            alert('Key copied to clipboard!');
        }

        
        
        $(document).ready(function() {
            let signer_public_key = localStorage.getItem('pk');
            $('input[type=radio][name=answer]').change(function() {
                let selectedAnswerId = $(this).val();
                let successRoute = "{{ route('success.page') }}"
                let successUrl = successRoute + '?id=' + encodeURIComponent(agendaId) + '&token=' + encodeURIComponent(token);
                
                $.ajax({
                    url: "{{ route('vote.post') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        answer_id: selectedAnswerId, 
                        id: agendaId,
                        signer_public_key: signer_public_key
                    },
                    success: function(response) {
                        if(response.status) {
                            alert(response.message);
                            window.location.href = successUrl;
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('An error occurred while submitting your vote:', error);
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection