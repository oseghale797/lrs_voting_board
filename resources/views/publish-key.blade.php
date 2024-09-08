@extends('layouts.app')

@section('content')
    <div class="row">
        <h3 class="text-center">Voting System with Linkable Ring Signature</h3>
        <div class="jumbotron text-center">
            <div class="row">
                <p>Click the button below to publish your keys</p>
                <p id="message"></p>
                    <div class="col-md-12">
                        
                            <div class="col-md-12">
                                <button  class="btn btn-sm btn-primary" id="publish-key-btn">Publish</button>
                            </div>
                        
                    </div>
              
            </div>
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
@endsection

@section('js')
    <script src="js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/crypto/elliptic.min.js') }}"></script>
    <script src="{{ asset('assets/js/crypto/custom.js') }}"></script>
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

            $('#publish-key-btn').on('click', async function() {
                
                await handlePublishKey();
            });

            async function handlePublishKey()
            {
                try {
                    if (storedPublicKey == undefined) {
                        alert('Unknown public key');
                        let routeUrl = "{{ route('key.generate') }}";
                        let voting_page = routeUrl + '?id=' + encodeURIComponent(agendaId) + '&token=' + encodeURIComponent(token);

                        window.location.href = voting_page;
                       
                    }
                    let response = await $.ajax({
                        url: "{{ route('key.publish.post') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: agendaId,
                            token: token,
                            public_key: storedPublicKey
                        }
                    });
                    
                    if (response.status) {
                        alert(response.message);
                        $('#message').text(response.message);
                    } else {
                        alert(response.message);
                    }
                } catch (error) {
                    alert('An error occurred while publishing the key.');
                    console.error(error);
                }      
            }

            // Define the function to be executed every 1 minute
            function countPublishedKey() {
                $.ajax({
                    url: "{{ route('key.publish.count') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        id: agendaId,
                        token: token
                    },
                    success: async function(response) {
                        console.log(response);
                        if (response.status && response.count === 3) {
                            console.log(response.publishedKeys);
                            
                            const ring = createRing(response.publishedKeys);
                            
                            if (ring.status) {
                                try {
                                    await signVote();
                                } catch (error) {
                                    console.error('Error in signVote:', error);
                                }
                            } else {
                                alert(ring.message);
                                return;
                            }

                        } else {
                            // alert(response.message || "Insufficient published keys.");
                        }
                    },
                    error: function(xhr) {
                        console.error('An error occurred');
                    }
                });
            }

            
            async function signVote() {
                try {
                    const signedMessage = await signMessage(storedSecretKey, agendaId);
                   
                    if (signedMessage.status) {
                        const signatureVerification = await verifySignature(storedPublicKey, signedMessage.signature, agendaId);
                        if (signatureVerification.status) {
                           
                            let response = await $.ajax({
                                url: "{{ route('key.sign.vote') }}",
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                data: {
                                    id: agendaId,
                                    public_key: storedPublicKey,
                                    linkability_tag: signedMessage.linkabilityTag,
                                    signature: signedMessage.convertedSignature.r+signedMessage.convertedSignature.s,
                                    message: signedMessage.messageHash
                                }
                            });
                            if(response.status) {
                                console.log(response);
                                window.location.href = fullUrl;
                            } else {
                                alert(response.message);
                                let routeUrl = "{{ route('bulletin.page') }}";
                                let fullUrl = routeUrl + '?id=' + encodeURIComponent(agendaId) + '&token=' + encodeURIComponent(token);
                                window.location.href = fullUrl;
                            }
                        }else {
                            alert(signatureVerification.message);
                            let routeUrl = "{{ route('bulletin.page') }}";
                            let fullUrl = routeUrl + '?id=' + encodeURIComponent(agendaId) + '&token=' + encodeURIComponent(token);
                            window.location.href = fullUrl;
                        }
                    }else{
                        alert(signedMessage.message);
                        let routeUrl = "{{ route('bulletin.page') }}";
                        let fullUrl = routeUrl + '?id=' + encodeURIComponent(agendaId) + '&token=' + encodeURIComponent(token);
                        window.location.href = fullUrl;
                    }
                      
                } catch (error) {
                    alert('An error occurred while update the server');
                    console.error(error); 
                }
            }

            countPublishedKey();
            setInterval(countPublishedKey, 1000);//60000);
        });
    </script>
@endsection