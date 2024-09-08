@extends('layouts.app-admin')

@section('css')
    <style>
        .space-inbetween-2 > div {
            margin-right: 15px;
            margin-bottom: 10px;
        }

    </style>
@endsection

    @section('content')
    
    
    <div class="nk-wrap ">
                @include('layouts.partials.sidebar')
              @include('layouts.partials.header')
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-lg">
                        <div class="nk-content-body">
                            <div class="components-preview wide-md mx-auto">
                                
                                <div class="nk-block nk-block-lg">
                                  
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
                                            <form action="{{ route('agenda.post') }}" method="post">
                                                @method('post')
                                                @csrf
                                                <div class="preview-block">
                                                    <span class="preview-title-lg overline-title">Create Agenda</span>
                                                    <div class="d-flex justify-content-end mt-2">
                                                        <span style="cursor: pointer; font-size: 20px" id="more-agenda" class="text-success"><em class="icon ni ni-plus-round"></em></span>
                                                    </div>
                                                    <div class="row gy-4">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label class="form-label" for="title-1">Agenda Title <i class="text-danger">*</i></label> 
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label class="form-label" for="description-1">Agenda Description</label>
                                                                <div class="form-control-wrap">
                                                                    <textarea class="form-control" name="description" id="description" value="{{ old('description') }}"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="d-flex justify-content-end mt-4">
                                                            <span style="cursor: pointer; font-size: 20px; display:none;" class="remove-agenda text-danger"><em class="icon ni ni-minus-round"></em></span>
                                                        </div>
                                                            --}}
                                                        <span class="preview-title-lg overline-title">Provide poll answer</span>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="answer-1" name="answer[]" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="answer-2" name="answer[]" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-end mb-3">
                                                            <button type="submit" class="btn btn-success btn-sm">Create</button>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                    </div>
                                                </div>
                                            </form>
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
                let maxAgendas = 5;
                let agendaCount = 1;

                $('#more-agenda').click(function() {
                    if (agendaCount < maxAgendas) {
                        let newAgenda = $('.agenda-container:first').clone();
                        newAgenda.find('input, textarea').val('');
                        newAgenda.find('.remove-agenda').show();
                        $('.agenda-container:last').after(newAgenda);
                        agendaCount++;

                        if (agendaCount === maxAgendas) {
                            $('#more-agenda').hide();
                        }
                    }
                });

                $(document).on('click', '.remove-agenda', function() {
                    $(this).closest('.agenda-container').remove();
                    agendaCount--;

                    if (agendaCount < maxAgendas) {
                        $('#more-agenda').show();
                    }
                });
            });


        </script>
    @endsection

@endsection
