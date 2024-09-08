@extends('layouts.app-admin')


    {{-- <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div> --}}

    @section('content')
    
    
    <div class="nk-wrap ">
                @include('layouts.partials.sidebar')
              @include('layouts.partials.header')
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-lg">
                        <div class="nk-content-body">
                            <div class="components-preview wide-md mx-auto">
                                
                                <div class="nk-block nk-block-lg">
                                    <div class="nk-block-head">
                                        <div class="nk-block-head-content">
                                            <h4 class="nk-block-title">Agenda</h4>
                                        </div>
                                    </div>
                                    <div class="card card-bordered card-preview">
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="card-inner">
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="{{ route('agenda') }}" class="btn btn-outline-success btn-sm">Create New Agenda</a>
                                            </div>
                                            <table class="datatable-init table mt-2">
                                                <thead>
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>Title</th>
                                                        <th>Voting Links</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    @foreach ($agendas as $index => $agenda)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $agenda->title }}</td>
                                                            <td>
                                                                <div class="form-control-wrap">
                                                                    <div class="form-icon form-icon-right">
                                                                        <em style="cursor: pointer" class="icon ni ni-copy copy-link-btn" data-agenda-id="{{ $agenda->id }}"></em>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="voting-link-{{ $agenda->id }}" value="*******************************" readonly>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-more-h"></em></a>
                                                                    <div class="dropdown-menu dropdown-menu-end">
                                                                        <ul class="link-list-opt no-bdr">
                                                                            <li><a href="javascript:void(0)" class="generateLinkBtn" data-agenda-id="{{ $agenda->id }}"><em class="icon ni ni-link"></em><span>Generate Link</span></a></li>
                                                                            {{-- <li><a href="#"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                            <li><a href="#"><em class="icon ni ni-edit"></em><span>Edit</span></a></li> --}}
                                                                            <li class="divider"></li>
                                                                            <li><a href="{{ route('agenda.delete', ['id' => $agenda->id]) }}" onclick="event.preventDefault(); document.getElementById('delete-form').submit();" class="text-danger"><em class="icon ni ni-delete-fill"></em><span>Delete</span></a></li>
                                                                            
                                                                            <form id="delete-form" action="{{ route('agenda.delete', ['id' => $agenda->id]) }}" method="POST" class="d-none">
                                                                                @csrf
                                                                                @method("DELETE")
                                                                            </form>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; 2024 iVote.
                            </div>
                           
                        </div>
                    </div>
                </div>
              
            </div>
        
    @section('js')
        <script src="{{ asset('assets/js/libs/datatable-btns.js?ver=3.2.0') }}"></script>
    
        <script>
            $(document).ready(function() {
                $('.generateLinkBtn').on('click', function() {
                    let agendaId = $(this).data('agenda-id');
                    let $inputField = $(this).closest('ul').find('.votingLinkInput');
            
                    let routeUrl = "{{ route('agenda.share', ':id') }}";
                    routeUrl = routeUrl.replace(':id', agendaId);
            
                    $.ajax({
                        url: routeUrl,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                $('#voting-link-' + agendaId).val(response.url);
                            } else {
                                alert(response.message);
                            }
                            
                        },
                        error: function(xhr) {
                            alert("An error occurred while generating the voting link.");
                        }
                    });
                });

                $('.copy-link-btn').on('click', function() {
                    var agendaId = $(this).data('agenda-id');
                    var inputField = $('#voting-link-' + agendaId);

                    if (inputField.val() !== '*******************************' ) {
                        inputField.select();
                        document.execCommand('copy');
                        alert('Link copied to clipboard!');
                    } else {
                        alert('No link to copy. Please generate a link first by clicking on the ellipsis.');
                    }
                });
            });
            </script>
    @endsection

@endsection
