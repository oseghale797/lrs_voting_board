@extends('layouts.app')

@section('content')
    <div class="row">
        <h3 class="text-center">Voting System with Linkable Ring Signature</h3>
        <div class="jumbotron text-center">
            <div class="row">
                <div class="col-md-12 p-4">
                    <h6>Thank you for voting.</h6>

                    <a href="{{ route('bulletin.page', ['id' => request('id'), 'token' => request('token')]) }}">Goto Bulletin</a>
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
    <script>
        let storedSecretKey = localStorage.getItem('sk');
        let storedPublicKey = localStorage.getItem('pk');
        
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
    </script>
   
@endsection