@extends('layouts.app')

@section('content')
    <div class="row">
        <h3 class="text-center">Voting System with Linkable Ring Signature</h3>
        <div class="jumbotron text-center">
            <div class="row">
                <p id="message">Click on the button below to generate and publish your public key on the bulleton board.</p>
                    <div class="col-md-12">
                        <div id="key-generator-container">
                            <div class="col-md-12">
                                <button  class="btn btn-sm btn-primary" id="generate-key-btn">Generate Key</button>
                            </div>
                        </div>
                        <div style="display: none" id="key-publisher-container">
                            <div class="col-md-12">
                                <button  class="btn btn-sm btn-primary" id="publish-key-btn">Publish Key</button>
                            </div>
                        </div>
                    </div>
              
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

        let routeUrl = "{{ route('key.publish') }}";
        let fullUrl = routeUrl + '?id=' + encodeURIComponent(agendaId) + '&token=' + encodeURIComponent(token);

        

        $(document).ready(function() {
            $('#generate-key-btn').on('click', async function() {
                let id = localStorage.getItem('agendaId');
                let sk = localStorage.getItem('sk');
                let pk = localStorage.getItem('pk');

                // Check if the key for this agenda already exists
                if (id !== null && sk !== null && pk !== null && id === token) {
                    // The key for this agenda has already been generated
                    console.log('Key for this agenda has already been generated.');
                    window.location.href = fullUrl;
                } else {
                    // Generate the key since it doesn't exist for this agendaId
                    try {
                        const response = await generateKeyPair();
                        console.log(response);
                       
                        if (response.sk && response.pk) {
                            // Store in localStorage
                            localStorage.setItem('sk', response.sk);
                            localStorage.setItem('pk', response.pk);
                            localStorage.setItem('agendaId', token); 

                            await handleServerStorage(response.pk);
                        } else {
                            console.error('Key generation failed.');
                            alert('An error occurred trying to generate your key. Try again later');
                        }
                    } catch (error) {
                        console.error('Error generating key pair:', error);
                        alert('An error occurred trying to generate your key. Try again later');
                    }
                }
            });

        });

        async function handleServerStorage(publicKey) {
            try {
                let response = await $.ajax({
                    url: "{{ route('key.generate.post') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        id: agendaId,
                        token: token,
                        public_key: publicKey
                    }
                });
                
                if (response.status) {
                    window.location.href = fullUrl;
                } else {
                    alert(response.message);
                }
            } catch (error) {
                alert('An error occurred while submitting your vote.');
                console.error(error); 
            }
        }


    </script>
@endsection